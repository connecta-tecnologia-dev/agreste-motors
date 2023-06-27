<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExitReceipt extends Model
{
    use HasFactory;

    public function exit(){
        return $this->belongsTo(VehicleExit::class);
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
