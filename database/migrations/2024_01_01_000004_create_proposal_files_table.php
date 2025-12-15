<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['logo', 'image'])->default('image');
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type');
            $table->integer('size');
            $table->json('meta')->nullable(); // Дополнительные данные
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_files');
    }
};



