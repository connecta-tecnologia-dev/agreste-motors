<script setup>
import { ref, reactive } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Create from './Create.vue';
import Edit from './Edit.vue';
import Show from './Show.vue';
import moment from 'moment';

import Multiselect from '@vueform/multiselect';
import TextInput from '@/Components/TextInput.vue';
import { vMaska } from "maska";

const props = defineProps({
    data: Object,
});

const form = reactive({
    plate: '',
    brand: '',
    owner: '',
    model: '',
    version: '',
    color: ''
});

const loading = ref(true);

const content = reactive({
    entries: Array,
    total: Number,
    perPage: 30,
    order: 'desc',
    page: 1,
    state: 0,
    lastPage: Number,
    pages: Array
})

const toUpper = {
    preProcess: val => val.toUpperCase()
}

const toLower = {
    preProcess: val => val.toLowerCase()
}

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
const loadEntrys = async (filters) => {
    loading.value = true;
    try {
        const response = await axios.get(route("listEntries"), { params: filters });
        content.entries = response.data.data;
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
loadEntrys({
    perPage: content.perPage,
    order: content.order,
    page: content.page,
    state: content.state,
});

const searchEntries = async () => {
    const queryParams = {
        perPage: 1,
        order: content.order,
        page: content.page,
        state: content.state,
        plate: form.plate,
        brand: form.brand,
        model: form.model,
        color: form.color,
    };
    await loadEntrys(queryParams);
};

const Paginate = (page) => {
    content.page = page;
    loadEntrys({
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
                Entradas
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex flex-wrap justify-center gap-3 mb-5">
                    <div class="w-32">
                        <span class="ml-2">Placa:</span>
                        <TextInput v-model="form.plate" @input="searchEntries" type="text" class="mt-1 block w-full"
                            placeholder="Placa" v-maska:[toUpper] data-maska="['@@@-****']" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Marca:</span>
                        <TextInput v-model="form.brand" @input="searchEntries" type="text" class="mt-1 block w-full"
                            placeholder="Marca" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Modelo:</span>
                        <TextInput v-model="form.model" @input="searchEntries" type="text" class="mt-1 block w-full"
                            placeholder="Modelo" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Versão:</span>
                        <TextInput v-model="form.version" @input="searchEntries" type="text" class="mt-1 block w-full"
                            placeholder="Versão" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Cor:</span>
                        <TextInput v-model="form.color" @input="searchEntries" type="text" class="mt-1 block w-full"
                            placeholder="Cor" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Finalizado:</span>
                        <Multiselect placeholder="Finalizado" v-model="content.state" @select="searchEntries"
                            :searchable="false" :show-labels="false"
                            :options="[{ value: 0, label: 'Não' }, { value: 1, label: 'Sim' }]" />
                    </div>
                    <div class="w-44">
                        <span class="ml-2">Ordem:</span>
                        <Multiselect placeholder="Finalizado" v-model="content.order" @select="searchEntries"
                            :searchable="false" :show-labels="false"
                            :options="[{ value: 'desc', label: 'Decrescente' }, { value: 'asc', label: 'Crescente' }]" />
                    </div>
                </div>
                <div class="flex gap-3 pb-3 justify-end">
                    <Create v-on:need-reload="Paginate(1)" :data="props.data">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Nova Entrada
                        </button>
                    </Create>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table v-if="content.entries.length > 0"
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4">ID</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Tipo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Marca</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Modelo</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Placa</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Antigo Proprietário</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Valor</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Data de Entrada</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template v-for="entry in content.entries">
                                    <Show v-if="entry.finished" v-on:need-reload="Paginate(1)" :entry="entry.id"
                                        :data="props.data">
                                        <td class="px-6 py-4">{{ entry.id }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.type.name }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.brand.name }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.model.name }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.plate }}</td>
                                        <td class="px-6 py-4">{{ entry.owner.name }}</td>
                                        <td class="px-6 py-4">{{ parseFloat(entry.value).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</td>
                                        <td class="px-6 py-4">{{ moment(new Date(entry.date)).format("DD/MM/yyyy") }}</td>
                                    </Show>
                                    <Edit v-else v-on:need-reload="Paginate(1)" :entry="entry.id">
                                        <td class="px-6 py-4">{{ entry.id }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.type.name }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.brand.name }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.model.name }}</td>
                                        <td class="px-6 py-4">{{ entry.vehicle.plate }}</td>
                                        <td class="px-6 py-4">{{ entry.owner.name }}</td>
                                        <td class="px-6 py-4">{{ parseFloat(entry.value).toLocaleString('pt-BR', {
                                            style:
                                                'currency', currency: 'BRL'
                                        }) }}</td>
                                        <td class="px-6 py-4">{{ moment(new Date(entry.date)).format("DD/MM/yyyy") }}</td>
                                    </Edit>
                                </template>
                            </tbody>
                        </table>
                        <div v-else class="p-3 font-black">Não existem veículos no estoque!</div>
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
<style src="@vueform/multiselect/themes/default.css"></style>