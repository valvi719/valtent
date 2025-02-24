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
        Schema::create('contents', function (Blueprint $table) {
            $$table->increments('id'); // auto-increment primary key
            $table->string('name', 250); // varchar(250)
            $table->string('type', 250); // varchar(250)
            $table->string('path', 250)->nullable(); // varchar(250), nullable
            $table->integer('cre_id')->nullable();
            $table->string('duration', 250)->nullable(); // varchar(250), nullable
            $table->string('origin', 250)->default('valtent'); // varchar(250), with default value
            $table->string('value', 250)->nullable(); // varchar(250), nullable
            $table->integer('nft_points')->nullable(); // integer, nullable
            $table->integer('nft_credits')->nullable(); // integer, nullable
            $table->timestamps(0); // created_at and updated_at with default current timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
