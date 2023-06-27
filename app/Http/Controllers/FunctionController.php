<?php

namespace App\Http\Controllers;

use App\Models\EmployeeFunction;
use Illuminate\Http\Request;

class FunctionController extends Controller
{
    public function list()
    {
        $functions = EmployeeFunction::all();
        return $functions;
    }

    public function show($id)
    {
        $function = EmployeeFunction::where('id', $id)
            ->with('employee')
            ->firstOrFail();
        return $function;
    }

    public function create(Request $request)
    {
        $find = EmployeeFunction::where('name', $request->name)->first();
        if ($find == null) {
            $function = new EmployeeFunction();
            $function->name = $request->name;
            $function->save();

            return $function;
        } else {
            return ['error' => 'O Cargo ' . $request->name . ' já está cadastrado!'];
        }
    }

    public function update(Request $request, $id)
    {
        $function = EmployeeFunction::where('id', $id)->firstOrFail();
        $function->name = $request->name;
        $function->save();
    }

    public function delete(Request $request)
    {
        $function = EmployeeFunction::where('id', $request->id)->firstOrFail();
        $function->delete();
    }
}
