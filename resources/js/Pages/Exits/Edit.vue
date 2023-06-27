<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextArea from '@/Components/TextArea.vue';
import { pt } from 'date-fns/locale';
import Datepicker from 'vue3-datepicker';
import moment from 'moment';
import Multiselect from '@vueform/multiselect';

import Find from './Partials/FindVehicle.vue';
import Payment from './Partials/Payment.vue';
import Owner from './Partials/NewExitOwner.vue';

const props = defineProps({
    exit: Number
});

const emit = defineEmits(['needReload']);

const form = reactive({
    dateExit: new Date(),
    vehicle: '',
    supplier: '',
    owner: '',
    mileage: '',
    clerks: [],
    value: '',
    services: '',
    addition: '',
    note: '',
    payments: [],
    finish: false
});

const vehicles = reactive({
    options: [],
});

const content = reactive({
    exit: Object,
})

const locale = pt;

const confirmingFinisher = ref(false);

const exitUpdate = ref(false);

const setOwner = (data) => {
    if (data) {
        form.owner = data;
    }
}

const setVehicle = (data) => {
    form.vehicle = data;
    selectVehicle();
}

const loadForwardingAgent = async () => {
    try {
        const options = [];
        const response = await axios.get(route('showFunction', 1));
        response.data.employee.forEach(employee => {
            options.push({
                value: employee.id,
                label: employee.name
            })
        });
        return options;

    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const paymentsForms = [];

const loadPayments = async () => {
    try {
        const response = await axios.post(route('listPayments'));
        response.data.forEach(payment => {
            paymentsForms[payment.id] = payment.name;
        });
    } catch (error) {
        console.log(error);
    }
}

const Banks = [];

const loadBanks = async () => {
    try {
        const response = await axios.post(route('listBanks'));
        response.data.forEach(bank => {
            Banks[bank.id] = bank.name;
        });
    } catch (error) {
        console.log(error);
    }
}

const updateExit = () => {
    form.processing = true;

    axios.put(route('updateExit', props.exit), {
        dateExit: moment(form.dateExit).format("yyyy-MM-DD"),
        vehicle: form.vehicle,
        supplier: form.supplier,
        owner: form.owner,
        mileage: form.mileage,
        clerks: form.clerks,
        value: form.value,
        services: form.services,
        addition: form.addition,
        note: form.note,
        payments: form.payments,
        finish: form.finish,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            emit('needReload');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const loadExit = async () => {
    try {
        const response = await axios.get(route('showExit', props.exit));
        content.exit = response.data;
        form.dateExit = new Date(response.data.date);
        vehicles.options.push({
            value: response.data.vehicle.id,
            label: response.data.vehicle.plate,
            mileage: response.data.vehicle.mileage,
            clerks: response.data.clerks.map((clerk) => {
                return clerk.dispatcher_id;
            })
        })
        form.vehicle = response.data.vehicle_id;
        form.supplier = response.data.supplier_id;
        form.owner = response.data.owner_id;
        form.mileage = response.data.mileage;
        form.value = response.data.sale_value;
        form.services = response.data.services;
        form.addition = response.data.addition;
        form.note = response.data.note;
        form.clerks = response.data.clerks.map((clerk) => {
            return clerk.id;
        })

        response.data.transactions.forEach(transaction => {
            if (transaction.type == 'receipt') {
                switch (transaction.destination_type) {
                    case 'cash':
                        form.payments.push({ "type": transaction.type, "formPayment": 1, "value": transaction.amount, "id": transaction.id });
                        break;

                    case 'bank':
                        form.payments.push({ "type": transaction.type, "formPayment": 2, "value": transaction.amount, "bank": transaction.destination_id, "id": transaction.id });
                        break;
                }
            } else {
                switch (transaction.origin_type) {
                    case 'cash':
                        form.payments.push({ "type": transaction.type, "formPayment": 1, "value": transaction.amount, "id": transaction.id });
                        break;

                    case 'bank':
                        form.payments.push({ "type": transaction.type, "formPayment": 2, "value": transaction.amount, "bank": transaction.origin_id, "id": transaction.id });
                        break;
                }
            }
        })

        response.data.debts.forEach(debt => {
            switch (debt.payment_type) {
                case 'financing':
                    form.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 3, "value": debt.total, 'installments': debt.installments, 'installment_value': debt.data['installment_value'], 'first_payment': new Date(debt.data['first_payment']), 'financial': debt.data['financial'], "id": debt.id });
                    break;
                case 'credit_card':
                    form.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 4, "value": debt.total, 'installments': debt.installments, 'installment_value': debt.data['installment_value'], 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
                case 'promissory':
                    form.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 5, "value": debt.total, 'installments': debt.installments, 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
                case 'checks':
                    form.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 6, "value": debt.total, 'installments': debt.installments, 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
                case 'slips':
                    form.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 7, "value": debt.total, 'installments': debt.installments, 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
            }
        })
    } catch (error) {
        console.log(error);
    }
}

const loadVehicles = async () => {
    try {
        const response = await axios.post(route('showStock'));
        response.data.forEach(data => {
            vehicles.options.push({
                value: data.entry.vehicle.id,
                label: data.entry.vehicle.plate,
                mileage: data.entry.vehicle.mileage,
                clerks: data.entry.clerks.map((clerk) => {
                    return clerk.dispatcher_id;
                })
            })
        });
        return vehicles.options;

    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const selectVehicle = () => {
    const selectedVehicle = vehicles.options.find(option => option.value === form.vehicle);
    if (selectedVehicle) {
        form.mileage = selectedVehicle.mileage,
            form.clerks = selectedVehicle.clerks
        inputStatus.owner = false;
    }
};

const paymentDifference = async () => {
    let total = 0;
    await form.payments.forEach(payment => {
        total += parseFloat(payment.value);
    })
    form.paymentTotal = (total - (parseFloat(form.value) + parseFloat(form.services) + parseFloat(form.addition)));
    if(form.paymentTotal == 0 && form.payments.length > 0){
        confirmingFinisher.value = true;
    }else{
        form.error = 'Não é possivel finalizar ate que os pagamentos estejam concluidos!'
    }
}

const addPayment = (data) => {
    form.payments.push(data);
}

const removePayment = (data) => {
    form.payments.splice(data, 1);;
}

const startexitUpdate = async () => {
    await loadExit();
    await loadPayments();
    await loadBanks();
    exitUpdate.value = true;
};

const finishEntry = async () => {
    form.finish = true;
    updateExit();
}

const closeModal = () => {
    confirmingFinisher.value = false;
    exitUpdate.value = false;
    form.finish = false;
    form.error = '';
    form.dateExit = new Date();
    form.vehicle = '';
    form.supplier = '';
    form.owner = '';
    form.mileage = '';
    form.clerks = [];
    form.value = '';
    form.services = '';
    form.addition = '';
    form.note = '';
    form.payments = [];
    vehicles.options = [];
};
</script>

<template>
    <tr @click="startexitUpdate" class="cursor-pointer hover:bg-green-400">
        <slot />
    </tr>

    <DialogModal :show="exitUpdate" @close="closeModal" :maxWidth="'6xl'">
        <template #title>
            Saída de Veículo do Estoque
        </template>

        <template #content>
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-5 mt-4">
                <div class="w-full flex flex-wrap gap-1 place-items-center">
                    <div>Veículo:</div>
                    <div class="w-full flex gap-5 place-items-center">
                        <Multiselect placeholder="Veículo" v-model="form.vehicle" noOptionsText="Sem veículos no estoque!"
                            @select="selectVehicle" :searchable="true" :canClear="false" :options="async (query) => {
                                return await loadVehicles(query)
                            }" />
                        <Find v-on:vehicle-set="setVehicle" />
                    </div>
                </div>
                <div class="w-full flex flex-col gap-1">
                    <div>Despachante:</div>
                    <Multiselect placeholder="Despachante" mode="tags" max="2" v-model="form.clerks" :disabled="false"
                        :searchable="true" :options="async (query) => {
                            return await loadForwardingAgent(query)
                        }" />
                </div>
                <div class="w-full flex flex-col gap-1">
                    <div>Proprietário:</div>
                    <Owner :owner="form.owner" v-on:owner-set="setOwner" :is-disabled="false" />
                </div>
                <div class="w-48 flex flex-col gap-1">
                    <div>Quilometragem:</div>
                    <money3 v-model="form.mileage" v-bind="mileageConfig"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </money3>
                </div>
                <div class="flex flex-col gap-1">
                    <div>Data de Saída:</div>
                    <datepicker v-model="form.dateExit" inputFormat="dd/MM/yyyy" :locale="locale"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                </div>
                <div class="w-48 flex flex-col gap-1">
                    <div>Valor:</div>
                    <money3 v-model="form.value" v-bind="moneyConfig"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </money3>
                </div>
                <div class="w-48 flex flex-col gap-1">
                    <span>Serviços:</span>
                    <money3 v-model="form.services" v-bind="moneyConfig"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </money3>
                </div>
                <div class="w-48 flex flex-col gap-1">
                    <span>Adição:</span>
                    <money3 v-model="form.addition" v-bind="moneyConfig"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </money3>
                </div>
                <div class="w-full">
                    <TextArea ref="noteInput" v-model="form.note" type="text" class="w-full"
                        placeholder="Observações"></TextArea>
                </div>
                <div class="w-full relative">
                    <div class="overflow-x-auto overflow-y-scroll max-h-60 h-60 border border-gray-300">
                        <table class='mx-auto w-full whitespace-nowrap bg-white divide-y divide-gray-300 mb-16'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Tipo</th>
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Form.
                                        Pagamento</th>
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Valor</th>

                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Parcelas
                                    </th>
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Valor das
                                        Parcelas</th>
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Primeiro
                                        Pagamento</th>
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Banco</th>
                                    <th class="font-semibold text-xs uppercase px-6 py-4 border border-black"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="payment in form.payments">
                                    <td class="px-6 py-4 border">{{ payment.type == 'payment' ? 'Pagamento' :
                                        'Recebimento' }}</td>
                                    <td class="px-6 py-4 border">{{ paymentsForms[payment.formPayment] }}</td>
                                    <td class="px-6 py-4 border">{{ parseFloat(payment.value).toLocaleString('pt-BR', {
                                        style:
                                            'currency', currency: 'BRL'
                                    }) }}</td>
                                    <td class="px-6 py-4 border">{{ payment.installments }}</td>
                                    <td class="px-6 py-4 border">{{
                                        payment.installment_value ?
                                        parseFloat(payment.installment_value).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) : '' }}</td>
                                    <td v-if="payment.first_payment" class="px-6 py-4 border">{{
                                        payment.first_payment.toLocaleDateString() }}</td>
                                    <td v-else class="px-6 py-4 border"></td>
                                    <td class="px-6 py-4 border">{{ Banks[payment.bank] }}</td>
                                    <td class="px-6 py-4 border"><font-awesome-icon
                                            @click="removePayment(form.payments.indexOf(payment))" class="cursor-pointer"
                                            :icon="['far', 'trash-can']" size="lg" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Payment v-on:add-payment="addPayment" />
                </div>
                <!-- Delete Team Confirmation Modal -->
                <ConfirmationModal :show="confirmingFinisher" @close="confirmingFinisher = false">
                    <template #title>
                        Finalizar
                    </template>

                    <template #content>
                        Deseja realmente finalizar a saída do veículo? Não será possivel editar a mesma após isso.
                    </template>

                    <template #footer>
                        <SecondaryButton @click="confirmingFinisher = false">
                            Cancelar
                        </SecondaryButton>

                        <DangerButton @click="finishEntry" class="ml-3" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Finalizar
                        </DangerButton>
                    </template>
                </ConfirmationModal>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="updateExit">
                Salvar
            </PrimaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="paymentDifference">
                Finalizar
            </DangerButton>
        </template>
    </DialogModal>
</template>
<script>
import { Money3Component } from 'v-money3';

export default {
    components: { money3: Money3Component, Datepicker },
    data() {
        return {
            amount: '',
            moneyConfig: {
                masked: false,
                prefix: 'R$ ',
                suffix: '',
                thousands: '.',
                decimal: ',',
                precision: 2,
                disableNegative: false,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
            },
            mileageConfig: {
                masked: false,
                prefix: '',
                suffix: ' km',
                thousands: '.',
                decimal: '',
                precision: 0,
                disableNegative: false,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
            }
        }
    }
}
</script>