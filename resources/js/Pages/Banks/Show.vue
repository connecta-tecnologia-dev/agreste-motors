<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateBank from './Create.vue';
import EditBank from './Edit.vue';
import { ref, reactive } from 'vue';

const data = reactive({ banks: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadBanks = async () => {
    try {
        const response = await axios.post(route("listBanks"));
        loading.value = true;
        data.banks = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Executa a função loadBanks a cada 1 minuto (60.000 milissegundos)
setInterval(() => {
    loadBanks();
}, 60000);

// Carrega os dados iniciais
loadBanks();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bancos
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateBank v-on:bank-has-created="loadBanks">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Novo Banco
                        </button>
                    </CreateBank>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> ID </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Nome </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Agência </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Conta </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Saldo </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <EditBank v-for="bank in data.banks" :key="bank.id" :bank="bank.id" v-on:bank-has-updated="loadBanks">
                                    <td class="px-6 py-4">{{ bank.id }}</td>
                                    <td class="px-6 py-4">{{ bank.name }}</td>
                                    <td class="px-6 py-4">{{ bank.agency }}</td>
                                    <td class="px-6 py-4">{{ bank.account }}</td>
                                    <td class="px-6 py-4">{{ parseFloat(bank.balance).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</td>
                                </EditBank>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>