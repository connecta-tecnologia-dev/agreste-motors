<?php

namespace App\Http\Controllers;

use App\Models\Fuel;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    public function show(Request $request, $id)
    {
        $fuels = Fuel::where('id', $id)->firstOrFail();
        return $fuels;
    }

    public function create(Request $request)
    {
        $find = Fuel::where('name', $request->name)->first();
        if ($find == null) {
            $fuel = new Fuel();
            $fuel->name = $request->name;
            $fuel->save();

            return $fuel;
        } else {
            return ['error' => 'O combustível ' . $request->name . ' já está cadastrado!'];
        }
    }

    public function update(Request $request, $id)
    {
        $fuel = Fuel::where('id', $id)->firstOrFail();
        $fuel->name = $request->name;
        $fuel->save();
    }

    public function delete(Request $request)
    {
        $fuel = Fuel::where('id', $request->id)->firstOrFail();
        $fuel->delete();
    }

    public function list(Request $request)
    {
        $fuels = Fuel::all();
        return $fuels;
    }
}