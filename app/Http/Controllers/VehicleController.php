<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function show($id)
    {
        $vehicles = Vehicle::where('id', $id)
            ->with('brand')
            ->with('color')
            ->with('owner')
            ->with('fuel')
            ->with('transmission')
            ->with('type')
            ->with('model')
            ->firstOrFail();
        return $vehicles;
    }

    public function list(Request $request)
    {
        $query = Vehicle::with('brand')
            ->with('color')
            ->with('owner')
            ->with('fuel')
            ->with('transmission')
            ->with('type')
            ->with('model');

        if ($request->filled('plate')) {
            $query->where('plate', 'like', '%' . $request->input('plate') . '%');
        }
        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('brand') . '%');
            });
        }
        if ($request->filled('model')) {
            $query->whereHas('model', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('model') . '%');
            });
        }
        if ($request->filled('color')) {
            $query->whereHas('color', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('color') . '%');
            });
        }

        $vehicles = $query->get();

        return $vehicles;
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $findPlate = Vehicle::where('plate', $request->plate)->first();
        $findRenavam = Vehicle::where('renavam', $request->renavam)->first();
        $findChassis = Vehicle::where('chassis', $request->chassis)->first();
        if ($findPlate == null && $findRenavam == null && $findChassis == null) {
            $vehicle = new Vehicle();
            $vehicle->type_id = $request->type;
            $vehicle->transmission_id = $request->transmission;
            $vehicle->plate = $request->plate;
            $vehicle->renavam = $request->renavam;
            $vehicle->chassis = $request->chassis;
            $vehicle->owner_id = $request->owner;
            $vehicle->brand_id = $request->brand;
            $vehicle->model_id = $request->model;
            $vehicle->version = $request->version;
            $vehicle->color_id = $request->color;
            $vehicle->fuel_id = $request->fuel;
            $vehicle->model_year = $request->modelYear;
            $vehicle->manufacture_year = $request->factureYear;
            $vehicle->doors = $request->doors;
            $vehicle->mileage = $request->mileage;
            $vehicle->power = $request->power;
            $vehicle->note = $request->note;
            $vehicle->creator = $user->id;
            $vehicle->save();

            return $vehicle;
        } else {
            if ($findPlate != null) {
                return ['error' => 'Um veículo com a placa: ' . $request->plate . ' já está cadastrado!'];
            } elseif ($findRenavam != null) {
                return ['error' => 'Um veículo com o renavam: ' . $request->renavam . ' já está cadastrado!'];
            } else {
                return ['error' => 'Um veículo com o chassis: ' . $request->chassis . ' já está cadastrado!'];
            }
        }
    }

    public function update(Request $request)
    {
    }

    public function delete(Request $request)
    {
    }
}
