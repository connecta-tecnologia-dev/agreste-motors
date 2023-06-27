<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    public function create(Request $request)
    {
        $find = Brand::where('name', $request->name)->first();
        if ($find == null) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->save();

            return $brand;
        } else {
            return $find;
        }
    }

    public function show($id)
    {
        $brand = Brand::where('id', $id)->firstOrFail();
        return $brand;
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::where('id', $id)->firstOrFail();
        $brand->name = $request->name;
        $brand->save();
    }

    public function delete(Request $request)
    {
        try {
            $brand = Brand::where('id', $request->id)->firstOrFail();
            $brand->delete();
        } catch (\Exception $e) {
            return ['error' => 'A marca está em uso não é possivel excluir!'];
        }
    }

    public function list(Request $request)
    {
        $brands = Brand::all();
        return $brands;
    }
}
