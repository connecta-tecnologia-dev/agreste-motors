<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Multiselect from '@vueform/multiselect';

const props = defineProps({
    title: {
        type: String,
        default: 'Cadastre um novo cargo para o funcionário',
    },
    content: {
        type: String,
        default: 'Verifique se a cargo já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
    function: {
        type: Number,
        default: null,
    }
});

const emit = defineEmits(['functionSet']);

const createFunction = ref(false);

const form = reactive({
    name: '',
    function: null,
});

const options = reactive([{ value: 0, label: 'Novo' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadFunctions = async () => {
    try {
        const response = await axios.get(route('listFunctions'));
        loading.value = true;

        await response.data.forEach(efunction => {
            options.push({
                value: efunction.id,
                label: efunction.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;

    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const start = async () => {
    await loadFunctions();
    form.function = props.function;
}

start();

const startCreateFunction = () => {
    if (form.function == 0) {
        createFunction.value = true;
    }
};

const newFunction = () => {
    form.processing = true;

    axios.post(route('createFunction'), {
        name: form.name,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona a nova cor à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            options.push(newOption);
            form.function = response.data.id;
            emit('functionSet', form.function);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createFunction.value = false;
    form.error = '';
    form.name = '';

    if (form.function == 0) {
        form.function = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Cargo" :canClear="false" :searchable="true"
            v-model="form.function" :options="options"
            @select="(event) => { startCreateFunction(); $emit('functionSet', form.function); }" />
        <Multiselect v-else placeholder="Cargo" :disabled="true" />

        <DialogModal :show="createFunction" @close="closeModal">
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
                    @click="newFunction">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>