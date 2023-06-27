<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Brand;
use App\Models\Cashier;
use App\Models\EntryClerk;
use App\Models\ExitClerk;
use App\Models\ExitPayment;
use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\VehicleHistory;
use App\Models\VehicleEntry;
use App\Models\VehicleEntryPayment;
use App\Models\VehicleExit;
use App\Models\VehicleExitPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\JsonDecoder;

class VehicleHistoryController extends Controller
{

    public function listEntrys()
    {
    }

    public function listExits()
    {
        $vehicleHistory = VehicleExit::with('vehicle')
            ->with('vehicle.brand')
            ->with('vehicle.color')
            ->with('vehicle.owner')
            ->with('vehicle.fuel')
            ->with('vehicle.transmission')
            ->with('vehicle.type')
            ->with('vehicle.model')
            ->with('clerks')
            ->get();
        return $vehicleHistory;
    }

    public function showEntry($id)
    {
        $vehicleHistory = VehicleEntry::where('id', $id)
            ->with('vehicle')
            ->with('clerks')
            ->with('payments')
            ->with('payments.transaction')
            ->firstOrFail();
        return $vehicleHistory;
    }

    public function showExit($id)
    {
        $vehicleHistory = VehicleExit::where('id', $id)
            ->with('vehicle')
            ->with('vehicle.brand')
            ->with('vehicle.color')
            ->with('vehicle.owner')
            ->with('vehicle.fuel')
            ->with('vehicle.transmission')
            ->with('vehicle.type')
            ->with('vehicle.model')
            ->with('clerks')
            ->firstOrFail();
        return $vehicleHistory;
    }

    public function showStock(Request $request)
    {
        $query = VehicleHistory::query()
            ->where('vehicle_exit_id', null)
            ->with([
                'entry',
                'entry.vehicle',
                'entry.vehicle.brand',
                'entry.vehicle.color',
                'entry.vehicle.owner',
                'entry.vehicle.fuel',
                'entry.vehicle.transmission',
                'entry.vehicle.type',
                'entry.vehicle.model',
                'entry.clerks'
            ]);

        $query->whereHas('entry', function ($q) {
            $q->where('finished', true);
        });

        $query->whereHas('entry.vehicle', function ($q) use ($request) {
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
        });

        $vehicleHistory = $query->get();
        return $vehicleHistory;
    }

    public function createExit(Request $request)
    {
        $find = VehicleHistory::where('vehicle_id', $request->vehicle)->where('vehicle_exit_id', null)->first();
        if ($find != null) {
            $transaction = DB::transaction(function () use ($request, $find) {
                $user = auth()->user();
                $vehicleHistoric = $find;
                $exit = new VehicleExit();
                $exit->vehicle_id = $request->vehicle;
                $exit->mileage = $request->mileage;
                $exit->owner_id = $request->owner;
                $exit->sale_value = $request->value;
                $exit->date = $request->dateExit;
                $exit->note = $request->note;
                $exit->creator = $user->id;
                $exit->save();

                $clerks = $request->clerks;

                foreach ($clerks as $clerk) {
                    $exitClerk = new ExitClerk();
                    $exitClerk->vehicle_exit_id = $exit->id;
                    $exitClerk->dispatcher_id = $clerk;
                    $exitClerk->save();
                }

                $payments = $request->payments;
                $transactions = [];
                foreach ($payments as $payment) {
                    $transaction = new Transaction();
                    $transaction->type = 'payment';
                    if ($payment['formPayment'] == 1) {
                        $transaction->origin_type = 'cash';
                        $transaction->origin_id = 2;
                        $Cashier = Cashier::where('id', $transaction->origin_id)->firstOrFail();
                        $Cashier->balance = $Cashier->balance + $payment['value'];
                        $Cashier->save();
                    } else {
                        $transaction->origin_type = 'bank';
                        $transaction->origin_id = $payment['bank'];
                        $Bank = Bank::where('id', $transaction->origin_id)->firstOrFail();
                        $Bank->balance = $Bank->balance + $payment['value'];
                        $Bank->save();
                    }
                    $transaction->destination_type = 'customer';
                    $transaction->destination_id = $request->owner;

                    $transaction->amount = $payment['value'];
                    $transaction->payment_form_id = $payment['formPayment'];
                    $transaction->creator = $user->id;
                    $transaction->save();
                    $transactions[] = $transaction;
                }

                $exitPayments = [];

                foreach ($transactions as $transaction) {
                    $exitPayment = new VehicleExitPayment();
                    $exitPayment->vehicle_exit_id = $exit->id;
                    $exitPayment->date = $exit->date;
                    $exitPayment->amount = $transaction->amount;
                    $exitPayment->transaction_id = $transaction->id;
                    $exitPayment->save();
                    $exitPayments[] = $exitPayment;
                }

                $vehicleHistoric->vehicle_id = $request->vehicle;
                $vehicleHistoric->exit_id = $exit->id;
                $vehicleHistoric->save();

                return $exit;
            });
            return $transaction;
        } else {
            return ['error' => 'Verifique os dados Inseridos!'];
        }
    }

