<?php
// app/Http/Middleware/AuditMiddleware.php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditMiddleware
{
    // Routes that trigger auto-logging
    private array $watchedRoutes = [
        'doctor.patients.show'          => 'viewed_patient',
        'doctor.patients.records'       => 'viewed_medical_records',
        'doctor.medical-records.show'   => 'viewed_medical_record',
        'specialist.cases.show'         => 'specialist_viewed_case',
    ];

    public function handle(Request $request, Closure $next): mixed
    {
        $response = $next($request);

        if (Auth::check() && $request->isMethod('GET')) {
            $routeName = $request->route()?->getName();

            if ($routeName && isset($this->watchedRoutes[$routeName])) {
                AuditLog::record(
                    $this->watchedRoutes[$routeName],
                    'Auto-logged: accessed ' . $routeName
                );
            }
        }

        return $response;
    }
}