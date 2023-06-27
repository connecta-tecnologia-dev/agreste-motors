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
    brand: Number,
});

const emit = defineEmits(['needReload']);

const showConfirm = ref(false);

const form = reactive({
    error: '',
    name: '',
});

const loadBrand = async () => {
    try {
        const response = await axios.get(route('showBrand', props.brand));
        form.id = response.data.id;
        form.name = response.data.name;

    } catch (error) {
        console.log(error);
    }
}

const brandUpdate = ref(false);

const startbrandUpdate = async () => {
    await loadBrand();
    brandUpdate.value = true;
};

const updateBrand = () => {
    form.processing = true;

    axios.put(route('updateBrand', props.brand), {
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

const deleteBrand = async () => {
    form.processing = true;
    axios.delete(route('deleteBrand', props.brand))
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
    brandUpdate.value = false;
    form.error = '';
    form.name = '';
};
</script>

<template>
    <tr @click="startbrandUpdate" class="hover:bg-green-300 cursor-pointer">
        <slot />
    </tr>

    <DialogModal :show="brandUpdate" @close="closeModal">
        <template #title>
            Modificar marca
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
                    Deseja realmente excluir a fabricante de veículos?
                </template>

                <template #footer>
                    <SecondaryButton @click="showConfirm = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteBrand">
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
                @click="updateBrand">
                Atualizar
            </PrimaryButton>

            <DangerButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                @click="deleteConfirm">
                Deletar
            </DangerButton>
        </template>
    </DialogModal>
</template>
