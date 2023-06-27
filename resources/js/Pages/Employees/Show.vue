<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import CreateEmployee from './Create.vue';
import EditEmployee from './Edit.vue';
import { ref, reactive } from 'vue';

const data = reactive({ employees: [] });

const loading = ref(true);
const optionsLoaded = ref(false);

// Carrega os dados dos Veículos
const loadEmployees = async () => {
    try {
        const response = await axios.get(route("listEmployees"));
        loading.value = true;
        data.employees = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
};

// Executa a função loadEmployees a cada 1 minuto (60.000 milissegundos)
setInterval(() => {
    loadEmployees();
}, 60000);

// Carrega os dados iniciais
loadEmployees();
</script>

<template>
    <AppLayout title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Funcionários
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="flex gap-3 pb-3 justify-end">
                    <CreateEmployee v-on:employee-has-created="loadEmployees">
                        <button type="button"
                            class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl">
                            Novo Funcionário
                        </button>
                    </CreateEmployee>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table
                            class='mx-auto w-full whitespace-nowrap rounded-lg bg-white divide-y divide-gray-300 overflow-hidden'>
                            <thead class="bg-gray-900">
                                <tr class="text-white text-left">
                                    <th class="font-semibold text-sm uppercase px-6 py-4">ID</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Nome</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Email</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">CPF</th>
                                    <th class="font-semibold text-sm uppercase px-6 py-4">Cargo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <EditEmployee v-for="employee in data.employees" :key="employee.id" :employee="employee.id"
                                    v-on:employee-has-updated="loadEmployees">
                                    <th class="px-6 py-4 text-left">{{ employee.id }}</th>
                                    <th class="px-6 py-4 text-left">{{ employee.name }}</th>
                                    <th class="px-6 py-4 text-left">{{ employee.email }}</th>
                                    <th class="px-6 py-4 text-left">{{ employee.cpf }}</th>
                                    <th class="px-6 py-4 text-left">{{ employee.employee_function.name }}</th>
                                </EditEmployee>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>