<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/inertia-vue3";
import axios from "axios";


const props = defineProps({
    orders: Array,
});


const form = useForm({
    nombre: "",
    descripcion: "",
    tipo: null,
    intervalo: null,
    dia_corte: null,
    condicionado_faltas: false,
    condicionado_seniority: false,
    condicionado_eficiencia: false,
});
// console.log(benefits);
// const orders = ref(null);
// orders.value = benefits;
const tipo = ref(null);
const intervalo = ref(null);

const intervaloSufijo = computed(() => {
    return tipo.value?.value === "dia" ? " día(s)" : " mes(es)";
});

const reglas = ref([]);

function agregarRegla() {
    reglas.value.push({ monto: null, otro: null, porcentaje: null });
}

function eliminarRegla(index) {
    reglas.value.splice(index, 1);
}

const toast = useToast();
const dt = ref();
const showOrder = ref(false);
const benefitDialog = ref(false);
const deleteBenefitDialog = ref(false);
const deleteBenefitsDialog = ref(false);
const product = ref({});
const selectedBenefits = ref();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const openNew = () => {
    benefitDialog.value = true;
};
const hideDialog = () => {
    benefitDialog.value = false;
    submitted.value = false;
    showOrder.value = false;
    selectedOrder.value = null;
};
const saveBenefit = () => {
    form.post(route("catalogo.prestaciones.store"), {
        onSuccess: () => {
            hideDialog();
            toast.add({
                severity: "success",
                summary: "Guardado",
                detail: "Prestacion Guardada Correctamente",
                life: 3000,
            });
        },
        onError: (errors) => {
            // Si el backend devuelve 422 con errores, Inertia los manejará automáticamente en form.errors
            // Puedes mostrar un toast de error aquí o simplemente dejar que los mensajes de error de PrimeVue aparezcan
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Hubo un problema al guardar la prestación.",
                life: 3000,
            });

            console.error("Errores al guardar la prestación:", errors);
        },
    });
};

const selectedOrder = ref(null);

const showOrders = async (id) => {
    showOrder.value = true;
    const response = await axios.get(route("purchase-orders.show", id))

        .then(response => {
            selectedOrder.value = response.data;
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });

};
const editProduct = (prod) => {
    benefitDialog.value = true;
};
const confirmDeleteProduct = (prod) => {
    deleteBenefitDialog.value = true;
};
const deleteProduct = () => {
    deleteBenefitDialog.value = true;

    toast.add({
        severity: "success",
        summary: "Successful",
        detail: "Beneficio Eliminado",
        life: 3000,
    });
};

const confirmDeleteSelected = () => {
    deleteBenefitsDialog.value = true;
};
const deleteselectedBenefits = () => {
    toast.add({
        severity: "success",
        summary: "Successful",
        detail: "Beneficios Eliminados",
        life: 3000,
    });
};

const showSelect = () => {
    if (!tipo.value || !tipo.value.value) {
        intervalo.value = null;
    }
};



</script>

