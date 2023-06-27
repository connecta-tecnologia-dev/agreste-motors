<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'array',
    ];

    public function exits()
    {
        return $this->belongsToMany(VehicleExit::class);
    }

    public function entrys()
    {
        return $this->belongsToMany(VehicleEntry::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class);
    }
}
