<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Services\CurrentTeamResolver;
use App\Services\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    public function __invoke(
        Request $request,
        CurrentTeamResolver $teams,
        GlobalSearchService $search,
    ): JsonResponse {
        $data = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'types' => ['sometimes', 'array'],
            'types.*' => ['string', Rule::in(GlobalSearchService::TYPES)],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:25'],
        ]);

        $results = $search->search(
            $teams->resolve($request->user()),
            trim($data['q']),
            $data['types'] ?? [],
            $data['limit'] ?? 10,
        );

        return response()->json([
            'data' => $results,
            'meta' => [
                'query' => trim($data['q']),
                'total' => collect($results)->sum(fn ($items) => count($items)),
            ],
        ]);
    }
}
