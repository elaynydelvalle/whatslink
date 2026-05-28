<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('plans')) return;
        Schema::create('plans', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->decimal('price', 8, 2)->default(0);
            $table->integer('max_links')->default(5);
            $table->boolean('highlighted')->default(false);
            $table->boolean('active')->default(true);
            $table->string('cta')->default('Assinar');
            $table->json('features')->nullable();
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
