<script setup>
import { watch, ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { vMaska } from "maska";

import Type from './Partials/NewType.vue';
import Model from './Partials/NewModel.vue';
import Color from './Partials/NewColor.vue';
import Brand from './Partials/NewBrand.vue';
import Owner from './Partials/NewOwner.vue';
import Fuel from './Partials/NewFuel.vue';
import Transmission from './Partials/NewTransmission.vue';

defineProps({
    title: {
        type: String,
        default: 'Novo Veículo',
    },
    content: {
        type: String,
        default: 'Verifique se o veículo já não está cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    }
});

const emit = defineEmits(['vehicleHasCreated']);

const form = reactive({
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

const toUpper = {
  preProcess: val => val.toUpperCase()
}

const toLower = {
  preProcess: val => val.toLowerCase()
}

const inputStatus = reactive({
    model: true,
});

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
    vehicleCreate.value = true;
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
            emit('vehicleHasCreated');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
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
};
</script>

<template>
    <span>
        <span @click="startvehicleCreate">
            <slot />
        </span>

        <DialogModal :show="vehicleCreate" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}
                <div class="flex flex-wrap gap-7 mt-4">
                    <InputError :message="form.error" class="mt-2" />
                    <div class="w-28">
                        <TextInput ref="plateInput" v-model="form.plate" type="text" class="w-full" placeholder="Placa"
                            v-maska:[toUpper] data-maska="['@@@-****']" />
                    </div>
                    <div class="w-36">
                        <TextInput ref="renavamInput" v-model="form.renavam" type="text" class="w-full"
                            placeholder="Renavam" v-maska data-maska="['###########']" />
                    </div>
                    <div class="w-46">
                        <TextInput ref="chassisInput" v-model="form.chassis" type="text" class="w-full"
                            placeholder="Chassis" v-maska:[toLower] data-maska="['*****************']" />
                    </div>
                    <div class="w-40">
                        <Type v-on:type-set="setType" />
                    </div>
                    <div class="w-40">
                        <Brand v-on:brand-set="setBrand" />
                    </div>
                    <div class="w-52">
                        <Model :is-disabled="inputStatus.model" :brand="form.brand" v-on:model-set="setModel" />
                    </div>
                    <div class="w-40">
                        <Transmission v-on:transmission-set="setTransmission" />
                    </div>
                    <div class="w-60">
                        <TextInput ref="versionInput" v-model="form.version" type="text" class="w-full"
                            placeholder="Versão" />
                    </div>
                    <div class="w-40">
                        <Fuel v-on:fuel-set="setFuel" />
                    </div>
                    <div class="w-52">
                        <Color v-on:color-set="setColor" />
                    </div>
                    <div class="w-40">
                        <TextInput ref="modelYearInput" v-model="form.modelYear" type="text" class="w-full"
                            placeholder="Ano do Modelo" v-maska data-maska="['####']" />
                    </div>
                    <div class="w-40">
                        <TextInput ref="manufactureYearInput" v-model="form.factureYear" type="text" class="w-full"
                            placeholder="Ano de Fabricação" v-maska data-maska="['####']" />
                    </div>
                    <div class="w-28">
                        <TextInput ref="doorsInput" v-model="form.doors" type="text" class="w-full" placeholder="Portas" />
                    </div>
                    <div class="w-40">
                        <TextInput ref="mileageInput" v-model="form.mileage" type="text" class="w-full"
                            placeholder="Quilometragem" />
                    </div>
                    <div class="w-40">
                        <TextInput ref="powerInput" v-model="form.power" type="text" class="w-full"
                            placeholder="Potência" />
                    </div>
                    <div class="w-full">
                        <Owner v-on:owner-set="setOwner" />
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