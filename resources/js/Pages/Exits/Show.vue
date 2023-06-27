<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import CustomerInfo from '@/Components/CustomerInfo.vue';
import VehicleInfo from '@/Components/VehicleInfo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import moment from 'moment';

const props = defineProps({
    data: Object,
    exit: {
        type: Number,
        default: 0
    }
});

const content = reactive({
    exit: Object,
    payments: []
})

const emit = defineEmits(['needReload']);

const exitShow = ref(false);

const confirmingFinisher = ref(false);

const loadExit = async () => {
    try {
        const response = await axios.get(route('showExit', props.exit));
        content.exit = response.data;
        response.data.transactions.forEach(transaction => {
            if (transaction.type == 'receipt') {
                switch (transaction.destination_type) {
                    case 'cash':
                        content.payments.push({ "type": transaction.type, "formPayment": 1, "value": transaction.amount, "id": transaction.id });
                        break;

                    case 'bank':
                        content.payments.push({ "type": transaction.type, "formPayment": 2, "value": transaction.amount, "bank": transaction.destination_id, "id": transaction.id });
                        break;
                }
            } else {
                switch (transaction.origin_type) {
                    case 'cash':
                        content.payments.push({ "type": transaction.type, "formPayment": 1, "value": transaction.amount, "id": transaction.id });
                        break;

                    case 'bank':
                        content.payments.push({ "type": transaction.type, "formPayment": 2, "value": transaction.amount, "bank": transaction.origin_id, "id": transaction.id });
                        break;
                }
            }
        })

        response.data.debts.forEach(debt => {
            switch (debt.payment_type) {
                case 'financing':
                    content.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 3, "value": debt.total, 'installments': debt.installments, 'installment_value': debt.data['installment_value'], 'first_payment': new Date(debt.data['first_payment']), 'financial': debt.data['financial'], "id": debt.id });
                    break;
                case 'credit_card':
                    content.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 4, "value": debt.total, 'installments': debt.installments, 'installment_value': debt.data['installment_value'], 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
                case 'promissory':
                    content.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 5, "value": debt.total, 'installments': debt.installments, 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
                case 'checks':
                    content.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 6, "value": debt.total, 'installments': debt.installments, 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
                case 'slips':
                    content.payments.push({ "bank": debt.data['bank'], "type": debt.type, "formPayment": 7, "value": debt.total, 'installments': debt.installments, 'first_payment': new Date(debt.data['first_payment']), "id": debt.id });
                    break;
            }
        })
    } catch (error) {
        console.log(error);
    }
}

const unfinishExit = async () => {
    await axios.patch(route('changeExit', props.exit));
    emit('needReload');
    closeModal();
}

const startExitShow = async () => {
    await loadExit();
    exitShow.value = true;
};


const closeModal = () => {
    exitShow.value = false;
    confirmingFinisher.value = false;
    content.payments = [];
};
</script>

