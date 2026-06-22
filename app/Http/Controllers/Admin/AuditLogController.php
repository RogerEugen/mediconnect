<?php

// app/Http/Controllers/Admin/AuditLogController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    private const CLINICAL_DISCUSSION_ACTIONS = [
        'posted_case',
        'viewed_case',
        'resolved_case',
        'closed_case',
        'assigned_case',
        'accepted_case',
        'declined_case',
        'completed_case',
        'posted_discussion',
        'specialist_viewed_case',
    ];

    public function index(Request $request)
    {
        $query = $this->adminVisibleLogs()->with('user')->latest('created_at');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        $logs = $query->paginate(25)->withQueryString();

        // Data for filters
        $users = User::orderBy('name')->get(['id', 'name', 'role']);
        $actions = $this->adminVisibleLogs()->distinct()->pluck('action')->sort()->values();
        $modelTypes = $this->adminVisibleLogs()->distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();

        // Summary stats
        $stats = [
            'today' => $this->adminVisibleLogs()->whereDate('created_at', today())->count(),
            'this_week' => $this->adminVisibleLogs()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'total' => $this->adminVisibleLogs()->count(),
            'unique_users' => $this->adminVisibleLogs()->distinct('user_id')->count('user_id'),
        ];

        return view('Admin.audit-logs.index', compact(
            'logs', 'users', 'actions', 'modelTypes', 'stats'
        ));
    }

    public function show(AuditLog $auditLog)
    {
        abort_if(
            in_array($auditLog->action, self::CLINICAL_DISCUSSION_ACTIONS, true)
            || in_array($auditLog->model_type, ['MedicalCase', 'Discussion'], true),
            404
        );

        $auditLog->load('user');

        return view('Admin.audit-logs.show', compact('auditLog'));
    }

    public function export(Request $request)
    {
        $query = $this->adminVisibleLogs()->with('user')->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->take(5000)->get();

        $filename = 'audit_logs_'.now()->format('Y_m_d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'User', 'Role', 'Action', 'Description',
                'Model Type', 'Model ID', 'IP Address', 'Date / Time',
            ]);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->user?->role ?? '—',
                    $log->action,
                    $log->description,
                    $log->model_type ?? '—',
                    $log->model_id ?? '—',
                    $log->ip_address ?? '—',
                    $log->created_at->format('d M Y H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function adminVisibleLogs()
    {
        return AuditLog::query()
            ->whereNotIn('action', self::CLINICAL_DISCUSSION_ACTIONS)
            ->where(fn ($query) => $query
                ->whereNull('model_type')
                ->orWhereNotIn('model_type', ['MedicalCase', 'Discussion'])
            );
    }
}