    public function updateEntry2(Request $request, $id)
    {
        $transaction = DB::transaction(function () use ($request, $id) {
            $user = auth()->user();
            $entry = VehicleEntry::where('id', $id)->firstOrFail();
            $entry->vehicle_id = $request->vehicle;
            $entry->mileage = $request->mileage;
            $entry->supplier_id = $request->supplier;
            $entry->owner_id = $request->owner;
            $entry->amount_paid = $request->entryValue;
            $entry->start_value = $request->value;
            $entry->value = $request->value;
            $entry->minimum_value = $request->minimumValue;
            $entry->date = $request->dateEntry;
            $entry->note = $request->note;
            $entry->modifier = $user->id;
            $entry->save();

            $clerks = $request->clerks;

            $entry->clerks()->delete();

            foreach ($clerks as $clerk) {
                $entryClerk = new EntryClerk();
                $entryClerk->vehicle_entry_id = $entry->id;
                $entryClerk->dispatcher_id = $clerk;
                $entryClerk->save();
            }

            return $entry;
        });

        return $transaction;
    }

    public function updateEntry(Request $request, $id)
    {
        $find = VehicleHistory::where('vehicle_id', $request->vehicle)->where('vehicle_exit_id', null)->first();
        if ($find != null) {
            try {
                $dbTransaction = DB::transaction(function () use ($request, $find, $id) {
                    $user = auth()->user();
                    $vehicleHistoric = $find;
                    $entry = VehicleEntry::where('id', $id)->where('finished', false)->with('payments')->firstOrFail();
                    $entry->vehicle_id = $request->vehicle;
                    $entry->mileage = $request->mileage;
                    $entry->supplier_id = $request->supplier;
                    $entry->owner_id = $request->owner;
                    $entry->amount_paid = $request->entryValue;
                    $entry->assessed_value = $request->assessedValue;
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

                    $clerks = $request->clerks;

                    $entry->clerks()->delete();

                    foreach ($clerks as $clerk) {
                        $entryClerk = new EntryClerk();
                        $entryClerk->vehicle_entry_id = $entry->id;
                        $entryClerk->dispatcher_id = $clerk;
                        $entryClerk->save();
                    }

                    foreach ($entry->payments as $payment) {
                        $transaction = Transaction::where('id', $payment->transaction_id)->firstOrFail();
                        if ($transaction->type = 'payment') {
                            if ($transaction->origin_type == 'cash') {
                                $Cashier = Cashier::where('id', $transaction->origin_id)->firstOrFail();
                                $Cashier->balance = $Cashier->balance + $transaction->amount;
                                $Cashier->save();
                            } elseif ($transaction->origin_type == 'bank') {
                                $Bank = Bank::where('id', $transaction->origin_id)->firstOrFail();
                                $Bank->balance = $Bank->balance + $transaction->amount;
                                $Bank->save();
                            }
                        }
                        $transaction->delete();
                    }

                    $payments = $request->payments;

                    $transactions = [];
                    foreach ($payments as $payment) {
                        $transaction = new Transaction();
                        $transaction->type = 'payment';
                        if ($payment['formPayment'] == 1) {
                            $transaction->origin_type = 'cash';
                            $transaction->origin_id = 2;
                            $Cashier = Cashier::where('id', $transaction->origin_id)->firstOrFail();
                            if ($Cashier->balance > $payment['value']) {
                                $Cashier->balance = $Cashier->balance - $payment['value'];
                            } else {
                                throw new \Exception("Não existe saldo no caixa para a transação!");
                            }
                            $Cashier->save();
                        } else {
                            $transaction->origin_type = 'bank';
                            $transaction->origin_id = $payment['bank'];
                            $Bank = Bank::where('id', $transaction->origin_id)->firstOrFail();
                            if ($Bank->balance > $payment['value']) {
                                $Bank->balance = $Bank->balance - $payment['value'];
                            } else {
                                throw new \Exception("Não existe saldo no banco para a transação!");
                            }
                            $Bank->save();
                        }
                        $transaction->destination_type = 'customer';
                        $transaction->destination_id = $request->owner;

                        $transaction->amount = $payment['value'];
                        $transaction->payment_form_id = $payment['formPayment'];
                        $transaction->creator = $user->id;
                        $transaction->save();
                        $transactions[] = $transaction;
                    }

                    foreach ($transactions as $transaction) {
                        $entryPayment = new VehicleEntryPayment();
                        $entryPayment->vehicle_entry_id = $entry->id;
                        $entryPayment->date = $entry->date;
                        $entryPayment->amount = $transaction->amount;
                        $entryPayment->transaction_id = $transaction->id;
                        $entryPayment->save();
                        $entryPayments[] = $entryPayment;
                    }

                    $vehicleHistoric->vehicle_id = $request->vehicle;
                    $vehicleHistoric->save();

                    return $entry;
                });
                return $dbTransaction;
            } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
            }
        } else {
            return ['error' => 'O veículo não está no estoque!'];
        }
    }

    public function updateExit(Request $request, $id)
    {
        $transaction = DB::transaction(function () use ($request, $id) {
            $user = auth()->user();
            $exit = VehicleExit::where('id', $id)->firstOrFail();
            $exit->vehicle_id = $request->vehicle;
            $exit->mileage = $request->mileage;
            $exit->owner_id = $request->owner;
            $exit->sale_value = $request->value;
            $exit->date = $request->dateExit;
            $exit->note = $request->note;
            $exit->modifier = $user->id;
            $exit->save();

            $clerks = $request->clerks;

            $exit->clerks()->delete();

            foreach ($clerks as $clerk) {
                $exitClerk = new ExitClerk();
                $exitClerk->vehicle_exit_id = $exit->id;
                $exitClerk->dispatcher_id = $clerk;
                $exitClerk->save();
            }

            return $exit;
        });
        return $transaction;
    }

    public function sellVehicles()
    {
        $customerId = 1;
        $vehicles = [1, 2];
        $paymentMethods = [
            ['payment_form_id' => 1, 'amount' => 100000],
        ];

        $user = auth()->user();

        try {
            DB::beginTransaction();
            // Cria as transações
            $transactions = [];
            $exitPayments = [];
            foreach ($paymentMethods as $paymentMethod) {
                $transaction = new Transaction();
                $transaction->type = 'payment';
                $transaction->origin_type = 'customer';
                $transaction->origin_id = $customerId;
                $transaction->destination_type = 'bank';
                $transaction->destination_id = 1;
                $transaction->amount = $paymentMethod['amount'];
                $transaction->payment_form_id = $paymentMethod['payment_form_id'];
                $transaction->creator = $user->id;
                $transaction->save();
                $transaction->amout_available = $paymentMethod['amount'];
                $transactions[] = $transaction;
            }

            // Cria as saídas de veículos
            foreach ($vehicles as $vehicleId) {
                $find = VehicleHistory::where('vehicle_id', $vehicleId)->where('vehicle_exit_id', null)->first();
                if ($find != null) {
                    $vehicle = Vehicle::findOrFail($vehicleId);
                    $vehicleExit = new VehicleExit();
                    $vehicleExit->vehicle_id = $vehicle->id;
                    $vehicleExit->mileage = $vehicle->mileage;
                    $vehicleExit->owner_id = $vehicle->owner_id;
                    $vehicleExit->sale_value = 50000;
                    $vehicleExit->date = Carbon::now();
                    $vehicleExit->note = '';
                    $vehicleExit->creator = $user->id;
                    $vehicleExit->save();

                    $vehicleValue = $vehicleExit->sale_value;

                    foreach ($transactions as $transaction) {

                        if ($transaction->amout_available > 0) {
                            if ($transaction->amout_available  >= $vehicleValue) {
                                // O pagamento atual é suficiente para cobrir o valor do veículo
                                $paymentAmount = $vehicleValue;
                            } else {
                                // O pagamento atual é parcialmente suficiente para cobrir o valor do veículo
                                $paymentAmount = $transaction->amout_available;
                            }

                            $exitPayment = new ExitPayment();
                            $exitPayment->exit_id = $vehicleExit->id;
                            $exitPayment->date = $vehicleExit->date;
                            $exitPayment->note = $vehicleExit->note;
                            $exitPayment->amount = $paymentAmount;
                            $exitPayment->transaction_id = $transaction->id;
                            $exitPayment->save();
                            $exitPayments[] = $exitPayment;

                            // Atualiza o valor do pagamento do método atual
                            $transaction->amout_available -= $paymentAmount;

                            if ($transaction->amout_available < 0) {
                                dd($transaction->amout_available);
                            }

                            $vehicleValue -= $paymentAmount;

                            if ($vehicleValue === 0) {
                                // Todos os pagamentos necessários para o veículo foram utilizados
                                break;
                            }
                        } else {
                            throw new \Exception("O Veículo não esta Disponível para venda!");
                        }
                    }
                }

                if ($vehicleValue > 0) {
                    throw new \Exception("Não há pagamentos suficientes para cobrir o valor da saída do veículo.");
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
            return false;
        }
    }

    public function tradeVehicles(Request $request)
    {
        $veiculosEntrada = $request->input('veiculosEntrada');
        $veiculosSaida = $request->input('veiculosSaida');

        $valorTotalEntrada = collect($veiculosEntrada)->reduce(function ($acc, $veiculo) {
            $acc['pendenciasTotal'] += $veiculo['pendencias'];
            $acc['valorTotal'] += $veiculo['valor'];
            return $acc;
        }, [
            'pendenciasTotal' => 0,
            'valorTotal' => 0,
        ]);

        $valorTotalSaida = collect($veiculosSaida)->reduce(function ($acc, $veiculo) {
            $acc['valorCustoTotal'] += $veiculo['valorCusto'];
            $acc['valorVendaTotal'] += $veiculo['valorVenda'];
            $acc['valorServicoTotal'] += $veiculo['valorServico'];
            return $acc;
        }, [
            'valorCustoTotal' => 0,
            'valorServicoTotal' => 0,
            'valorVendaTotal' => 0
        ]);

        $lucroTotal = ($valorTotalSaida['valorVendaTotal'] + $valorTotalSaida['valorServicoTotal']) - $valorTotalEntrada['valorTotal'];

        foreach ($veiculosEntrada as $veiculo) {
            $proporcao = $veiculo['valor'] / $valorTotalEntrada['valorTotal'];
            $valorCusto = ($valorTotalSaida['valorCustoTotal'] - $lucroTotal) * $proporcao;

            echo $valorCusto;
        }
    }

    public function delete(Request $request)
    {
        $vehicleModel = VehicleHistory::firstOrFail($request->id);
        $vehicleModel->delete();
    }
}
