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
import Toast from 'primevue/toast';
import Ripple from 'primevue/ripple';

const props = defineProps({
    invoices: Array,
    items: Array,
    orders: Object,
    receipt: Array,
    supplier: Array,
});

onMounted(() => {
    console.log('Datos de invoices:', props.invoices);
    console.log('Datos de items:', props.items);
    console.log('Datos de orders:', props.orders);
    console.log('Datos de receipt:', props.receipt);
    console.log('Datos de proveedor:', props.supplier);
});


const toast = useToast();
const dtItems = ref();
const loading = ref(false);

const visible = ref(false);

const form = useForm({
    receipt_number: null,
    factura: null,
    xml: null,
    supplier_purchase_order_id: props.orders.id,
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

const onFacturaRemove = () => {
    form.factura = null;
    buttonLabelPdf.value = 'Seleccionar Factura';
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

const onXmlRemove = () => {
    form.xml = null;
    buttonLabelXml.value = 'Seleccionar XML';
};

const store = async () => {
    loading.value = true;

    try {

        // const hasEmptyFields = ;
        if (!form.receipt_number) {
            setTimeout(() => {
                loading.value = false;
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Agrega el número de recepción asignado.",
                    life: 4000,
                });
            }, 1000);
            return;
        }

        if (!form.factura && !form.xml) {
            setTimeout(() => {
                loading.value = false;
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Los documentos no pueden ir vacios, favor de cargar la Factura y el XML.",
                    life: 4000,
                });
            }, 1000);
            return;
        }

        if (!form.factura) {
            setTimeout(() => {
                loading.value = false;
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Favor de cargar la Factura.",
                    life: 4000,
                });
            }, 1000);
            return;
        }

        if (!form.xml) {
            setTimeout(() => {
                loading.value = false;
                toast.add({
                    severity: "error",
                    summary: "Error",
                    detail: "Favor de cargar el XML.",
                    life: 4000,
                });
            }, 1000);
            return;
        }

        const formData = new FormData();

        formData.append("receipt_number", form.receipt_number);
        formData.append("supplier_purchase_order_id", form.supplier_purchase_order_id);

        if (form.factura) formData.append("factura", form.factura);
        if (form.xml) formData.append("xml", form.xml);

        const response = await axios.post(route("purchase-orders.store"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });

        setTimeout(() => {
            loading.value = false;
            toast.add({
                severity: "success",
                summary: "Guardado",
                detail: "Factura registrada y artículos recibidos",
                life: 3000,
            });
            window.location.reload();
        }, 3000);

    } catch (error) {
        console.error(error);
        setTimeout(() => {
            loading.value = false;
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Hubo un problema al guardar los datos",
                life: 3000,
            });
        }, 2000);
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

// const activeTab = ref('Lista de Recepciones');

// const tabsItems = ref([
//     // { label: 'Subir Archivos', icon: 'pi pi-upload' },
//     { label: 'Lista de Recepciones', icon: 'pi pi-list' }
// ]);

const dialogVisible = ref(false);
// const currentDocumentUrl = ref('');

const showDocument = (url) => {
    // currentDocumentUrl.value = url;
    window.open(url, '_blank');
    // dialogVisible.value = true;
};

const formatDate = (dateString) => {
    if (!dateString) return '';

    const date = new Date(dateString);

    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();

    return `${day}-${month}-${year}`;
};


// const receptionData = ref([]);

const filtersReception = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

// Lógica para determinar la severidad de la etiqueta
const getSeverity = (status) => {
    switch (status) {
        case 'Factura Cargada':
            return 'success';
        case 'Pendiente de Factura':
            return 'danger';
        default:
            return null;
    }
};

// onMounted(() => {
//     const data = [];
//     if (props.receipt && props.receipt.length > 0) {
//         props.receipt.forEach(receiptItem => {
//             if (receiptItem && receiptItem.tranid) {
//                 const tranidFromReceipt = receiptItem.tranid;

//                 // Buscar la factura correspondiente en el array de invoices
//                 const invoiceExists = props.invoices.some(invoiceItem =>
//                     invoiceItem.receipt == tranidFromReceipt
//                 );

//                 // Determinar el estado
//                 const status = invoiceExists ? 'Factura Cargada' : 'Pendiente de Factura';

//                 data.push({
//                     tranid: tranidFromReceipt,
//                     status: status
//                 });
//             }
//         });
//     }
//     receptionData.value = data;
// });

