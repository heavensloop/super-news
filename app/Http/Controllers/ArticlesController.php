<?php

namespace App\Http\Controllers;

use App\Enum\FilterType;
use App\Http\Requests\FilteredArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ArticlesController extends Controller
{
    public function featured()
    {
        // fetch the latest 12 articles grouped by category
        $articles = Article::inRandomOrder()
            ->take(12)
            ->get();

        return ArticleResource::collection($articles);
    }

    public function filtered(FilteredArticleRequest $filterRequest)
    {
        $filters = $filterRequest->getFilters();

        $articleQuery = Article::query();

        foreach(FilterType::cases() as $filterType) {
            $filterKey = $filterType->value;

            if (isset($filters[$filterKey]) && is_array($filters[$filterKey])) {
                $articleQuery->orWhere(function(Builder $query) use ($filterKey, $filters) {
                    $query->whereIn($filterKey, $filters[$filterKey]);
                });
            }
        }

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
