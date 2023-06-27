<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function exits()
    {
        return $this->belongsToMany(VehicleExit::class);
    }

    public function entrys()
    {
        return $this->belongsToMany(VehicleEntry::class);
    }

    public function debts(){
        return $this->belongsToMany(Debt::class);
    }
}
