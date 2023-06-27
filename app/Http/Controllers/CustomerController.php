<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show($id)
    {
        $customers = Customer::where('id', $id)->firstOrFail();
        return $customers;
    }

    public function list()
    {
        $customers = Customer::all();
        return $customers;
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $find = Customer::where('cpf', $request->cpf)->first();
        if ($find == null) {
            $customer = new Customer();
            $customer->name = $request->name;
            $customer->cpf = $request->cpf;
            $customer->rg = $request->rg;
            $customer->emitter = $request->emitter;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->number = $request->number;
            $customer->district = $request->district;
            $customer->city = $request->city;
            $customer->state = $request->state;
            $customer->cep = $request->cep;
            $customer->creator = $user->id;
            $customer->save();

            return $customer;
        } else {
            return ['error' => 'O cliente ' . $request->name . ' já está cadastrado!'];
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $customer = Customer::where('id', $id)->firstOrFail();
        $customer->name = $request->name;
        $customer->cpf = $request->cpf;
        $customer->rg = $request->rg;
        $customer->emitter = $request->emitter;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->number = $request->number;
        $customer->district = $request->district;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->cep = $request->cep;
        $customer->modifier = $user->id;
        $customer->save();
    }

    public function delete(Request $request)
    {
        $customer = Customer::firstOrFail($request->id);
        $customer->delete();
    }
}
