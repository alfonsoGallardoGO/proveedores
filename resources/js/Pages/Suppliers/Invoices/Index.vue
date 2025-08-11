<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    orders: Array,
});

// onMounted(() => {
//     console.log('Datos de orders:', props.orders);
// });

const form = useForm({
    cantidades: {},
    factura: null,
    xml: null,
    supplier_id: null,
    supplier_purchase_order_id: null,
});

const toast = useToast();
const dt = ref();
const benefitDialog = ref(false);
const showFiltrer = ref(false);
const deleteBenefitsDialog = ref(false);
const op = ref(null);


const selectedBenefits = ref();
// const invoices = ref();


// Variables para los inputs (lo que el usuario selecciona)
const selectedStatus = ref(null);
const selectedDate = ref(null);

// Variables para los filtros que se aplicarán (solo cuando se haga clic en "Buscar")
const appliedStatus = ref(null);
const appliedDate = ref(null);

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
    showInvoice.value = false;
    showFiltrer.value = false;
    selectedOrder.value = null;
};


const selectedOrder = ref(null);
const invoices = ref([]);
const selectedInvoices = ref(null);
const currentOpenPopoverId = ref(null);
const invoicePdfRoute = ref(null);
const showInvoice = ref(false);

const handleInvoiceSelect = () => {
    if (selectedInvoices.value) {
        invoicePdfRoute.value = selectedInvoices.value;
        showInvoice.value = true;
    } else {
        invoicePdfRoute.value = null;
        showInvoice.value = false;
    }
};

