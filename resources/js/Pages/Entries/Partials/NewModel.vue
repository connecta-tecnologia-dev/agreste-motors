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
        default: 'Cadastre um novo modelo de veículo',
    },
    content: {
        type: String,
        default: 'Verifique se o modelo veículo já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
    isDisabled: {
        type: Boolean,
        default: true,
    },
    brand: {
        type: String,
        default: '',
    }
});

const emit = defineEmits(['modelSet']);

const createModel = ref(false);

const form = reactive({
    _method: 'POST',
    name: '',
    brand: '',
    model: '',
});

const vueSelect = reactive({
    options: [],
});

const loading = ref(true);
const optionsLoaded = ref(false);

const loadVehicleModel = async () => {
    try {
        vueSelect.options = [];
        vueSelect.options.push({ value: 0, label: 'Nova' });
        const response = await axios.get(route('listModels', 
        {
            brand: props.brand
        }
        ));
        loading.value = true;

        response.data.forEach(model => {
            vueSelect.options.push({
                value: model.id,
                label: model.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

watchEffect(() => {
  if ((!props.isDisabled || props.brand != form.brand) && props.brand != '') {
    form.brand = props.brand;
    loadVehicleModel();
  }
  form.model = '';
});

const startCreateModel = () => {
    if (form.model == 0) {
        createModel.value = true;
    }
};

const newModel = () => {
    form.processing = true;

    axios.post(route('createModel'), {
        name: form.name,
        brand: form.brand,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona o novo model à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            vueSelect.options.push(newOption);
            form.model = response.data.id;
            emit('modelSet', form.model);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createModel.value = false;
    form.error = '';
    form.name = '';

    if (form.model == 0) {
        form.model = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Modelo" :searchable="true" :canClear="false" :disabled="isDisabled"
            v-model="form.model" :options="vueSelect.options" @select="(event) => { startCreateModel(); $emit('modelSet', form.model);}" />
        <Multiselect v-else placeholder="Modelo" :disabled="true" />

        <DialogModal :show="createModel" @close="closeModal">
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
                    @click="newModel">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>