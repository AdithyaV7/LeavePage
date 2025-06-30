<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDetail extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
