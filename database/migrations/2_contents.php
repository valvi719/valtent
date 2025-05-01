<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['NFT', 'Media']);
            $table->string('value');
            $table->unsignedBigInteger('cre_id')->nullable();
            $table->integer('duration')->nullable();
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
