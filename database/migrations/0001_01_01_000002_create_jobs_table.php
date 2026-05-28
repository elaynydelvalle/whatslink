<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('links')) return;
        Schema::create('links', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('phone', 20);
            $table->text('message');
            $table->string('url');
            $table->unsignedInteger('clicks')->default(0);
            $table->timestamps();
        });

        Schema::create('link_hits', function (Blueprint $table) {
            $table->id();
            $table->string('link_id');
            $table->foreign('link_id')->references('id')->on('links')->cascadeOnDelete();
            $table->string('ip', 45)->nullable();
            $table->string('ua', 300)->nullable();
            $table->string('referer', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_hits');
        Schema::dropIfExists('links');
    }
};
