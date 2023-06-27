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

const fuelCreate = ref(false);

const startFuelCreate = () => {
  fuelCreate.value = true;
};

const createFuel = async () => {
  form.processing = true;

  try {
    const response = await axios.post(route('createFuel'), {
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
  fuelCreate.value = false;
  form.error = '';
  form.name = '';
};
</script>

<template>
  <span>
    <span @click="startFuelCreate">
      <slot />
    </span>

    <DialogModal :show="fuelCreate" @close="closeModal">
      <template #title>
        Cadastro de um Combustível
      </template>

      <template #content>
        <InputError :message="form.error" class="mt-2" />
        <div class="flex flex-wrap gap-7 mt-4">
          <div class="w-46">
            <TextInput ref="nameInput" v-model="form.name" type="text" class="w-full" placeholder="Nome do Combustível" />
          </div>
        </div>
      </template>

      <template #footer>
        <SecondaryButton @click="closeModal">
          Cancelar
        </SecondaryButton>

        <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="createFuel">
          Salvar
        </PrimaryButton>
      </template>
    </DialogModal>
  </span>
</template>