<template>
    <tr @click="startExitShow" class="cursor-pointer hover:bg-green-400">
        <slot />
    </tr>

    <DialogModal :show="exitShow" @close="closeModal" :maxWidth="'6xl'">
        <template #title>
            Saída
        </template>

        <template #content>
            <div v-if="content.exit" class="flex flex-wrap gap-5 mt-4">
                <div class="w-full flex flex-wrap gap-6">
                    <div class="text-base"><span class="font-bold">Tipo:</span> {{
                        content.exit.vehicle.type.name }}</div>
                    <div class="text-base"><span class="font-bold">Fabricante:</span> {{
                        content.exit.vehicle.brand.name }}</div>
                    <div class="text-base"><span class="font-bold">Cor:</span> {{
                        content.exit.vehicle.color.name }}
                    </div>
                    <div class="text-base"><span class="font-bold">Modelo:</span>
                        {{ content.exit.vehicle.model.name }}
                    </div>
                    <div class="text-base"><span class="font-bold">Chassis: </span>
                        <VehicleInfo :vehicle="content.exit.vehicle.id">{{ content.exit.vehicle.chassis }}
                        </VehicleInfo>
                    </div>
                    <div class="text-base"><span class="font-bold">Placa: </span>
                        <VehicleInfo :vehicle="content.exit.vehicle.id">{{ content.exit.vehicle.plate }}
                        </VehicleInfo>
                    </div>
                    <div class="text-base"><span class="font-bold">Renavam: </span>
                        <VehicleInfo :vehicle="content.exit.vehicle.id">{{ content.exit.vehicle.renavam }}
                        </VehicleInfo>
                    </div>
                    <div class="text-base"><span class="font-bold">Combustível:</span> {{
                        content.exit.vehicle.fuel.name }}</div>
                    <div class="text-base"><span class="font-bold">Novo Proprietário: </span>
                        <CustomerInfo :customer="content.exit.owner.id">{{ content.exit.owner.name }}
                        </CustomerInfo>
                    </div>
                    <div class="text-base" v-if="content.exit.clerks.length > 0">
                        <span class="font-bold">{{ content.exit.clerks.length > 1 ? 'Despachantes' : 'Despachante' }}:
                        </span>
                        <template v-for="(clerk, index) in content.exit.clerks">
                            <span>{{ clerk.name }}</span>
                            <span v-if="index < content.exit.clerks.length - 1">, </span>
                        </template>
                    </div>
                    <div class="text-base"><span class="font-bold">Quilometragem: </span>{{
                        content.exit.mileage.toLocaleString('pt-BR') }}</div>
                    <div class="text-base"><span class="font-bold">Data de Entrada: </span>{{ moment(new
                        Date(content.exit.date)).format("DD/MM/yyyy") }}</div>
                    <div class="text-base"><span class="font-bold">Valor: </span>{{
                        parseFloat(content.exit.sale_value).toLocaleString('pt-BR', {
                            style: 'currency', currency: 'BRL'
                        })
                    }}</div>
                    <div class="text-base"><span class="font-bold">Serviços: </span>{{
                        parseFloat(content.exit.services).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}
                    </div>
                    <div class="text-base"><span class="font-bold">Adição: </span>{{
                        parseFloat(content.exit.addition).toLocaleString('pt-BR', {
                            style: 'currency', currency:
                                'BRL'
                        }) }}</div>
                </div>
                <div class="text-base" v-if="content.exit.note">
                    <div class="font-bold w-full">Observações: </div>
                    <p>{{ content.exit.note }}</p>
                </div>
                <div class="w-full relative">
                    <div class="text-base font-bold mb-3">Pagamentos: </div>
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
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="payment in content.payments">
                                    <td class="px-6 py-4 border">{{ payment.type == 'payment' ? 'Pagamento' :
                                        'Recebimento' }}</td>
                                    <td class="px-6 py-4 border">{{ props.data.payments[payment.formPayment].name }}</td>
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
                                    <td v-if="payment.first_payment" class="px-6 py-4">{{
                                        payment.first_payment.toLocaleDateString() }}</td>
                                    <td v-else class="px-6 py-4 border"></td>
                                    <td class="px-6 py-4 border">{{ payment.bank ? props.data.banks[payment.bank].name : ""
                                    }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="w-full flex justify-end pr-6">
                    <i class="fa-solid fa-paperclip"></i>
                    
                </div>
                <!-- Delete Team Confirmation Modal -->
                <ConfirmationModal :show="confirmingFinisher" @close="confirmingFinisher = false">
                    <template #title>
                        Deseja Reabilitar a Saída para Edição?
                    </template>

                    <template #content>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="confirmingFinisher = false">
                            Não
                        </SecondaryButton>

                        <DangerButton @click="unfinishExit" class="ml-3">
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
                Não Finalizado?
            </DangerButton>
        </template>
    </DialogModal>
</template>