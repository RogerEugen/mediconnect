<?php

namespace App\Services;

use App\Models\MedicalCase;
use Illuminate\Support\Collection;

class CaseSimilarityService
{
    private const STOP_WORDS = [
        'the', 'and', 'with', 'from', 'that', 'this', 'patient', 'case', 'for', 'has', 'have',
        'was', 'were', 'are', 'but', 'not', 'kwa', 'na', 'ya', 'wa', 'ni', 'hii', 'mgonjwa',
    ];

    public function score(MedicalCase $source, MedicalCase $candidate): int
    {
        $sourceTokens = $this->tokens($source);
        $candidateTokens = $this->tokens($candidate);

        if ($sourceTokens === [] || $candidateTokens === []) {
            return 0;
        }

        $intersection = count(array_intersect($sourceTokens, $candidateTokens));
        $union = count(array_unique(array_merge($sourceTokens, $candidateTokens)));
        $textScore = $union > 0 ? ($intersection / $union) * 80 : 0;
        $specialtyScore = $source->specialization_id === $candidate->specialization_id ? 20 : 0;

        return (int) round(min(100, $textScore + $specialtyScore));
    }

    public function matchesFor(MedicalCase $source, int $limit = 5, int $minimum = 30): Collection
    {
        return MedicalCase::query()
            ->whereKeyNot($source->id)
            ->where('posted_by', '!=', $source->posted_by)
            ->where(function ($query) {
                $query->whereNotNull('resolution_notes')->orWhereHas('discussions');
            })
            ->with(['specialization', 'discussions' => fn ($query) => $query->with('user')->oldest()])
            ->withCount('discussions')
            ->latest('updated_at')
            ->limit(100)
            ->get()
            ->map(fn (MedicalCase $case) => ['case' => $case, 'score' => $this->score($source, $case)])
            ->filter(fn (array $match) => $match['score'] >= $minimum)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private function tokens(MedicalCase $case): array
    {
        $text = implode(' ', array_filter([
            $case->title,
            $case->description,
            $case->symptoms,
            $case->clinical_history,
            $case->investigation_results,
            $case->discussion_question,
        ]));
        preg_match_all('/[\p{L}\p{N}]{3,}/u', mb_strtolower($text), $matches);

        return array_values(array_unique(array_filter(
            $matches[0],
            fn (string $token) => ! in_array($token, self::STOP_WORDS, true)
        )));
    }
}
