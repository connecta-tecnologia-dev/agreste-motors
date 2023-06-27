<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function employee_function()
    {
        return $this->belongsTo(EmployeeFunction::class);
    }
}
