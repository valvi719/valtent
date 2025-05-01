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
        Schema::create('fbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id');
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->decimal('deposit', 12, 2)->default(0.00);
            $table->decimal('withdraw', 12, 2)->default(0.00);
            $table->decimal('interest', 8, 4)->default(0.00);
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade'); // Assuming you have a 'creators' table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fbs');
    }
};