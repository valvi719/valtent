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
        Schema::create('moments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id'); // Recipient
            $table->unsignedBigInteger('triggered_by'); // Actor
            $table->string('type'); // like, follow
            $table->string('message');
            $table->string('link')->nullable(); // e.g., /content/123
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade');
            $table->foreign('triggered_by')->references('id')->on('creators')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moments');
    }
};
