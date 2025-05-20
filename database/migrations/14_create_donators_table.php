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
        Schema::create('donators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('donator_id'); // the creator who donates
            $table->unsignedBigInteger('recipient_id'); // the creator who receives the donation
            $table->unsignedBigInteger('content_id')->nullable(); // optional: specific content
            $table->decimal('amount', 10, 2); // donation amount
            $table->timestamps();
    
            // Foreign keys
            $table->foreign('donator_id')->references('id')->on('creators')->onDelete('cascade');
            $table->foreign('recipient_id')->references('id')->on('creators')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donators');
    }
};
