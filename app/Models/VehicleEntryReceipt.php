<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleEntryReceipt extends Model
{
    use HasFactory;

    public function entry(){
        return $this->belongsTo(VehicleEntry::class);
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }
}
