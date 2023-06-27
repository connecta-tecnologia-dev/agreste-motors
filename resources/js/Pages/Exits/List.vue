<script setup>
import { ref, reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Create from './Create.vue';
import CreateMultiple from './CreateMultiple.vue';
import CreateTrade from './CreateTrade.vue';
import Edit from './Edit.vue';
import Show from './Show.vue';
import moment from 'moment';

const props = defineProps({
    data: Object,
});

const loading = ref(true);

const content = reactive({
    exits: Array,
    total: Number,
    perPage: 30,
    order: 'desc',
    page: 1,
    lastPage: Number,
    pages: Array
})

const calcPages = () => {
    const pages = [];
    if (content.page - 4 > 0) {
        for (let index = content.page - 4; index < content.page; index++) {
            pages.push(index);
        }
    }
    pages.push(content.page);
    if (content.lastPage - content.page > 0) {
        for (let index = content.page + 1; index <= content.lastPage; index++) {
            pages.push(index);
        }
    }
    content.pages = pages;
}

// Carrega os dados dos Veículos
const loadExits = async (filters) => {
    loading.value = true;
    try {
        const response = await axios.get(route("listExits"), filters);
        content.exits = response.data.data;
        content.total = response.data.total;
        content.lastPage = response.data.last_page;
        loading.value = false;
        calcPages();
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Carrega os dados iniciais
loadExits({
    perPage: content.perPage,
    order: content.order,
    page: content.page,
});

const Paginate = (page) => {
    content.page = page;
    loadExits({
        perPage: content.perPage,
        order: content.order,
        page: content.page,
    });
}
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Saídas
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <Create v-on:need-reload="Paginate(1)">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Nova Saída
                        </button>
                    </Create>
                    <CreateMultiple v-on:need-reload="Paginate(1)">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Multiplas Saídas
                        </button>
                    </CreateMultiple>
                    <CreateTrade v-on:need-reload="Paginate(1)">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Troca de Veículos
                        </button>
                    </CreateTrade>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table v-if="content.exits.length > 0"
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4">ID</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Tipo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Marca</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Modelo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Placa</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Proprietário</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor de Venda</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Data de Saída</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template v-for="exit in content.exits">
                                    <Show v-if="exit.finished" v-on:need-reload="Paginate(1)" :exit="exit.id"
                                        :data="props.data">
                                        <td class="px-6 py-4">{{ exit.id }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.type.name }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.brand.name }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.model.name }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.plate }}</td>
                                        <td class="px-6 py-4">{{ exit.owner.name }}</td>
                                        <td class="px-6 py-4">{{ parseFloat(exit.sale_value).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</td>
                                        <td class="px-6 py-4">{{ moment(new Date(exit.date)).format("DD/MM/yyyy") }}</td>
                                    </Show>
                                    <Edit v-else :exit="exit.id" v-on:need-reload="Paginate(1)">
                                        <td class="px-6 py-4">{{ exit.id }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.type.name }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.brand.name }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.model.name }}</td>
                                        <td class="px-6 py-4">{{ exit.vehicle.plate }}</td>
                                        <td class="px-6 py-4">{{ exit.owner.name }}</td>
                                        <td class="px-6 py-4">{{ parseFloat(exit.sale_value).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</td>
                                        <td class="px-6 py-4">{{ moment(new Date(exit.date)).format("DD/MM/yyyy") }}</td>
                                    </Edit>
                                </template>
                            </tbody>
                        </table>
                        <div v-else class="p-3 font-black">Não existem saídas de veículos no estoque!</div>
                    </div>
                </div>
                <ul v-if="content.lastPage > 1" class="w-full flex gap-5 justify-center mt-6">
                    <template v-if="content.page != 1">
                        <li class="bg-gray-900 hover:bg-red-600 text-white px-3 py-1 cursor-pointer rounded font-semibold text-sm"
                            @click="Paginate(1)">
                            Primeira</li>
                    </template>
                    <template v-for="page in content.pages">
                        <li v-if="page == content.page"
                            class="bg-red-600 text-white px-3 py-1 rounded font-semibold text-sm">
                            {{ page }}</li>
                        <li v-else
                            class="bg-gray-900 hover:bg-red-600 text-white px-3 py-1 cursor-pointer rounded font-semibold text-sm"
                            @click="Paginate(page)">
                            {{ page }}</li>
                    </template>
                    <template v-if="content.page != content.lastPage">
                        <li class="bg-gray-900 hover:bg-red-600 text-white px-3 py-1 cursor-pointer rounded font-semibold text-sm"
                            @click="Paginate(content.lastPage)">
                            Ultima</li>
                    </template>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>