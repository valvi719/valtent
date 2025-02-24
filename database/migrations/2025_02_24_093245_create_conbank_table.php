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
        Schema::create('conbank', function (Blueprint $table) {
            $table->increments('id'); // auto-increment primary key
            $table->integer('cre_id')->nullable();
            $table->integer('balance')->nullable();
            $table->integer('deposits')->nullable();
            $table->integer('withdrawals')->nullable();
            $table->integer('loans')->nullable();
            $table->integer('interests')->nullable();
            $table->timestamp('created_at')->nullable(); // timestamp column
            $table->timestamp('updated_at')->nullable(); // timestamp column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conbank');
    }
};
