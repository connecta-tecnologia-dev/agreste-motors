<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    customer:Number
});

const data = reactive({
    customer: null
});

const customerShow = ref(false);

const loadCustomer= async () => {
    try {
        const response = await axios.get(route('showCustomer', props.customer));
        data.customer = response.data;
    } catch (error) {
        console.log(error);
    }
};

const startCustomerShow = async() => {
    loadCustomer();
    customerShow.value = true;
};

const closeModal = () => {
    customerShow.value = false;
};
</script>

<template>
    <span @click="startCustomerShow" class="hover:text-blue-600 cursor-pointer">
        <slot />
    </span>

    <DialogModal :show="customerShow" @close="closeModal">
        <template #title>
            Informações do Cliente:
        </template>

        <template #content>
            {{ content }}
            <div v-if="data.customer" class="flex flex-wrap gap-6 mt-4">
                <div class="text-base"><span class="font-bold">Nome: </span>{{ data.customer.name }}</div>
                <div class="text-base"><span class="font-bold">CPF: </span>{{ data.customer.cpf }}</div>
                <div class="text-base"><span class="font-bold">RG: </span>{{ data.customer.rg }}</div>
                <div class="text-base"><span class="font-bold">Org. Emissor: </span>{{ data.customer.emitter }}</div>
                <div class="text-base" v-if="data.customer.email"><span class="font-bold">Email: </span>{{ data.customer.email }}</div>
                <div class="text-base"><span class="font-bold">Endereço: </span>{{ data.customer.address }}</div>
                <div class="text-base"><span class="font-bold">N°: </span>{{ data.customer.number }}</div>
                <div class="text-base"><span class="font-bold">Bairro: </span>{{ data.customer.district }}</div>
                <div class="text-base"><span class="font-bold">Estado: </span>{{ data.customer.state }}</div>
                <div class="text-base"><span class="font-bold">Cep: </span>{{ data.customer.cep }}</div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Sair
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>