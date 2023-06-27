<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div class="p-6 lg:p-8 bg-white border-b border-gray-200 flex flex-wrap gap-5">
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/vehicles">Veículos</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/entries">Entradas</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/exits">Saídas</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/bills_to_pay">Contas a Pagar</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/reports">Relatórios</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/debtors">Devedores</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/settings">Configurações</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/banks">Bancos</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/employees">Funcionários</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/customers">Clientes</a>
        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl" href="/admin/transactions">Transações</a>
    </div>
</template>