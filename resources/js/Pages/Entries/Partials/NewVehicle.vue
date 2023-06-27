<script setup>
import { watchEffect, ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import Multiselect from '@vueform/multiselect';
import { vMaska } from "maska";

import Type from './NewType.vue';
import Model from './NewModel.vue';
import Color from './NewColor.vue';
import Brand from './NewBrand.vue';
import Owner from './NewOwner.vue';
import Fuel from './NewFuel.vue';
import Transmission from './NewTransmission.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Novo veículo',
    },
    content: {
        type: String,
        default: 'Verifique se o veículo já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    vehicle: {
        type: String,
        default: '',
    }
});

const emit = defineEmits(['vehicleSet', 'ownerSet', 'mileageSet']);

const form = reactive({
    vehicle: '',
    type: '',
    plate: '',
    renavam: '',
    chassis: '',
    owner: '',
    brand: '',
    model: '',
    version: '',
    color: '',
    transmission: '',
    modelYear: '',
    factureYear: '',
    doors: '',
    fuel: '',
    mileage: '',
    power: '',
    note: '',
});

const options = reactive([{ value: 0, label: 'Novo' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadVehicles = async () => {
    try {
        const response = await axios.get(route('listVehicles'));
        loading.value = true;

        response.data.forEach(vehicle => {
            options.push({
                value: vehicle.id,
                label: vehicle.plate,
                owner: vehicle.owner.id,
                mileage: vehicle.mileage,
            });
        });

        loading.value = false;
        optionsLoaded.value = true;

        if (props.vehicle != '') {
            form.vehicle = props.vehicle;
        }
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadVehicles();

watchEffect( async () => {
  if (props.vehicle != '') {
    form.vehicle = props.vehicle;
    await loadVehicles();
    selectVehicle();
  }
});

const inputStatus = reactive({
    model: true,
});

const toUpper = {
    preProcess: val => val.toUpperCase()
}

const toLower = {
    preProcess: val => val.toLowerCase()
}

const setBrand = (data) => {
    if (data) {
        form.brand = data;
        inputStatus.model = false;
    } else {
        inputStatus.model = true;
        form.brand = '';
    }
}

const setColor = (data) => {
    if (data) {
        form.color = data;
    }
}

const setModel = (data) => {
    if (data) {
        form.model = data;
    }
}

const setType = (data) => {
    if (data) {
        form.type = data;
    }
}

const setTransmission = (data) => {
    if (data) {
        form.transmission = data;
    }
}

const setFuel = (data) => {
    if (data) {
        form.fuel = data;
    }
}

const setOwner = (data) => {
    if (data) {
        form.owner = data;
    }
}

const vehicleCreate = ref(false);

const startvehicleCreate = () => {
    if (form.vehicle == 0) {
        vehicleCreate.value = true;
    }
};

const newVehicle = () => {
    form.processing = true;

    axios.post(route('createVehicle'), {
        type: form.type,
        plate: form.plate,
        renavam: form.renavam,
        chassis: form.chassis,
        owner: form.owner,
        brand: form.brand,
        model: form.model,
        version: form.version,
        color: form.color,
        transmission: form.transmission,
        modelYear: form.modelYear,
        factureYear: form.factureYear,
        doors: form.doors,
        mileage: form.mileage,
        fuel: form.fuel,
        power: form.power,
        note: form.note,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona o novo cliente à lista de opções
            const newOption = { value: response.data.id, label: response.data.plate };
            options.push(newOption);
            form.vehicle = response.data.id;
            emit('vehicleSet', form.vehicle);
            emit('ownerSet', form.owner);
            emit('mileageSet', form.mileage);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const selectVehicle = () => {
    startvehicleCreate();
    emit('vehicleSet', form.vehicle);
    const selectedVehicle = options.find(option => option.value === form.vehicle);
    if (selectedVehicle) {
        emit('ownerSet', selectedVehicle.owner);
        emit('mileageSet', selectedVehicle.mileage);
    }
};

const closeModal = () => {
    vehicleCreate.value = false;
    form.error = '';
    form.type = '';
    form.plate = '';
    form.renavam = '';
    form.chassis = '';
    form.owner = '';
    form.brand = '';
    form.model = '';
    form.version = '';
    form.color = '';
    form.transmission = '';
    form.modelYear = '';
    form.factureYear = '';
    form.doors = '';
    form.fuel = '';
    form.mileage = '';
    form.power = '';
    form.note = '';
    props.vehicle = '';
    if (form.vehicle == 0) {
        form.vehicle = null;
    }
};
</script>

<template>
    <span>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Veículo" :searchable="true" :canClear="false"
            v-model="form.vehicle" :options="options" @select="selectVehicle" />
        <Multiselect v-else placeholder="Veículo" :disabled="true" />

        <DialogModal :show="vehicleCreate" @close="closeModal" :maxWidth="'5xl'">
            <template #title>
                {{ props.title }}
            </template>

            <template #content>
                {{ content }}
                <InputError :message="form.error" class="mt-2" />
                <div class="flex flex-wrap gap-5 mt-4">
                    <div class="w-full flex flex-col gap-1">
                        <div>Proprietário</div>
                        <Owner v-on:owner-set="setOwner" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Placa:</div>
                        <TextInput ref="plateInput" v-model="form.plate" type="text" class="w-full" placeholder="Placa"
                            v-maska:[toUpper] data-maska="['@@@-****']" />
                    </div>
                    <div class="w-48 flex flex-col gap-1">
                        <div>Renavam:</div>
                        <TextInput ref="renavamInput" v-model="form.renavam" type="text" class="w-full"
                            placeholder="Renavam" v-maska data-maska="['###########']" />
                    </div>
                    <div class="w-48 flex flex-col gap-1">
                        <div>Chassis:</div>
                        <TextInput ref="chassisInput" v-model="form.chassis" type="text" class="w-full"
                            placeholder="Chassis" v-maska:[toLower] data-maska="['*****************']" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Tipo:</div>
                        <Type v-on:type-set="setType" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Marca:</div>
                        <Brand v-on:brand-set="setBrand" />
                    </div>
                    <div class="w-52 flex flex-col gap-1">
                        <div>Modelo:</div>
                        <Model :is-disabled="inputStatus.model" :brand="form.brand" v-on:model-set="setModel" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Transmissão:</div>
                        <Transmission v-on:transmission-set="setTransmission" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Versão:</div>
                        <TextInput ref="versionInput" v-model="form.version" type="text" class="w-full"
                            placeholder="Versão" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Combustível:</div>
                        <Fuel v-on:fuel-set="setFuel" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Cor:</div>
                        <Color v-on:color-set="setColor" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Ano do Modelo:</div>
                        <TextInput ref="modelYearInput" v-model="form.modelYear" type="text" class="w-full"
                            placeholder="Ano do Modelo" v-maska data-maska="['####']" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Ano de Fabricação:</div>
                        <TextInput ref="manufactureYearInput" v-model="form.factureYear" type="text" class="w-full"
                            placeholder="Ano de Fabricação" v-maska data-maska="['####']" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Portas:</div>
                        <TextInput ref="doorsInput" v-model="form.doors" type="text" class="w-full" placeholder="Portas" />
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Quilometragem:</div>
                        <money3 v-model="form.mileage" v-bind="mileageConfig"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </money3>
                    </div>
                    <div class="w-40 flex flex-col gap-1">
                        <div>Potência:</div>
                        <TextInput ref="powerInput" v-model="form.power" type="text" class="w-full"
                            placeholder="Potência" />
                    </div>
                    <div class="w-full">
                        <TextArea ref="powerInput" v-model="form.note" type="text" class="w-full"
                            placeholder="Observações"></TextArea>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="newVehicle">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </span>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>
<script>
import { Money3Component } from 'v-money3';

export default {
    components: { money3: Money3Component },
    data() {
        return {
            amount: '',
            mileageConfig: {
                masked: false,
                prefix: '',
                suffix: ' km',
                thousands: '.',
                decimal: '',
                precision: 0,
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