const showCompletedInvoices = async (event, id) => {
    if (currentOpenPopoverId.value === id) {
        op.value.hide(event);
        currentOpenPopoverId.value = null;
        return;
    }

    if (op.value) {
        op.value.hide(event);
    }
    currentOpenPopoverId.value = id;

    try {
        op.value.show(event);
        const response = await axios.get(route('invoices.show', id));

        invoices.value = (response.data?.invoices || [])
            .filter(invoice => invoice.pdf_route)
            .map(invoice => {
                const fecha = new Date(invoice.created_at);
                const fechaFormateada = fecha.toLocaleDateString();
                return {
                    label: fechaFormateada,
                    value: invoice.pdf_route
                };
            });

    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

const filtrer = (prod) => {
    showFiltrer.value = true;
};

const statuses = ref([
    { name: 'Cerrada', code: 'Cerrada' },
    { name: 'Factura pendiente', code: 'Factura pendiente' },
    { name: 'Facturación pendiente/parcialmente recibido', code: 'Facturación pendiente/parcialmente recibido' },
    { name: 'Parcialmente recibida', code: 'Parcialmente recibida' },
    { name: 'Recepción pendiente', code: 'Recepción pendiente' },
    { name: 'Totalmente facturada', code: 'Totalmente facturada' }
]);

const applyFilters = () => {
    appliedStatus.value = selectedStatus.value;
    appliedDate.value = selectedDate.value;
    showFiltrer.value = false;
};

const closeFilters = () => {
    showFiltrer.value = false;
}

const clearFilters = () => {
    selectedStatus.value = null;
    selectedDate.value = null;

    appliedStatus.value = null;
    appliedDate.value = null;

    filters.value.global.value = null;
    showFiltrer.value = false;
};

const tableData = computed(() => {
    let result = props.orders;

    if (appliedStatus.value) {
        result = result.filter(order => order.status === appliedStatus.value.code);
    }

    if (appliedDate.value) {
        const selectedMonth = appliedDate.value.getMonth();
        const selectedYear = appliedDate.value.getFullYear();
        result = result.filter(order => {
            const orderDate = new Date(order.date);
            return (orderDate.getMonth() === selectedMonth) && (orderDate.getFullYear() === selectedYear);
        });
    }

    return result;
});

const getSeverity = (status) => {
    switch (status) {
        case "Recepción pendiente":
            return "warning";
        case "Cerrada":
            return "success";
        case "Parcialmente recibida":
            return "info";
        case "Factura pendiente":
            return "danger";
        case "Totalmente facturada":
            return "success";
        case "Facturación pendiente/parcialmente recibido":
            return "warning";
        default:
            return "secondary";
    }
};
</script>

<template>
    <AppLayout title="Ordenes de compra">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'ORDENES DE COMPRAS COMPLETAS'" />
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="container-fluid" id="kt_content_container">
                    <div class="card">
                        <Toast />
                        <Toolbar class="p-5">
                            <template #start>
                                <Button label="Filtros" icon="pi pi-filter-fill" severity="help" outlined
                                    @click="filtrer()" />
                            </template>
                        </Toolbar>
                        <DataTable ref="dt" v-model:selection="selectedBenefits" :value="tableData" dataKey="id"
                            paginator :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones">
                            <template #header>
                                <div class="flex flex-wrap gap-2 items-center justify-between">
                                    <h4 class="m-0"></h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText v-model="filters['global'].value" placeholder="Buscar..." />
                                    </IconField>
                                </div>
                            </template>

                            <!-- <Column
                                selectionMode="multiple"
                                style="width: 3rem"
                                :exportable="false"
                            ></Column> -->
                            <Column field="id" header="Id" sortable style="min-width: 8rem"></Column>
                            <Column field="purchase_order" header="Orden de compra" sortable style="min-width: 12rem"
                                bodyClass="ml-2"></Column>
                            <Column field="impuesto" header="Impuestos" sortable style="min-width: 8rem"
                                bodyClass="ml-2"></Column>
                            <Column field="subtotal" header="Subtotal" sortable style="min-width: 8rem"
                                bodyClass="ml-2"></Column>
                            <Column field="total" header="Total" sortable style="min-width: 8rem" bodyClass="ml-2">
                            </Column>
                            <Column field="status" header="Estatus" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.status" :severity="getSeverity(slotProps.data.status)"
                                        rounded />
                                </template>
                            </Column>
                            <Column field="date" header="Fecha" sortable style="min-width: 8rem" bodyClass="ml-2">
                            </Column>
                            <Column :exportable="false" header="Facturas" style="min-width: 13rem">
                                <template #body="slotProps">
                                    <!-- <Button icon="pi pi-file-pdf" outlined severity="danger" class="w-full py-0.5"
                                        @click="showCompletedInvoices($event, slotProps.data.id)" /> -->
                                    <Button 
                                        label="Facturas Cargadas"
                                        outlined 
                                        severity="danger" 
                                        @click="showCompletedInvoices($event, slotProps.data.id)" 
                                    />
                                </template>
                            </Column>
                        </DataTable>

                        <Dialog v-model:visible="showInvoice" :style="{ width: '80%' }" header="FACTURA" :modal="true">
                            <div class="card flex justify-center w-full h-full">
                                <iframe v-if="invoicePdfRoute" :src="`/${invoicePdfRoute}`" width="100%" height="600px"
                                    style="border: none;"></iframe>
                            </div>
                        </Dialog>
                        <!-- FILTROS PARA BUSCAR RESULTADOS EN TABLA -->
                        <Dialog v-model:visible="showFiltrer" header="Aplicar Filtros" :style="{ width: '35rem' }"
                            :position="'top'" :modal="true" :draggable="false">
                            <div class="flex items-center gap-4 mb-8">
                                <Select v-model="selectedStatus" :options="statuses" optionLabel="name"
                                    placeholder="Selecciona el estatus" class="w-full md:w-65" />
                            </div>
                            <div class="flex items-center gap-4 mb-8">
                                <DatePicker v-model="selectedDate" showIcon fluid view="month" dateFormat="mm/yy"
                                    iconDisplay="input" inputId="icondisplay" placeholder="Fecha"
                                    class="w-full md:w-65" />
                            </div>
                            <div class="flex justify-end gap-4">
                                <Button type="button" severity="contrast" @click="closeFilters"><i
                                        class="pi pi-times-circle">
                                        Cerrar</i></Button>
                                <Button type="button" severity="warn" @click="clearFilters"><i
                                        class="pi pi-filter-slash"> Eliminar
                                        Filtros</i></Button>
                                <Button type="button" label="Filtrar" @click="applyFilters"><i
                                        class="pi pi-check-circle"> Filtrar</i></Button>
                            </div>
                        </Dialog>
                        <Popover ref="op">
                            <div class="flex flex-col gap-4">
                                <Select v-model="selectedInvoices" filter :options="invoices" optionLabel="label"
                                    optionValue="value" placeholder="Selecciona una factura" class="w-full md:w-26"
                                    @change="handleInvoiceSelect" />
                            </div>
                        </Popover>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
