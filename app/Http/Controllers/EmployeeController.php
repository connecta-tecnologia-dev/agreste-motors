<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function list()
    {
        $employees = Employee::with('employee_function')->get();
        return $employees;
    }

    public function show($id)
    {
        $employees = Employee::where('id', $id)->firstOrFail();
        return $employees;
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $find = Employee::where('cpf', $request->cpf)->first();
        if ($find == null) {
            $employee = new Employee();
            $employee->name = $request->name;
            $employee->cpf = $request->cpf;
            $employee->rg = $request->rg;
            $employee->emitter = $request->emitter;
            $employee->email = $request->email;
            $employee->address = $request->address;
            $employee->number = $request->number;
            $employee->district = $request->district;
            $employee->city = $request->city;
            $employee->state = $request->state;
            $employee->cep = $request->cep;
            $employee->employee_function_id = $request->function;
            $employee->salary = $request->salary;
            $employee->creator = $user->id;
            $employee->save();

            return $employee;
        } else {
            return ['error' => 'O Funcionário ' . $request->name . ' já está cadastrado!'];
        }
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $employee = Employee::where('id', $id)->firstOrFail();
        $employee->name = $request->name;
        $employee->cpf = $request->cpf;
        $employee->rg = $request->rg;
        $employee->emitter = $request->emitter;
        $employee->email = $request->email;
        $employee->address = $request->address;
        $employee->number = $request->number;
        $employee->district = $request->district;
        $employee->city = $request->city;
        $employee->state = $request->state;
        $employee->cep = $request->cep;
        $employee->employee_function_id = $request->function;
        $employee->salary = $request->salary;
        $employee->modifier = $user->id;
        $employee->save();
        return $employee;
    }

    public function delete(Request $request)
    {
        $employee = Employee::firstOrFail($request->id);
        $employee->delete();
    }
}
