<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Employee;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\VehicleExit;
use App\Models\VehicleHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleExitController extends Controller
{

    public function show($id)
    {
        $vehicleHistory = VehicleExit::where('id', $id)
            ->with('vehicle')
            ->with('vehicle.model')
            ->with('vehicle.type')
            ->with('vehicle.brand')
            ->with('vehicle.transmission')
            ->with('vehicle.fuel')
            ->with('vehicle.color')
            ->with('owner')
            ->with('clerks')
            ->with('transactions')
            ->with('debts')
            ->with('debts.transactions')
            ->firstOrFail();
        return $vehicleHistory;
    }

    public function list(Request $request)
    {

        $perPage = $request->perPage ?? 30;
        $order = $request->order ?? 'desc';

        try {
            $exits = VehicleExit::with('vehicle')
                ->with('owner')
                ->with('vehicle.model')
                ->with('vehicle.type')
                ->with('vehicle.brand')
                ->orderBy('id', $order)
                ->paginate($perPage);

            return $exits;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function create(Request $request)
    {
        try {
            $DBTransaction = DB::transaction(function () use ($request) {
                $vehicleHistoric = VehicleHistory::where('vehicle_id', $request->vehicle)->where('vehicle_exit_id', null)->firstOrFail();

                $user = auth()->user();

                $exit = new VehicleExit();
                $exit->vehicle_id = $request->vehicle;
                $exit->mileage = $request->mileage;
                $exit->owner_id = $request->owner;
                $exit->sale_value = $request->value;
                $exit->services = $request->services;
                $exit->addition = $request->addition;
                $exit->date = $request->dateExit;
                $exit->note = $request->note;
                $exit->creator = $user->id;
                if ($request->finish == true) {
                    $exit->finisher = $user->id;
                    $exit->finished = true;
                }
                $exit->save();

                $clerks = Employee::whereIn('id', $request->clerks)->get();

                $exit->clerks()->attach($clerks);

                $transactions = [];
                $debts = [];
                foreach ($request->payments as $payment) {
                    if ($payment['formPayment'] <= 2) {
                        $transaction = new Transaction();
                        $transaction->type = $payment['type'];
                        if ($payment['formPayment'] == 1) {
                            if ($transaction->type == 'payment') {
                                $transaction->origin_type = 'cash';
                                $transaction->origin_id = 2;
                                $transaction->destination_type = 'customer';
                                $transaction->destination_id = $request->owner;
                            } else {
                                $transaction->origin_type = 'customer';
                                $transaction->origin_id = $request->owner;
                                $transaction->destination_type = 'cash';
                                $transaction->destination_id = 2;
                            }
                        } else {
                            if ($transaction->type == 'payment') {
                                $transaction->origin_type = 'bank';
                                $transaction->origin_id = $payment['bank'];
                                $transaction->destination_type = 'customer';
                                $transaction->destination_id = $request->owner;
                            } else {
                                $transaction->origin_type = 'customer';
                                $transaction->origin_id = $request->owner;
                                $transaction->destination_type = 'bank';
                                $transaction->destination_id = $payment['bank'];
                            }
                        }
                        $transaction->amount = $payment['value'];
                        $transaction->save();
                        $transactions[] = $transaction->id;
                    } else {
                        $debt = new Debt();
                        $debt->type = $payment['type'];
                        $debt->installments = $payment['installments'];
                        $debt->total = $payment['value'];
                        switch ($payment['formPayment']) {
                            case 3:
                                $debt->payment_type = 'financing';
                                $debt->data = [
                                    'first_payment' => $payment['first_payment'],
                                    'payment_date' => $payment['first_payment'],
                                    'installment_value' => $payment['installment_value'],
                                    'financial' => $payment['financial'],
                                    'bank' => $payment['bank'],
                                ];
                                break;
                            case 4:
                                $debt->payment_type = 'credit_card';
                                $debt->data = [
                                    'first_payment' => $payment['first_payment'],
                                    'payment_date' => $payment['first_payment'],
                                    'installment_value' => $payment['installment_value'],
                                    'bank' => $payment['bank'],
                                ];
                                break;
                            case 5:
                                $debt->payment_type = 'promissory';
                                $debt->data = [
                                    'first_payment' => $payment['first_payment'],
                                    'payment_date' => $payment['first_payment'],
                                    'bank' => $payment['bank'],
                                ];
                                break;
                            case 6:
                                $debt->payment_type = 'checks';
                                $debt->data = [
                                    'first_payment' => $payment['first_payment'],
                                    'payment_date' => $payment['first_payment'],
                                    'bank' => $payment['bank'],
                                ];
                                break;
                            case 7:
                                $debt->payment_type = 'slips';
                                $debt->data = [
                                    'first_payment' => $payment['first_payment'],
                                    'payment_date' => $payment['first_payment'],
                                    'bank' => $payment['bank'],
                                ];
                                break;
                        }
                        $debt->save();
                        if ($payment['formPayment'] >= 5) {
                            $installment = new Installment();
                            $installment->debt_id = $debt->id;
                            $installment->date = $debt->id;
                            $installment->value = $debt->id;
                        }
                        $debts[] = $debt->id;
                    }
                }

                $exit->transactions()->attach($transactions);
                $exit->debts()->attach($debts);

                $vehicleHistoric->exit_id = $exit->id;
                $vehicleHistoric->save();

                $vehicle = Vehicle::where('id', $exit->vehicle_id)->firstOrFail();
                $vehicle->owner_id = $exit->owner_id;
                $vehicle->mileage = $exit->mileage;
                $vehicle->save();

                return $exit;
            });

            return $DBTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $DBTransaction = DB::transaction(function () use ($request, $id) {
                $vehicleHistoric = VehicleHistory::where('vehicle_exit_id', $id)->firstOrFail();

                $user = auth()->user();

                $exit = VehicleExit::where('id', $id)->firstOrFail();
                if ($exit->vehicle_id != $request->vehicle) {
                    $vehicleHistoric->exit_id = null;
                    $vehicleHistoric->save();
                    $vehicleHistoric = VehicleHistory::where('vehicle_id', $request->vehicle)->where('vehicle_exit_id', null)->firstOrFail();
                }
                $exit->vehicle_id = $request->vehicle;
                $exit->mileage = $request->mileage;
                $exit->owner_id = $request->owner;
                $exit->sale_value = $request->value;
                $exit->services = $request->services;
                $exit->addition = $request->addition;
                $exit->date = $request->dateExit;
                $exit->note = $request->note;
                $exit->creator = $user->id;
                if ($request->finish == true) {
                    $exit->finisher = $user->id;
                    $exit->finished = true;
                }
                $exit->save();

                $clerks = Employee::whereIn('id', $request->clerks)->get();

                $exit->clerks()->sync($clerks);

                $transactions = [];
                $debts = [];
                foreach ($request->payments as $payment) {
                    if ($payment['formPayment'] <= 2) {
                        if (isset($payment['id'])) {
                            $transactions[] = $payment['id'];
                        } else {
                            $transaction = new Transaction();
                            $transaction->type = $payment['type'];
                            if ($payment['formPayment'] == 1) {
                                if ($transaction->type == 'payment') {
                                    $transaction->origin_type = 'cash';
                                    $transaction->origin_id = 2;
                                    $transaction->destination_type = 'customer';
                                    $transaction->destination_id = $request->owner;
                                } else {
                                    $transaction->origin_type = 'customer';
                                    $transaction->origin_id = $request->owner;
                                    $transaction->destination_type = 'cash';
                                    $transaction->destination_id = 2;
                                }
                            } else {
                                if ($transaction->type == 'payment') {
                                    $transaction->origin_type = 'bank';
                                    $transaction->origin_id = $payment['bank'];
                                    $transaction->destination_type = 'customer';
                                    $transaction->destination_id = $request->owner;
                                } else {
                                    $transaction->origin_type = 'customer';
                                    $transaction->origin_id = $request->owner;
                                    $transaction->destination_type = 'bank';
                                    $transaction->destination_id = $payment['bank'];
                                }
                            }
                            $transaction->amount = $payment['value'];
                            $transaction->save();
                            $transactions[] = $transaction->id;
                        }
                    } else {
                        if (isset($payment['id'])) {
                            $debts[] = $payment['id'];
                        } else {
                            $debt = new Debt();
                            $debt->type = $payment['type'];
                            $debt->installments = $payment['installments'];
                            $debt->total = $payment['value'];
                            switch ($payment['formPayment']) {
                                case 3:
                                    $debt->payment_type = 'financing';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'installment_value' => $payment['installment_value'],
                                        'financial' => $payment['financial'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 4:
                                    $debt->payment_type = 'credit_card';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'installment_value' => $payment['installment_value'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 5:
                                    $debt->payment_type = 'promissory';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 6:
                                    $debt->payment_type = 'checks';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 7:
                                    $debt->payment_type = 'slips';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                            }
                            $debt->save();
                            if ($payment['formPayment'] >= 5) {
                                foreach ($payment['installmentsData'] as $installmentData) {
                                    $installment = new Installment();
                                    $installment->debt_id = $debt->id;
                                    $installment->date = date($installmentData['date']);
                                    $installment->value = $installmentData['value'];
                                    $installment->save();
                                }
                            }
                            $debts[] = $debt->id;
                        }
                    }
                }

                // Salvar a coleçãos antes de chamar o sync
                $currentTransactions = $exit->transactions()->get();
                $currentDebts = $exit->debts()->get();

                // Executar o sync para atualizar os relacionamentos
                $exit->transactions()->sync($transactions);
                $exit->debts()->sync($debts);

                // Verificar e excluir "debts" que não possuem mais relacionamentos
                foreach ($currentDebts as $debt) {
                    if (!$exit->debts()->find($debt->id)) {
                        $debt->transactions()->delete();
                        $debt->delete();
                    }
                }

                foreach ($currentTransactions as $transaction) {
                    if (!$exit->transactions()->find($transaction->id)) {
                        $transaction->delete();
                    }
                }

                $vehicleHistoric->exit_id = $exit->id;
                $vehicleHistoric->save();

                $vehicle = Vehicle::where('id', $exit->vehicle_id)->firstOrFail();
                $vehicle->owner_id = $exit->owner_id;
                $vehicle->mileage = $exit->mileage;
                $vehicle->save();

                return $exit;
            });

            return $DBTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $DBTransaction = DB::transaction(function () use ($request, $id) {

                $vehicleHistoric = VehicleHistory::where('vehicle_exit_id', $id)->firstOrFail();

                $exit = VehicleExit::where('id', $id)->firstOrFail();

                // Salvar a coleçãos antes de chamar o sync
                $currentTransactions = $exit->transactions()->get();
                $currentDebts = $exit->debts()->get();

                // Executar o sync para atualizar os relacionamentos
                $exit->transactions()->delete();
                $exit->debts()->delete();

                // Verificar e excluir "debts" que não possuem mais relacionamentos
                foreach ($currentDebts as $debt) {
                    if (!$exit->debts()->find($debt->id)) {
                        $debt->transactions()->delete();
                        $debt->delete();
                    }
                }

                foreach ($currentTransactions as $transaction) {
                    if (!$exit->transactions()->find($transaction->id)) {
                        $transaction->delete();
                    }
                }

                $exit->clerks()->delete();

                $vehicleHistoric->exit_id = null;
                $vehicleHistoric->save();

                $vehicle = Vehicle::where('id', $exit->vehicle_id)->firstOrFail();
                $vehicle->owner_id = $exit->owner_id;
                $vehicle->mileage = $exit->mileage;
                $vehicle->save();

                return $exit;
            });

            return $DBTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function multipleExit(Request $request)
    {
        try {
            $DBTransaction = DB::transaction(function () use ($request) {

                $user = auth()->user();

                $transactions = [];
                $debts = [];
                foreach ($request->payments as $payment) {
                    if ($payment['formPayment'] <= 2) {
                        if (isset($payment['id'])) {
                            $transactions[] = $payment['id'];
                        } else {
                            $transaction = new Transaction();
                            $transaction->type = $payment['type'];
                            if ($payment['formPayment'] == 1) {
                                if ($transaction->type == 'payment') {
                                    $transaction->origin_type = 'cash';
                                    $transaction->origin_id = 2;
                                    $transaction->destination_type = 'customer';
                                    $transaction->destination_id = $request->owner;
                                } else {
                                    $transaction->origin_type = 'customer';
                                    $transaction->origin_id = $request->owner;
                                    $transaction->destination_type = 'cash';
                                    $transaction->destination_id = 2;
                                }
                            } else {
                                if ($transaction->type == 'payment') {
                                    $transaction->origin_type = 'bank';
                                    $transaction->origin_id = $payment['bank'];
                                    $transaction->destination_type = 'customer';
                                    $transaction->destination_id = $request->owner;
                                } else {
                                    $transaction->origin_type = 'customer';
                                    $transaction->origin_id = $request->owner;
                                    $transaction->destination_type = 'bank';
                                    $transaction->destination_id = $payment['bank'];
                                }
                            }
                            $transaction->amount = $payment['value'];
                            $transaction->save();
                            $transactions[] = $transaction->id;
                        }
                    } else {
                        if (isset($payment['id'])) {
                            $debts[] = $payment['id'];
                        } else {
                            $debt = new Debt();
                            $debt->type = $payment['type'];
                            $debt->installments = $payment['installments'];
                            $debt->total = $payment['value'];
                            switch ($payment['formPayment']) {
                                case 3:
                                    $debt->payment_type = 'financing';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'installment_value' => $payment['installment_value'],
                                        'financial' => $payment['financial'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 4:
                                    $debt->payment_type = 'credit_card';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'installment_value' => $payment['installment_value'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 5:
                                    $debt->payment_type = 'promissory';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 6:
                                    $debt->payment_type = 'checks';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                                case 7:
                                    $debt->payment_type = 'slips';
                                    $debt->data = [
                                        'first_payment' => $payment['first_payment'],
                                        'bank' => $payment['bank'],
                                    ];
                                    break;
                            }
                            $debt->save();
                            $debt->amout_available = $debt->total;
                            if ($payment['formPayment'] >= 5) {
                                foreach ($payment['installmentsData'] as $installmentData) {
                                    $installment = new Installment();
                                    $installment->debt_id = $debt->id;
                                    $installment->date = date($installmentData['date']);
                                    $installment->value = $installmentData['value'];
                                    $installment->save();
                                }
                            }
                            $debts[] = $debt->id;
                        }
                    }
                }

                $exits = [];

                // Cria as saídas de veículos
                foreach ($request->vehicles as $vehicle) {
                    dd($request->vehicles);
                    $vehicleHistoric = VehicleHistory::where('vehicle_id', $vehicle['id'])->where('vehicle_exit_id', null)->firstOrFail();
                    $exit = new VehicleExit();
                    $exit->vehicle_id = $vehicle['id'];
                    $exit->mileage = $vehicle['mileage'];
                    $exit->owner_id = $request->owner;
                    $exit->sale_value = $vehicle['value'];
                    $exit->services = $vehicle['services'];
                    $exit->addition = $vehicle['addition'];
                    $exit->date = $vehicle['date'];
                    $exit->note = $vehicle['note'];
                    $exit->creator = $user->id;
                    if ($request->finish == true) {
                        $exit->finisher = $user->id;
                        $exit->finished = true;
                    }
                    $exit->save();

                    $clerks = Employee::whereIn('id', $request->clerks)->get();

                    $exit->clerks()->attach($clerks);

                    $exit->transactions()->attach($transactions);
                    $exit->debts()->attach($debts);

                    $vehicleHistoric->exit_id = $exit->id;
                    $vehicleHistoric->save();

                    if ($request->finish == true) {
                        $vehicle = Vehicle::where('id', $exit->vehicle_id)->firstOrFail();
                        $vehicle->owner_id = $exit->owner_id;
                        $vehicle->mileage = $exit->mileage;
                        $vehicle->save();
                    }
                    $exits[] = $exit;
                }
            });

            return $DBTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function change($id)
    {
        $exit = VehicleExit::where('id', $id)->firstOrFail();
        $exit->finished = false;
        $exit->save();
    }
}
