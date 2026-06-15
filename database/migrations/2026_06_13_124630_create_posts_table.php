<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->string('visibility', 10)->default('public');
            $table->timestamps();

            $table->index('user_id');
            $table->index(['visibility', 'created_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
