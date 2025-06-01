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
        Schema::table('contents', function (Blueprint $table) {
            $table->string('moderation_status')->default('pending')->after('duration'); // pending, approved, rejected
            $table->string('moderation_id')->nullable()->after('moderation_status'); // Sightengine request_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn('moderation_status');
            $table->dropColumn('moderation_id');
        });
    }
};
