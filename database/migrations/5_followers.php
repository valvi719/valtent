<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id');    // User being followed
            $table->unsignedBigInteger('follower');  // Follower user
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade');
            $table->foreign('follower')->references('id')->on('creators')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
