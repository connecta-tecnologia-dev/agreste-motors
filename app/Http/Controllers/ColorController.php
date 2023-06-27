<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function show(Request $request, $id)
    {
        $color = Color::where('id', $id)->firstOrFail();
        return $color;
    }

    public function create(Request $request)
    {
        $find = Color::where('name', $request->name)->first();
        if ($find == null) {
            $color = new Color();
            $color->name = $request->name;
            $color->save();

            return $color;
        } else {
            return $find;
        }
    }

    public function update(Request $request, $id)
    {
        $color = Color::where('id', $id)->firstOrFail();
        $color->name = $request->name;
        $color->save();
    }

    public function delete(Request $request)
    {
        try {
            $color = Color::where('id', $request->id)->firstOrFail();
            $color->delete();
        } catch (\Exception $e) {
            return ['error' => 'A Cor está em uso não é possivel excluir!'];
        }
    }

    public function list(Request $request)
    {
        $colors = Color::all();
        return $colors;
    }
}
