<?php

use App\Http\Controllers\BankController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\VehicleEntryController;
use App\Http\Controllers\FunctionController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\VehicleModelController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\TransmissionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VehicleExitController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/admin', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/admin/vehicles', [PanelController::class, 'showVehicles'])->name('vehicles');
    Route::get('/admin/entries', [PanelController::class, 'showEntries'])->name('entries');
    Route::get('/admin/exits', [PanelController::class, 'showExits'])->name('exits');
    Route::get('/admin/debtors', [PanelController::class, 'showDebtors'])->name('debtors');
    Route::get('/admin/banks', [PanelController::class, 'showBanks'])->name('banks');
    Route::get('/admin/brands', [PanelController::class, 'showBrands'])->name('brands');
    Route::get('/admin/models', [PanelController::class, 'showModels'])->name('models');
    Route::get('/admin/transmissions', [PanelController::class, 'showTransmissions'])->name('transmissions');
    Route::get('/admin/fuels', [PanelController::class, 'showFuels'])->name('fuels');
    Route::get('/admin/types', [PanelController::class, 'showTypes'])->name('types');
    Route::get('/admin/colors', [PanelController::class, 'showColors'])->name('colors');
    Route::get('/admin/customers', [PanelController::class, 'showCustomers'])->name('customers');
    Route::get('/admin/employees', [PanelController::class, 'showEmployees'])->name('employees');
    Route::get('/admin/reports', [PanelController::class, 'showReports'])->name('reports');
    Route::get('/admin/transactions', [PanelController::class, 'showTransactions'])->name('transactions');
    Route::get('/admin/stock', [PanelController::class, 'showStock'])->name('stock');
    Route::get('/admin/settings', [PanelController::class, 'showSettings'])->name('settings');

    Route::get('/vehicle/transaction/list', [TransactionController::class, 'list'])->name('listTransactions');
    Route::get('/vehicle/transaction/profits', [TransactionController::class, 'listProfits'])->name('listProfits');
    Route::get('/vehicle/transaction/show', [TransactionController::class, 'show']);
    Route::post('/vehicle/transaction/create', [TransactionController::class, 'create']);
    Route::post('/vehicle/transaction/update', [TransactionController::class, 'update']);
    Route::post('/vehicle/transaction/delete', [TransactionController::class, 'delete']);

    Route::get('/vehicle/show/{id}', [VehicleController::class, 'show'])->name('showVehicle');
    Route::get('/vehicle/list', [VehicleController::class, 'list'])->name('listVehicles');
    Route::post('/vehicle/create', [VehicleController::class, 'create'])->name('createVehicle');
    Route::post('/vehicle/update', [VehicleController::class, 'update']);
    Route::post('/vehicle/delete', [VehicleController::class, 'delete']);

    Route::get('/vehicle/brands', [BrandController::class, 'list'])->name('listBrands');
    Route::post('/vehicle/brand', [BrandController::class, 'create'])->name('createBrand');
    Route::get('/vehicle/brand/{id}', [BrandController::class, 'show'])->name('showBrand');
    Route::put('/vehicle/brand/{id}', [BrandController::class, 'update'])->name('updateBrand');
    Route::delete('/vehicle/brand/{id}', [BrandController::class, 'delete'])->name('deleteBrand');

    Route::get('/vehicle/colors', [ColorController::class, 'list'])->name('listColors');
    Route::post('/vehicle/color', [ColorController::class, 'create'])->name('createColor');
    Route::get('/vehicle/color/{id}', [ColorController::class, 'show'])->name('showColor');
    Route::put('/vehicle/color/{id}', [ColorController::class, 'update'])->name('updateColor');
    Route::delete('/vehicle/color/{id}', [ColorController::class, 'delete'])->name('deleteColor');

    Route::get('/vehicle/transmissions', [TransmissionController::class, 'list'])->name('listTransmissions');
    Route::post('/vehicle/transmission', [TransmissionController::class, 'create'])->name('createTransmission');
    Route::get('/vehicle/transmission/{id}', [TransmissionController::class, 'show'])->name('showTransmission');
    Route::put('/vehicle/transmission/{id}', [TransmissionController::class, 'update'])->name('updateTransmission');
    Route::delete('/vehicle/transmission/{id}', [TransmissionController::class, 'delete'])->name('deleteTransmission');

    Route::get('/vehicle/types', [VehicleTypeController::class, 'list'])->name('listTypes');
    Route::post('/vehicle/type', [VehicleTypeController::class, 'create'])->name('createType');
    Route::get('/vehicle/type/{id}', [VehicleTypeController::class, 'show'])->name('showType');
    Route::put('/vehicle/type/{id}', [VehicleTypeController::class, 'update'])->name('updateType');
    Route::delete('/vehicle/type/{id}', [VehicleTypeController::class, 'delete'])->name('deleteType');

    Route::get('/vehicle/fuels', [FuelController::class, 'list'])->name('listFuels');
    Route::post('/vehicle/fuel', [FuelController::class, 'create'])->name('createFuel');
    Route::get('/vehicle/fuel/{id}', [FuelController::class, 'show'])->name('showFuel');
    Route::put('/vehicle/fuel/{id}', [FuelController::class, 'update'])->name('updateFuel');
    Route::delete('/vehicle/fuel/{id}', [FuelController::class, 'delete'])->name('deleteFuel');

    Route::get('/vehicle/models', [VehicleModelController::class, 'list'])->name('listModels');
    Route::post('/vehicle/model', [VehicleModelController::class, 'create'])->name('createModel');
    Route::get('/vehicle/model/{id}', [VehicleModelController::class, 'show'])->name('showModel');
    Route::put('/vehicle/model/{id}', [VehicleModelController::class, 'update'])->name('updateModel');
    Route::delete('/vehicle/model/{id}', [VehicleModelController::class, 'delete'])->name('deleteModel');

    Route::get('/customer/list', [CustomerController::class, 'list'])->name('listCustomers');
    Route::get('/customer/show/{id}', [CustomerController::class, 'show'])->name('showCustomer');
    Route::post('/customer/create', [CustomerController::class, 'create'])->name('createCustomer');
    Route::post('/customer/update/{id}', [CustomerController::class, 'update'])->name('updateCustomer');
    Route::post('/customer/delete', [CustomerController::class, 'delete']);

    Route::get('/employee/list', [EmployeeController::class, 'list'])->name('listEmployees');
    Route::get('/employee/show/{id}', [EmployeeController::class, 'show'])->name('showEmployee');
    Route::post('/employee/create', [EmployeeController::class, 'create'])->name('createEmployee');
    Route::post('/employee/update/{id}', [EmployeeController::class, 'update'])->name('updateEmployee');
    Route::post('/employee/delete', [EmployeeController::class, 'delete']);

    Route::get('/function/list', [FunctionController::class, 'list'])->name('listFunctions');
    Route::get('/function/show/{id}', [FunctionController::class, 'show'])->name('showFunction');
    Route::post('/function/create', [FunctionController::class, 'create'])->name('createFunction');
    Route::post('/function/update', [FunctionController::class, 'update']);
    Route::post('/function/delete', [FunctionController::class, 'delete']);

    Route::post('/payments/list', [PaymentController::class, 'list'])->name('listPayments');

    Route::post('/banks/list', [BankController::class, 'list'])->name('listBanks');
    Route::get('/banks/show/{id}', [BankController::class, 'show'])->name('showBank');
    Route::post('/bank/create', [BankController::class, 'create'])->name('createBank');
    Route::post('/bank/update/{id}', [BankController::class, 'update'])->name('updateBank');
    Route::post('/bank/delete', [BankController::class, 'delete']);

    Route::post('/cashier/list', [CashierController::class, 'list'])->name('listCashiers');
    Route::post('/cashier/show/{id}', [CashierController::class, 'show'])->name('showCashier');
    Route::post('/cashier/create', [CashierController::class, 'create'])->name('createCashier');
    Route::post('/cashier/update/{id}', [CashierController::class, 'update'])->name('updateCashier');
    Route::post('/cashier/delete', [CashierController::class, 'delete']);

    Route::get('/entrys', [VehicleEntryController::class, 'list'])->name('listEntries');
    Route::post('/entry', [VehicleEntryController::class, 'create'])->name('createEntry');
    Route::get('/entry/{id}', [VehicleEntryController::class, 'show'])->name('showEntry');
    Route::put('/entry/{id}', [VehicleEntryController::class, 'update'])->name('updateEntry');
    Route::patch('/entry/{id}', [VehicleEntryController::class, 'change'])->name('changeEntry');

    Route::get('/exits', [VehicleExitController::class, 'list'])->name('listExits');
    Route::post('/exit', [VehicleExitController::class, 'create'])->name('createExit');
    Route::get('/exit/{id}', [VehicleExitController::class, 'show'])->name('showExit');
    Route::put('/exit/{id}', [VehicleExitController::class, 'update'])->name('updateExit');
    Route::post('/exit/multipleExit', [VehicleExitController::class, 'multipleExit'])->name('multipleExit');
    Route::patch('/exit/{id}', [VehicleExitController::class, 'change'])->name('changeExit');

    Route::get('/debts', [DebtController::class, 'list'])->name('listDebts');
    Route::post('/debt/show/{id}', [DebtController::class, 'show'])->name('showDebt');
    Route::post('/debt/create', [DebtController::class, 'create'])->name('createDebt');
    Route::post('/debt/normalPayment/{id}', [DebtController::class, 'normalPayment'])->name('normalDebt');
    Route::post('/debt/installmentPayment/{id}', [DebtController::class, 'installmentPayment'])->name('installmentPayment');
    Route::get('/debt/installment/{id}', [DebtController::class, 'getInstallment'])->name('getInstallment');

    Route::get('/stock', [VehicleHistoryController::class, 'showStock'])->name('showStock');
    Route::post('/trade', [VehicleHistoryController::class, 'tradeVehicles'])->name('tradeVehicles');
});
