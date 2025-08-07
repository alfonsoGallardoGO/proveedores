<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";
import Card from 'primevue/card';
import { usePrimeVue } from 'primevue/config';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import FileUpload from "primevue/fileupload";
import Message from 'primevue/message';


const props = defineProps({
    invoices: Array,
    items: Array,
});

const toast = useToast();
const dtItems = ref();
// const $primevue = usePrimeVue();

// Variables y funciones para el input de PDF
// const totalPdfSize = ref(0);
// const totalPdfSizePercent = ref(0);
const pdfFiles = ref([]);
const xmlFiles = ref([]);
const fileSizeError = ref(null);
const maxFileSize = 1000000; // 1 MB en bytes
const pdfUploader = ref(null); // Asegúrate de tener esta referencia

// const onSelectedPdfFiles = (event) => {
//     pdfFiles.value = event.files;
//     if (pdfFiles.value.length > 0) {
//         toast.add({ severity: 'success', summary: 'PDF Seleccionado', detail: `Se seleccionó el archivo: ${pdfFiles.value[0].name}`, life: 3000 });
//     }
// };

// Propiedad computada para verificar si hay un PDF cargado
const hasPdfFile = computed(() => pdfFiles.value.length > 0);

// const uploadPdfEvent = (callback) => {
//     totalPdfSizePercent.value = totalPdfSize.value / 10;
//     callback();
// };

// const onPdfUpload = () => {
//     toast.add({ severity: "info", summary: "Éxito", detail: "Archivos PDF subidos", life: 3000 });
// };

// Variables y funciones para el input de XML
// const totalXmlSize = ref(0);
// const totalXmlSizePercent = ref(0);



// Variables reactivas para el mensaje de error y los archivos



// const onSelectedXmlFiles = (event) => {
//     xmlFiles.value = event.files;
//     if (xmlFiles.value.length > 0) {
//         toast.add({ severity: 'success', summary: 'XML Seleccionado', detail: `Se seleccionó el archivo: ${xmlFiles.value[0].name}`, life: 3000 });
//     }
// };


// const onFileSelect = (event, type) => {
//     const maxFileSize = 1000000; // 1 MB en bytes
//     const file = event.files[0];

//     // Esta parte de tu código ahora se ejecutará.
//     if (file.size > maxFileSize) {
//         console.log("El archivo excede el tamaño máximo.");
//         console.log("Tamaño del archivo:", file.size);
//         toast.add({
//             severity: 'error',
//             summary: 'Error de subida',
//             detail: `El archivo '${file.name}' excede el tamaño máximo de 1 MB.`,
//             life: 5000,
//         });
//         event.preventDefault(); // Esto es clave para que no se muestre el archivo en la interfaz
//     } else {
//         console.log("El archivo es válido.");
//         // Tu lógica de éxito
//         if (type === 'pdf') {
//             toast.add({ severity: 'success', summary: 'PDF Seleccionado', detail: `Se seleccionó el archivo: ${file.name}`, life: 3000 });
//         } else {
//             toast.add({ severity: 'success', summary: 'XML Seleccionado', detail: `Se seleccionó el archivo: ${file.name}`, life: 3000 });
//         }
//     }
// };

const hasXmlFile = computed(() => xmlFiles.value.length > 0);

// const uploadXmlEvent = (callback) => {
//     totalXmlSizePercent.value = totalXmlSize.value / 10;
//     callback();
// };

const onXmlUpload = () => {
    toast.add({ severity: "info", summary: "Éxito", detail: "Archivos XML subidos", life: 3000 });
};

const formatSize = (bytes) => {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};



const onFileSelect = (event) => {
    fileSizeError.value = null; // Reinicia el error

    const files = event.files;
    for (const file of files) {
        if (file.size > maxFileSize) {
            fileSizeError.value = `Error: El archivo "${file.name}" es demasiado grande. El tamaño máximo permitido es 1 MB.`;

            // Esta es la solución clave: llama al método 'clear()' del componente
            if (pdfUploader.value) {
                pdfUploader.value.clear();
            }

            console.log("Se ha evitado la carga del archivo:", file.name);
            return;
        }
    }
};

const onPdfUpload = (event) => {
    // Manejo de la carga exitosa
    pdfFiles.value = event.files;
    hasPdfFile.value = pdfFiles.value.length > 0;
    fileSizeError.value = null; // Limpia el error si la carga es exitosa
};

const hasBothFiles = computed(() => hasPdfFile.value && hasXmlFile.value);

