<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryClerk extends Model
{
    use HasFactory;

    public function entry(){
        return $this->hasOne(VehicleEntry::class);
    }
}
