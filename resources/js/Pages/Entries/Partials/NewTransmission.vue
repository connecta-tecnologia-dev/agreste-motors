<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect';

defineProps({
    title: {
        type: String,
        default: 'Cadastre um novo tipo de Transmissão',
    },
    content: {
        type: String,
        default: 'Verifique se o tipo de transmissão já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
});

const emit = defineEmits(['transmissionSet']);

const createTransmission = ref(false);

const form = reactive({
    _method: 'POST',
    name: '',
    transmission: null,
});

const options = reactive([{ value: 0, label: 'Novo' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadTransmissions = async () => {
    try {
        const response = await axios.get(route('listTransmissions'));
        loading.value = true;

        response.data.forEach(transmission => {
            options.push({
                value: transmission.id,
                label: transmission.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadTransmissions();

const startCreateTransmission = () => {
    if (form.transmission == 0) {
        createTransmission.value = true;
    }
};

const newTransmission = () => {
    form.processing = true;

    axios.post(route('createTransmission'), {
        name: form.name,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona a nova marca à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            options.push(newOption);
            form.transmission = response.data.id;
            emit('transmissionSet', form.transmission);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createTransmission.value = false;
    form.error = '';
    form.name = '';

    if (form.transmission == 0) {
        form.transmission = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Transmissão" :searchable="true" :canClear="false" v-model="form.transmission"
            :options="options" @select="(event) => { startCreateTransmission(); $emit('transmissionSet', form.transmission); }" />
        <Multiselect v-else placeholder="Transmissão" :disabled="true" />


        <DialogModal :show="createTransmission" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}
                <div class="flex flex-wrap gap-3 mt-4 justify-between">
                    <div>
                        <TextInput ref="nameInput" v-model="form.name" type="text" class="mt-1 block" placeholder="Nome" />
                        <InputError :message="form.error" class="mt-2" />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="newTransmission">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>