<?php

// app/Http/Controllers/Specialist/SpecialistController.php

namespace App\Http\Controllers\Specialist;

use App\Http\Controllers\Controller;
use App\Models\Discussion;
use App\Models\MedicalCase;
use Illuminate\Support\Facades\Auth;

class SpecialistController extends Controller
{
    public function dashboard()
    {
        $specialist = Auth::user();

        $visibleCases = MedicalCase::query()->visibleTo($specialist);
        $stats = [
            'relevant_cases' => (clone $visibleCases)->whereIn('status', ['open', 'in_discussion'])->count(),
            'my_contributions' => Discussion::where('user_id', $specialist->id)->count(),
            'unanswered' => (clone $visibleCases)->doesntHave('discussions')->count(),
            'resolved' => (clone $visibleCases)->where('status', 'resolved')->count(),
        ];
        $recentCases = MedicalCase::with(['specialization', 'postedBy'])
            ->visibleTo($specialist)
            ->withCount('discussions')
            ->whereIn('status', ['open', 'in_discussion'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        return view('Specialist.Dashboard', compact('stats', 'recentCases'));
    }
}
