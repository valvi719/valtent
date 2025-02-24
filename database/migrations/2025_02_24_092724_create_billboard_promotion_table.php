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
        Schema::create('billboard_promotion', function (Blueprint $table) {
            $table->increments('id'); // auto-increment primary key
            $table->integer('cre_id')->nullable();
            $table->integer('con_id')->nullable();
            $table->string('billboard_name', 250)->nullable();
            $table->string('billboard_address', 250)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billboard_promotion');
    }
};
