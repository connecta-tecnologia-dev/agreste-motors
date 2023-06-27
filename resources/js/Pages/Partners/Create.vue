<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';

const form = reactive({
  error: '',
  name: '',
  processing: false,
});

const emit = defineEmits(['needReload']);

const colorCreate = ref(false);

const startColorCreate = () => {
  colorCreate.value = true;
};

const createColor = async () => {
  form.processing = true;

  try {
    const response = await axios.post(route('createColor'), {
      name: form.name,
    });

    if (response.data.error) {
      form.error = response.data.error;
    } else {
      closeModal();
      emit('needReload');
    }
  } catch (error) {
    form.error = 'Ocorreu uma falha ao cadastrar!';
  } finally {
    form.processing = false;
  }
};

const closeModal = () => {
  colorCreate.value = false;
  form.error = '';
  form.name = '';
};
</script>

<template>
  <span>
    <span @click="startColorCreate">
      <slot />
    </span>

    <DialogModal :show="colorCreate" @close="closeModal">
      <template #title>
        Cadastro de uma nova cor
      </template>

      <template #content>
        <InputError :message="form.error" class="mt-2" />
        <div class="flex flex-wrap gap-7 mt-4">
          <div class="w-46">
            <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full" placeholder="Nome da cor" />
          </div>
        </div>
      </template>

      <template #footer>
        <SecondaryButton @click="closeModal">
          Cancelar
        </SecondaryButton>

        <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="createColor">
          Salvar
        </PrimaryButton>
      </template>
    </DialogModal>
  </span>
</template>