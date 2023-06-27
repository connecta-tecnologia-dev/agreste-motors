<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, reactive } from 'vue';

const data = reactive({ transactions: [], totalProfit: 0 });

// Carrega os dados dos Veículos
const loadTransactions = async () => {
    try {
        const response = await axios.get(route("listProfits"), {
            params: {
                startDate: '',
                endDate: ''
            }
        });
        data.transactions = response.data;

        response.data.forEach(transaction => {
            if(transaction.type == 'payment'){
                data.totalProfit -= parseFloat(transaction.amount);
            }else if(transaction.type == 'receipt'){
                data.totalProfit += parseFloat(transaction.amount);
            }
        });

    } catch (error) {
        console.log(error);
    }
};

// Carrega os dados iniciais
loadTransactions();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Relatório de Lucros
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> ID </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Tipo </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4 w-full"> Valor </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="transaction in data.transactions" :key="transaction.id">
                                    <td class="px-6 py-4">{{ transaction.id }}</td>
                                    <td class="px-6 py-4">{{ transaction.type }}</td>
                                    <td class="px-6 py-4">{{ transaction.amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h3>Lucro Total: {{ data.totalProfit }}</h3>
                </div>
            </div>
        </div>
    </AppLayout>
</template>