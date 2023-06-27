<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Datepicker from 'vue3-datepicker';
import Multiselect from '@vueform/multiselect';
import { pt } from 'date-fns/locale';
import moment from 'moment';

const locale = pt;

const emit = defineEmits(['addPayment']);

const form = reactive({
    payment: '',
    value: '',
    bank: '',
    date: new Date()
});

const payments = [
    { value: 1 , label: 'Em Especie'},
    { value: 2 , label: 'Transferência'}
];

const startPayment = ref(false);

const loadBanks = async () => {
    try {
        const options = [];
        const response = await axios.post(route('listBanks'));
        response.data.forEach(bank => {
            options.push({
                value: bank.id,
                label: bank.name,
            })
        });
        return options;

    } catch (error) {
        console.log(error);
    }
}

const returnData = async () => {
    const payment = { formPayment: form.payment, value: form.value, bank: form.bank, date: moment(form.date).format("yyyy-MM-DD") };
    emit('addPayment', payment);
    closeModal();
};

const closeModal = () => {
    startPayment.value = false;
    form.payment = '',
    form.value = '',
    form.bank = '',
    form.date = new Date()
};
</script>

<template>
    <span @click="startPayment = true"
        class="absolute right-0 bottom-0 mb-2 mr-6 bg-purple-500 hover:bg-purple-600 cursor-pointer h-10 w-10 rounded-full flex justify-center items-center font-black text-white text-xl">+</span>


    <DialogModal :show="startPayment" @close="closeModal">
        <template #title>
            Pagar Parcela
        </template>

        <template #content>
            {{ content }}
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-6 mt-4">
                <div class="w-60 flex flex-col gap-1">
                    <span>Forma de Pagamento:</span>
                    <div class="flex w-full gap-3 items-center">
                        <Multiselect class="w-full" placeholder="Forma de Pagamento" v-model="form.payment"
                            @select="selectPayment" :searchable="true" :canClear="false" :options="payments" />
                    </div>
                </div>
                <div v-if="form.payment == 2" class="w-48 flex flex-col gap-1">
                    <span>Banco de Recebimento:</span>
                    <Multiselect class="w-full" placeholder="Banco" v-model="form.bank"
                        @select="selectBank" :searchable="true" :canClear="false"
                        noOptionsText="Não existe Bancos cadastrados!" :options="async (query) => {
                            return await loadBanks(query)
                        }" />
                </div>
                <div class="w-48 flex flex-col gap-1">
                    <span>Valor pago:</span>
                    <money3 v-model="form.value" v-bind="config"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                </div>
                <div class="flex flex-col gap-1">
                    <span>Data de Pagamento:</span>
                    <datepicker v-model="form.date" inputFormat="dd/MM/yyyy"
                        :locale="locale"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="returnData">
                Salvar
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