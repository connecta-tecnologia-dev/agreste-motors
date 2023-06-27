<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateModel from './Create.vue';
import EditModel from './Edit.vue';
import { ref, reactive } from 'vue';

const props = defineProps({
    data: Object,
});

const data = reactive({ models: [] });

// Carrega os dados dos Veículos
const loadModels = async () => {
    try {
        const response = await axios.get(route("listModels"));
        data.models = response.data;
    } catch (error) {
        console.log(error);
    }
};

// Carrega os dados iniciais
loadModels();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Modelos
            </h2>
        </template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex gap-3 pb-3 justify-end">
                <CreateModel v-on:need-reload="loadModels" :data="props.data['brands']">
                    <button type="button"
                        class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                        Novo Modelo
                    </button>
                </CreateModel>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table
                        class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                        <thead class="bg-gray-900">
                            <tr class="text-white text-left">
                                <th class="font-semibold text-sm uppercase px-6 py-4 w-40"> ID </th>
                                <th class="font-semibold text-sm uppercase px-6 py-4 w-full"> Nome </th>
                                <th class="font-semibold text-sm uppercase px-6 py-4 w-60"> Marca </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <EditModel v-for="model in data.models" :key="model.id" :model="model.id" :data="props.data['brands']"
                                v-on:need-reload="loadModels">
                                <td class="px-6 py-4">{{ model.id }}</td>
                                <td class="px-6 py-4">{{ model.name }}</td>
                                <td class="px-6 py-4">{{ props.data['brands'][model.brand_id].name }}</td>
                            </EditModel>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>