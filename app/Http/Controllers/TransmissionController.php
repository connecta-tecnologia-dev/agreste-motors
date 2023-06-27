<?php

namespace App\Http\Controllers;

use App\Models\Transmission;
use Illuminate\Http\Request;

class TransmissionController extends Controller
{
    public function show(Request $request, $id)
    {
        $transmission = Transmission::where('id', $id)->firstOrFail();
        return $transmission;
    }

    public function create(Request $request)
    {
        $find = Transmission::where('name', $request->name)->first();
        if ($find == null) {
            $transmission = new Transmission();
            $transmission->name = $request->name;
            $transmission->save();

            return $transmission;
        } else {
            return $find;
        }
    }

    public function update(Request $request, $id)
    {
        $transmission = Transmission::where('id', $id)->firstOrFail();
        $transmission->name = $request->name;
        $transmission->save();
    }

    public function delete($id)
    {
        try {
            $transmission = Transmission::where('id', $id)->firstOrFail();
            $transmission->delete();
        } catch (\Exception $e) {
            return ['error' => 'O tipo de transmissão está em uso não é possivel excluir!'];
        }
    }

    public function list(Request $request)
    {
        $transmissions = Transmission::all();
        return $transmissions;
    }
}