const mergedReceptionData = computed(() => {
    if (!props.receipt || props.receipt.length === 0) {
        return [];
    }

    const invoicesMap = props.invoices.reduce((map, invoice) => {
        if (invoice.receipt) {
            map[invoice.receipt] = invoice;
        }
        return map;
    }, {});

    return props.receipt.map(receptionItem => {
        const invoice = invoicesMap[receptionItem.tranid];
        const combinedData = {
            ...receptionItem,
            status: invoice ? 'Factura Cargada' : 'Pendiente de Factura',
            invoice_id: invoice ? invoice.id : null,
            invoice_date: invoice ? invoice.created_at : null,
            pdf_route: invoice ? invoice.pdf_route : null,
            xml_route: invoice ? invoice.xml_route : null,
        };

        return combinedData;
    });
});

const showOrder = () => {
    // console.log('ID de la orden:', props.orders.id);
    // console.log('ID del proveedor:', props.supplier.id);
    // console.log('Nombre del proveedor:', props.supplier.name);
    // console.log('Todos los props de la orden:', props.orders);
    // console.log('Todos los props del proveedor:', props.supplier);
    if (props.orders && props.orders.id && props.supplier) {
        const url = route('generate.invoice', {
            id: props.orders.id,
            supplier_id: props.supplier.id,
            supplier_name: props.supplier.name,
        });

        window.open(url, '_blank');
    } else {
        console.error("Faltan datos de la orden o del proveedor.");
        setTimeout(() => {
            loading.value = false;
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Faltan datos de la orden o del proveedor",
                life: 3000,
            });
        }, 2000);
    }
};

