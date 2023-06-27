<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateVehicle from './Create.vue';
import { ref, reactive } from 'vue';

const data = reactive({ vehicles: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadVehicles = async () => {
    try {
        const response = await axios.get(route("listVehicles"));
        loading.value = true;
        data.vehicles = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Carrega os dados iniciais
loadVehicles();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Veículos
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateVehicle v-on:vehicle-has-created="loadVehicles">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Novo Veículo
                        </button>
                    </CreateVehicle>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table v-if="data.vehicles.length > 0"
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> ID </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Tipo </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Marca </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Modelo </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Placa </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Proprietário
                                    </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Ano Modelo </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Ano Fabricação
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="vehicle in data.vehicles" :key="vehicle.id">
                                    <td class="px-6 py-4 text-left">{{ vehicle.id }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.type.name }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.brand.name }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.model.name }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.plate }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.owner.name }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.model_year }}</td>
                                    <td class="px-6 py-4 text-left">{{ vehicle.manufacture_year }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else class="p-3 font-black">Não existem veículos cadastrados!</div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>