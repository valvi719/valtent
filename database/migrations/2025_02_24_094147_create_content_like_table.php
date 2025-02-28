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
            $table->integer('con_id')->constrained()->onDelete('cascade');
            $table->integer('liked_by')->constrained()->onDelete('cascade');
            $table->timestamps(0); // created_at and updated_at with default current timestamp
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
