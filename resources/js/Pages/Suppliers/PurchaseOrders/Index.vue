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

const props = defineProps({
    orders: Array,
});

onMounted(() => {
    console.log('Datos de orders:', props.orders);
});


const navigateToOrders = (id) => {
    router.visit(route('suppliers.orders-files'), {
        method: 'post',
        data: { purchaseOrderId: id },
    });
};


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

onMounted(() => {
    // props.orders.forEach(order => {
    //     order.items.forEach(data => {
    //         if (!form.cantidades[data.id]) {
    //             form.cantidades[data.id] = 0;
    //         }
    //     });
    // });
});

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const filtersItems = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});


const store = async () => {
    try {
        progress.value = 0;
        toast.add({
            severity: 'info',
            summary: 'Subiendo archivos...',
            group: 'headless',
            life: 999999,
        });

        const formData = new FormData();
        for (const [itemId, amount] of Object.entries(form.cantidades)) {
            formData.append(`cantidades[${itemId}]`, amount);
        }
        formData.append("supplier_id", form.supplier_id);
        formData.append("supplier_purchase_order_id", form.supplier_purchase_order_id);
        if (form.factura) formData.append("factura", form.factura);
        if (form.xml) formData.append("xml", form.xml);
        await axios.post(route("purchase-orders.store"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
            onUploadProgress: (event) => {
                if (event.total) {
                    progress.value = Math.round((event.loaded * 100) / event.total);
                }
            },
        });

        toast.removeGroup("headless");

        toast.add({
            severity: "success",
            summary: "Guardado",
            detail: "Datos guardados correctamente",
            life: 3000,
        });
    } catch (error) {
        console.error(error);

        toast.removeGroup("headless");

        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Hubo un problema al guardar",
            life: 3000,
        });
    } finally {
        progress.value = 0; // Reinicia el progreso
    }
};

const selectedOrder = ref(null);

