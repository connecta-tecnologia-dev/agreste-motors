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
        default: 'Cadastre uma nova marca fabricante do veículo',
    },
    content: {
        type: String,
        default: 'Verifique se a marca já não esta cadastrada no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
});

const emit = defineEmits(['brandSet']);

const createBrand = ref(false);

const form = reactive({
    _method: 'POST',
    name: '',
    brand: null,
});

const options = reactive([{ value: 0, label: 'Nova' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadBrands = async () => {
    try {
        const response = await axios.get(route('showBrands'));
        loading.value = true;

        response.data.forEach(brand => {
            options.push({
                value: brand.id,
                label: brand.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadBrands();

const startCreateBrand = () => {
    if (form.brand == 0) {
        createBrand.value = true;
    }
};

const newBrand = () => {
    form.processing = true;

    axios.post(route('createBrand'), {
        name: form.name,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona a nova marca à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            options.push(newOption);
            form.brand = response.data.id;
            emit('brandSet', form.brand);
            closeModal();
        }
    }).catch(error => {
        alert(error)
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createBrand.value = false;
    form.error = '';
    form.name = '';
    
    if (form.brand == 0) {
        form.brand = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Marca" :searchable="true" :canClear="false" v-model="form.brand"
            :options="options" @select="(event) => { startCreateBrand(); $emit('brandSet', form.brand); }" />
        <Multiselect v-else placeholder="Marca" :disabled="true" />


        <DialogModal :show="createBrand" @close="closeModal">
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
                    @click="newBrand">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>