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
            // Add HOD fields
            $table->string('hod_name')->nullable(); // HOD's name
            $table->boolean('hod_recommendation')->nullable(); // 0 or 1 for recommendation
            $table->dateTime('hod_forwarded_date')->nullable(); // When HOD forwarded
            $table->string('hod_designation')->nullable(); // HOD's designation (for future use)
            $table->text('leave_document')->nullable()->change();
            $table->text('consent_letter')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            // Remove HOD fields
            $table->dropColumn(['hod_name', 'hod_recommendation', 'hod_forwarded_date', 'hod_designation']);
        });
    }
};
