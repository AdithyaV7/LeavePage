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
        Schema::create('leave_details', function (Blueprint $table) {
            $table->id();
            
            // Employee identification fields
            $table->unsignedInteger('empno');
            $table->string('nic');
            
            // Department and faculty hierarchy
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('faculty_id')->nullable();
            
            // Employee numbers for approval hierarchy
            $table->string('ma_empno')->nullable()->comment('Management Assistant Employee Number');
            $table->string('hod_empno')->nullable()->comment('Head of Department Employee Number');
            $table->string('dean_empno')->nullable()->comment('Dean Employee Number');
            $table->string('vc_empno')->nullable()->comment('Vice Chancellor Employee Number');
            
            // Leave application details
            $table->string('reference_no')->unique();
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->integer('duration')->nullable();
            $table->text('leave_document')->nullable();
            $table->text('consent_letter')->nullable();
            
            // Status fields
            $table->tinyInteger('form_status')->default(4);
            $table->integer('status_id');
            $table->date('applied_date');
            $table->text('remark')->nullable();
            
            // HOD review fields
            $table->boolean('hod_adequate_staff')->nullable();
            $table->boolean('hod_teaching_covered')->nullable();
            $table->boolean('hod_exam_work_completed')->nullable();
            $table->boolean('hod_recommend')->nullable();
            $table->text('hod_not_recommend_reason')->nullable();
            $table->text('hod_other_remarks')->nullable();
            $table->string('hod_reviewed_by')->nullable();
            $table->dateTime('hod_reviewed_at')->nullable();
            $table->string('hod_signature')->nullable();
            $table->string('hod_name')->nullable(); // HOD's name
            $table->boolean('hod_recommendation')->nullable(); // 0 or 1 for recommendation
            $table->dateTime('hod_forwarded_date')->nullable(); // When HOD forwarded
            $table->string('hod_designation')->nullable(); // HOD's designation
            
            // Dean review fields
            $table->boolean('dean_recommend')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->string('dean_reviewed_by')->nullable();
            $table->dateTime('dean_reviewed_at')->nullable();
            $table->string('dean_name')->nullable();
            $table->string('dean_designation')->nullable();
            
            // VC review fields
            $table->boolean('vc_recommend_committee')->nullable();
            $table->boolean('vc_approved_council')->nullable();
            $table->text('vc_remarks')->nullable();
            $table->string('vc_signature')->nullable();
            $table->string('vc_name')->nullable();
            $table->dateTime('vc_reviewed_at')->nullable();
            $table->boolean('vc_checked')->default(false);
            
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('empno')->references('employee_no')->on('employees')->onDelete('cascade');
            $table->foreign('nic')->references('nic')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreign('status_id')->references('stat_id')->on('statuses');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_details');
    }
};
