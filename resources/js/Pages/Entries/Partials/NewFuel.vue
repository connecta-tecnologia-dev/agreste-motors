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
        default: 'Cadastre um novo combustível',
    },
    content: {
        type: String,
        default: 'Verifique se o nome do combustível já não esta cadastrado no sistema.',
    },
    button: {
        type: String,
        default: 'Confirmar',
    },
    selected: null,
});

const emit = defineEmits(['fuelSet']);

const createFuel = ref(false);

const form = reactive({
    _method: 'POST',
    name: '',
    fuel: null,
});

const options = reactive([{ value: 0, label: 'Novo' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadFuels = async () => {
    try {
        const response = await axios.get(route('listFuels'));
        loading.value = true;

        response.data.forEach(fuel => {
            options.push({
                value: fuel.id,
                label: fuel.name
            });
        });

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

loadFuels();

const startCreateFuel = () => {
    if (form.fuel == 0) {
        createFuel.value = true;
    }
};

const newFuel = () => {
    form.processing = true;

    axios.post(route('createFuel'), {
        name: form.name,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            // Adiciona a nova marca à lista de opções
            const newOption = { value: response.data.id, label: response.data.name };
            options.push(newOption);

            form.fuel = response.data.id;
            emit('fuelSet', form.fuel);
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao cadastrar!';
    });
};

const closeModal = () => {
    createFuel.value = false;
    form.error = '';
    form.name = '';

    if (form.fuel == 0) {
        form.fuel = null;
    }
};
</script>

<template>
    <div>
        <Multiselect v-if="optionsLoaded" ref="multiselect" placeholder="Combustível" :searchable="true" :canClear="false" v-model="form.fuel"
            :options="options" @select="(event) => { startCreateFuel(); $emit('fuelSet', form.fuel); }" />
        <Multiselect v-else placeholder="Combustível" :disabled="true" />


        <DialogModal :show="createFuel" @close="closeModal">
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
                    @click="newFuel">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>