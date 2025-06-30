<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormStatus extends Model
{
    use HasFactory;

    protected $table = 'form_statuses';
    protected $primaryKey = 'form_stat_id';
    public $incrementing = false;

    protected $fillable = [
        'form_stat_id',
        'form_status',
        'description'
    ];

    /**
     * Get the leave details for this form status
     */
    public function leaveDetails()
    {
        return $this->hasMany(LeaveDetail::class, 'form_status', 'form_stat_id');
    }

    /**
     * Form status constants for easy reference
     */
    const DRAFT = 1;
    const COMPLETE = 2;
    const RETURNED = 3;

    /**
     * Get form status name by ID
     */
    public static function getStatusName($formStatId)
    {
        $status = self::find($formStatId);
        return $status ? $status->form_status : 'Unknown';
    }

    /**
     * Get all form statuses as array for dropdowns
     */
    public static function getStatusesArray()
    {
        return self::pluck('form_status', 'form_stat_id')->toArray();
    }
}
