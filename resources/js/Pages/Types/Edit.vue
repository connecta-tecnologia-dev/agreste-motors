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
    type: Number,
});

const emit = defineEmits(['needReload']);

const showConfirm = ref(false);

const form = reactive({
    error: '',
    name: '',
});

const loadType = async () => {
    try {
        const response = await axios.get(route('showType', props.type));
        form.id = response.data.id;
        form.name = response.data.name;

    } catch (error) {
        console.log(error);
    }
}

const typeUpdate = ref(false);

const starttypeUpdate = async () => {
    await loadType();
    typeUpdate.value = true;
};

const updateType = () => {
    form.processing = true;

    axios.put(route('updateType', props.type), {
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

const deleteType = async () => {
    form.processing = true;
    axios.delete(route('deleteType', props.type))
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
    typeUpdate.value = false;
    form.error = '';
    form.name = '';
};
</script>

<template>
    <tr @click="starttypeUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="typeUpdate" @close="closeModal">
        <template #title>
            Modificar combustível
        </template>

        <template #content>
            <InputError :message="form.error" class="mt-2" />
            <div class="flex flex-wrap gap-7 mt-4">
                <div class="w-46">
                    <div>Nome:</div>
                    <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full" placeholder="Nome do combustível"/>
                </div>
            </div>
            <ConfirmationModal :show="showConfirm" @close="showConfirm = false">
                <template #title>
                    Finalizar
                </template>

                <template #content>
                    Deseja realmente excluir o tipo de veículo?
                </template>

                <template #footer>
                    <SecondaryButton @click="showConfirm = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteType">
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
                @click="updateType">
                Atualizar
            </PrimaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteConfirm">
                Deletar
            </DangerButton>
        </template>
    </DialogModal>
</template>
