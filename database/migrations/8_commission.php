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
        Schema::create('commission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('net_amount', 10, 2);
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade'); // Assuming you have a 'creators' table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission');
    }
};