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
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
            $table->string('slug')->nullable()->change();
            $table->text('brief_intro')->nullable()->change();
            $table->longText('content')->nullable()->change();
            $table->boolean('status_save_draft')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title')->nullable(false)->change();
        $table->string('slug')->nullable(false)->change();
        $table->text('brief_intro')->nullable(false)->change();
        $table->longText('content')->nullable(false)->change();
        $table->boolean('status_save_draft')->default(0)->change();
        });
    }
};
