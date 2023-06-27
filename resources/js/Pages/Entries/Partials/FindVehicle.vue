<script setup>
import { ref, reactive, watchEffect } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect';

const props = defineProps({
    title: {
        type: String,
        default: 'Veículos em estoque',
    },
    content: {
        type: String,
        default: '',
    },
    button: {
        type: String,
        default: 'Selecionar',
    },
});

const emit = defineEmits(['vehicleSet']);

const showSearch = ref(false);

const data = reactive({
    vehicles: [],
});

const form = reactive({
    plate: '',
    model: '',
    color: '',
    brand: '',
    vehicle: '',
});

const selectVehicle = (h) => {
    if(form.vehicle != h.id){
        form.vehicle = h.id;
    }else{
        form.vehicle = '';
    }
};

const chooseVehicle = () => {
    emit('vehicleSet', form.vehicle);
    closeModal();
}

const loadVehicles = async (queryParams = {}) => {
    try {
        form.processing = true;
        const response = await axios.get(route('listVehicles'), { params: queryParams });
        data.vehicles = response.data;
    } catch (error) {
        console.log(error);
    }
    form.processing = false;
}

const searchVehicles = async () => {
    const queryParams = {
        plate: form.plate,
        brand: form.brand,
        model: form.model,
        color: form.color,
    };
    await loadVehicles(queryParams);
};

const startFind = async () => {
    await loadVehicles();
    showSearch.value = true;
}

const closeModal = () => {
    showSearch.value = false;
    form.error = '';
    form.plate = '';
    form.model = '';
    form.colo = '';
    form.brand = '';
    form.vehicle = '';
};
</script>

<template>
    <div>
        <div @click="startFind">
            <font-awesome-icon class="bg-green-500 p-3 rounded-full cursor-pointer border-2 border-green-500 text-white" :icon="['fas', 'magnifying-glass']" size="2sx" />
        </div>

        <DialogModal :show="showSearch" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}
                <div class="flex flex-wrap gap-3 mb-5">
                    <div class="w-32">
                        <span class="ml-2">Placa:</span>
                        <TextInput v-model="form.plate" @input="searchVehicles" type="text" class="mt-1 block w-full"
                            placeholder="Placa" v-mask="['AAA-####', 'AAA-#A##']" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Marca:</span>
                        <TextInput v-model="form.brand" @input="searchVehicles" type="text" class="mt-1 block w-full"
                            placeholder="Marca" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Modelo:</span>
                        <TextInput v-model="form.model" @input="searchVehicles" type="text" class="mt-1 block w-full"
                            placeholder="Modelo" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Versão:</span>
                        <TextInput v-model="form.version" @input="searchVehicles" type="text" class="mt-1 block w-full"
                            placeholder="Versão" />
                    </div>
                    <div class="w-32">
                        <span class="ml-2">Cor:</span>
                        <TextInput v-model="form.color" @input="searchVehicles" type="text" class="mt-1 block w-full"
                            placeholder="Cor" />
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table v-if="data.vehicles.length > 0"
                        class='mx-auto w-full whitespace-nowrap bg-white divide-y divide-gray-300 overflow-hidden text-xs'>
                        <thead class="bg-gray-900">
                            <tr class="text-white text-left">
                                <th class="font-semibold uppercase px-6 py-4 border border-black">ID</th>
                                <th class="font-semibold uppercase px-6 py-4 border border-black">Marca</th>
                                <th class="font-semibold uppercase px-6 py-4 border border-black">Modelo</th>
                                <th class="font-semibold uppercase px-6 py-4 border border-black">Versão</th>
                                <th class="font-semibold uppercase px-6 py-4 border border-black">Placa</th>
                                <th class="font-semibold uppercase px-6 py-4 border border-black">Cor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="dataVehicle in data.vehicles" v-on:exit-has-updated="loadExits"
                                @click="selectVehicle(dataVehicle)">
                                <td :class="{ 'bg-green-400': form.vehicle == dataVehicle.id }"
                                    class="px-6 py-4 cursor-pointer border border-black">{{ dataVehicle.id }}</td>
                                <td :class="{ 'bg-green-400': form.vehicle == dataVehicle.id }"
                                    class="px-6 py-4 cursor-pointer border border-black">{{
                                        dataVehicle.brand.name }}</td>
                                <td :class="{ 'bg-green-400': form.vehicle == dataVehicle.id }"
                                    class="px-6 py-4 cursor-pointer border border-black">{{
                                        dataVehicle.model.name }}</td>
                                <td :class="{ 'bg-green-400': form.vehicle == dataVehicle.id }"
                                    class="px-6 py-4 cursor-pointer border border-black">{{
                                        dataVehicle.version }}</td>
                                <td :class="{ 'bg-green-400': form.vehicle == dataVehicle.id }"
                                    class="px-6 py-4 cursor-pointer border border-black">{{ dataVehicle.plate
                                    }}</td>
                                <td :class="{ 'bg-green-400': form.vehicle == dataVehicle.id }"
                                    class="px-6 py-4 cursor-pointer border border-black">{{
                                        dataVehicle.color.name }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="font-black">Não existem veículos no estoque!</div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="chooseVehicle">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>
<script>
import { mask } from 'vue-the-mask'
export default {
    directives: { mask },
}
</script>