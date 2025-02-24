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
            $table->increments('id'); // auto-increment primary key
            $table->string('name', 250); // varchar(250)
            $table->integer('con_id')->nullable(); // integer, nullable
            $table->integer('viewed_by')->nullable(); // integer, nullable
            $table->timestamps(0); // created_at and updated_at with default current timestamp
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
