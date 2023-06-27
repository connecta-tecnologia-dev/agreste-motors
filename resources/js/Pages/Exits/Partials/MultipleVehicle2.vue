<script setup>
import { watch, ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import Datepicker from 'vue3-datepicker';
import Multiselect from '@vueform/multiselect';

import Find from './FindVehicle.vue';

defineProps({
    title: {
        type: String,
        default: 'Seleção de veículo',
    },
    content: {
        type: String,
        default: '',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
});

const emit = defineEmits(['addVehicle']);

const form = reactive({
    dateExit: new Date(),
    vehicle: '',
    mileage: '',
    clerks: [],
    services: '',
    addition: '',
    value: '',
    note: '',
});

const vehicles = reactive({
    data: [],
    options: [],
    selected: null,
});

const inputStatus = reactive({
    owner: true,
});

const entryCreate = ref(false);

const startentryCreate = () => {
    entryCreate.value = true;
};

const loadForwardingAgent = async () => {
    try {
        const options = [];
        const response = await axios.get(route('showFunction', 1));
        response.data.employee.forEach(employee => {
            options.push({
                value: employee.id,
                label: employee.name
            })
        });
        return options;

    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const loadVehicles = async () => {
    try {
        vehicles.options = [];
        const response = await axios.post(route('showStock'));
        vehicles.data = response.data;
        response.data.forEach(data => {
            vehicles.options.push({
                value: data.entry.vehicle.id,
                label: data.entry.vehicle.plate,
                mileage: data.entry.vehicle.mileage,
                clerks: data.entry.clerks.map((clerk) => {
                    return clerk.dispatcher_id;
                })
            })
        });
        return vehicles.options;

    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const selectVehicle = () => {
    const selectedVehicle = vehicles.data.find(h => h.entry.vehicle.id === form.vehicle);
    vehicles.selected = selectedVehicle;
    if (selectedVehicle) {
        form.mileage = selectedVehicle.entry.vehicle.mileage,
            form.clerks = selectedVehicle.entry.clerks.map((clerk) => {
                return clerk.dispatcher_id;
            })
        inputStatus.owner = false;
    }
};

const setVehicle = (data) => {
    form.vehicle = data;
    selectVehicle();
}

const returnData = () => {
    emit('addVehicle', { data: vehicles.selected, mileage: form.mileage, clerks: form.clerks, value: form.value, addition: form.addition, services: form.services });
    closeModal();
};

const closeModal = () => {
    inputStatus.owner = true;
    entryCreate.value = false;
    form.vehicle = '';
    form.mileage = '';
    form.clerks = [];
    form.value = '';
    form.addition = '';
    form.services = '';
    form.note = '';
};
</script>

<template>
    <span @click="startentryCreate"
        class="absolute right-0 bottom-0 mb-2 mr-6 bg-green-500 hover:bg-green-600 cursor-pointer h-10 w-10 rounded-full flex justify-center items-center font-black text-white text-xl">+</span>


    <DialogModal :show="entryCreate" @close="closeModal">
        <template #title>
            {{ title }}
        </template>

        <template #content>
            {{ content }}
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-6 mt-4">
                <div class="w-48 flex flex-col">
                    <span>Placa:</span>
                    <div class="flex w-full gap-3 items-center">
                        <Multiselect class="w-full" placeholder="Veículo" v-model="form.vehicle"
                            noOptionsText="Sem veículos no estoque!" @select="selectVehicle" :searchable="true"
                            :canClear="false" :options="async (query) => {
                                return await loadVehicles(query)
                            }" />
                        <Find class="cursor-pointer" v-on:vehicle-set="setVehicle" />
                    </div>
                </div>
                <div class="w-40">
                    <span>Quilometragem:</span>
                    <TextInput ref="mileageInput" v-model="form.mileage" type="text" class="w-full"
                        placeholder="Quilometragem" />
                </div>
                <div class="w-full">
                    <span>Despachantes:</span>
                    <Multiselect placeholder="Despachantes" mode="tags" max="2" v-model="form.clerks"
                        :disabled="inputStatus.owner" :searchable="true" :options="async (query) => {
                            return await loadForwardingAgent(query)
                        }" />
                </div>
                <div class="w-48">
                    <span>Valor de Venda:</span>
                    <money3 v-model="form.value" v-bind="config"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </money3>
                </div>
                <div class="w-48">
                    <span>Serviços:</span>
                    <money3 v-model="form.services" v-bind="config"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </money3>
                </div>
                <div class="w-48">
                    <span>Adição:</span>
                    <money3 v-model="form.addition" v-bind="config"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                    </money3>
                </div>
                <div class="w-full">
                    <TextArea ref="noteInput" v-model="form.note" type="text" class="w-full"
                        placeholder="Observações"></TextArea>
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="returnData">
                {{ button }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
<script>
import { Money3Component } from 'v-money3';

export default {
    components: { money3: Money3Component, Datepicker },
    data() {
        return {
            amount: '',
            config: {
                masked: false,
                prefix: 'R$ ',
                suffix: '',
                thousands: '.',
                decimal: ',',
                precision: 2,
                disableNegative: false,
                disabled: false,
                min: null,
                max: null,
                allowBlank: false,
                minimumNumberOfCharacters: 0,
            }
        }
    }
}
</script>