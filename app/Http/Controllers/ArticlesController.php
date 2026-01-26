<?php

namespace App\Http\Controllers;

use App\Enum\ContentStatus;
use App\Http\Requests\FilteredArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ApplyFilterToQuery;
use Illuminate\Routing\Controller;

class ArticlesController extends Controller
{
    public function __construct(
        private readonly ApplyFilterToQuery $applyFilterToQuery
    ) {}
    public function featured()
    {
        $articles = Article::where('content_status', ContentStatus::POPULATED->value)->inRandomOrder()->take(12)->get();

        return ArticleResource::collection($articles);
    }

    public function filtered(FilteredArticleRequest $filterRequest)
    {
        $filters = $filterRequest->getFilters();
        $articleQuery = Article::where('content_status', ContentStatus::POPULATED->value);

        $articleQuery->where(function ($query) use ($filters) {
            foreach ($filters as $filter) {
                ['type' => $filterType, 'value' => $value] = $filter;
                ($this->applyFilterToQuery)($query, $filterType, $value);
            }
        });

        $articles = $articleQuery->orderBy('id', 'desc')->paginate(12);

        return ArticleResource::collection($articles);
    }

    public function show(string $article_id)
    {
        // Extract the numeric ID from the article_id
        $id = (int) explode('-', $article_id)[0];
        $article = Article::findOrFail($id);

        return new ArticleResource($article);
    }
}
