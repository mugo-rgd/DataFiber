<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'employee_id',
        'department',
        'justification',
        'status',
    ];
}
