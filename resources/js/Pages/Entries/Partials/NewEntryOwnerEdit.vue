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
        default: 'Cadastre um novo proprietário para o veículo',
    },
    content: {
        type: String,
        default: 'Verifique se o cliente já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    isDisabled: {
        type: Boolean,
        default: true,
    },
    owner: String
});

const emit = defineEmits(['ownerSet']);

const createCustomer = ref(false);

const form = reactive({
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
    customer: null,
});

const vueSelect = reactive({
    options: [],
});

const loading = ref(true);
const optionsLoaded = ref(false);

const loadCustomers = async () => {
    try {
        vueSelect.options = [];
        vueSelect.options.push({ value: 0, label: 'Novo' });
        const response = await axios.get(route('listCustomers'));
        loading.value = true;

        await response.data.forEach(customer => {
            vueSelect.options.push({
                value: customer.id,
                label: customer.name + (customer.cpf.length <= 15 ? ", CPF: " : ", CNPJ: ") + customer.cpf
            });
        });

        form.customer = props.owner;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadCustomers();

const startCreateCustomer = () => {
    if (form.customer == 0) {
        createCustomer.value = true;
    }
};

const newCustomer = () => {
    form.processing = true;

    axios.post(route('createCustomer'), {
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
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona o novo cliente à lista de opções
            const newOption = { value: response.data.id, label: response.data.name + (response.data.cpf.length <= 15 ? ", CPF: " : ", CNPJ: ") + response.data.cpf };
            vueSelect.options.push(newOption);
            props.owner = response.data.id;
            form.customer = response.data.id;
            emit('ownerSet', form.customer);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createCustomer.value = false;
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
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Proprietário" :searchable="true" :canClear="false"
            v-model="form.customer" :options="vueSelect.options" :disabled="isDisabled"
            @select="(event) => { startCreateCustomer(); $emit('ownerSet', form.customer); }" />
        <Multiselect v-else placeholder="Proprietário" :disabled="true" />

        <DialogModal :show="createCustomer" @close="closeModal">
            <template #title>
                {{ title }}
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
                        <TextInput ref="cpfInput" v-model="form.cpf" type="text" class="mt-1 block w-full"
                            placeholder="CPF/CNPJ" v-mask="['###.###.###-##', '##.###.###/####-##']" />
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
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="newCustomer">
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