// const showOrder = () => {
//     window.open(route('generate.invoice', { id: props.orders.id }));
// };

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
                            <div class="card">
                                <DataTable :value="receptionData" rowGroupMode="rowspan" groupRowsBy="number.order"
                                    :filters="filtersReception" sortMode="single" sortField="number.order"
                                    :sortOrder="1" tableStyle="min-width: 50rem" scrollable scrollHeight="200px">
                                    <template #header>
                                        <div class="flex flex-wrap gap-2 items-center justify-between">
                                            <Message severity="info" icon="pi pi-file" class="mr-2">
                                                <span class="text-lg font-bold">Orden de compra: {{
                                                    props.orders.purchase_order
                                                    }}</span>
                                            </Message>
                                            <IconField>
                                                <InputIcon>
                                                    <i class="pi pi-search" />
                                                </InputIcon>
                                                <InputText v-model="filtersReception['global'].value"
                                                    placeholder="Buscar..." />
                                            </IconField>
                                        </div>
                                    </template>
                                    <Column field="tranid" header="N. Recepción" style="min-width: 200px">
                                    </Column>

                                    <Column field="status" header="Status" style="min-width: 100px">
                                        <template #body="slotProps">
                                            <Tag :value="slotProps.data.status"
                                                :severity="getSeverity(slotProps.data.status)" />
                                        </template>
                                    </Column>

                                    <Column header="Acciones" style="min-width: 200px">
                                        <template #body="slotProps">
                                            <Button v-if="slotProps.data.status === 'Pendiente de Factura'"
                                                icon="pi pi-plus-circle" label="Agregar" severity="help" raised
                                                @click="visible = true" variant="outlined" />

                                            <Button v-else-if="slotProps.data.status === 'Factura Cargada'"
                                                icon="pi pi-check" label="Completado" severity="success" raised disabled
                                                @click="visible = true" variant="outlined" />
                                        </template>
                                    </Column>
                                </DataTable>
                            </div>
                            <DataTable ref="dtItems" :value="items" dataKey="id" paginator :rows="10"
                                :filters="filtersItems" :rowsPerPageOptions="[5, 10, 25]"
                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones">
                                <template #header>
                                    <div class="flex flex-wrap gap-2 items-center justify-between">
                                        <h4 class="m-0 text-1xl font-bold">Articulos de la orden de compra</h4>
                                        <Button label="Ver orden" icon="pi pi-file-pdf" severity="danger" class="ml-2"
                                            @click="showOrder" raised variant="outlined" />
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
                                            {{ Math.max(0, data.quantity - (data.deliveries_sum_amount ?? 0)) }}
                                        </span>
                                    </template>
                                </Column>
                                <Column header="Monto" style="min-width: 10rem">
                                    <template #body="{ data }">
                                        <span class="font-bold text-gray-800">{{ formatCurrency(data.amount)
                                        }}</span>
                                    </template>
                                </Column>
                            </DataTable>
                            <Dialog v-model:visible="visible" modal header="Agregar Número de Recepción"
                                :style="{ width: '50rem' }" position="top">
                                <span class="text-surface-500 dark:text-surface-400 block mb-8"></span>
                                <div class="flex flex-col items-center gap-4 mb-4">
                                    <label for="receipt_number" class="font-semibold">Número de Recepción</label>
                                    <InputText id="receipt_number" v-model="form.receipt_number" class="w-80"
                                        autocomplete="off" />
                                </div>
                                <div class="flex flex-row gap-4">
                                    <div class="card p-4 border rounded-lg shadow-sm flex-1">
                                        <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                                            <i class="pi pi-file-pdf text-red-500"></i>
                                            Subir Factura (PDF)
                                        </h4>
                                        <FileUpload name="factura" ref="facturaFileUpload" accept=".pdf" :auto="false"
                                            @select="onFacturaUpload" @remove="onFacturaRemove" :customUpload="true">
                                            <template #header="{ chooseCallback }">
                                                <Button :label="buttonLabelPdf" icon="pi pi-file-pdf"
                                                    @click="chooseCallback()" severity="danger"
                                                    class="p-button-sm p-button-rounded" outlined />
                                            </template>
                                        </FileUpload>
                                    </div>

                                    <div class="card p-4 border rounded-lg shadow-sm flex-1">
                                        <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                                            <i class="pi pi-code text-green-600"></i>
                                            Subir XML
                                        </h4>
                                        <FileUpload name="xml" ref="xmlFileUpload" accept=".xml" :auto="false"
                                            @select="onXmlUpload" @remove="onXmlRemove" :customUpload="true">
                                            <template #header="{ chooseCallback }">
                                                <Button :label="buttonLabelXml" icon="pi pi-file-excel"
                                                    @click="chooseCallback()" severity="success"
                                                    class="p-button-sm p-button-rounded" outlined />
                                            </template>
                                        </FileUpload>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-4">
                                    <Button type="button" label="Cancelar" severity="danger" @click="visible = false"
                                        icon="pi pi-times-circle"></Button>
                                    <Button label="Subir Documentos" icon="pi pi-cloud-upload" severity="info"
                                        :loading="loading" @click="store()"></Button>
                                </div>
                            </Dialog>
                            <!-- <div class="card"> -->
                                <!-- <div class="card">
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

                                <div class="tab-content0 mt-4"> -->
                                    <!-- <div v-if="activeTab === 'Subir Archivos'" class="flex gap-4">

                                    </div>
                                    <div v-if="activeTab === 'Subir Archivos'" class="flex flex-col gap-4">
                                        <div class="flex gap-4">
                                        </div>

                                        <div class="flex justify-center w-full">
                                            <div class="card p-4">
                                                <Button label="Subir Documentos" icon="pi pi-cloud-upload"
                                                    severity="help" :loading="loading" @click="store()" outlined
                                                    class="w-80" />
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- <div v-if="activeTab === 'Lista de Recepciones'">
                                        <div class="card"> -->
                                            <!-- <DataTable :value="invoices" stripedRows paginator :rows="10"
                                                :rowsPerPageOptions="[5, 10, 25]"
                                                currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} facturas"
                                                :filters="filtersInvoices">
                                                <template #header>
                                                    <div class="flex flex-wrap gap-2 items-center justify-between">
                                                        <h4 class="m-0">Recepciones Cargadas</h4>
                                                        <IconField>
                                                            <InputIcon>
                                                                <i class="pi pi-search" />
                                                            </InputIcon>
                                                            <InputText v-model="filtersInvoices['global'].value"
                                                                placeholder="Buscar..." />
                                                        </IconField>
                                                    </div>
                                                </template>

                                                <Column field="id" header="#Recepción" sortable
                                                    style="min-width: 12rem">
                                                    <template #body="slotProps">
                                                        <span class="font-semibold">{{ slotProps.data.receipt }}</span>
                                                    </template>
                                                </Column>
                                                <Column field="created_at" header="Fecha" sortable
                                                    style="min-width: 12rem">
                                                    <template #body="slotProps">
                                                        {{ formatDate(slotProps.data.created_at) }}
                                                    </template>
                                                </Column>
                                                <Column header="Documentos" style="min-width: 16rem">
                                                    <template #body="slotProps">
                                                        <div class="flex items-center gap-4">
                                                            <Button v-if="slotProps.data.pdf_route"
                                                                @click="showDocument(slotProps.data.pdf_route)"
                                                                label="Ver PDF" icon="pi pi-file-pdf"
                                                                class="p-button-sm p-button-outlined"
                                                                aria-label="Ver PDF" severity="danger" />
                                                            <span v-else class="flex items-center text-gray-400">
                                                                <i class="pi pi-file text-xl"></i>
                                                                <span class="ml-2 hidden sm:inline">Sin PDF</span>
                                                            </span>
                                                        </div>
                                                    </template>
                                                </Column>
                                            </DataTable> -->
                                        <!-- </div>
                                    </div> -->
                                <!-- </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
