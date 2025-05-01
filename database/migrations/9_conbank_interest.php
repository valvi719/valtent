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
        Schema::create('conbank_interest', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id');
            $table->unsignedBigInteger('con_id')->nullable(); // Assuming 'con_id' might be nullable
            $table->unsignedBigInteger('conbank_id');
            $table->decimal('interest', 8, 4);
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade'); // Assuming 'creators' table
            $table->foreign('con_id')->references('id')->on('contents')->onDelete('set null'); // Assuming 'contents' table
            $table->foreign('conbank_id')->references('id')->on('conbank')->onDelete('cascade'); // Assuming 'conbanks' table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conbank_interest');
    }
};