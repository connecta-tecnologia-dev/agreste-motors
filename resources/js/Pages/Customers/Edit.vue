<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect';
import { vMaska } from "maska";

const props = defineProps({
    title: {
        type: String,
        default: 'Cadastre um novo Cliente',
    },
    content: {
        type: String,
        default: 'Verifique se o cliente já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    customer: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['customerHasUpdated']);

const customerUpdate = ref(false);

const form = reactive({
    _method: 'POST',
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
});

const loadCustomer = async () => {
    try {
        const response = await axios.get(route('showCustomer', props.customer));
        form.id = response.data.id;
        form.name = response.data.name;
        form.cpf = response.data.cpf;
        form.rg = response.data.rg;
        form.emitter = response.data.emitter;
        form.email = response.data.email;
        form.address = response.data.address;
        form.number = response.data.number;
        form.district = response.data.district;
        form.city = response.data.city;
        form.state = response.data.state;
        form.cep = response.data.cep;
    } catch (error) {
        console.log(error);
    }
}

const startCustomerUpdate = () => {
    loadCustomer();
    customerUpdate.value = true;
};

const updateCustomer = () => {
    form.processing = true;

    axios.post(route('updateCustomer', form.id), {
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
            emit('customerHasUpdated');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao Atualizar!';
    });
};

const closeModal = () => {
    customerUpdate.value = false;
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
    <tr @click="startCustomerUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="customerUpdate" @close="closeModal">
        <template #title>
            {{ title }}
        </template>

        <template #content>
            {{ content }}
            <div class="flex flex-wrap gap-3 mt-4">
                <div class="w-72">
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="mt-1 block w-full"
                        placeholder="Nome" />
                </div>
                <div class="w-52">
                    <TextInput ref="cpfInput" v-model="form.cpf" type="text" class="mt-1 block w-full"
                        placeholder="CPF/CNPJ" v-maska data-maska="['###.###.###-##', '##.###.###/####-##']" />
                    <InputError :message="form.error" class="mt-2" />
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
                        v-maska data-maska="['#####-###']" />
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="updateCustomer">
                {{ button }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>