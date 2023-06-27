<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';

</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configurações
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white border-b border-gray-200 flex flex-wrap gap-5">
                        <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl"
                            href="/admin/types">Tipos</a>
                            <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl"
                            href="/admin/brands">Marcas</a>
                            <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl"
                            href="/admin/models">Modelos</a>
                            <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl"
                            href="/admin/transmissions">Transmissão</a>
                            <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl"
                            href="/admin/fuels">Combustíveis</a>
                            <a class="bg-gray-900 hover:bg-red-600 text-white font-black flex justify-center p-3 rounded-lg shadow-xl"
                            href="/admin/colors">Cores</a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
