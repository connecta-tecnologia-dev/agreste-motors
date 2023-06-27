<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Employee;
use App\Models\Fuel;
use App\Models\PaymentForm;
use App\Models\Transmission;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Inertia\Inertia;

class PanelController extends Controller
{
    public function showEmployees()
    {
        return Inertia::render('Employees/Show');
    }

    public function showCustomers()
    {
        return Inertia::render('Customers/Show');
    }

    public function showVehicles()
    {
        return Inertia::render('Vehicles/Show');
    }

    public function showSettings()
    {
        return Inertia::render('Settings/Show');
    }

    public function showTypes()
    {
        return Inertia::render('Types/List');
    }

    public function showModels()
    {
        $data = [];
        $data['brands'] = Brand::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });

        return Inertia::render('Models/List', ['data' => $data]);
    }

    public function showTransmissions()
    {
        return Inertia::render('Transmissions/List');
    }

    public function showBrands()
    {
        return Inertia::render('Brands/List');
    }

    public function showFuels()
    {
        return Inertia::render('Fuels/List');
    }

    public function showColors()
    {
        return Inertia::render('Colors/List');
    }

    public function showEntries()
    {
        $data = [];

        $data['brands'] = Brand::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['colors'] = Color::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['fuels'] = Fuel::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['transmissions'] = Transmission::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['types'] = VehicleType::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['banks'] = Bank::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['payments'] = PaymentForm::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['models'] = VehicleModel::select('id', 'name', 'brand_id')
            ->get()
            ->mapWithKeys(function ($model) {
                return [$model->id => ['name' => $model->name, 'brand' => $model->brand_id]];
            })
            ->toArray();
        $data['clerks'] = Employee::where('employee_function_id', 1)->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });

        return Inertia::render('Entries/List', ['data' => $data]);
    }

    public function showExits()
    {
        $data = [];

        $data['brands'] = Brand::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['colors'] = Color::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['fuels'] = Fuel::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['transmissions'] = Transmission::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['types'] = VehicleType::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['banks'] = Bank::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['payments'] = PaymentForm::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['models'] = VehicleModel::select('id', 'name', 'brand_id')
            ->get()
            ->mapWithKeys(function ($model) {
                return [$model->id => ['name' => $model->name, 'brand' => $model->brand_id]];
            })
            ->toArray();
        $data['clerks'] = Employee::where('employee_function_id', 1)->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });

        return Inertia::render('Exits/List', ['data' => $data]);
    }

    public function showBanks()
    {
        return Inertia::render('Banks/Show');
    }

    public function showReports()
    {
        return Inertia::render('Reports/Show');
    }

    public function showTransactions()
    {
        return Inertia::render('Transactions/Show');
    }

    public function showStock()
    {
        return Inertia::render('Stock/Show');
    }
    public function showDebtors()
    {
        $data = [];

        $data['brands'] = Brand::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['colors'] = Color::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['fuels'] = Fuel::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['transmissions'] = Transmission::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['types'] = VehicleType::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['banks'] = Bank::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['payments'] = PaymentForm::select('id', 'name')->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });
        $data['models'] = VehicleModel::select('id', 'name', 'brand_id')
            ->get()
            ->mapWithKeys(function ($model) {
                return [$model->id => ['name' => $model->name, 'brand' => $model->brand_id]];
            })
            ->toArray();
        $data['clerks'] = Employee::where('employee_function_id', 1)->get()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
            return [$id => ['name' => $name]];
        });

        return Inertia::render('Debtors/List', ['data' => $data]);
    }
}