const submitDocuments = () => {
    if (hasBothFiles.value) {
        form.factura = pdfFiles.value[0];
        form.xml = xmlFiles.value[0];

        router.post(route('ruta.enviar.documentos'), form, {
            onSuccess: () => {
                toast.add({
                    severity: 'success',
                    summary: 'Documentos subidos',
                    detail: 'Los archivos se han subido correctamente.',
                    life: 3000,
                });
                pdfFiles.value = [];
                xmlFiles.value = [];
            },
            onError: (errors) => {
                console.error(errors);
                toast.add({
                    severity: 'error',
                    summary: 'Error al subir',
                    detail: 'Hubo un problema al subir los archivos.',
                    life: 3000,
                });
            },
        });
    } else {
        toast.add({
            severity: 'warn',
            summary: 'Archivos faltantes',
            detail: 'Por favor, selecciona un PDF y un XML.',
            life: 3000,
        });
    }
};

// const onUploadError = (event) => {
//     const error = event.xhr.statusText;
//     toast.add({
//         severity: 'error',
//         summary: 'Error de subida',
//         detail: `El archivo seleccionado excede el tamaño máximo de 1MB. Por favor, selecciona un archivo más pequeño.`,
//         life: 5000,
//     });
// };

const form = useForm({
    cantidades: {},
    factura: null,
    xml: null,
    supplier_id: null,
    supplier_purchase_order_id: null,
});

