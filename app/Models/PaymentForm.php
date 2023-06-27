<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentForm extends Model
{
    use HasFactory;

    protected $fillable = ['type'];

    protected $casts = [
        'type' => 'array',
    ];

    public function requirements()
    {
        return $this->hasMany(PaymentFormRequirement::class);
    }
}
