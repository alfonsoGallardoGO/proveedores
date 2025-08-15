<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import Card from 'primevue/card';
import { Link } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import ProgressBar from 'primevue/progressbar';

const props = defineProps({
    orders: Array,
});

const form = useForm({
    cantidades: {},
    factura: null,
    xml: null,
    supplier_id: null,
    supplier_purchase_order_id: null,
});

const toast = useToast();
const dt = ref();
const dtItems = ref();
const showOrder = ref(false);
const invoices = ref();
const isLooadingItems = ref(true);
const progress = ref(0);
const selectedOrder = ref(null);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const formatNumber = (rowData) => {
    const value = rowData.total;
    const safeValue = typeof value === 'number' && !isNaN(value) ? value : 0;

    return new Intl.NumberFormat('es-MX', {
        style: 'decimal',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(safeValue);
};

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
            <Header :title="'ORDENES DE COMPRA PENDIENTES'" />
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="container-fluid" id="kt_content_container">
                    <div class="card">
                        <Toast />

                        <Toast position="top-center" group="headless" @close="visible = false">
                            <template #container="{ message, closeCallback }">
                                <section class="flex flex-col p-4 gap-4 w-full bg-primary/70 rounded-xl">
                                    <div class="flex items-center gap-5">
                                        <i class="pi pi-cloud-upload text-white dark:text-black text-2xl"></i>
                                        <span class="font-bold text-base text-white dark:text-black">{{ message.summary
                                            }}</span>
                                    </div>
                                    <div class="flex flex-col gap-2">
                                        <ProgressBar :value="progress" :showValue="false" :style="{ height: '4px' }"
                                            pt:value:class="!bg-primary-50 dark:!bg-primary-900" class="!bg-primary/80">
                                        </ProgressBar>
                                        <label class="text-sm font-bold text-white dark:text-black">{{ progress }}%
                                            uploaded</label>
                                    </div>
                                    <div class="flex gap-4 mb-4 justify-end">
                                        <Button label="Another Upload?" size="small" @click="closeCallback"></Button>
                                        <Button label="Cancel" size="small" @click="closeCallback"></Button>
                                    </div>
                                </section>
                            </template>
                        </Toast>

                        <DataTable ref="dt"  :value="orders" dataKey="id" paginator
                            :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones"
                            sortField="id" :sortOrder="-1">
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
                            
                            <Column field="id" header="Id" sortable style="min-width: 2rem"></Column>
                            <Column field="purchase_order" header="Orden de compra" sortable style="min-width: 8rem"
                                bodyClass="ml-2"></Column>
                            <Column field="impuesto" header="Impuesto" sortable style="min-width: 8rem"
                                bodyClass="ml-2" :body="formatNumber">
                            </Column>
                            <Column field="subtotal" header="Subtotal" sortable style="min-width: 8rem"
                                bodyClass="ml-2" :body="formatNumber">
                            </Column>
                            <Column field="total" header="Total" sortable style="min-width: 8rem" bodyClass="ml-2"
                                :body="formatNumber">
                            </Column>


                            <Column field="status" header="Estatus" sortable style="min-width: 10rem">
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.status" :severity="getSeverity(slotProps.data.status)"
                                        rounded />
                                </template>
                            </Column>

                            <Column field="date" header="Fecha" sortable style="min-width: 8rem" bodyClass="ml-2">
                            </Column>
                            <Column :exportable="false" header="Acciones" style="min-width: 4rem">
                                <template #body="slotProps">
                                    <Link :href="route('purchase-orders.show', slotProps.data.id)">
                                        <Button 
                                            icon="pi pi-eye" 
                                            outlined rounded severity="warn" 
                                            class="mr-2"
                                        />
                                    </Link>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
