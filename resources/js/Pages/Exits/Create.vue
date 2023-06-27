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

import Payment from './Partials/Payment.vue';
import Owner from './Partials/NewExitOwner.vue';
import Find from './Partials/FindVehicle.vue';

defineProps({
    title: {
        type: String,
        default: 'Saída de Veículo do Estoque',
    },
    content: {
        type: String,
        default: '',
    },
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

const locale = pt;

const confirmingFinisher = ref(false);

const inputStatus = reactive({
    owner: true,
});

const entryCreate = ref(false);

const startentryCreate = async () => {
    await loadPayments();
    await loadBanks();
    entryCreate.value = true;
};

const setOwner = (data) => {
    if (data) {
        form.owner = data;
    }
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

const vehicles = reactive({
    options: [],
});

const loadVehicles = async () => {
    try {
        vehicles.options = [];
        const response = await axios.get(route('showStock'));
        response.data.forEach(data => {
            vehicles.options.push({
                value: data.entry.vehicle.id,
                label: data.entry.vehicle.plate,
                mileage: data.entry.vehicle.mileage,
                clerks: data.entry.clerks.map((clerk) => {
                    return clerk.id;
                })
            })
        });
        return vehicles.options;

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

const newExit = () => {
    form.processing = true;
    confirmingFinisher.value = false;
    axios.post(route('createExit'), {
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
    if (form.paymentTotal == 0 && form.payments.length > 0) {
        confirmingFinisher.value = true;
    } else {
        form.error = 'Não é possivel finalizar ate que os pagamentos estejam concluidos!'
    }
}

const setVehicle = (data) => {
    form.vehicle = data;
    selectVehicle();
}

const addPayment = (data) => {
    form.payments.push(data);
}

const removePayment = (data) => {
    form.payments.splice(data, 1);;
}

const closeModal = () => {
    inputStatus.owner = true;
    entryCreate.value = false;
    form.error = '';
    form.dateExit = new Date();
    form.entry = '';
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
    form.finish = false;
};
</script>

<template>
    <span>
        <span @click="startentryCreate">
            <slot />
        </span>

        <DialogModal :show="entryCreate" @close="closeModal" :maxWidth="'6xl'">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}
                <InputError :message="form.error" class="mt-2" />
                <div class="flex flex-wrap gap-5 mt-4">
                    <div class="w-full flex flex-wrap gap-1 place-items-center">
                        <div>Veículo:</div>
                        <div class="w-full flex gap-5 place-items-center">
                            <Multiselect placeholder="Veículo" v-model="form.vehicle"
                                noOptionsText="Sem veículos no estoque!" @select="selectVehicle" :searchable="true"
                                :canClear="false" :options="async (query) => {
                                    return await loadVehicles(query)
                                }" />
                            <Find v-on:vehicle-set="setVehicle" />
                        </div>
                    </div>
                    <div class="w-full flex flex-col gap-1">
                        <div>Despachante:</div>
                        <Multiselect placeholder="Despachante" mode="tags" max="2" v-model="form.clerks"
                            :disabled="inputStatus.owner" :searchable="true" :options="async (query) => {
                                return await loadForwardingAgent(query)
                            }" />
                    </div>
                    <div class="w-full flex flex-col gap-1">
                        <div>Proprietário:</div>
                        <Owner :owner="form.owner" v-on:owner-set="setOwner" :is-disabled="inputStatus.owner" />
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
                                                @click="removePayment(form.payments.indexOf(payment))"
                                                class="cursor-pointer" :icon="['far', 'trash-can']" size="lg" /></td>
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

                            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing" @click="newExit">
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
                    @click="newExit">
                    Salvar
                </PrimaryButton>

                <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="paymentDifference">
                    Finalizar
                </DangerButton>
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