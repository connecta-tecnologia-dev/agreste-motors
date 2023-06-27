<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

import Multiselect from '@vueform/multiselect';

const props = defineProps({
    data: Object,
    model: Number,
});

const emit = defineEmits(['needReload']);

const showConfirm = ref(false);

const form = reactive({
    error: '',
    name: '',
    brand: ''
});

const content = reactive({
    options: []
});

const loadModel = async () => {
    try {
        const response = await axios.get(route('showModel', props.model));
        form.id = response.data.id;
        form.name = response.data.name;
        form.brand = response.data.brand_id;

    } catch (error) {
        console.log(error);
    }
}

const loadBrands = async () => {
    const options = [];
    Object.keys(props.data).forEach(function (key) {
        options.push({ value: key, label: props.data[key].name });
    });
    content.options = options;
}

const modelUpdate = ref(false);

const startmodelUpdate = async () => {
    await loadBrands();
    await loadModel();
    modelUpdate.value = true;
};

const updateModel = () => {
    form.processing = true;

    axios.put(route('updateModel', props.model), {
        name: form.name,
        brand: form.brand,
    }).then(response => {
        form.processing = false;
        if (response.data.error) {
            form.error = response.data.error;
        } else {
            emit('needReload');
            closeModal();
        }
    }).catch(error => {
        form.processing = false;
        form.error = 'Ocorreu uma falha ao atualizar!';
    });
};

const deleteConfirm = () => {
    showConfirm.value = true;
}

const deleteModel = async () => {
    form.processing = true;
    axios.delete(route('deleteModel', props.model))
        .then(response => {
            showConfirm.value = false;
            form.processing = false;
            if (response.data.error) {
                form.error = response.data.error;
            } else {
                emit('needReload');
                closeModal();
            }
        }).catch(error => {
            form.processing = false;
            form.error = 'Ocorreu uma falha ao deletar!';
        });
};

const closeModal = () => {
    showConfirm.value = false;
    modelUpdate.value = false;
    form.error = '';
    form.name = '';
    form.brand = '';
};
</script>

<template>
    <tr @click="startmodelUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="modelUpdate" @close="closeModal">
        <template #title>
            Modificar cor
        </template>

        <template #content>
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-col gap-3 mt-4">
                <Multiselect ref="multiselect" placeholder="Marca" :searchable="true" :canClear="false" v-model="form.brand"
                    :options="content.options" />
                <div class="w-full">
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full"
                        placeholder="Informe o nome do modelo" v-on:input="checkPlateOnChange" />
                </div>
            </div>
            <ConfirmationModal :show="showConfirm" @close="showConfirm = false">
                <template #title>
                    Finalizar
                </template>

                <template #content>
                    Deseja realmente excluir a cor?
                </template>

                <template #footer>
                    <SecondaryButton @click="showConfirm = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteModel">
                        Deletar
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">
                Cancelar
            </SecondaryButton>

            <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="updateModel">
                Atualizar
            </PrimaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteConfirm">
                Deletar
            </DangerButton>
        </template>
    </DialogModal>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>