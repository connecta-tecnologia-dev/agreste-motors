<?php

namespace App\Http\Controllers;

use App\Models\Cashier;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function list()
    {
        $cashiers = Cashier::all();
        return $cashiers;
    }
}
