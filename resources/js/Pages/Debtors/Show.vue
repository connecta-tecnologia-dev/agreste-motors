<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CustomerInfo from '@/Components/CustomerInfo.vue';
import VehicleInfo from '@/Components/VehicleInfo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import moment from 'moment';

import Installment from './Installment.vue';

const props = defineProps({
    data: Object,
    debt: {
        type: Number,
        default: 0
    },
    customer: {
        type: Number,
        default: 0
    }
});

const content = reactive({
    debt: Object,
    customer: Object,
    payments: [],
    type: {
        'financing': 'Financiamento',
        'credit_card': 'Cartão de Credito',
        'promissory': 'Promissórias', 'checks':
            'Cheques', 'slips': 'Boletos'
    }
})

const emit = defineEmits(['needReload']);

const confirmingFinisher = ref(false);

const debtShow = ref(false);

const loadDebt = async () => {
    try {
        const response = await axios.post(route('showDebt', props.debt));
        content.debt = response.data;
    } catch (error) {
        console.log(error);
    }
}

const loadCustomer = async () => {
    try {
        const response = await axios.get(route('showCustomer', props.customer));
        content.customer = response.data;
    } catch (error) {
        console.log(error);
    }
}

const normalPayment = async () => {
    try {
        await axios.post(route('normalDebt', props.debt),
            { owner: props.customer }
        );
        emit('needReload');
        closeModal();
    } catch (error) {
        console.log(error);
    }
}

const countTransactions = (transactions) => {
    let sum = 0;
    transactions.forEach(transaction => {
        sum += parseFloat(transaction.amount);
    });
    return sum;
}

const startEntryShow = async () => {
    await loadDebt();
    await loadCustomer();
    debtShow.value = true;
};

const closeModal = () => {
    debtShow.value = false;
    content.payments = [];
    confirmingFinisher.value = false;
};
</script>

<template>
    <tr @click="startEntryShow" class="cursor-pointer hover:bg-green-400">
        <slot />
    </tr>

    <DialogModal :show="debtShow" @close="closeModal">
        <template #title>
            Receber Pagamento
        </template>

        <template #content>
            <div v-if="content.debt.payment_type" class="flex flex-wrap gap-5 mt-4">
                <div class="w-full flex flex-wrap gap-6">
                    <div class="flex gap-1 text-base">
                        <div class="font-bold">Forma de Pagamento:</div>
                        <div>{{ content.type[content.debt.payment_type] }}</div>
                    </div>
                    <div class="flex gap-1 text-base">
                        <div class="font-bold">Devedor:</div>
                        <div>
                            <CustomerInfo :data="content.customer">{{ content.customer.name }}
                            </CustomerInfo>
                        </div>
                    </div>
                    <div class="flex gap-1 text-base">
                        <div class="font-bold">Banco de Recebimento:</div>
                        <div>{{ props.data['banks'][content.debt.data['bank']].name }}</div>
                    </div>
                    <div class="w-48 flex gap-1 text-base">
                        <div class="font-bold">Valor Total:</div>
                        <div>{{ parseFloat(content.debt.total).toLocaleString('pt-BR', {
                            style:
                                'currency', currency: 'BRL'
                        }) }}</div>
                    </div>
                    <div class="flex gap-2 flex-wrap" v-if="content.debt.installments.length > 0">
                        <template v-for="installment in content.debt.installments">
                            <Installment :installment="installment.id" :number="content.debt.installments.indexOf(installment) + 1" class="flex gap-2 flex-col p-3 border border-black">
                                <span>Parcela {{ content.debt.installments.indexOf(installment) + 1 }}</span>
                                <div class="flex gap-1">
                                    <div>Data de Vencimento:</div>
                                    <div>{{ moment(new Date(installment.date)).format("DD/MM/yyyy") }}</div>
                                </div>
                                <div class="flex gap-1">
                                    <div>Valor:</div>
                                    <div>{{ parseFloat(installment.value).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</div>
                                </div>
                                <div class="flex gap-1">
                                    <div>Valor Pago:</div>
                                    <div>{{ parseFloat(countTransactions(installment.transactions)).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</div>
                                </div>
                            </Installment>
                        </template>
                    </div>
                </div>
                <!-- Delete Team Confirmation Modal -->
                <ConfirmationModal :show="confirmingFinisher" @close="confirmingFinisher = false">
                    <template #title>
                        Deseja Confirmar o Pagamento do Debito?
                    </template>

                    <template #content>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="confirmingFinisher = false">
                            Não
                        </SecondaryButton>

                        <DangerButton @click="normalPayment" class="ml-3">
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