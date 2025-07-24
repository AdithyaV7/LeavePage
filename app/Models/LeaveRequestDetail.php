<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no',
        'detail',
        'country',
        'travel_from_date',
        'travel_to_date',
        'documents',
    ];

    protected $casts = [
        'documents' => 'array',
    ];

    // Relationship with LeaveDetail
    public function leaveDetail()
    {
        return $this->belongsTo(LeaveDetail::class, 'reference_no', 'reference_no');
    }
}
