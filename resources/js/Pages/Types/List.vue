<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateType from './Create.vue';
import EditType from './Edit.vue';
import { ref, reactive } from 'vue';

const data = reactive({ types: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadTypes = async () => {
    try {
        const response = await axios.get(route("listTypes"));
        loading.value = true;
        data.types = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Carrega os dados iniciais
loadTypes();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tipos de veículo
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateType v-on:need-reload="loadTypes">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Novo Tipo
                        </button>
                    </CreateType>
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
                                <EditType v-for="t in data.types" :key="t.id" :type="t.id" v-on:need-reload="loadTypes">
                                    <td class="px-6 py-4">{{ t.id }}</td>
                                    <td class="px-6 py-4">{{ t.name }}</td>
                                </EditType>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>