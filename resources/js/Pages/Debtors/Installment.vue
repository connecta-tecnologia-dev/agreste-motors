<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import moment from 'moment';

import Payment from './Payment.vue';

const props = defineProps({
    installment: Number,
    number: Number
});

const form = reactive({
    payments: []
});

const content = reactive({
    installment: Object,
    paidValue: 0
})

const emit = defineEmits(['needReload']);

const installmentShow = ref(false);

const confirmingFinisher = ref(false);

const addPayment = (data) => {
    form.payments.push(data);
    content.paidValue += parseFloat(data.value);
}

const removePayment = (data) => {
    content.paidValue = content.paidValue - parseFloat(form.payments[data].value);
    form.payments.splice(data, 1);
}

const loadDebt = async () => {
    const response = await axios.get(route('getInstallment', props.installment));
    content.installment = response.data;
    response.data.transactions.forEach(transaction => {
        content.paidValue += parseFloat(transaction.amount);
        if (transaction.type == 'receipt') {
            switch (transaction.destination_type) {
                case 'cash':
                    form.payments.push({ "formPayment": 1, "value": transaction.amount, "date": moment(transaction.date).format("yyyy-MM-DD"), "id": transaction.id });
                    break;

                case 'bank':
                    form.payments.push({ "formPayment": 2, "value": transaction.amount, "date": moment(transaction.date).format("yyyy-MM-DD"), "bank": transaction.destination_id, "id": transaction.id });
                    break;
            }
        }
    })
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

const payInstallment = async () => {
    try {
        await axios.post(route('installmentPayment', props.installment),
            { payments: form.payments }
        );
        closeModal();
    } catch (error) {
        console.log(error);
    }
}

const startEntryShow = async () => {
    await loadBanks();
    await loadPayments();
    await loadDebt();
    installmentShow.value = true;
};

const closeModal = () => {
    installmentShow.value = false;
    form.payments = [];
    confirmingFinisher.value = false;
};
</script>

<template>
    <div @click="startEntryShow" class="cursor-pointer hover:bg-green-400 flex gap-2 flex-col p-3 border border-black">
        <slot />
    </div>

    <DialogModal :show="installmentShow" @close="closeModal">
        <template #title>
            Pagar Parcela {{ props.number }}
        </template>

        <template #content>
            <div class="flex flex-wrap gap-5 mt-4">
                <div class="w-full flex flex-wrap gap-6">
                    <div class="flex gap-1 text-base">
                        <div class="font-bold">Valor da Parcela:</div>
                        <div>{{ parseFloat(content.installment.value).toLocaleString('pt-BR', {
                            style:
                                'currency', currency: 'BRL'
                        }) }}</div>
                    </div>
                    <div class="flex gap-1 text-base">
                        <div class="font-bold">Valor Pago:</div>
                        <div>{{ parseFloat(content.paidValue).toLocaleString('pt-BR', {
                            style:
                                'currency', currency: 'BRL'
                        }) }}</div>
                    </div>
                    <div class="flex gap-1 text-base">
                        <div class="font-bold">Data de Vencimento :</div>
                        <div>{{ moment(new Date(content.installment.date)).format("DD/MM/yyyy") }}</div>
                    </div>
                    <div class="w-full relative">
                        <div class="overflow-x-auto overflow-y-scroll max-h-60 h-60 border border-gray-300">
                            <table class='mx-auto w-full whitespace-nowrap bg-white divide-y divide-gray-300 mb-16'>
                                <thead class="bg-gray-900">
                                    <tr class="text-white text-left">
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Form.
                                            Pagamento</th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Valor</th>
                                        <th class="font-semibold text-xs uppercase px-6 py-4 border border-black">Data de
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
                                        <td class="px-6 py-4 border">{{ moment(new Date(payment.date)).format("DD/MM/yyyy")
                                        }}</td>
                                        <td class="px-6 py-4 border">{{ Banks[payment.bank] }}</td>
                                        <td class="px-6 py-4 border"><font-awesome-icon
                                                @click="removePayment(form.payments.indexOf(payment))"
                                                class="cursor-pointer" :icon="['far', 'trash-can']" size="lg" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Payment :data="props.data" v-on:add-payment="addPayment" />
                    </div>
                </div>
                <!-- Delete Team Confirmation Modal -->
                <ConfirmationModal :show="confirmingFinisher" @close="confirmingFinisher = false">
                    <template #title>
                        Deseja Confirmar o Pagamento do Parcela?
                    </template>

                    <template #content>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="confirmingFinisher = false">
                            Não
                        </SecondaryButton>

                        <DangerButton @click="payInstallment" class="ml-3">
                            Sim
                        </DangerButton>
                    </template>
                </ConfirmationModal>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Sair
            </SecondaryButton>

            <DangerButton class="ml-3" @click="confirmingFinisher = true">
                Confirmar Pagamento
            </DangerButton>
        </template>
    </DialogModal>
</template>
<script>
import { Money3Component } from 'v-money3';

export default {
    components: { money3: Money3Component },
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