<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

import Function from './Partials/NewFunction.vue';

const props = defineProps({
    employee: Number,
});

const emit = defineEmits(['employeeHasUpdated']);

const form = reactive({
    id: '',
    name: '',
    cpf: '',
    rg: '',
    emitter: '',
    email: '',
    address: '',
    number: '',
    district: '',
    city: '',
    state: '',
    cep: '',
    function: '',
    salary: '',
    employee: null,
});

const loadEmployee = async () => {
    try {
        const response = await axios.get(route('showEmployee', props.employee));
        form.id = response.data.id;
        form.error = '';
        form.name = response.data.name;
        form.rg = response.data.rg;
        form.emitter = response.data.emitter;
        form.cpf = response.data.cpf;
        form.email = response.data.email;
        form.address = response.data.address;
        form.number = response.data.number;
        form.district = response.data.district;
        form.city = response.data.city;
        form.state = response.data.state;
        form.cep = response.data.cep;
        form.function = response.data.employee_function_id;
        form.salary = response.data.salary;

    } catch (error) {
        console.log(error);
    }
}

const employeeUpdate = ref(false);

const startemployeeUpdate = () => {
    loadEmployee();
    employeeUpdate.value = true;
};

const updateEmployee = () => {
    form.processing = true;

    axios.post(route('updateEmployee', form.id), {
        name: form.name,
        cpf: form.cpf,
        rg: form.rg,
        emitter: form.emitter,
        email: form.email,
        address: form.address,
        number: form.number,
        district: form.district,
        city: form.city,
        state: form.state,
        cep: form.cep,
        function: form.function,
        salary: form.salary,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            emit('employeeHasUpdated');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    employeeUpdate.value = false;
    form.error = '';
    form.name = '';
    form.cpf = '';
    form.rg = '';
    form.emitter = '';
    form.email = '';
    form.address = '';
    form.number = '';
    form.district = '';
    form.city = '';
    form.state = '';
    form.cep = '';
    form.function = '';
    form.salary = '';
};
</script>

<template>
    <tr @click="startemployeeUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="employeeUpdate" @close="closeModal">
        <template #title>
            Edição de funcionário
        </template>

        <template #content>
            {{ content }}
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-3 mt-4">
                <div class="w-72">
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="mt-1 block w-full"
                        placeholder="Nome" />
                </div>
                <div class="w-52">
                    <Function v-on:function-set="setFunction" :function="form.function" />
                </div>
                <div class="w-52">
                    <TextInput ref="cpfInput" v-model="form.cpf" type="text" class="mt-1 block w-full" placeholder="CPF"
                        v-mask="['###.###.###-##']" />
                </div>
                <div class="w-72">
                    <TextInput ref="rgInput" v-model="form.rg" type="text" class="mt-1 block w-full" placeholder="RG" />
                </div>
                <div class="w-40">
                    <TextInput ref="emitterInput" v-model="form.emitter" type="text" class="mt-1 block w-full"
                        placeholder="Org. Emissor" />
                </div>
                <div class="w-72">
                    <TextInput ref="emailInput" v-model="form.email" type="text" class="mt-1 block w-full"
                        placeholder="Email" />
                </div>
                <div class="w-72">
                    <TextInput ref="addressInput" v-model="form.address" type="text" class="mt-1 block w-full"
                        placeholder="Endereço" />
                </div>
                <div class="w-20">
                    <TextInput ref="numberInput" v-model="form.number" type="text" class="mt-1 block w-full"
                        placeholder="N°" />
                </div>
                <div class="w-40">
                    <TextInput ref="districtInput" v-model="form.district" type="text" class="mt-1 block w-full"
                        placeholder="Bairro" />
                </div>
                <div class="w-40">
                    <TextInput ref="cityInput" v-model="form.city" type="text" class="mt-1 block w-full"
                        placeholder="Cidade" />
                </div>
                <div class="w-40">
                    <TextInput ref="stateInput" v-model="form.state" type="text" class="mt-1 block w-full"
                        placeholder="Estado" />
                </div>
                <div class="w-40">
                    <TextInput ref="cepInput" v-model="form.cep" type="text" class="mt-1 block w-full" placeholder="CEP"
                        v-mask="['#####-###']" />
                </div>
                <div class="w-46">
                    <div>Salario:</div>
                    <money3 v-model="form.salary" v-bind="config"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </money3>
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="updateEmployee">
                Salvar
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>
<script>
import { mask } from 'vue-the-mask'
import { Money3Component } from 'v-money3';

export default {
    components: { money3: Money3Component },
    directives: { mask },
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