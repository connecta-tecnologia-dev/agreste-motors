<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { pt } from 'date-fns/locale';
import Datepicker from 'vue3-datepicker';
import moment from 'moment';

import Owner from './Partials/NewExitOwner.vue';
import Payment from './Partials/Payment.vue';
import MultipleVehicle from './Partials/MultipleVehicle.vue';

const props = defineProps({
    data: Object,
    title: {
        type: String,
        default: 'Saída de multiplos veículos',
    },
    content: {
        type: String,
        default: '',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
});

const emit = defineEmits(['needReload']);

const form = reactive({
    dateExit: new Date(),
    vehicles: [],
    payments: []
});

const locale = pt;

const entryCreate = ref(false);

const startentryCreate = async () => {
    await loadPayments();
    entryCreate.value = true;
};

const setOwner = (data) => {
    if (data) {
        form.owner = data;
    }
}

const newExit = () => {
    form.processing = true;

    axios.post(route('multipleExit'), {
        dateExit: moment(form.dateExit).format("yyyy-MM-DD"),
        vehicles: form.vehicles,
        owner: form.owner,
        payments: form.payments,
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

const findVehicle = (f) => {
    let find = false;
    form.vehicles.forEach(e => {
        if (e.data.vehicle_id == f.data.vehicle_id) {
            find = true;
            return false;
        }
    })
    return find;
}

const addVehicle = (data) => {
    if (!findVehicle(data)) {
        form.vehicles.push(data);
    }
}

const removeVehicle = (data) => {
    form.vehicles.splice(data, 1);;
}

const addPayment = (data) => {
    console.log(data);
    form.payments.push(data);
}

const removePayment = (data) => {
    form.payments.splice(data, 1);;
}

const closeModal = () => {
    entryCreate.value = false;
    form.dateExit = new Date();
    form.vehicles = [];
    form.owner = '';
    form.payments = [];
    form.clerks = [];
};
</script>

<template>
    <span>
        <span @click="startentryCreate">
            <slot />
        </span>

        <DialogModal :show="entryCreate" @close="closeModal" :maxWidth="'7xl'">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}
                <InputError :message="form.error" class="mt-2" />
                <div class="flex flex-wrap gap-3 mt-4 justify-between">
                    <div class="w-full flex flex-wrap mb-2 gap-3">
                        <div class="w-9/12">
                            <div>Novo Proprietário:</div>
                            <Owner :owner="form.owner" v-on:owner-set="setOwner" :is-disabled="false" />
                        </div>
                        <div>
                            <div>Data:</div>
                            <datepicker v-model="form.dateExit" inputFormat="dd/MM/yyyy" :locale="locale"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                    </div>

                    <div class="w-full relative">
                        <div class="overflow-x-auto overflow-y-scroll max-h-60 h-60 border border-gray-300">
                            <table class='mx-auto w-full whitespace-nowrap bg-white divide-y divide-gray-300 mb-16'>
                                <thead class="bg-gray-900">
                                    <tr class="text-white text-left">
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Marca
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Modelo
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Versão
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Cor
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Placa
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">
                                            Quilometragem</th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Valor
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">
                                            Serviços</th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Adição
                                        </th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="h in form.vehicles">
                                        <td class="px-6 py-4 border border-black">{{ h.data.entry.vehicle.brand.name }}
                                        </td>
                                        <td class="px-6 py-4 border border-black">{{ h.data.entry.vehicle.model.name }}
                                        </td>
                                        <td class="px-6 py-4 border border-black">{{ h.data.entry.vehicle.version }}
                                        </td>
                                        <td class="px-6 py-4 border border-black">{{ h.data.entry.vehicle.color.name }}
                                        </td>
                                        <td class="px-6 py-4 border border-black">{{ h.data.entry.vehicle.plate }}</td>
                                        <td class="px-6 py-4 border border-black">{{ h.mileage }}</td>
                                        <td class="px-6 py-4 border border-black">{{
                                            parseFloat(h.value).toLocaleString('pt-BR', {
                                                style: 'currency', currency:
                                                    'BRL'
                                            }) }}</td>
                                        <td class="px-6 py-4 border border-black">{{
                                            parseFloat(h.services).toLocaleString('pt-BR', {
                                                style: 'currency',
                                                currency: 'BRL'
                                            }) }}</td>
                                        <td class="px-6 py-4 border border-black">{{
                                            parseFloat(h.addition).toLocaleString('pt-BR', {
                                                style: 'currency',
                                                currency: 'BRL'
                                            }) }}</td>
                                        <td class="px-6 py-4 border border-black"><font-awesome-icon
                                                @click="removeVehicle(form.payments.indexOf(h))" class="cursor-pointer"
                                                :icon="['far', 'trash-can']" size="lg" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <MultipleVehicle v-on:add-vehicle="addVehicle" />
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
                                    <template v-if="form.payments.length > 0">
                                        <tr v-for="payment in form.payments">
                                            <td class="px-6 py-4 border">{{ payment.type == 'payment' ? 'Pagamento' :
                                                'Recebimento' }}</td>
                                            <td class="px-6 py-4 border">{{ paymentsForms[payment.formPayment] }}</td>
                                            <td class="px-6 py-4 border">{{
                                                parseFloat(payment.value).toLocaleString('pt-BR', {
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
                                                    @click="removePayment(form.payments.indexOf(payment))"
                                                    class="cursor-pointer" :icon="['far', 'trash-can']" size="lg" /></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <Payment v-on:add-payment="addPayment" />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="newExit">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </span>
</template>
<script>
import { Money3Component } from 'v-money3';

export default {
    components: { money3: Money3Component, Datepicker },
    data() {
        return {
            amount: '',
            config: {
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
            }
        }
    }
}
</script>