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
        Schema::create('creators', function (Blueprint $table) {
            $table->increments('id'); // auto-increment primary key
            $table->string('name', 250)->comment('Name of Creator'); // varchar(250)
            $table->string('phone', 250); // varchar(250)
            $table->string('email', 255)->nullable()->charset('utf8')->collation('utf8_general_ci'); // varchar(255)
            $table->string('password', 250); // varchar(250)
            $table->integer('otp')->nullable(); // integer, nullable
            $table->text('address'); // text field
            $table->string('city', 100); // varchar(100)
            $table->string('linked_account', 250)->nullable(); // varchar(250), nullable
            $table->integer('networth')->nullable()->comment('networth of creator'); // integer, nullable
            $table->string('email_verified_at', 250)->nullable(); // varchar(250), nullable
            $table->timestamps(0); // created_at and updated_at with default current timestamp
            $table->timestamp('otp_expires_at')->nullable(); // timestamp, nullable
            $table->string('profile_photo', 255)->nullable(); // varchar(255), nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creators');
    }
};
