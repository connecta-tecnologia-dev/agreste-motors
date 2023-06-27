<script setup>
import { watch, ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

defineProps({
    title: {
        type: String,
        default: 'Cadastre um novo banco',
    },
    content: {
        type: String,
        default: 'Verifique se o banco já não está cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
});

const emit = defineEmits(['bankHasCreated']);

const form = reactive({
    error: '',
    name: '',
    agency: '',
    account: '',
    balance: 0,
});

const bankCreate = ref(false);

const startbankCreate = () => {
    bankCreate.value = true;
};

const newBank = () => {
    form.processing = true;

    axios.post(route('createBank'), {
        name: form.name,
        agency: form.agency,
        account: form.account,
        balance: form.balance
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            emit('bankHasCreated');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    bankCreate.value = false;
    form.error = '';
    form.name = '';
    form.agency = '';
    form.account = '';
    form.balance = '';
};
</script>

<template>
    <span>
        <span @click="startbankCreate">
            <slot />
        </span>

        <DialogModal :show="bankCreate" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}
                <InputError :message="form.error" class="mt-2" />
                <div class="flex flex-wrap gap-7 mt-4">
                    <div class="w-46">
                        <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full"
                            placeholder="Nome do Banco" v-on:input="checkPlateOnChange" />
                    </div>
                    <div class="w-36">
                        <TextInput ref="agencyInput" v-model="form.agency" type="text" class="w-full"
                            placeholder="Agência" />
                    </div>
                    <div class="w-46">
                        <TextInput ref="accountInput" v-model="form.account" type="text" class="w-full"
                            placeholder="Conta" />
                    </div>
                    <div class="w-46">
                        <money3 v-model="form.balance" v-bind="config"
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
                    @click="newBank">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </span>
</template>
<script>
import { Money3Component } from 'v-money3'

export default {
    components: { money3: Money3Component },
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