<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateBrand from './Create.vue';
import EditBrand from './Edit.vue';
import { ref, reactive } from 'vue';

const data = reactive({ brands: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

const loadBrands = async () => {
    try {
        const response = await axios.get(route("listBrands"));
        loading.value = true;
        data.brands = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

loadBrands();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Marcas
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateBrand v-on:need-reload="loadBrands">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Nova Marca
                        </button>
                    </CreateBrand>
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
                                <EditBrand v-for="brand in data.brands" :key="brand.id" :brand="brand.id"
                                    v-on:need-reload="loadBrands">
                                    <td class="px-6 py-4">{{ brand.id }}</td>
                                    <td class="px-6 py-4">{{ brand.name }}</td>
                                </EditBrand>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>