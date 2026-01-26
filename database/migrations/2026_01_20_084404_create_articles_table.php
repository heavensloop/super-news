<?php

use App\Enum\ContentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category');
            $table->string('source');
            $table->string('url');
            $table->string('news_source')->nullable();
            $table->string('reference_hash')->unique();
            $table->longText('content')->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at');
            $table->string('content_status')->default(ContentStatus::PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
