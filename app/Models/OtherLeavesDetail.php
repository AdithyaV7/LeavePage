<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherLeavesDetail extends Model
{
    use HasFactory;

    protected $table = 'otherLeavesDetails';

    protected $fillable = [
        'reference_no',
        'leave_type_id',
        'from_date',
        'end_date',
        'duration',
        'leave_document',
        'consent_letter',
    ];

    protected $casts = [
        'from_date' => 'date',
        'end_date' => 'date',
        'leave_document' => 'array',
        'consent_letter' => 'array',
    ];

    /**
     * Get the leave type that owns the other leave detail.
     */
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
