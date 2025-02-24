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
            $table->increments('id'); // auto-increment primary key
            $table->integer('cre_id')->nullable(); // integer, nullable
            $table->string('balance', 250); // varchar(250)
            $table->string('deposit', 250); // varchar(250)
            $table->string('withdrawal', 250); // varchar(250)
            $table->string('interest', 250); // varchar(250)
            $table->timestamps(0); // created_at and updated_at with default current timestamp
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
