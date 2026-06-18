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

        $specializationIds = $specialist->specializations()->pluck('specializations.id');
        $stats = [
            'relevant_cases' => MedicalCase::whereIn('specialization_id', $specializationIds)->whereIn('status', ['open', 'in_discussion'])->count(),
            'my_contributions' => Discussion::where('user_id', $specialist->id)->count(),
            'unanswered' => MedicalCase::doesntHave('discussions')->count(),
            'resolved' => MedicalCase::where('status', 'resolved')->count(),
        ];
        $recentCases = MedicalCase::with(['specialization', 'postedBy'])
            ->withCount('discussions')
            ->when($specializationIds->isNotEmpty(), fn ($query) => $query->whereIn('specialization_id', $specializationIds))
            ->whereIn('status', ['open', 'in_discussion'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        return view('Specialist.Dashboard', compact('stats', 'recentCases'));
    }
}
