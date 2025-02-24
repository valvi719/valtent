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
            $table->increments('id'); // auto-increment primary key
            $table->integer('cre_id')->nullable();
            $table->integer('con_id')->nullable();
            $table->integer('conbank_id')->nullable();
            $table->integer('interest')->nullable()->comment('percentage'); // storing percentage
            $table->timestamps(0); // created_at and updated_at with default current timestamp
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
