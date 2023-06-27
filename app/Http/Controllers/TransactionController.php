<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function list()
    {
        $transactions = Transaction::all();
        return $transactions;
    }

    public function listProfits()
    {
        $transactions = Transaction::with('exits')
        ->with('exits.transactions')
        ->with('exits.history.entry')
        ->get();
        return $transactions;
    }

    public function show($id)
    {
        $function = Transaction::where('id', $id)
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
