<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleEntry extends Model
{
    use HasFactory;

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function clerks(){
        return $this->belongsToMany(Employee::class);
    }

    public function owner()
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(){
        return $this->belongsToMany(Transaction::class);
    }

    public function debts(){
        return $this->belongsToMany(Debt::class);
    }

    public function history(){
        return $this->hasOne(VehicleHistory::class);
    }
}
