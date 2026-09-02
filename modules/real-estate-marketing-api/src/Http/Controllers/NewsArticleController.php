<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Marketing\Models\NewsArticle;
use Liberu\RealEstate\MarketingApi\Http\Resources\NewsArticleResource;

final class NewsArticleController
{
    public function index(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return NewsArticleResource::collection(NewsArticle::query()->published()->visibleToTeam($teamId)->latest('published_at')->paginate(min(50, max(1, $request->integer('limit', 25)))))->response();
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return (new NewsArticleResource(NewsArticle::query()->published()->visibleToTeam($teamId)->where('slug', $slug)->firstOrFail()))->response();
    }

    public function latest(Request $request): JsonResponse
    {
        return $this->index($request);
    }

    public function featured(Request $request): JsonResponse
    {
        $teamId = $request->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return NewsArticleResource::collection(NewsArticle::query()->published()->featured()->visibleToTeam($teamId)->latest('published_at')->limit(min(50, max(1, $request->integer('limit', 10))))->get())->response();
    }
}
