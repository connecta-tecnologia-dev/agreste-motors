<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';

const props = defineProps({
    color: Number,
});

const emit = defineEmits(['needReload']);

const showConfirm = ref(false);

const form = reactive({
    error: '',
    name: '',
});

const loadColor = async () => {
    try {
        const response = await axios.get(route('showColor', props.color));
        form.id = response.data.id;
        form.name = response.data.name;

    } catch (error) {
        console.log(error);
    }
}

const colorUpdate = ref(false);

const startcolorUpdate = async () => {
    await loadColor();
    colorUpdate.value = true;
};

const updateColor = () => {
    form.processing = true;

    axios.put(route('updateColor', props.color), {
        name: form.name,
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

const deleteColor = async () => {
    form.processing = true;
    axios.delete(route('deleteColor', props.color))
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
    colorUpdate.value = false;
    form.error = '';
    form.name = '';
};
</script>

<template>
    <tr @click="startcolorUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="colorUpdate" @close="closeModal">
        <template #title>
            Modificar cor
        </template>

        <template #content>
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-7 mt-4">
                <div class="w-46">
                    <div>Nome:</div>
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full" placeholder="Nome da cor"/>
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
                        @click="deleteColor">
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
                @click="updateColor">
                Atualizar
            </PrimaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteConfirm">
                Deletar
            </DangerButton>
        </template>
    </DialogModal>
</template>
