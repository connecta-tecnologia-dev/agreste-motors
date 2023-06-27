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
    transmission: Number,
});

const emit = defineEmits(['needReload']);

const showConfirm = ref(false);

const form = reactive({
    error: '',
    name: '',
});

const loadTransmission = async () => {
    try {
        const response = await axios.get(route('showTransmission', props.transmission));
        form.id = response.data.id;
        form.name = response.data.name;

    } catch (error) {
        console.log(error);
    }
}

const transmissionUpdate = ref(false);

const starttransmissionUpdate = async () => {
    await loadTransmission();
    transmissionUpdate.value = true;
};

const updateTransmission = () => {
    form.processing = true;

    axios.put(route('updateTransmission', props.transmission), {
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

const deleteTransmission = async () => {
    form.processing = true;
    axios.delete(route('deleteTransmission', props.transmission))
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
    transmissionUpdate.value = false;
    form.error = '';
    form.name = '';
};
</script>

<template>
    <tr @click="starttransmissionUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="transmissionUpdate" @close="closeModal">
        <template #title>
            Modificar tipo de transmissão
        </template>

        <template #content>
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-7 mt-4">
                <div class="w-46">
                    <div>Nome:</div>
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full" placeholder="Nome da marca"/>
                </div>
            </div>
            <ConfirmationModal :show="showConfirm" @close="showConfirm = false">
                <template #title>
                    Finalizar
                </template>

                <template #content>
                    Deseja realmente excluir o tipo de transmissão?
                </template>

                <template #footer>
                    <SecondaryButton @click="showConfirm = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteTransmission">
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
                @click="updateTransmission">
                Atualizar
            </PrimaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteConfirm">
                Deletar
            </DangerButton>
        </template>
    </DialogModal>
</template>
