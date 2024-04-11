<?php

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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->constrained();
            $table->text('brief_intro');
            $table->longText('content');
            $table->boolean('status_save_draft')->default(0);
            $table->enum('send_approval', [0, 1])->default(0);
            $table->enum('status_approval', [0, 1, 2])->default(0);
            $table->enum('status_get_post', [0, 1])->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
