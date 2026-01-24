<?php

namespace Tests\Feature;

use App\Models\Article;
use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticlesTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_fetch_featured_articles(): void
    {
        $this->seed(ArticleSeeder::class);
        $response = $this->get('/api/v1/articles/featured');
        $response->assertStatus(200);

        $response->assertJsonIsArray();
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'description',
                'content',
                'category',
                'source',
                'url',
                'image_url',
                'author',
                'published_at',
                'created_at',
                'updated_at',
                'slug',
                'formatted_date',
            ]
        ]);

        $response->assertJsonCount(12);
    }
}
