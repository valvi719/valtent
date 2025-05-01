<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('following', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id');   // Follower user
            $table->unsignedBigInteger('whom');     // User being followed
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade');
            $table->foreign('whom')->references('id')->on('creators')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('following');
    }
};
