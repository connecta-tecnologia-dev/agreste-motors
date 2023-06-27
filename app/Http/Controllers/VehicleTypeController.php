<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    public function show(Request $request, $id)
    {
        $vehicles = VehicleType::where('id', $id)->firstOrFail();
        return $vehicles;
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $find = VehicleType::where('name', $request->name)->first();
        if ($find == null) {
            $vehicleType = new VehicleType();
            $vehicleType->name = $request->name;
            $vehicleType->save();

            return $vehicleType;
        } else {
            return ['error' => 'O tipo de veículo ' . $request->name . ' já está cadastrado!'];
        }
    }

    public function update(Request $request, $id)
    {
        $vehicleType = VehicleType::where('id', $id)->firstOrFail();
        $vehicleType->name = $request->name;
        $vehicleType->save();
    }

    public function list(Request $request)
    {
        $vehicleTypes = VehicleType::all();
        return $vehicleTypes;
    }

    public function delete(Request $request)
    {
        $vehicleModel = VehicleType::where('id', $request->id)->firstOrFail();
        $vehicleModel->delete();
    }
}
