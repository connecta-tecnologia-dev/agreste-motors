<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Datepicker from 'vue3-datepicker';
import moment from 'moment';
import { pt } from 'date-fns/locale';

const props = defineProps({
    installments: {
        type: Number,
        default: null,
    },
    startPayment: {
        type: Text,
        default: null
    },
    value: {
        type: Text,
        default: '',
    }
});

const locale = pt;

const emit = defineEmits(['addInstallments']);

const form = reactive({
    data: [],
    total: '',
});

const installmentsCreate = ref(false);

const returnData = async () => {
    form.data.forEach(e => {
        e.date = moment(e.date).format("yyyy-MM-DD")
    });
    const payment = { value: form.total, installmentsData: form.data };
    emit('addInstallments', payment);
    closeModal();
};

const calcTotal = async () => {
    let total = 0;
    await form.data.forEach(e => {
        total += parseFloat(e.value);
    });
    form.total = total;
}

const start = async () => {
    installmentsCreate.value = true;

    const valorParcela = props.value / props.installments;
    const dataInicio = new Date(props.startPayment); // lembrando que o mês em JS começa em 0 (Janeiro = 0, Fevereiro = 1, etc)
    const numParcelas = props.installments;

    for (let i = 0; i < numParcelas; i++) {
        const mesAtual = (dataInicio.getMonth() + i) % 12;
        const anoAtual = dataInicio.getFullYear() + Math.floor((dataInicio.getMonth() + i) / 12);
        const diaInicio = dataInicio.getDate();
        const ultimoDiaMesAtual = new Date(anoAtual, mesAtual + 1, 0).getDate();
        const diaPagamento = Math.min(diaInicio, ultimoDiaMesAtual);
        const dataPagamento = new Date(anoAtual, mesAtual, diaPagamento);
        form.data.push({ date: dataPagamento, value: valorParcela });
    }
    calcTotal();
}

const addInstallment = () => {
    form.data.push({ date: '', value: 0 });
}

const closeModal = () => {
    installmentsCreate.value = false;
    form.data = [];
    form.total = '';
};
</script>

<template>
    <PrimaryButton class="ml-3" @click="start">
        <slot />
    </PrimaryButton>

    <DialogModal :show="installmentsCreate" @close="closeModal" :maxWidth="'6xl'">
        <template #title>
            Parcelas
        </template>

        <template #content>
            {{ content }}
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-6 mt-4">
                <template v-for="data in form.data">
                    <div class="flex gap-2 flex-col p-3 border border-black">
                        <span>Parcela {{ form.data.indexOf(data) + 1 }}</span>
                        <div class="flex gap-1 flex-col">
                            <div>Data:</div>
                            <datepicker v-model="data.date" inputFormat="dd/MM/yyyy" :locale="locale"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                        <div class="flex gap-1 flex-col">
                            <div>Valor:</div>
                            <money3 v-model="data.value" v-bind="config" @Change="calcTotal"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                    </div>
                </template>
            </div>
            <div>
                <span>Valor total: {{ parseFloat(form.total).toLocaleString('pt-BR', {
                    style: 'currency', currency: 'BRL'
                }) }}</span>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="addInstallment">
                Adicionar Parcelar
            </SecondaryButton>

            <SecondaryButton class="ml-3" @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="returnData">
                Gerar Parcelas
            </PrimaryButton>
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