<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            Article::factory()->times(50)->create();
        } catch (\Exception $e) {
            // Handle exception if needed
            dd($e->getMessage());
        }
    }
}
