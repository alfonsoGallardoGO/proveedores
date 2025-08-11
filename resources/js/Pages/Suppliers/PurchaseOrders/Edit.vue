<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import FileUpload from "primevue/fileupload";
import Message from 'primevue/message';


const props = defineProps({
    invoices: Array,
    items: Array,
});

// onMounted(() => {
//     console.log('Datos de invoices:', props.invoices);
//     console.log('Datos de items:', props.items);
// });


const toast = useToast();
const dtItems = ref();
const progress = ref(0);

const form = useForm({
    cantidades: {},
    factura: null,
    xml: null,
    supplier_purchase_order_id: null,
});

onMounted(() => {
    if (props.items && props.items.length > 0) {
        form.supplier_purchase_order_id = props.items[0].supplier_purchase_order_id;

        props.items.forEach(item => {
            if (!form.cantidades[item.id]) {
                form.cantidades[item.id] = 0;
            }
        });
    }
});

const buttonLabelPdf = ref('Seleccionar Factura');
const buttonLabelXml = ref('Seleccionar XML');

const onFacturaUpload = (event) => {
    const file = event.files[0];
    form.factura = file;
    if (file) {
        buttonLabelPdf.value = 'Factura seleccionada';
    } else {
        buttonLabelPdf.value = 'Seleccionar Factura';
    }
};

const onXmlUpload = (event) => {
    const file = event.files[0];
    form.xml = file;

    if (file) {
        buttonLabelXml.value = 'XML seleccionado';
    } else {
        buttonLabelXml.value = 'Seleccionar XML';
    }
};


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
            // console.log(`El item con ID ${itemId} tiene una cantidad de ${amount}.`);
            formData.append(`cantidades[${itemId}]`, amount);
        }

        // formData.append("supplier_id", form.supplier_id);
        formData.append("supplier_purchase_order_id", form.supplier_purchase_order_id);

        if (form.factura) formData.append("factura", form.factura);
        if (form.xml) formData.append("xml", form.xml);

        if (!form.factura && !form.xml) {
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Hubo un problema al guardar",
                life: 3000,
            });
        } else {
            await axios.post(route("purchase-orders.store"), formData, {
                headers: { "Content-Type": "multipart/form-data" },
                onUploadProgress: (event) => {
                    if (event.total) {
                        progress.value = Math.round((event.loaded * 100) / event.total);
                    }
                },
            });

            toast.add({
                severity: "success",
                summary: "Guardado",
                detail: "Datos guardados correctamente",
                life: 3000,
            });
        }

    } catch (error) {
        console.error(error);

        toast.add({
            severity: "error",
            summary: "Error",
            detail: "Hubo un problema al guardar los datos",
            life: 3000,
        });
    } finally {
        progress.value = 0;
    }
};

const filtersItems = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const filtersInvoices = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const formatCurrency = (value) => {
    if (!value) return "$0.00";
    return new Intl.NumberFormat("es-MX", {
        style: "currency",
        currency: "MXN",
        minimumFractionDigits: 2,
    }).format(Number(value));
};

const activeTab = ref('Subir Archivos');

const tabsItems = ref([
    { label: 'Subir Archivos', icon: 'pi pi-upload' },
    { label: 'Lista de Documentos', icon: 'pi pi-list' }
]);

const dialogVisible = ref(false);
const currentDocumentUrl = ref('');

