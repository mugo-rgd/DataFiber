<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    protected $guarded = [];
    protected $casts = ['metadata' => 'array'];

    public function routes(){ return $this->hasMany(Route::class); }
    public function quotations(){ return $this->hasMany(Quotation::class); }
   // public function contracts(){ return $this->hasMany(Contract::class); }
}

