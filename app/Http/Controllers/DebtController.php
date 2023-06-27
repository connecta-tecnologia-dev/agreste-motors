<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Installment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function show($id)
    {
        $debt = Debt::where('id', $id)->with('installments')->with('installments.transactions')->firstOrFail();
        return $debt;
    }

    public function normalPayment(Request $request, $id)
    {
        try {
            $dbTransaction = DB::transaction(function () use ($request, $id) {
                $debt = Debt::where('id', $id)->firstOrFail();
                $debt->paid = $debt->total;
                $debt->status = true;
                $debt->save();

                $transaction = new Transaction();
                $transaction->type = 'receipt';
                $transaction->origin_type = 'customer';
                $transaction->origin_id = $request->owner;
                $transaction->destination_type = 'bank';
                $transaction->destination_id = $debt->data['bank'];
                $transaction->amount = $debt->total;
                $transaction->save();

                $debt->transactions()->attach($transaction);

                return $debt;
            });
            return $dbTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function installmentPayment(Request $request, $id)
    {
        try {
            $dbTransaction = DB::transaction(function () use ($request, $id) {
                $installment = Installment::where('id', $id)->firstOrFail();

                $debtData = Debt::select('customers.id as customer_id')
                    ->leftJoin('debt_vehicle_entry', 'debts.id', '=', 'debt_vehicle_entry.debt_id')
                    ->leftJoin('debt_vehicle_exit', 'debts.id', '=', 'debt_vehicle_exit.debt_id')
                    ->leftJoin('vehicle_entries', 'vehicle_entries.id', '=', 'debt_vehicle_entry.vehicle_entry_id')
                    ->leftJoin('vehicle_exits', 'vehicle_exits.id', '=', 'debt_vehicle_exit.vehicle_exit_id')
                    ->leftJoin('vehicles', function ($join) {
                        $join->on('vehicle_entries.vehicle_id', '=', 'vehicles.id')
                            ->orOn('vehicle_exits.vehicle_id', '=', 'vehicles.id');
                    })
                    ->leftJoin('customers', function ($join) {
                        $join->on('vehicle_entries.owner_id', '=', 'customers.id')
                            ->orOn('vehicle_exits.owner_id', '=', 'customers.id');
                    })
                    ->where('debts.id', $installment->debt_id)
                    ->firstOrFail();

                $transactions = [];
                foreach ($request->payments as $payment) {
                    if (isset($payment['id'])) {
                        $transactions[] = $payment['id'];
                    } else {
                        $transaction = new Transaction();
                        if ($payment['formPayment'] == 1) {
                            $transaction->type = 'receipt';
                            $transaction->origin_type = 'customer';
                            $transaction->origin_id = $debtData->customer_id;
                            $transaction->date = $payment['date'];
                            $transaction->destination_type = 'cash';
                            $transaction->destination_id = 2;
                            $transaction->amount = $payment['value'];
                        } else {
                            $transaction->type = 'receipt';
                            $transaction->origin_type = 'customer';
                            $transaction->origin_id = $debtData->customer_id;
                            $transaction->date = $payment['date'];
                            $transaction->destination_type = 'bank';
                            $transaction->destination_id = $payment['bank'];
                            $transaction->amount = $payment['value'];
                        }
                        $transaction->save();
                        $transactions[] = $transaction->id;
                    }
                }

                $debt = Debt::where('id', $installment->debt_id)->firstOrFail();

                // Salvar a coleçãos antes de chamar o sync
                $currentTransactions = $installment->transactions()->get();

                $installment->transactions()->sync($transactions);
                $debt->transactions()->sync($transactions);

                foreach ($currentTransactions as $transaction) {
                    if (!$installment->transactions()->find($transaction->id)) {
                        $transaction->delete();
                    }
                }

                $total = 0;
                foreach ($debt->transactions()->get() as $transaction) {
                    $total += $transaction->amount;
                }

                $debt->paid = $total;
                if ($debt->total <= $total) {
                    $debt->status = true;
                }
                $debt->save();
                return $debt;
            });
            return $dbTransaction;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getInstallment($id)
    {
        $installment = Installment::where('id', $id)->with('transactions')->firstOrFail();
        return $installment;
    }

    public function list(Request $request)
    {
        try {

            $perPage = $request->perPage ?? 30;
            $order = $request->order ?? 'desc';

            $results = Debt::select('debts.id', 'debts.payment_type', 'debts.total', 'debts.paid', 'vehicles.plate', 'customers.name', 'customers.id as customer_id', 'vehicles.type_id', 'vehicles.model_id', 'vehicles.brand_id', 'vehicles.color_id', 'vehicles.fuel_id', 'vehicles.plate', 'vehicles.color_id')
                ->selectRaw("CASE
                WHEN debt_vehicle_entry.debt_id IS NOT NULL THEN vehicle_entries.date
                WHEN debt_vehicle_exit.debt_id IS NOT NULL THEN vehicle_exits.date
                END AS date")
                ->leftJoin('debt_vehicle_entry', 'debts.id', '=', 'debt_vehicle_entry.debt_id')
                ->leftJoin('debt_vehicle_exit', 'debts.id', '=', 'debt_vehicle_exit.debt_id')
                ->leftJoin('vehicle_entries', 'vehicle_entries.id', '=', 'debt_vehicle_entry.vehicle_entry_id')
                ->leftJoin('vehicle_exits', 'vehicle_exits.id', '=', 'debt_vehicle_exit.vehicle_exit_id')
                ->leftJoin('vehicles', function ($join) {
                    $join->on('vehicle_entries.vehicle_id', '=', 'vehicles.id')
                        ->orOn('vehicle_exits.vehicle_id', '=', 'vehicles.id');
                })
                ->leftJoin('customers', function ($join) {
                    $join->on('vehicle_entries.owner_id', '=', 'customers.id')
                        ->orOn('vehicle_exits.owner_id', '=', 'customers.id');
                })
                ->where('debts.status', false)
                ->where('vehicle_entries.finished', true)
                ->orWhere('vehicle_exits.finished', true)
                ->orderBy('id', $order)
                ->paginate($perPage);

            return $results;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
