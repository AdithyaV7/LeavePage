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
            // Add department_id, faculty_id columns
            $table->unsignedBigInteger('department_id')->nullable()->after('nic');
            $table->unsignedBigInteger('faculty_id')->nullable()->after('department_id');
            
            // Add employee numbers for approval hierarchy
            $table->string('ma_empno')->nullable()->after('faculty_id')->comment('Management Assistant Employee Number');
            $table->string('hod_empno')->nullable()->after('ma_empno')->comment('Head of Department Employee Number');
            $table->string('dean_empno')->nullable()->after('hod_empno')->comment('Dean Employee Number');
            $table->string('vc_empno')->nullable()->after('dean_empno')->comment('Vice Chancellor Employee Number');
            
            // Add foreign key constraints
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_details', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['department_id']);
            $table->dropForeign(['faculty_id']);
            
            // Drop columns
            $table->dropColumn([
                'department_id',
                'faculty_id', 
                'ma_empno',
                'hod_empno',
                'dean_empno',
                'vc_empno'
            ]);
        });
    }
};
