<script setup>
import { ref, reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from 'moment';

import Show from './Show.vue';

const props = defineProps({
    data: Object,
});

const loading = ref(true);

const content = reactive({
    debts: [],
    total: Number,
    perPage: 30,
    order: 'desc',
    page: 1,
    lastPage: Number,
    pages: Array,
    type: {
        'financing': 'Financiamento',
        'credit_card': 'Cartão de Credito',
        'promissory': 'Promissórias', 'checks':
            'Cheques', 'slips': 'Boletos'
    }
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
const loadDebts = async (filters) => {
    loading.value = true;
    try {
        const response = await axios.get(route("listDebts"), filters);
        content.debts = response.data.data;
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
loadDebts({
    perPage: content.perPage,
    order: content.order,
    page: content.page,
});

const reload = () => {
    loadDebts({
        perPage: content.perPage,
        order: content.order,
        page: content.page,
    })
}

const Paginate = (page) => {
    content.page = page;
    loadDebts({
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
                Devedores
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">

                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table v-if="content.debts.length > 0"
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4">ID</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Form. Pagamento</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Tipo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Marca</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Modelo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Placa</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Cliente</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template v-for="debt in content.debts">
                                    <Show v-on:need-reload="reload" :debt="debt.id" :customer="debt.customer_id"
                                        :data="props.data">
                                        <td class="px-6 py-4">{{ debt.id }}</td>
                                        <td class="px-6 py-4">{{ content.type[debt.payment_type] }}</td>
                                        <td class="px-6 py-4">{{ props.data.types[debt.type_id].name }}</td>
                                        <td class="px-6 py-4">{{ props.data.brands[debt.brand_id].name }}</td>
                                        <td class="px-6 py-4">{{ props.data.models[debt.model_id].name }}</td>
                                        <td class="px-6 py-4">{{ debt.plate }}</td>
                                        <td class="px-6 py-4">{{ debt.name }}</td>
                                        <td class="px-6 py-4">{{ parseFloat(debt.total).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</td>
                                        <td class="px-6 py-4">{{ moment(new Date(debt.date)).format("DD/MM/yyyy") }}
                                        </td>
                                    </Show>
                                </template>
                            </tbody>
                        </table>
                        <div v-else class="p-3 font-black">Não existem Devedores!</div>
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
    </AppLayout></template>