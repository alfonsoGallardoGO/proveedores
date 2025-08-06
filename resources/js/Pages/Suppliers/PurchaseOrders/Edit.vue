<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import axios from "axios";
import Card from 'primevue/card';

const props = defineProps({
    purchaseOrderId: Number,
});


onMounted(() => {

    if (props.purchaseOrderId) {
        show(props.purchaseOrderId, null);
    }
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

// const filters = ref({
//     global: { value: null, matchMode: FilterMatchMode.CONTAINS },
// });

const filtersItems = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});


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



</script>

<template>
    <AppLayout title="Ordenes de compra">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'ORDENES DE COMPRA PENDIENTES'" />
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div class="container-fluid" id="kt_content_container">
                    <div class="card">
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
                            <div class="mt-10">
                                <!-- <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2 mb-6">
                                    <i class="pi pi-upload text-indigo-500"></i>
                                    Subir Documentos
                                </h2> -->

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- <Card class="shadow-md rounded-xl">
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
                                    </Card> -->
                                    <div class="card">
                                        <Toast />
                                        <FileUpload name="demo[]" url="/api/upload" @upload="onTemplatedUpload($event)" :multiple="true" accept="image/*" :maxFileSize="1000000" @select="onSelectedFiles">
                                            <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                                                <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                                    <div class="flex gap-2">
                                                        <Button @click="chooseCallback()" icon="pi pi-images" rounded variant="outlined" severity="secondary"></Button>
                                                        <Button @click="uploadEvent(uploadCallback)" icon="pi pi-cloud-upload" rounded variant="outlined" severity="success" :disabled="!files || files.length === 0"></Button>
                                                        <Button @click="clearCallback()" icon="pi pi-times" rounded variant="outlined" severity="danger" :disabled="!files || files.length === 0"></Button>
                                                    </div>
                                                    <ProgressBar :value="totalSizePercent" :showValue="false" class="md:w-20rem h-1 w-full md:ml-auto">
                                                        <span class="whitespace-nowrap">{{ totalSize }}B / 1Mb</span>
                                                    </ProgressBar>
                                                </div>
                                            </template>
                                            <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
                                                <div class="flex flex-col gap-8 pt-4">
                                                    <div v-if="files.length > 0">
                                                        <h5>Pending</h5>
                                                        <div class="flex flex-wrap gap-4">
                                                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size" class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                                                <div>
                                                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100" height="50" />
                                                                </div>
                                                                <span class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                                <div>{{ formatSize(file.size) }}</div>
                                                                <Badge value="Pending" severity="warn" />
                                                                <Button icon="pi pi-times" @click="onRemoveTemplatingFile(file, removeFileCallback, index)" variant="outlined" rounded severity="danger" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div v-if="uploadedFiles.length > 0">
                                                        <h5>Completed</h5>
                                                        <div class="flex flex-wrap gap-4">
                                                            <div v-for="(file, index) of uploadedFiles" :key="file.name + file.type + file.size" class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                                                <div>
                                                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100" height="50" />
                                                                </div>
                                                                <span class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                                <div>{{ formatSize(file.size) }}</div>
                                                                <Badge value="Completed" class="mt-4" severity="success" />
                                                                <Button icon="pi pi-times" @click="removeUploadedFileCallback(index)" variant="outlined" rounded severity="danger" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template #empty>
                                                <div class="flex items-center justify-center flex-col">
                                                    <i class="pi pi-cloud-upload !border-2 !rounded-full !p-8 !text-4xl !text-muted-color" />
                                                    <p class="mt-6 mb-0">Drag and drop files to here to upload.</p>
                                                </div>
                                            </template>
                                        </FileUpload>                                        
                                    </div>
                                    <div class="card">
                                        <Toast />
                                        <FileUpload name="demo[]" url="/api/upload" @upload="onTemplatedUpload($event)" :multiple="true" accept="image/*" :maxFileSize="1000000" @select="onSelectedFiles">
                                            <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                                                <div class="flex flex-wrap justify-between items-center flex-1 gap-4">
                                                    <div class="flex gap-2">
                                                        <Button @click="chooseCallback()" icon="pi pi-images" rounded variant="outlined" severity="secondary"></Button>
                                                        <Button @click="uploadEvent(uploadCallback)" icon="pi pi-cloud-upload" rounded variant="outlined" severity="success" :disabled="!files || files.length === 0"></Button>
                                                        <Button @click="clearCallback()" icon="pi pi-times" rounded variant="outlined" severity="danger" :disabled="!files || files.length === 0"></Button>
                                                    </div>
                                                    <ProgressBar :value="totalSizePercent" :showValue="false" class="md:w-20rem h-1 w-full md:ml-auto">
                                                        <span class="whitespace-nowrap">{{ totalSize }}B / 1Mb</span>
                                                    </ProgressBar>
                                                </div>
                                            </template>
                                            <template #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
                                                <div class="flex flex-col gap-8 pt-4">
                                                    <div v-if="files.length > 0">
                                                        <h5>Pending</h5>
                                                        <div class="flex flex-wrap gap-4">
                                                            <div v-for="(file, index) of files" :key="file.name + file.type + file.size" class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                                                <div>
                                                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100" height="50" />
                                                                </div>
                                                                <span class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                                <div>{{ formatSize(file.size) }}</div>
                                                                <Badge value="Pending" severity="warn" />
                                                                <Button icon="pi pi-times" @click="onRemoveTemplatingFile(file, removeFileCallback, index)" variant="outlined" rounded severity="danger" />
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div v-if="uploadedFiles.length > 0">
                                                        <h5>Completed</h5>
                                                        <div class="flex flex-wrap gap-4">
                                                            <div v-for="(file, index) of uploadedFiles" :key="file.name + file.type + file.size" class="p-8 rounded-border flex flex-col border border-surface items-center gap-4">
                                                                <div>
                                                                    <img role="presentation" :alt="file.name" :src="file.objectURL" width="100" height="50" />
                                                                </div>
                                                                <span class="font-semibold text-ellipsis max-w-60 whitespace-nowrap overflow-hidden">{{ file.name }}</span>
                                                                <div>{{ formatSize(file.size) }}</div>
                                                                <Badge value="Completed" class="mt-4" severity="success" />
                                                                <Button icon="pi pi-times" @click="removeUploadedFileCallback(index)" variant="outlined" rounded severity="danger" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                            <template #empty>
                                                <div class="flex items-center justify-center flex-col">
                                                    <i class="pi pi-cloud-upload !border-2 !rounded-full !p-8 !text-4xl !text-muted-color" />
                                                    <p class="mt-6 mb-0">Drag and drop files to here to upload.</p>
                                                </div>
                                            </template>
                                        </FileUpload>                                        
                                    </div>
                                    <!-- <Card class="shadow-md rounded-xl">
                                        <template #title>
                                            <div class="flex items-center gap-2">
                                                <i class="pi pi-code text-green-600 text-xl"></i>
                                                <span class="font-semibold text-gray-700">Archivo XML</span>
                                            </div>
                                        </template>
                                        <template #content>
                                            <FileUpload mode="basic" name="xml" accept=".xml" :auto="false"
                                                @select="onXmlUpload" chooseLabel="Seleccionar XML" uploadLabel="Subir"
                                                cancelLabel="Cancelar" class="w-full" />
                                            <small class="block mt-2 text-gray-500">Solo archivos XML. Máximo
                                                1MB.</small>
                                        </template>
                                    </Card> -->
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
                                                    <a v-if="invoice.pdf_route" :href="`/storage/${invoice.pdf_route}`"
                                                        target="_blank"
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
                                                    <a v-if="invoice.xml_route" :href="`/storage/${invoice.xml_route}`"
                                                        target="_blank"
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
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>


                        <!-- <template #footer>
                            <Button label="Cancelar" icon="pi pi-times" text @click="showOrder = false" />
                            <Button label="Guardar" icon="pi pi-check" @click="store()" />
                        </template> -->