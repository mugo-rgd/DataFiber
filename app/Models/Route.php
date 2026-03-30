<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Route extends Model {
    protected $guarded = [];
    protected $table = 'routes';
    protected $casts = ['survey_geojson' => 'array'];

    public function customer(){ return $this->belongsTo(Customer::class); }
   // public function designs(){ return $this->hasMany(Design::class); }
}
