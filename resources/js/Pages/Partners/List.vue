<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateColor from './Create.vue';
import EditColor from './Edit.vue';
import { ref, onMounted, watch, computed } from 'vue';
const colors = ref([]);
// Carrega os dados dos Veículos
const loadColors = async () => {
    try {
        const response = await axios.get(route("listColors"));
        colors.value = response.data;
    } catch (error) {
        console.log(error);
    }
};
// Carrega os dados iniciais 
onMounted(loadColors);
// Observa alterações na lista de parceiros 
watch(colors, () => {
    console.log('Lista de parceiros atualizada!');
});
// Cria uma nova lista contendo as informações de renderização necessárias 
const colorRows = computed(() => {
    return colors.value.map(color => {
        return { id: color.id, name: color.name, };
    });
}); 
</script> 
<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight"> Cores </h2>
        </template>
        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateColor v-on:need-reload="loadColors">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Nova Cor
                        </button>
                    </CreateColor>
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
                                <template v-for="color in colorRows" :key="color.id">
                                    <EditColor :color="color.id" v-on:need-reload="loadColors">
                                        <td class="px-6 py-4">{{ color.id }}</td>
                                        <td class="px-6 py-4">{{ color.name }}</td>
                                    </EditColor>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>