<template>
    <AppLayout title="Prestaciones">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'Prestaciones'" />
            <!-- {{ orders }} -->
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="container-fluid" id="kt_content_container">
                    <div class="card">
                        <Toast />
                        <Toolbar class="p-5">
                            <template #start>
                                <Button label="Añadir" icon="pi pi-plus" class="mr-2" @click="openNew" />
                                <Button label="Eliminar Seleccionados" icon="pi pi-trash" severity="danger" outlined
                                    @click="confirmDeleteSelected" :disabled="!selectedBenefits ||
                                        !selectedBenefits.length
                                        " />
                            </template>
                        </Toolbar>

                        <DataTable ref="dt" v-model:selection="selectedBenefits" :value="orders" dataKey="id" :rows="10"
                            :filters="filters" :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones">
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0">Prestaciones</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                                    </IconField>
                                </div>
                            </template>

                            <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
                            <Column field="id" header="Id" sortable style="min-width: 12rem"></Column>
                            <Column field="data.tranid" header="ORDEN DE COMPRAS" sortable style="min-width: 16rem">
                            </Column>

                            <Column field="data.estado" header="ESTATUS" sortable style="min-width: 16rem"
                                bodyClass="ml-2"></Column>
                            <Column field="data.fecha" header="FECHA" sortable style="min-width: 10rem"
                                bodyClass="ml-2"></Column>
                            <Column :exportable="false" style="min-width: 12rem">
                                <template #body="slotProps">
                                    <Button icon="pi pi-eye" outlined rounded severity="secondary" class="mr-2"
                                        @click="showOrders(slotProps.data.id)" />
                                    <Button icon="pi pi-file-pdf" outlined rounded severity="danger" class="mr-2"
                                        @click="confirmDeleteProduct()" />
                                    <Button icon="pi pi-file-excel" outlined rounded @click="editProduct()" />
                                </template>
                            </Column>
                        </DataTable>

                        <Dialog v-model:visible="showOrder" :style="{ width: '80%' }" header="ORDEN DE COMPRA"
                            :modal="true">


                            <template>
                                <div class="card">
                                    <DataView :value="selectedOrder" paginator :rows="5">
                                        <template>
                                            <div class="card">
                                                <DataView v-if="selectedOrder.value && selectedOrder.value.length > 0"
                                                    :value="selectedOrder.value" paginator :rows="5">
                                                    <template #list="slotProps">
                                                        <div class="flex flex-col">
                                                            <div v-for="item in slotProps.items" :key="item.id">
                                                                <div class="flex flex-col sm:flex-row sm:items-center p-6 gap-4"
                                                                    :class="{ 'border-t border-surface-200 dark:border-surface-700': item.id !== selectedOrder.value[0].id }">
                                                                    <div
                                                                        class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6">
                                                                        <div
                                                                            class="flex flex-row md:flex-col justify-between items-start gap-2">
                                                                            <div>
                                                                                <span
                                                                                    class="font-medium text-surface-500 dark:text-surface-400 text-sm">{{
                                                                                    item.class }}</span>
                                                                                <div class="text-lg font-medium mt-2">{{
                                                                                    item.description }}</div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex flex-col md:items-end gap-8">
                                                                            <span class="text-xl font-semibold">{{
                                                                                item.amount }}</span>
                                                                            <div
                                                                                class="flex flex-row-reverse md:flex-row gap-2">
                                                                                <Button icon="pi pi-eye"
                                                                                    label="Ver Detalles"
                                                                                    class="flex-auto md:flex-initial whitespace-nowrap"></Button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </DataView>
                                                <div v-else class="text-center p-6 text-surface-500">
                                                    Cargando datos o no hay órdenes de compra.
                                                </div>
                                            </div>
                                        </template>
                                    </DataView>
                                </div>
                            </template>


                            <template #footer>
                                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                                <Button label="Guardar" icon="pi pi-check" @click="saveBenefit()" />
                            </template>
                        </Dialog>

                        <!-- <Dialog v-model:visible="deleteBenefitDialog" :style="{ width: '450px' }" header="Confirm"
                            :modal="true">
                            <div class="flex items-center gap-4">
                                <i class="pi pi-exclamation-triangle !text-3xl" />
                                <span v-if="benefits.length">¿Estás seguro de que deseas eliminar?</span>

                            </div>
                            <template #footer>
                                <Button label="No" icon="pi pi-times" text @click="deleteBenefitDialog = false"
                                    severity="secondary" variant="text" />
                                <Button label="Si" icon="pi pi-check" text @click="deleteselectedBenefits"
                                    severity="danger" />
                            </template>
                        </Dialog>

                        <Dialog v-model:visible="deleteBenefitsDialog" :style="{ width: '450px' }" header="Confirmar"
                            :modal="true">
                            <div class="flex items-center gap-4">
                                <i class="pi pi-exclamation-triangle !text-3xl" />
                                <span v-if="product">¿Estás seguro de que deseas eliminar los
                                    registros seleccionados?</span>
                            </div>
                            <template #footer>
                                <Button label="No" icon="pi pi-times" text @click="deleteBenefitsDialog = false"
                                    severity="secondary" variant="text" />
                                <Button label="Si" icon="pi pi-check" text @click="deleteselectedBenefits"
                                    severity="danger" />
                            </template>
                        </Dialog> -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
