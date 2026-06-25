<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('link_hits', function (Blueprint $table) {
            $table->id();
            $table->string('link_id');
            $table->string('ip', 45)->nullable();
            $table->string('ua', 300)->nullable();
            $table->string('referer', 500)->nullable();
            $table->foreign('link_id')->references('id')->on('links')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('link_hits');
    }
};
