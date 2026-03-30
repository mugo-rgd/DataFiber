<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignRequestActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_request_id',
        'user_id',
        'action',
        'description',
        'icon'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function designRequest()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