// onMounted(() => {
//     setTimeout(() => {
//         isLooadingItems.value = false;
//     }, 3000);
// });

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
                        <!-- <div v-if="isLooadingItems" class="overlay">
                            <div class="spinner-container">
                                <VueSpinnerPuff size="120" color="violet" />
                                <p class="loading-text">CARGANDO DATOS...</p>
                            </div>
                        </div> -->

                        <div class="card flex justify-center">
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

                                <div class="tab-content">
                                    <div v-if="activeTab === 'Subir Archivos'" class="flex gap-4">
                                        <div class="card w-1/2 p-4 border rounded-lg shadow-sm">
                                            <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                                                <i class="pi pi-file-pdf text-red-500"></i>
                                                Subir Factura (PDF)
                                            </h4>
                                            <Message v-if="fileSizeError" severity="error">{{ fileSizeError }}</Message>
                                            <FileUpload ref="pdfUploader" v-model="pdfFiles" name="pdf[]"
                                                accept="application/pdf" @select="onFileSelect"
                                                :showUploadButton="false" :showCancelButton="false" :multiple="false"
                                                :customUpload="true" url="/api/upload">

                                                <template #header="{ chooseCallback, files }">
                                                    <div class="flex items-center gap-2">
                                                        <Button @click="chooseCallback()" icon="pi pi-file-pdf" rounded
                                                            variant="outlined" severity="secondary" />
                                                        <span v-if="!hasPdfFile"
                                                            class="text-sm text-gray-500">Seleccionar PDF</span>
                                                        <span v-else class="text-sm font-semibold text-green-600">
                                                            <i class="pi pi-check-circle mr-2" />
                                                            Archivo cargado: {{ pdfFiles[0]?.name }}
                                                        </span>
                                                    </div>
                                                </template>
                                                <template
                                                    #content="{ files, uploadedFiles, removeUploadedFileCallback, removeFileCallback }">
                                                    <div class="flex flex-col gap-8 pt-4">
                                                        <div v-for="file of files" :key="file.name"
                                                            class="p-4 border rounded-lg flex items-center justify-between shadow-sm">
                                                            <div class="flex items-center gap-4">
                                                                <i class="pi pi-file-pdf text-4xl text-red-500"></i>
                                                                <div>
                                                                    <span class="font-bold block">{{ file.name }}</span>
                                                                    <span class="text-sm text-gray-500">{{
                                                                        formatSize(file.size) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <Chip label="Pending"
                                                                    class="bg-orange-400 text-white font-bold" />
                                                                <Button icon="pi pi-times" severity="danger" text
                                                                    @click="removeFileCallback(file)" />
                                                            </div>
                                                        </div>
                                                        <div v-for="uploadedFile of uploadedFiles"
                                                            :key="uploadedFile.name"
                                                            class="p-4 border rounded-lg flex items-center justify-between shadow-sm">
                                                            <div class="flex items-center gap-4">
                                                                <i
                                                                    class="pi pi-check-circle text-4xl text-green-500"></i>
                                                                <div>
                                                                    <span class="font-bold block">{{ uploadedFile.name
                                                                        }}</span>
                                                                    <span class="text-sm text-gray-500">{{
                                                                        formatSize(uploadedFile.size) }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <Chip label="Uploaded"
                                                                    class="bg-green-500 text-white font-bold" />
                                                                <Button icon="pi pi-times" severity="danger" text
                                                                    @click="removeUploadedFileCallback(uploadedFile)" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </FileUpload>
                                        </div>

                                        <div class="card w-1/2 p-4 border rounded-lg shadow-sm">
                                            <h4 class="text-lg font-bold mb-4 flex items-center gap-2">
                                                <i class="pi pi-code text-green-600"></i>
                                                Subir XML
                                            </h4>
                                            <Message v-if="fileSizeError" severity="error">{{ fileSizeError }}</Message>
                                            <FileUpload ref="xmlUploader" v-model="xmlFiles" name="xml[]"
                                                url="/api/upload/xml" accept="text/xml" @select="onFileSelect"
                                                :showUploadButton="false" :showCancelButton="false" :multiple="false"
                                                :customUpload="true">
                                                <template #header="{ chooseCallback, files }">
                                                    <div class="flex items-center gap-2">
                                                        <Button @click="chooseCallback()" icon="pi pi-code" rounded
                                                            variant="outlined" severity="secondary" />
                                                        <span v-if="!hasXmlFile"
                                                            class="text-sm text-gray-500">Seleccionar XML</span>
                                                        <span v-else class="text-sm font-semibold text-green-600">
                                                            <i class="pi pi-check-circle mr-2" />
                                                            Archivo cargado: {{ xmlFiles[0]?.name }}
                                                        </span>
                                                    </div>
                                                </template>
                                                <template #content="{
                                                    files,
                                                    uploadedFiles,
                                                    removeUploadedFileCallback,
                                                    removeFileCallback,
                                                }">
                                                    <div class="flex flex-col gap-8 pt-4">
                                                        <div v-for="file of files" :key="file.name"
                                                            class="p-4 border rounded-lg flex items-center justify-between shadow-sm">
                                                            <div class="flex items-center gap-4">
                                                                <i class="pi pi-code text-4xl text-green-600"></i>
                                                                <div>
                                                                    <span class="font-bold block">{{ file.name }}</span>
                                                                    <span class="text-sm text-gray-500">{{
                                                                        formatSize(file.size)
                                                                        }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <Chip label="Pending"
                                                                    class="bg-orange-400 text-white font-bold" />
                                                                <Button icon="pi pi-times" severity="danger" text
                                                                    @click="removeFileCallback(file)" />
                                                            </div>
                                                        </div>
                                                        <div v-for="uploadedFile of uploadedFiles"
                                                            :key="uploadedFile.name"
                                                            class="p-4 border rounded-lg flex items-center justify-between shadow-sm">
                                                            <div class="flex items-center gap-4">
                                                                <i
                                                                    class="pi pi-check-circle text-4xl text-green-500"></i>
                                                                <div>
                                                                    <span class="font-bold block">{{ uploadedFile.name
                                                                        }}</span>
                                                                    <span class="text-sm text-gray-500">{{
                                                                        formatSize(uploadedFile.size)
                                                                        }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <Chip label="Uploaded"
                                                                    class="bg-green-500 text-white font-bold" />
                                                                <Button icon="pi pi-times" severity="danger" text
                                                                    @click="removeUploadedFileCallback(uploadedFile)" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </FileUpload>
                                        </div>
                                    </div>
                                    <div v-if="activeTab === 'Subir Archivos'" class="mt-6 flex justify-end">
                                        <Button label="Subir Documentos" icon="pi pi-cloud-upload" severity="help"
                                            :disabled="!hasBothFiles" @click="submitDocuments" />
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

                                                            <!-- <a v-if="slotProps.data.xml_route"
                                                                :href="`/storage/${slotProps.data.xml_route}`"
                                                                target="_blank"
                                                                class="flex items-center text-green-600 hover:text-green-800 transition"
                                                                aria-label="Descargar XML">
                                                                <i class="pi pi-code text-xl"></i>
                                                                <span
                                                                    class="ml-2 font-semibold hidden sm:inline">XML</span>
                                                            </a>
                                                            <div v-else class="flex items-center text-gray-400">
                                                                <i class="pi pi-file text-xl"></i>
                                                                <span class="ml-2 hidden sm:inline">Sin XML</span>
                                                            </div> -->
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
                            <!-- <div class="mt-10">
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
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
