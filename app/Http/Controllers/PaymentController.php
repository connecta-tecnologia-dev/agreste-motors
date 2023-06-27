<?php

namespace App\Http\Controllers;

use App\Models\PaymentForm;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function list()
    {
        $payments = PaymentForm::with('requirements')->get();
        return $payments;
    }

    public function show($id)
    {
        $function = PaymentForm::where('id', $id)
            ->firstOrFail();
        return $function;
    }

    public function create(Request $request)
    {
    }

    public function update(Request $request)
    {
    }

    public function delete(Request $request)
    {
    }
}
