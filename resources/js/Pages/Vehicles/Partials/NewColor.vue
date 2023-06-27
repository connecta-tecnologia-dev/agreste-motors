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
        default: 'Cadastre uma nova cor para o veículo',
    },
    content: {
        type: String,
        default: 'Verifique se a cor do veículo já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
});

const emit = defineEmits(['colorSet']);

const createColor = ref(false);

const form = reactive({
    _method: 'POST',
    name: '',
    color: null,
});

const options = reactive([{ value: 0, label: 'Nova' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadColors = async () => {
    try {
        const response = await axios.get(route('showColors'));
        loading.value = true;

        response.data.forEach(color => {
            options.push({
                value: color.id,
                label: color.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadColors();

const startCreateColor = () => {
    if (form.color == 0) {
        createColor.value = true;
    }
};

const newColor = () => {
    form.processing = true;

    axios.post(route('createColor'), {
        name: form.name,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona a nova cor à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            options.push(newOption);
            form.color = response.data.id;
            emit('colorSet', form.color);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createColor.value = false;
    form.error = '';
    form.name = '';

    if (form.color == 0) {
        form.color = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Cor" :canClear="false" :searchable="true" v-model="form.color"
            :options="options" @select="(event) => { startCreateColor(); $emit('colorSet', form.color); }" />
        <Multiselect v-else placeholder="Cor" :disabled="true" />

        <DialogModal :show="createColor" @close="closeModal">
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
                    @click="newColor">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>