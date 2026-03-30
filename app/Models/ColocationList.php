<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColocationList extends Model
{
    use HasFactory;

    protected $table = 'colocation_list'; // Specify the table name

    protected $primaryKey = 'service_id'; // Specify the primary key

    public $incrementing = false; // Since service_id is likely a string
    protected $keyType = 'string'; // If service_id is string like "COL-001"

    protected $fillable = [
        'service_id',
        'service_type',
        'service_category',
        'specifications',
        'power_kw',
        'space_sqm',
        'monthly_price_usd',
        'setup_fee_usd',
        'min_contract_months',
        'is_active',
        'fibrestatus',
        'oneoff_rate',
        'recurrent_per_Annum',
    ];

    protected $casts = [
        'monthly_price_usd' => 'decimal:2',
        'setup_fee_usd' => 'decimal:2',
        'power_kw' => 'decimal:2',
        'space_sqm' => 'decimal:2',
        'min_contract_months' => 'integer',
        'is_active' => 'boolean',
        'oneoff_rate' => 'decimal:2',
        'recurrent_per_Annum' => 'decimal:2',
         ];

    public function designRequests()
    {
        return $this->belongsToMany(DesignRequest::class, 'design_request_colocation_list')
                    ->withTimestamps();
    }

    // In ColocationList.php model
public function quotations()
{
    return $this->belongsToMany(Quotation::class, 'quotation_colocation_services',
                                'colocation_service_id', 'quotation_id',
                                'service_id', 'id')
                ->withPivot(['quantity', 'duration_months', 'unit_price', 'total_price'])
                ->withTimestamps();
}
}

