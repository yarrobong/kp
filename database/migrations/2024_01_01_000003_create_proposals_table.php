<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->string('offer_number')->nullable();
            $table->date('offer_date');
            $table->text('seller_info')->nullable();
            $table->text('buyer_info')->nullable();
            $table->longText('body_html');
            $table->string('currency', 10)->default('₽');
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->text('terms')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('proposal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('unit', 20)->default('шт.');
            $table->decimal('price', 10, 2);
            $table->decimal('discount', 5, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_items');
        Schema::dropIfExists('proposals');
    }
};



