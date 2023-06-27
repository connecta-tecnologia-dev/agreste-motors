<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';

import Multiselect from '@vueform/multiselect';

const props = defineProps({
  data: Object
});

const form = reactive({
  error: '',
  brand: '',
  name: '',
  processing: false,
});

const content = reactive({
  options : []
});

const emit = defineEmits(['needReload']);

const modelCreate = ref(false);

const startModelCreate = async () => {
  await loadBrands();
  modelCreate.value = true;
};

const loadBrands = async () => {
  const options = [];
  Object.keys(props.data).forEach(function (key) {
    options.push({ value: key, label: props.data[key].name });
  });
  content.options = options;
}

const createModel = async () => {
  form.processing = true;

  try {
    const response = await axios.post(route('createModel'), {
      name: form.name,
      brand: form.brand,
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
  modelCreate.value = false;
  form.error = '';
  form.name = '';
  form.brand = '';
};
</script>

<template>
  <span>
    <span @click="startModelCreate">
      <slot />
    </span>

    <DialogModal :show="modelCreate" @close="closeModal">
      <template #title>
        Cadastro de um novo modelo
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
      </template>

      <template #footer>
        <SecondaryButton @click="closeModal">
          Cancelar
        </SecondaryButton>

        <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
          @click="createModel">
          Salvar
        </PrimaryButton>
      </template>
    </DialogModal>
  </span>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>