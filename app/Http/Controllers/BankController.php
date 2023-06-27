<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function show($id)
    {
        $banks = Bank::where('id', $id)->firstOrFail();
        return $banks;
    }

    public function list()
    {
        $banks = Bank::all();
        return $banks;
    }

    public function create(Request $request)
    {
        $find = Bank::where('agency', $request->account)->first();
        if ($find == null) {
            $bank = new Bank();
            $bank->name = $request->name;
            $bank->account = $request->account;
            $bank->agency = $request->agency;
            $bank->balance = $request->balance;
            $bank->save();

            return $bank;
        } else {
            return ['error' => 'Um banco com a conta: ' . $request->account . ' já está cadastrada!'];
        }
    }

    public function update(Request $request, $id)
    {
        $bank = Bank::where('id', $id)->firstOrFail();
        $bank->name = $request->name;
        $bank->account = $request->account;
        $bank->agency = $request->agency;
        $bank->balance = $request->balance;
        $bank->save();
    }

    public function delete(Request $request)
    {
        $bank = Bank::where('id', $request->id)->firstOrFail();
        $bank->delete();
    }
}
