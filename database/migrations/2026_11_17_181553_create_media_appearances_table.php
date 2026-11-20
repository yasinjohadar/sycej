<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_appearances', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 20)->default('article');
            $table->string('title', 500);
            $table->string('title_language', 5)->default('ar');
            $table->string('channel_ar', 150);
            $table->string('channel_en', 150)->nullable();
            $table->string('url', 1000);
            $table->string('youtube_id', 20)->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->date('published_on')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order', 'published_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_appearances');
    }
};
