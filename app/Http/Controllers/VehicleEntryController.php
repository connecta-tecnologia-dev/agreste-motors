<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Employee;
use App\Models\Installment;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\VehicleEntry;
use App\Models\VehicleHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleEntryController extends Controller
{

    public function show($id)
    {
        $entry = VehicleEntry::where('id', $id)
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
        return $entry;
    }

    public function list(Request $request)
    {
        $perPage = $request->perPage ?? 30;
        $order = $request->order ?? 'desc';

        $entries = VehicleEntry::with('vehicle')
            ->with('owner')
            ->with('vehicle.model')
            ->with('vehicle.type')
            ->with('vehicle.brand')
            ->whereHas('vehicle', function ($q) use ($request) {
                if ($request->filled('plate')) {
                    $q->where('plate', 'like', '%' . $request->input('plate') . '%');
                }
                if ($request->filled('brand')) {
                    $q->whereHas('brand', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->input('brand') . '%');
                    });
                }
                if ($request->filled('model')) {
                    $q->whereHas('model', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->input('model') . '%');
                    });
                }
                if ($request->filled('color')) {
                    $q->whereHas('color', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->input('color') . '%');
                    });
                }
            })
            ->orderBy('id', $order);

        if ($request->filled('state')) {
            $entries->where('finished', $request->input('state'));
        }

        $entries = $entries->paginate($perPage);

        return $entries;
    }


    public function create(Request $request)
    {
        try {
            $dbTransaction = DB::transaction(function () use ($request) {
                $user = auth()->user();

                $entry = new VehicleEntry();
                $entry->vehicle_id = $request->vehicle;
                $entry->mileage = $request->mileage;
                $entry->supplier_id = $request->supplier;
                $entry->owner_id = $request->owner;
                $entry->amount_paid = $request->entryValue;
                $entry->value = $request->value;
                $entry->minimum_value = $request->minimumValue;
                $entry->date = $request->dateEntry;
                $entry->note = $request->note;
                $entry->creator = $user->id;
                if ($request->finish == true) {
                    $entry->finisher = $user->id;
                    $entry->finished = true;
                }
                $entry->save();

                $clerks = Employee::whereIn('id', $request->clerks)->get();

                $entry->clerks()->attach($clerks);

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
                        $transactions[$transaction->id] = ['value' => $transaction->amount];
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
                        $debts[$debt->id] = ['value' => $debt->total];
                    }
                }

                $entry->transactions()->attach($transactions);
                $entry->debts()->attach($debts);

                $vehicleHistoric = new VehicleHistory();
                $vehicleHistoric->vehicle_id = $request->vehicle;
                $vehicleHistoric->vehicle_entry_id = $entry->id;
                $vehicleHistoric->save();

                $vehicle = Vehicle::where('id', $entry->vehicle_id)->firstOrFail();
                $vehicle->owner_id = 1;
                $vehicle->mileage = $entry->mileage;
                $vehicle->save();

                return $entry;
            });

            return $dbTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $dbTransaction = DB::transaction(function () use ($request, $id) {
                $user = auth()->user();
                $vehicleHistoric = VehicleHistory::where('vehicle_entry_id', $id)->firstOrFail();
                $entry = VehicleEntry::where('id', $id)->where('finished', false)->firstOrFail();
                $entry->vehicle_id = $request->vehicle;
                $entry->mileage = $request->mileage;
                $entry->supplier_id = $request->supplier;
                $entry->owner_id = $request->owner;
                $entry->amount_paid = $request->entryValue;
                $entry->value = $request->value;
                $entry->minimum_value = $request->minimumValue;
                $entry->date = $request->dateEntry;
                $entry->note = $request->note;
                $entry->modifier = $user->id;
                if ($request->finish == true) {
                    $entry->finisher = $user->id;
                    $entry->finished = true;
                }
                $entry->save();

                $clerks = Employee::whereIn('id', $request->clerks)->get();

                $entry->clerks()->sync($clerks);

                $transactions = [];
                $debts = [];
                foreach ($request->payments as $payment) {
                    if ($payment['formPayment'] <= 2) {
                        if (isset($payment['id'])) {
                            $transactions[$payment['id']] = ['value' => $payment['value']];
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
                            $transactions[$transaction->id] = ['value' => $transaction->amount];
                        }
                    } else {
                        if (isset($payment['id'])) {
                            $debts[$payment['id']] = ['value' => $payment['value']];
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
                            $debts[$debt->id] = ['value' => $debt->total];
                        }
                    }
                }

                // Salvar a coleçãos antes de chamar o sync
                $currentTransactions = $entry->transactions()->get();
                $currentDebts = $entry->debts()->get();

                // Executar o sync para atualizar os relacionamentos
                $entry->transactions()->sync($transactions);
                $entry->debts()->sync($debts);

                // Verificar e excluir "debts" que não possuem mais relacionamentos
                foreach ($currentDebts as $debt) {
                    if (!$entry->debts()->find($debt->id)) {
                        $debt->transactions()->delete();
                        $debt->delete();
                    }
                }

                foreach ($currentTransactions as $transaction) {
                    if (!$entry->transactions()->find($transaction->id)) {
                        $transaction->delete();
                    }
                }

                $vehicleHistoric->vehicle_id = $request->vehicle;
                $vehicleHistoric->save();

                $vehicle = Vehicle::where('id', $entry->vehicle_id)->firstOrFail();
                $vehicle->owner_id = 1;
                $vehicle->mileage = $entry->mileage;
                $vehicle->save();

                return $entry;
            });

            return $dbTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function change($id)
    {
        $entry = VehicleEntry::where('id', $id)->firstOrFail();
        $entry->finished = false;
        $entry->save();
    }
}
