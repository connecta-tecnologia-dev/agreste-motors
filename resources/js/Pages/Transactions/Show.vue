<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from 'moment';

import { ref, reactive } from 'vue';

const data = reactive({ transactions: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadTransactions = async () => {
    try {
        const response = await axios.get(route("listTransactions"));
        loading.value = true;
        data.transactions = response.data;
        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Executa a função loadExits a cada 1 minuto (60.000 milissegundos)
setInterval(() => {
    loadTransactions();
}, 60000);

// Carrega os dados iniciais
loadTransactions();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transações
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table v-if="data.transactions.length > 0"
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4">ID</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Tipo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="transactions in data.transactions">
                                    <td class="px-6 py-4">{{ transactions.id }}</td>
                                    <td class="px-6 py-4">{{ transactions.type == 'receipt' ? 'Recebimento' : 'Pagamento' }}</td>
                                    <td class="px-6 py-4">{{ transactions.amount }}</td>
                                    <td class="px-6 py-4">{{ moment(new Date(transactions.date)).format("DD/MM/yyyy") }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-3 font-black">Não existem transações cadastradas!</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>