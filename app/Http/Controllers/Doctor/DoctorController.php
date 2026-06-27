<?php

// app/Http/Controllers/Doctor/DoctorController.php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\MedicalCase;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $stats = [
            'community_cases' => MedicalCase::where('posted_by', $user->id)->whereIn('status', ['open', 'in_discussion'])->count(),
            'my_cases' => MedicalCase::where('posted_by', $user->id)->count(),
            'my_contributions' => Discussion::where('user_id', $user->id)->count(),
            'unanswered' => MedicalCase::where('posted_by', $user->id)->doesntHave('discussions')->count(),
        ];
        $recentCases = MedicalCase::with(['specialization', 'postedBy'])
            ->where('posted_by', $user->id)
            ->withCount('discussions')
            ->latest('updated_at')
            ->take(6)
            ->get();

        return view('Doctor.Dashboard', compact('stats', 'recentCases'));
    }
}
