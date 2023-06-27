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

import Installments from './Installments.vue';

defineProps({
    title: {
        type: String,
        default: 'Pagamento do veículo',
    },
    content: {
        type: String,
        default: '',
    },
    button: {
        type: String,
        default: 'Adicionar Pagamento',
    },
});

const locale = pt;

const emit = defineEmits(['addPayment']);

const form = reactive({
    payment: '',
    paymentType: 'payment',
    dateExit: new Date(),
    requirements: {},
});

const payments = reactive({
    data: [],
    options: [],
    selected: null,
});

const banks = reactive({
    data: [],
    selected: null,
});

const inputStatus = reactive({
    owner: true,
});

const entryCreate = ref(false);

const loadPayments = async () => {
    try {
        const response = await axios.post(route('listPayments'));
        payments.data = response.data;

    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const startentryCreate = async () => {
    await loadPayments();
    filterPayments();
    entryCreate.value = true;
};

const filterPayments = () => {
    payments.selected = null;
    form.payment = '';
    const options = [];
    payments.data.forEach(payment => {
        if (payment.type.indexOf(form.paymentType) != -1) {
            options.push({
                value: payment.id,
                label: payment.name,
            })
        }
    });
    payments.options = options;
}

const paymentTypes = [
    {
        value: 'payment',
        label: 'Pagamento',
    },
    {
        value: 'receipt',
        label: 'Recebimento',
    }
];

const loadBanks = async () => {
    try {
        const options = [];
        const response = await axios.post(route('listBanks'));
        banks.data = response.data;
        response.data.forEach(banks => {
            options.push({
                value: banks.id,
                label: banks.name,
            })
        });
        return options;

    } catch (error) {
        console.log(error);
    }
}

const selectPayment = async () => {
    payments.selected = payments.data.find(payment => payment.id === form.payment);
    payments.selected.requirements.forEach(requirement => {
        if (requirement.options.type == 'date') {
            form.requirements[requirement.options.slug] = new Date();
        } else {
            if (requirement.options.default) {
                form.requirements[requirement.options.slug] = requirement.options.default;
            } else {
                form.requirements[requirement.options.slug] = '';
            }
        }
    })
};

const addInstallments = async (data) => {
    form.requirements[['installments']] = data.installmentsData.length;
    const payment = data;
    payment.formPayment = form.payment;
    payment.type = form.paymentType;
    if (payments.selected.requirements != null) {
        await payments.selected.requirements.forEach(requirement => {
            payment[requirement.options.slug] = form.requirements[requirement.options.slug];
        })
    }
    console.log(payment)
    emit('addPayment', payment);
    closeModal();
}

const returnData = async () => {
    const payment = { type: form.paymentType , formPayment: form.payment, value: form.value };
    if (payments.selected.requirements != null) {
        await payments.selected.requirements.forEach(requirement => {
            payment[requirement.options.slug] = form.requirements[requirement.options.slug];
        })
    }
    emit('addPayment', payment);
    closeModal();
};

const closeModal = () => {
    inputStatus.owner = true;
    entryCreate.value = false;
    form.payment = '';
    payments.selected = null;
};
</script>

<template>
    <span @click="startentryCreate"
        class="absolute right-0 bottom-0 mb-2 mr-6 bg-purple-500 hover:bg-purple-600 cursor-pointer h-10 w-10 rounded-full flex justify-center items-center font-black text-white text-xl">+</span>


    <DialogModal :show="entryCreate" @close="closeModal">
        <template #title>
            {{ title }}
        </template>

        <template #content>
            {{ content }}
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-6 mt-4">
                <div v-if="form.paymentType" class="w-60 flex flex-col gap-1">
                    <span>Forma de Pagamento:</span>
                    <div class="flex w-full gap-3 items-center">
                        <Multiselect class="w-full" placeholder="Forma de Pagamento" v-model="form.payment"
                            @select="selectPayment" :searchable="true" :canClear="false" :options="payments.options" />
                    </div>
                </div>
                <div v-if="payments.selected" class="w-48 flex flex-col gap-1">
                    <span>Valor pago:</span>
                    <money3 v-model="form.value" v-bind="config"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full" />
                </div>
                <template v-if="payments.selected && payments.selected.requirements.length > 0">
                    <template v-for="requirement in payments.selected.requirements">
                        <div class="w-48 flex flex-col gap-1" v-if="requirement.options.type == 'bank'">
                            <span>{{ requirement.name }}:</span>
                            <Multiselect class="w-full" placeholder="Banco"
                                v-model="form.requirements[requirement.options.slug]" @select="selectBank"
                                :searchable="true" :canClear="false" noOptionsText="Não existe Bancos cadastrados!"
                                :options="async (query) => {
                                    return await loadBanks(query)
                                }" />
                        </div>
                        <div class="w-40 flex flex-col gap-1" v-if="requirement.options.type == 'text'">
                            <span>{{ requirement.name }}:</span>
                            <TextInput type="text" v-model="form.requirements[requirement.options.slug]"
                                :placeholder="requirement.name" />
                        </div>
                        <div v-if="requirement.options.type == 'money'" class="w-48 flex flex-col gap-1">
                            <span>{{ requirement.name }}:</span>
                            <money3 v-model="form.requirements[requirement.options.slug]" v-bind="config"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                        <div v-if="requirement.options.type == 'date'" class="flex flex-col gap-1">
                            <span>{{ requirement.name }}:</span>
                            <datepicker v-model="form.requirements[requirement.options.slug]" inputFormat="dd/MM/yyyy"
                                :locale="locale"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" />
                        </div>
                    </template>
                </template>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <Installments v-if="[5, 6, 7].indexOf(form.payment) >= 0" :value="form.value"
                :startPayment="form.requirements['first_payment']" :installments="form.requirements['installments']"
                v-on:add-installments="addInstallments">
                {{ button }}
            </Installments>

            <PrimaryButton v-else class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="returnData">
                {{ button }}
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