const show = async (id, supplier) => {
    isLooadingItems.value = true;
    form.supplier_purchase_order_id = id;
    try {
        showOrder.value = true;
        const response = await axios.get(route("purchase-orders.show", id));
        selectedOrder.value = response.data?.items;
        invoices.value = response.data?.invoices;
        isLooadingItems.value = false;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

const onFacturaUpload = (event) => {
    const file = event.files[0];
    form.factura = file;
};

const onXmlUpload = (event) => {
    const file = event.files[0];
    form.xml = file;

};


const formatCurrency = (value) => {
    if (!value) return "$0.00";
    return new Intl.NumberFormat("es-MX", {
        style: "currency",
        currency: "MXN",
        minimumFractionDigits: 2,
    }).format(Number(value));
};


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

                        <DataTable ref="dt" v-model:selection="selectedBenefits" :value="orders" dataKey="id" paginator
                            :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]"
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
                            
                            <Column field="id" header="Id" sortable style="min-width: 12rem"></Column>
                            <Column field="purchase_order" header="Orden de compra" sortable style="min-width: 16rem"
                                bodyClass="ml-2"></Column>
                            <Column field="impuesto" header="Impuesto" sortable style="min-width: 16rem"
                                bodyClass="ml-2" :body="formatNumber">
                            </Column>
                            <Column field="subtotal" header="Subtotal" sortable style="min-width: 16rem"
                                bodyClass="ml-2" :body="formatNumber">
                            </Column>
                            <Column field="total" header="Total" sortable style="min-width: 16rem" bodyClass="ml-2"
                                :body="formatNumber">
                            </Column>


                            <Column field="status" header="Estatus" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    <Tag :value="slotProps.data.status" :severity="getSeverity(slotProps.data.status)"
                                        rounded />
                                </template>
                            </Column>

                            <Column field="date" header="Fecha" sortable style="min-width: 10rem" bodyClass="ml-2">
                            </Column>
                            <Column :exportable="false" header="Acciones" style="min-width: 12rem">
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

                        <!-- <Dialog v-model:visible="showOrder" :style="{ width: '80%' }" header="ORDEN DE COMPRA"
                            :modal="true">

                            <div class="card flex justify-center" v-if="isLooadingItems">
                                <ProgressSpinner :strokeWidth="6" :size="50" />
                            </div>

                            <div class="card flex justify-center" v-if="!isLooadingItems">
                                <DataTable ref="dtItems" :value="selectedOrder" dataKey="id" paginator :rows="10"
                                    :filters="filtersItems" :rowsPerPageOptions="[5, 10, 25]"
                                    currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones">
                                    <template #header>
                                        <div class="flex flex-wrap gap-2 items-center justify-between">
                                            <h4 class="m-0">Articulos de la orden de compra</h4>
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText v-model="filtersItems['global'].value"
                                                    placeholder="Buscar..." />
                                            </IconField>
                                        </div>
                                    </template>
                                    <Column field="id" header="Id" sortable style="min-width: 12rem"></Column>
                                    <Column header="Descripción" style="min-width: 16rem">
                                        <template #body="{ data }">
                                            <span class="font-medium text-gray-700">
                                                {{ data.description ?? data.memo }}
                                            </span>
                                        </template>
                                    </Column>
                                    <Column header="Cantidad Solicitada" style="min-width: 8rem">
                                        <template #body="{ data }">
                                            {{ data.quantity }}
                                        </template>
                                    </Column>
                                    <Column header="Cantidad Entregada" style="min-width: 8rem">
                                        <template #body="{ data }">
                                            {{ data.deliveries_sum_amount ?? 0 }}
                                        </template>
                                    </Column>
                                    <Column header="Faltan" style="min-width: 8rem">
                                        <template #body="{ data }">
                                            <span
                                                :class="(data.quantity - (data.deliveries_sum_amount ?? 0)) > 0 ? 'text-red-500 font-semibold' : 'text-green-600 font-semibold'">
                                                {{ data.quantity - (data.deliveries_sum_amount ?? 0) }}
                                            </span>
                                        </template>
                                    </Column>
                                    <Column header="Monto" style="min-width: 10rem">
                                        <template #body="{ data }">
                                            <span class="font-bold text-gray-800">{{ formatCurrency(data.amount)
                                                }}</span>
                                        </template>
                                    </Column>
                                    <Column header="Entrega" style="min-width: 12rem">
                                        <template #body="{ data }">
                                            <InputNumber v-model="form.cantidades[data.id]" :min="0"
                                                :max="data.quantity - (data.deliveries_sum_amount ?? 0)" showButtons
                                                inputClass="w-20 text-center" class="w-full" />
                                        </template>
                                    </Column>
                                </DataTable>
                                <div class="mt-10">
                                    <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2 mb-6">
                                        <i class="pi pi-upload text-indigo-500"></i>
                                        Subir Documentos
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <Card class="shadow-md rounded-xl">
                                            <template #title>
                                                <div class="flex items-center gap-2">
                                                    <i class="pi pi-file-pdf text-red-500 text-xl"></i>
                                                    <span class="font-semibold text-gray-700">Factura (PDF)</span>
                                                </div>
                                            </template>
                                            <template #content>
                                                <FileUpload mode="basic" name="factura" accept=".pdf" :auto="false"
                                                    @select="onFacturaUpload" chooseLabel="Seleccionar Factura"
                                                    uploadLabel="Subir" cancelLabel="Cancelar" class="w-full" />
                                                <small class="block mt-2 text-gray-500">Solo archivos PDF. Máximo
                                                    2MB.</small>
                                            </template>
                                        </Card>
                                        <Card class="shadow-md rounded-xl">
                                            <template #title>
                                                <div class="flex items-center gap-2">
                                                    <i class="pi pi-code text-green-600 text-xl"></i>
                                                    <span class="font-semibold text-gray-700">Archivo XML</span>
                                                </div>
                                            </template>
                                            <template #content>
                                                <FileUpload mode="basic" name="xml" accept=".xml" :auto="false"
                                                    @select="onXmlUpload" chooseLabel="Seleccionar XML"
                                                    uploadLabel="Subir" cancelLabel="Cancelar" class="w-full" />
                                                <small class="block mt-2 text-gray-500">Solo archivos XML. Máximo
                                                    1MB.</small>
                                            </template>
                                        </Card>
                                    </div>
                                </div>
                                <div class="mt-10">
                                    <h2 class="text-xl font-bold flex items-center gap-2 mb-6">
                                        <i class="pi pi-folder-open text-indigo-500"></i>
                                        Documentos de Facturación
                                    </h2>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

                                        <Card v-for="(invoice, index) in invoices" :key="index"
                                            class="shadow-md hover:shadow-xl transition-all duration-300 rounded-xl">
                                            <template #header>
                                                <div
                                                    class="flex items-center justify-between bg-gradient-to-r from-indigo-500 to-blue-600 px-4 py-3 rounded-t-xl">
                                                    <span class="font-semibold">Factura #{{ invoice.id }}</span>
                                                    <Chip :label="new Date(invoice.created_at).toLocaleDateString()"
                                                        class="text-xs text-indigo-600 px-2 py-1 rounded-md font-medium" />
                                                </div>
                                            </template>

                                            <template #content>
                                                <div class="flex flex-col items-center justify-center gap-4 py-6">
                                                    <div class="flex flex-col items-center">
                                                        <a v-if="invoice.pdf_route"
                                                            :href="`/storage/${invoice.pdf_route}`" target="_blank"
                                                            class="flex flex-col items-center text-red-500 hover:text-red-700 transition">
                                                            <i class="pi pi-file-pdf text-6xl"></i>
                                                            <span class="mt-2 text-sm font-semibold">PDF</span>
                                                        </a>
                                                        <div v-else class="flex flex-col items-center text-gray-400">
                                                            <i class="pi pi-file text-6xl"></i>
                                                            <span class="mt-2 text-sm">Sin PDF</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col items-center">
                                                        <a v-if="invoice.xml_route"
                                                            :href="`/storage/${invoice.xml_route}`" target="_blank"
                                                            class="flex flex-col items-center text-green-600 hover:text-green-800 transition">
                                                            <i class="pi pi-code text-6xl"></i>
                                                            <span class="mt-2 text-sm font-semibold">XML</span>
                                                        </a>
                                                        <div v-else class="flex flex-col items-center text-gray-400">
                                                            <i class="pi pi-file text-6xl"></i>
                                                            <span class="mt-2 text-sm">Sin XML</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </Card>
                                    </div>
                                </div>
                            </div>

                            <template #footer>
                                <Button label="Cancelar" icon="pi pi-times" text @click="showOrder = false" />
                                <Button label="Guardar" icon="pi pi-check" @click="store()" />
                            </template>
                        </Dialog> -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
