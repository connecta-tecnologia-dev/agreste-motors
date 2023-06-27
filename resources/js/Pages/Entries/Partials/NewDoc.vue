<script setup>
import { ref, reactive } from 'vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import html2pdf from "html2pdf.js";


const props = defineProps({
    title: {
        type: String,
        default: 'Solicitação de ATPV:',
    },
    button: {
        type: String,
        default: 'Gerar PDF',
    },
    selected: null,
    owner: {
        type: String,
        default: null,
    },
    clerk: {
        type: String,
        default: null,
    },
    vehicle: {
        type: String,
        default: null,
    }
});

const emit = defineEmits(['colorSet']);

const createDocument = ref(false);

const form = reactive({
    owner: null,
    clerk: null,
    vehicle: null,
});

const options = reactive([{ value: 0, label: 'Nova' }]);
const loading = ref(true);
const optionsLoaded = ref(false);

const loadOwner = async () => {
    try {
        const response = await axios.get(route('showCustomer', props.owner));
        loading.value = true;

        form.owner = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const loadClerk = async () => {
    try {
        const response = await axios.get(route('showEmployee', props.clerk));
        loading.value = true;

        form.clerk = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const loadVehicle = async () => {
    try {
        const response = await axios.get(route('showVehicle', props.vehicle));
        loading.value = true;

        form.vehicle = response.data;

        loading.value = false;
        optionsLoaded.value = true;
    } catch (error) {
        console.log(error);
        loading.value = false;
    }
}

const startCreateDocumento = () => {
    if (props.clerk != null && props.owner != null && props.vehicle != null) {
        createDocument.value = true;
        loadClerk();
        loadOwner();
        loadVehicle();
    }
};

const closeModal = () => {
    createDocument.value = false;
    form.error = '';
};
</script>

<template>
    <div>
        <div v-if="props.clerk && props.owner && props.vehicle" @click="startCreateDocumento">
            <font-awesome-icon :icon="['fas', 'print']" beat-fade size="2xl" class="cursor-pointer" style="color: #25d058;" />
        </div>
        <div v-else>
            <font-awesome-icon :icon="['fas', 'print']" size="2xl" style="color: #ff0000;" />
        </div>

        <DialogModal :show="createDocument" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                <div v-if="form.clerk && form.owner && form.vehicle" id="app" ref="document">
                    <div id="element-to-convert" class="PDF">
                        <div class="flex justify-between">
                            <span
                                style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 157.06px; height: 86.57px;"><img
                                    alt="" src="/img/documents/detran.png"
                                    style="width: 157.06px; height: 86.57px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"
                                    title=""></span>
                            <span
                                style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 329.64px; height: 83.71px;"><img
                                    alt="" src="/img/documents/gov_pe.png"
                                    style="width: 329.64px; height: 83.71px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);"
                                    title=""></span>
                        </div>
                        <div class="mt-16 mb-1 text-xs flex flex-col gap-3">
                            <div class="font-black">
                                <p class="text-center">PROCURAÇÃO PARTICULAR</p>
                                <p class="text-center">(Específica para serviços de Veículos)</p>
                            </div>
                            <p><span class="font-black">OUTORGANTE</span> (Proprietário do Veículo)</p>
                            <div class="flex flex-col">
                                <p>Nome (Completo) : {{ form.owner.name }}</p>
                                <div class="grid grid-cols-3">
                                    <p>RG: {{ form.owner.rg }}</p>
                                    <p>Org. Emissor: {{ form.owner.emitter }}</p>
                                    <p>CPF/CNPJ: {{ form.owner.cpf }}</p>
                                </div>
                                <div class="grid grid-cols-2">
                                    <p>Endereço: {{ form.owner.address }}</p>
                                    <p>Nº: {{ form.owner.number }}</p>
                                </div>
                                <div class="grid grid-cols-4">
                                    <p>Bairro: {{ form.owner.district }}</p>
                                    <p>Cidade: {{ form.owner.city }}</p>
                                    <p>UF: {{ form.owner.state }}</p>
                                    <p>CEP: {{ form.owner.cep }}</p>
                                </div>
                            </div>
                            <p><span class="font-black">OUTORGADO</span> (Procurador Legal)</p>
                            <div class="flex flex-col">
                                <p>Nome (Completo) : {{ form.clerk.name }}</p>
                                <div class="grid grid-cols-3">
                                    <p>RG: {{ form.clerk.rg }}</p>
                                    <p>Org. Emissor: {{ form.clerk.emitter }}</p>
                                    <p>CPF: {{ form.clerk.cpf }}</p>
                                </div>
                                <div class="grid grid-cols-2">
                                    <p>Endereço: {{ form.clerk.address }}</p>
                                    <p>Nº: {{ form.clerk.number }}</p>
                                </div>
                                <div class="grid grid-cols-4">
                                    <p>Bairro: {{ form.clerk.district }}</p>
                                    <p>Cidade: {{ form.clerk.city }}</p>
                                    <p>UF: {{ form.clerk.state }}</p>
                                    <p>CEP: {{ form.clerk.cep }}</p>
                                </div>
                            </div>
                            <p class="font-black">DADOS DO VEÍCULO:</p>
                            <div class="flex flex-col">
                                <div class="grid grid-cols-3">
                                    <p>PLACA: {{ form.vehicle.plate }}</p>
                                    <p>RENAVAM: {{ form.vehicle.renavam }}</p>
                                </div>
                                <p>MARCA/MODELO: {{ form.vehicle.brand.name + '/' + form.vehicle.model.name + ' ' +
                                    form.vehicle.version + ' ' + form.vehicle.model_year + '/' +
                                    form.vehicle.manufacture_year }}</p>
                                <p>CHASSI: {{ form.vehicle.chassis }}</p>
                            </div>
                            <p>Com poderes de representação junto ao DETRAN/PE e/ou com fins específicos para realizar os
                                seguintes serviços: (Obrigatório especificar os Serviços)</p>
                            <div class="flex flex-col">
                                <p class="font-black">SOLICITAR ATPV</p>
                                <p>Podendo, para tanto, assinar, requerer, desistir, receber documentos, enfim tudo fazer e
                                    praticar o fiel cumprimento e desempenho do presente mandato.</p>
                            </div>
                            <div class="text-center mt-3">
                                <p>__________________, ___ de ______________ de 20___.</p>
                                <p>(Local)</p>
                            </div>
                            <div class="text-center">
                                <p>_____________________________________</p>
                                <p>Assinatura do Outorgante (Proprietário do Veículo)</p>
                            </div>
                            <div class="mt-52 text-center text-xs signature">
                                <p class="font-black">Departamento Estadual de Trânsito de Pernambuco/DETRAN/PE.</p>
                                <p>Estrada do Barbalho, nº 889 – Iputinga – Recife/PE. - CEP. 50.690-900</p>
                                <p>Fone: (0xx81) 3184–8004/8005 - FAX: (81) 3271-2176</p>
                                <p class="underline font-black">www.detran.pe.gov.br &nbsp; &nbsp; &nbsp; &nbsp;
                                    &nbsp; &nbsp; &nbsp;presidencia@detran.pe.gov.br</p>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                    @click="exportToPDF">
                    {{ button }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
<style src="@vueform/multiselect/themes/default.css"></style>
<script>

export default {
    name: "app",
    methods: {
        async exportToPDF() {
            const content = document.getElementById("element-to-convert");

            const opt = {
                margin: [0.5, 1],
                filename: 'documento.pdf',
                image: { type: 'jpeg', quality: 2 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            };

            await html2pdf().from(content).set(opt).toPdf().get('pdf').then(function (pdf) {
                const blob = pdf.output('blob');
                const url = URL.createObjectURL(blob);
                window.open(url, '_blank');
            });
        },
    },
};
</script>

<style>
.PDF {
    font-size: 10pt;
    font-family: "times New Roman";
    color: #000000;
    font-weight: 400;
}

.signature {
    font-size: 8pt;
    color: #0000ff;
}
</style>