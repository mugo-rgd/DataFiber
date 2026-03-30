<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyAssignmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_request_id',
        'surveyor_id',
        'assigned_by',
        'action',
        'notes',
    ];

    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function surveyor()
    {
        return $this->belongsTo(User::class, 'surveyor_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
