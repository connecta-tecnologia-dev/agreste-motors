<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import CustomerInfo from '@/Components/CustomerInfo.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    vehicle: Number,
});

const data = reactive({
    vehicle: null
});

const vehicleShow = ref(false);

const loadVehicle= async () => {
    try {
        const response = await axios.get(route('showVehicle', props.vehicle));
        data.vehicle = response.data;
    } catch (error) {
        console.log(error);
    }
};

const startvehicleShow = async () => {
    loadVehicle();
    vehicleShow.value = true;
};

const closeModal = () => {
    vehicleShow.value = false;
};
</script>

<template>
    <span>
        <span @click="startvehicleShow" class="hover:text-blue-600 cursor-pointer">
            <slot />
        </span>

        <DialogModal :show="vehicleShow" @close="closeModal" :maxWidth="'5xl'">
            <template #title>
                Informações do Veículo:
            </template>

            <template #content>
                {{ content }}
                <div v-if="data.vehicle" class="flex flex-wrap gap-5 mt-4">
                    <div class="text-base"><span class="font-bold">Tipo:</span> {{ data.vehicle.type.name }}</div>
                    <div class="text-base"><span class="font-bold">Fabricante:</span> {{ data.vehicle.brand.name
                    }}</div>
                    <div class="text-base"><span class="font-bold">Cor:</span> {{ data.vehicle.color.name }}
                    </div>
                    <div class="text-base"><span class="font-bold">Modelo:</span> {{ data.vehicle.model.name }}
                    </div>
                    <div class="text-base"><span class="font-bold">Chassis:</span> {{ data.vehicle.chassis }}</div>
                    <div class="text-base"><span class="font-bold">Placa:</span> {{ data.vehicle.plate }}</div>
                    <div class="text-base"><span class="font-bold">Renavam:</span> {{ data.vehicle.renavam }}</div>
                    <div class="text-base"><span class="font-bold">Combustível:</span> {{ data.vehicle.fuel.name
                    }}</div>
                    <div class="text-base"><span class="font-bold">Proprietário Atual: </span>
                        <CustomerInfo :customer="data.vehicle.owner.id">{{ data.vehicle.owner.name }}</CustomerInfo>
                    </div>
                    <div class="text-base"><span class="font-bold">Quilometragem: </span>{{
                        data.vehicle.mileage.toLocaleString('pt-BR') }}</div>
                </div>
                <div>
                    <div class="text-base font-bold mt-3">Histórico: </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Sair
                </SecondaryButton>
            </template>
        </DialogModal>
    </span>
</template>