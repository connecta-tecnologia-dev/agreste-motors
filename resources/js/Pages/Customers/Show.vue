<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateCustomer from './Create.vue';
import EditCustomer from './Edit.vue';
import { ref, reactive } from 'vue';

const data = reactive({ customers: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadCustomers = async () => {
    try {
        const response = await axios.get(route("listCustomers"));
        loading.value = true;
        data.customers = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Executa a função loadCustomers a cada 1 minuto (60.000 milissegundos)
setInterval(() => {
    loadCustomers();
}, 60000);

// Carrega os dados iniciais
loadCustomers();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bancos
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateCustomer v-on:customer-has-created="loadCustomers">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Novo Cliente
                        </button>
                    </CreateCustomer>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> ID </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Nome </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> CPF/CNPF </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Email </th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4"> Endereço </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <EditCustomer v-for="customer in data.customers" :key="customer.id" :customer="customer.id" v-on:customer-has-updated="loadCustomers">
                                    <td class="px-6 py-4">{{ customer.id }}</td>
                                    <td class="px-6 py-4">{{ customer.name }}</td>
                                    <td class="px-6 py-4">{{ customer.cpf }}</td>
                                    <td class="px-6 py-4">{{ customer.email }}</td>
                                    <td class="px-6 py-4">{{ customer.address }}</td>
                                </EditCustomer>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>