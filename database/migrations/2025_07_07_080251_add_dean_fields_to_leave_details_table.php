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
            $table->string('dean_reviewed_by')->nullable()->after('dean_remarks');
            $table->dateTime('dean_reviewed_at')->nullable()->after('dean_reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            $table->dropColumn(['dean_reviewed_by', 'dean_reviewed_at']);
        });
    }
};
