<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function fuel()
    {
        return $this->belongsTo(Fuel::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function owner()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transmission()
    {
        return $this->belongsTo(Transmission::class);
    }

    public function type()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function history()
    {
        return $this->hasMany(VehicleHistory::class);
    }
}