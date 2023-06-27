<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateTransmission from './Create.vue';
import EditTransmission from './Edit.vue';
import { ref, reactive } from 'vue';

const data = reactive({ transmissions: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadTransmissions = async () => {
    try {
        const response = await axios.get(route("listTransmissions"));
        loading.value = true;
        data.transmissions = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

loadTransmissions();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Transmissão
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateTransmission v-on:need-reload="loadTransmissions">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Nova Transmissão
                        </button>
                    </CreateTransmission>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> ID </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4 w-full"> Nome </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <EditTransmission v-for="transmission in data.transmissions" :key="transmission.id" :transmission="transmission.id"
                                    v-on:need-reload="loadTransmissions">
                                    <td class="px-6 py-4">{{ transmission.id }}</td>
                                    <td class="px-6 py-4">{{ transmission.name }}</td>
                                </EditTransmission>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>