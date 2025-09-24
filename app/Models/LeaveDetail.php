<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'empno',
        'nic',
        'leave_type_id',
        'from_date',
        'to_date',
        'duration',
        'leave_document',
        'consent_letter',
        'status_id',
        'applied_date',
        'reference_no',
        'form_status',
        'remark',
        'department_id',
        'faculty_id',
        'academic_year',
        // Employee numbers for approval hierarchy
        'ma_empno',
        'hod_empno',
        'dean_empno',
        'vc_empno',
        // HOD fields
        'hod_adequate_staff',
        'hod_teaching_covered',
        'hod_exam_work_completed',
        'hod_recommend',
        'hod_not_recommend_reason',
        'hod_other_remarks',
        'hod_reviewed_by',
        'hod_reviewed_at',
        'hod_signature',
        'hod_name',
        'hod_recommendation',
        'hod_forwarded_date',
        'hod_designation',
        // VC fields
        'vc_recommend_committee',
        'vc_approved_council',
        'vc_remarks',
        'vc_signature',
        'vc_name',
        'vc_reviewed_at',
        'vc_checked',
        'dean_reviewed_at',
        'dean_name',
        'dean_designation',
    ];

    protected $casts = [
        'leave_document' => 'array',
        'consent_letter' => 'array',
    ];

    // Relationships
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'stat_id');
    }

    public function user()
    {
        return $this->belongsTo(PersonalDetail::class, 'nic', 'nic');
    }

    public function personalDetail()
    {
        return $this->belongsTo(PersonalDetail::class, 'empno', 'empno');
    }

    public function leaveRequestDetails()
    {
        return $this->hasMany(LeaveRequestDetail::class, 'reference_no', 'reference_no');
    }
}
