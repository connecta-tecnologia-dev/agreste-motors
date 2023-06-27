<?php

namespace App\Http\Controllers;

use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{

    public function show($id)
    {
        $vehicles = VehicleModel::where('id', $id)->firstOrFail();
        return $vehicles;
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $find = VehicleModel::where('name', $request->name)->where('brand_id', $request->brand)->first();
        if ($find == null) {
            $vehicleModel = new VehicleModel();
            $vehicleModel->brand_id = $request->brand;
            $vehicleModel->name = $request->name;
            $vehicleModel->save();

            return $vehicleModel;
        } else {
            return ['error' => 'o modelo ' . $request->name . ' já está cadastrado!'];
        }
    }

    public function update(Request $request, $id)
    {
        $vehicleModel = VehicleModel::where('id', $id)->firstOrFail();
        $vehicleModel->brand_id = $request->brand;
        $vehicleModel->name = $request->name;
        $vehicleModel->save();
    }

    public function list(Request $request)
    {
        $query = VehicleModel::query(); // Inicia a consulta

        // Verifica se o parâmetro "marca" está presente na solicitação
        if ($request->has('brand')) {
            $brand = $request->input('brand');
            $query->where('brand_id', $brand); // Adiciona o filtro da marca
        }

        $vehicleModels = $query->get(); // Executa a consulta e obtém os resultados

        return $vehicleModels;
    }

    public function delete(Request $request)
    {
        $vehicleModel = VehicleModel::where('id', $request->id)->firstOrFail();
        $vehicleModel->delete();
    }
}
