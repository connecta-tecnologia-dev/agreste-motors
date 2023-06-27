<script setup>
import { watch, ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Modificar banco',
    },
    content: {
        type: String,
        default: 'Tenha cuidado ao alterar as informações do banco!',
    },
    button: {
        type: String,
        default: 'Atualizar',
    },
    bank: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['bankHasUpdated']);

const form = reactive({
    error: '',
    id: '',
    name: '',
    agency: '',
    account: '',
    balance: 0,
});

const loadBank = async () => {
    try {
        const response = await axios.get(route('showBank', props.bank));
        form.id = response.data.id;
        form.name = response.data.name;
        form.agency = response.data.agency;
        form.account = response.data.account;
        form.balance = response.data.balance;

    } catch (error) {
        console.log(error);
    }
}

const bankUpdate = ref(false);

const startbankUpdate = () => {
    loadBank();
    bankUpdate.value = true;
};

const updateBank = () => {
    form.processing = true;

    axios.post(route('updateBank', form.id), {
        name: form.name,
        agency: form.agency,
        account: form.account,
        balance: form.balance
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            emit('bankHasUpdated');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao Atualizar!';
    });
};

const closeModal = () => {
    bankUpdate.value = false;
    form.error = '';
    form.name = '';
    form.agency = '';
    form.account = '';
    form.balance = '';
};
</script>

<template>
    <tr @click="startbankUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="bankUpdate" @close="closeModal">
        <template #title>
            {{ title }}
        </template>

        <template #content>
            {{ content }}
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-7 mt-4">
                <div class="w-46">
                    <div>Nome:</div>
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full" placeholder="Nome do Banco"
                        v-on:input="checkPlateOnChange" />
                </div>
                <div class="w-36">
                    <div>Agência:</div>
                    <TextInput ref="agencyInput" v-model="form.agency" type="text" class="w-full" placeholder="Agência" />
                </div>
                <div class="w-46">
                    <div>Número da conta:</div>
                    <TextInput ref="accountInput" v-model="form.account" type="text" class="w-full" placeholder="Conta" />
                </div>
                <div class="w-46">
                    <div>Saldo:</div>
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
                @click="updateBank">
                {{ button }}
            </PrimaryButton>
        </template>
    </DialogModal>
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