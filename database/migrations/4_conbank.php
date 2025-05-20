<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conbank', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cre_id');
            $table->decimal('balance', 15, 2)->default(0)->nullable();
            $table->decimal('deposits', 15, 2)->default(0);
            $table->decimal('withdrawals', 15, 2)->default(0);
            $table->decimal('loans', 15, 2)->default(0);
            $table->decimal('interests', 15, 2)->default(0);
            $table->timestamp('last_interest_applied')->nullable();
            $table->timestamps();

            $table->foreign('cre_id')->references('id')->on('creators')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conbank');
    }
};
