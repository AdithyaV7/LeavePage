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
        Schema::table('leave_details', function (Blueprint $table) {
            $table->string('dean_name')->nullable()->after('dean_reviewed_at');
            $table->string('dean_designation')->nullable()->after('dean_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->dropColumn(['dean_name', 'dean_designation']);
        });
    }
};
