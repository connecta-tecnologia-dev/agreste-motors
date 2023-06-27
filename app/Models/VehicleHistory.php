<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleHistory extends Model
{
    use HasFactory;

    public function entry()
    {
        return $this->belongsTo(VehicleEntry::class, 'vehicle_entry_id');
    }

    public function exit(){
        return $this->belongsTo(VehicleExit::class, 'vehicle_exit_id');
    }
}
