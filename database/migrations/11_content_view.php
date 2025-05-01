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
        Schema::create('content_view', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('con_id');
            $table->unsignedBigInteger('viewed_by')->nullable(); // Assuming 'viewed_by' might reference a users table
            $table->timestamps();

            $table->foreign('con_id')->references('id')->on('contents')->onDelete('cascade'); // Assuming 'contents' table
            $table->foreign('viewed_by')->references('id')->on('creators')->onDelete('set null'); // Assuming 'users' table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_view');
    }
};