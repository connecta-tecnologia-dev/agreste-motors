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
        default: 'Cadastre um novo tipo de veículo',
    },
    content: {
        type: String,
        default: 'Verifique se o tipo de veículo já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
});

const emit = defineEmits(['typeSet']);

const createType = ref(false);

const form = reactive({
    _method: 'POST',
    name: '',
    type: null,
});

const options = reactive([{ value: 0, label: 'Novo' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadTypes = async () => {
    try {
        const response = await axios.get(route('listTypes'));
        loading.value = true;

        response.data.forEach(type => {
            options.push({
                value: type.id,
                label: type.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadTypes();

const startCreateType = () => {
    if (form.type == 0) {
        createType.value = true;
    }
};

const newType = () => {
    form.processing = true;

    axios.post(route('createType'), {
        name: form.name,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona a nova marca à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            options.push(newOption);
            form.type = response.data.id;
            emit('typeSet', form.type);
            closeModal();        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createType.value = false;
    form.error = '';
    form.name = '';

    if (form.type == 0) {
        form.type = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Tipo" :searchable="true" :canClear="false" v-model="form.type"
            :options="options" @select="(event) => { startCreateType(); $emit('typeSet', form.type); }" />
        <Multiselect v-else placeholder="Tipo" :disabled="true" />


        <DialogModal :show="createType" @close="closeModal">
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
                    @click="newType">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>