const showDocument = (url) => {
    currentDocumentUrl.value = url;
    dialogVisible.value = true;
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
                        <div class="card flex justify-center">
                            <Toolbar class="p-5">
                                <template #start>
                                    <Link :href="route('purchase-orders.index')" class="menu-link" as="button">
                                    <Button label="Regresar" icon="pi pi-arrow-circle-left" severity="help" outlined />
                                    </Link>
                                </template>
                            </Toolbar>
                            <DataTable ref="dtItems" :value="items" dataKey="id" paginator :rows="10"
                                :filters="filtersItems" :rowsPerPageOptions="[5, 10, 25]"
                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones">
                                <template #header>
                                    <div class="flex flex-wrap gap-2 items-center justify-between">
                                        <h4 class="m-0">Articulos de la orden de compra</h4>
                                        <IconField>
                                            <InputIcon>
                                                <i class="pi pi-search" />
                                            </InputIcon>
                                            <InputText v-model="filtersItems['global'].value" placeholder="Buscar..." />
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
                            <div class="card">
                                <div class="card">
                                    <Tabs :value="activeTab">
                                        <TabList>
                                            <Tab v-for="tab in tabsItems" :key="tab.label" :value="tab.label"
                                                @click="activeTab = tab.label">
                                                <a v-ripple class="flex items-center gap-2 text-inherit">
                                                    <i :class="tab.icon" />
                                                    <span>{{ tab.label }}</span>
                                                </a>
                                            </Tab>
                                        </TabList>
                                    </Tabs>
                                </div>

                                <div class="tab-content0 mt-4">
                                    <div v-if="activeTab === 'Subir Archivos'" class="flex gap-4">
                                        <div class="card w-1/2 p-4 border rounded-lg shadow-sm">
                                            <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                                                <i class="pi pi-file-pdf text-red-500"></i>
                                                Subir Factura (PDF)
                                            </h4>
                                            <FileUpload name="factura" accept=".pdf" :auto="false"
                                                @select="onFacturaUpload" :customUpload="true">
                                                <template #header="{ chooseCallback }">
                                                    <Button :label="buttonLabelPdf" icon="pi pi-file-pdf"
                                                        @click="chooseCallback()" severity="error"
                                                        class="p-button-sm p-button-rounded" outlined />
                                                </template>
                                                <template #content="{ files }">
                                                    <div v-for="file of files" :key="file.name"
                                                        class="p-4 flex items-center justify-between">
                                                        <span class="flex items-center gap-2">
                                                            <i class="pi pi-check-circle text-green-500" />
                                                            {{ file.name }}
                                                        </span>
                                                    </div>
                                                </template>
                                            </FileUpload>
                                        </div>
                                        <div class="card w-1/2 p-4 border rounded-lg shadow-sm">
                                            <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                                                <i class="pi pi-code text-green-600"></i>
                                                Subir XML
                                            </h4>
                                            <FileUpload name="xml" accept=".xml" :auto="false" @select="onXmlUpload"
                                                :customUpload="true">
                                                <template #header="{ chooseCallback }">
                                                    <Button :label="buttonLabelXml" icon="pi pi-file-excel"
                                                        @click="chooseCallback()" severity="success"
                                                        class="p-button-sm p-button-rounded" outlined />
                                                </template>
                                                <template #content="{ files }">
                                                    <div v-for="file of files" :key="file.name"
                                                        class="p-4 flex items-center justify-between">
                                                        <span class="flex items-center gap-2">
                                                            <i class="pi pi-check-circle text-green-500" />
                                                            {{ file.name }}
                                                        </span>
                                                    </div>
                                                </template>
                                            </FileUpload>
                                        </div>
                                    </div>
                                    <div v-if="activeTab === 'Subir Archivos'" class="flex flex-col gap-4">
                                        <div class="flex gap-4">
                                        </div>

                                        <div class="flex justify-center w-full">
                                            <div class="card p-4">
                                                <Button label="Subir Documentos" icon="pi pi-cloud-upload"
                                                    severity="help" @click="store()" outlined class="w-80" />
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="activeTab === 'Lista de Documentos'">
                                        <div class="card">
                                            <DataTable :value="invoices" stripedRows paginator :rows="10"
                                                :rowsPerPageOptions="[5, 10, 25]"
                                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} facturas"
                                                :filters="filtersInvoices">
                                                <template #header>
                                                    <div class="flex flex-wrap gap-2 items-center justify-between">
                                                        <h4 class="m-0">Documentos Cargados</h4>
                                                        <IconField>
                                                            <InputIcon>
                                                                <i class="pi pi-search" />
                                                            </InputIcon>
                                                            <InputText v-model="filtersInvoices['global'].value"
                                                                placeholder="Buscar..." />
                                                        </IconField>
                                                    </div>
                                                </template>

                                                <Column field="id" header="Factura #" sortable style="min-width: 12rem">
                                                    <template #body="slotProps">
                                                        <span class="font-semibold">#{{ slotProps.data.id }}</span>
                                                    </template>
                                                </Column>
                                                <Column field="created_at" header="Fecha" sortable
                                                    style="min-width: 12rem">
                                                    <template #body="slotProps">
                                                        {{ new Date(slotProps.data.created_at).toLocaleDateString() }}
                                                    </template>
                                                </Column>
                                                <Column header="Documentos" style="min-width: 16rem">
                                                    <template #body="slotProps">
                                                        <div class="flex items-center gap-4">
                                                            <Button v-if="slotProps.data.pdf_route"
                                                                @click="showDocument(`/storage/${slotProps.data.pdf_route}`)"
                                                                label="Ver PDF" icon="pi pi-file-pdf"
                                                                class="p-button-sm p-button-outlined"
                                                                aria-label="Ver PDF" />
                                                            <span v-else class="flex items-center text-gray-400">
                                                                <i class="pi pi-file text-xl"></i>
                                                                <span class="ml-2 hidden sm:inline">Sin PDF</span>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </Column>
                                            </DataTable>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <Dialog v-model:visible="dialogVisible" modal :style="{ width: '80vw' }"
                                header="Visualización de Factura">
                                <div v-if="currentDocumentUrl" class="h-[80vh] w-full">
                                    <iframe :src="currentDocumentUrl" class="w-full h-full border-none"
                                        title="Documento"></iframe>
                                </div>
                                <div v-else class="text-center text-gray-500">
                                    <Message severity="error" variant="outlined">No se ha seleccionado ningún documento
                                        para ver.</Message>
                                </div>
                            </Dialog>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
