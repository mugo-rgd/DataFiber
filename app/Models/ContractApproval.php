<?php
// app/Models/ContractApproval.php

namespace App\Models;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'approved_by',
        'notes',
        'approval_type'
    ];

    /**
     * Get the user who approved the contract
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the contract that owns the approval
     */
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
