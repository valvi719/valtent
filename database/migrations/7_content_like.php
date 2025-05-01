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
        Schema::create('content_like', function (Blueprint $table) {
            $table->increments('id'); // auto-increment primary key
            $table->string('name', 250)->nullable(); // varchar(250), nullable
            $table->unsignedBigInteger('con_id');
            $table->unsignedBigInteger('liked_by');
            $table->timestamps(0); // created_at and updated_at with default current timestamp

            $table->foreign('con_id')->references('id')->on('contents')->onDelete('cascade');
            $table->foreign('liked_by')->references('id')->on('creators')->onDelete('cascade');

            $table->index('con_id'); // Recommended
            $table->index('liked_by'); // Recommended
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_like');
    }
};