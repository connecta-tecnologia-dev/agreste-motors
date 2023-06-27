<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from 'moment';

import { ref, reactive } from 'vue';

const data = reactive({ vehicles: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadStock = async (queryParams = {}) => {
    try {
        data.processing = true;
        const response = await axios.post(route('showStock'), queryParams);
        data.vehicles = response.data;
    } catch (error) {
        console.log(error);
    }
    data.processing = false;
}

// Executa a função loadStock a cada 1 minuto (60.000 milissegundos)
setInterval(() => {
    loadStock();
}, 60000);

// Carrega os dados iniciais
loadStock();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Estoque
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table v-if="data.vehicles.length > 0"
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4">ID</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Tipo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Marca</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Modelo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Placa</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor de custo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor Minimo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Data de Entrada</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="dataVehicle in data.vehicles">
                                    <td class="px-6 py-4">{{ dataVehicle.entry.id }}</td>
                                    <td class="px-6 py-4">{{ dataVehicle.entry.vehicle.type.name }}</td>
                                    <td class="px-6 py-4">{{ dataVehicle.entry.vehicle.brand.name }}</td>
                                    <td class="px-6 py-4">{{ dataVehicle.entry.vehicle.model.name }}</td>
                                    <td class="px-6 py-4">{{ dataVehicle.entry.vehicle.plate }}</td>
                                    <td class="px-6 py-4">{{ parseFloat(dataVehicle.entry.amount_paid ).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</td>
                                    <td class="px-6 py-4">{{ parseFloat(dataVehicle.entry.minimum_value ).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</td>
                                    <td class="px-6 py-4">{{ parseFloat(dataVehicle.entry.value ).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }) }}</td>
                                    <td class="px-6 py-4">{{ moment(new Date(dataVehicle.entry.date)).format("DD/MM/yyyy") }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-3 font-black">Não existem veículos no estoque!</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>