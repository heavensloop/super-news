<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FeaturedArticlesController extends Controller
{
    public function featured()
    {
        // fetch the latest 12 articles grouped by category
        $articles = Article::inRandomOrder()
            ->take(12)
            ->get();

        return ArticleResource::collection($articles);
    }

    public function filtered(Request $filterRequest)
    {
        // fetch the latest 12 articles grouped by category
        $articles = Article::orderBy('id', 'desc')->paginate(12);

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
