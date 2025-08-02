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

const form = useForm({
    cantidades: {},
    factura: null,
    xml: null,
    supplier_id: null,
    supplier_purchase_order_id: null,
});

const toast = useToast();
const dt = ref();
const showOrder = ref(false);
const benefitDialog = ref(false);
const deleteBenefitDialog = ref(false);
const deleteBenefitsDialog = ref(false);

const selectedBenefits = ref();
const invoices = ref();

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

const store = () => {
    const formData = new FormData();
    for (const [itemId, amount] of Object.entries(form.cantidades)) {
        formData.append(`cantidades[${itemId}]`, amount);
    }
    formData.append("supplier_id", form.supplier_id);
    formData.append(
        "supplier_purchase_order_id",
        form.supplier_purchase_order_id
    );
    if (form.factura) {
        formData.append("pdf", form.factura);
    }
    if (form.xml) {
        formData.append("xml", form.xml);
    }
    axios
        .post(route("purchase-orders.store"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        })
        .then(() => {
            toast.add({
                severity: "success",
                summary: "Guardado",
                detail: "Datos guardados correctamente",
                life: 3000,
            });
        })
        .catch((err) => {
            console.error(err);
            toast.add({
                severity: "error",
                summary: "Error",
                detail: "Hubo un problema al guardar",
                life: 3000,
            });
        });
};

const selectedOrder = ref(null);

const show = async (id, supplier) => {
    form.supplier_purchase_order_id = id;
    // form.supplier_id = supplier;
    // console.log(supplier.data)
    try {
        showOrder.value = true;
        const response = await axios.get(route("purchase-orders.show", id));
        console.log(response)
        selectedOrder.value = response.data?.items;
        invoices.value = response.data?.invoices;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

const onFacturaSelect = (event) => {
    if (event.files && event.files.length > 0) {
        form.factura = event.files[0];
    }
};

const onXmlSelect = (event) => {
    if (event.files && event.files.length > 0) {
        form.xml = event.files[0];
    }
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
            <Header :title="'Prestaciones'" />
            <div
                class="content d-flex flex-column flex-column-fluid"
                id="kt_content"
            >
                <div class="container-fluid" id="kt_content_container">
                    <div class="card">
                        <Toast />
                        <Toolbar class="p-5">
                            <template #start>
                                <Button
                                    label="Añadir"
                                    icon="pi pi-plus"
                                    class="mr-2"
                                    @click="openNew"
                                />
                                <Button
                                    label="Eliminar Seleccionados"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    outlined
                                    @click="confirmDeleteSelected"
                                    :disabled="
                                        !selectedBenefits ||
                                        !selectedBenefits.length
                                    "
                                />
                            </template>
                        </Toolbar>

                        <DataTable
                            ref="dt"
                            v-model:selection="selectedBenefits"
                            :value="orders"
                            dataKey="id"
                            paginator
                            :rows="10"
                            :filters="filters"
                            :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} prestaciones"
                        >
                            <template #header>
                                <div
                                    class="flex flex-wrap gap-2 items-center justify-between"
                                >
                                    <h4 class="m-0">Ordenes de compra</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText
                                            v-model="filters['global'].value"
                                            placeholder="Buscar..."
                                        />
                                    </IconField>
                                </div>
                            </template>

                            <Column
                                selectionMode="multiple"
                                style="width: 3rem"
                                :exportable="false"
                            ></Column>
                            <Column
                                field="id"
                                header="Id"
                                sortable
                                style="min-width: 12rem"
                            ></Column>
                            <Column
                                field="data.tranid"
                                header="Orden de compra"
                                sortable
                                style="min-width: 16rem"
                            >
                            </Column>

                            <Column
                                field="data.estado"
                                header="Estatus"
                                sortable
                                style="min-width: 16rem"
                                bodyClass="ml-2"
                            ></Column>
                            <Column
                                field="data.fecha"
                                header="Fecha"
                                sortable
                                style="min-width: 10rem"
                                bodyClass="ml-2"
                            ></Column>
                            <Column
                                :exportable="false"
                                header="Acciones"
                                style="min-width: 12rem"
                            >
                                <template #body="slotProps">
                                    <Button
                                        icon="pi pi-eye"
                                        outlined
                                        rounded
                                        severity="secondary"
                                        class="mr-2"
                                        @click="
                                            show(
                                                slotProps.data.id,
                                                slotProps.data
                                            )
                                        "
                                    />

                                    <Button
                                        icon="pi pi-file-pdf"
                                        outlined
                                        rounded
                                        severity="danger"
                                        class="mr-2"
                                        @click="confirmDeleteProduct()"
                                    />
                                    <Button
                                        icon="pi pi-file-excel"
                                        outlined
                                        rounded
                                        @click="editProduct()"
                                    />
                                </template>
                            </Column>
                        </DataTable>

                        <Dialog
                            v-model:visible="showOrder"
                            :style="{ width: '80%' }"
                            header="ORDEN DE COMPRA"
                            :modal="true"
                        >
                            <div class="card p-4">
                                <DataView :value="selectedOrder" paginator :rows="5">
                                    <template #list="slotProps">
                                        <div class="flex flex-col">
                                            <div
                                                v-for="(
                                                    item, index
                                                ) in slotProps.items"
                                                :key="index"
                                            >
                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center p-6 gap-4 bg-white rounded-lg shadow-sm mt-2"
                                                    :class="{
                                                        'border-t border-surface-200 dark:border-surface-700':
                                                            index !== 0,
                                                    }"
                                                >
                                                    <div
                                                        class="md:w-40 relative text-center"
                                                    >
                                                        <i
                                                            class="pi pi-box text-6xl text-gray-400"
                                                        ></i>
                                                    </div>
                                                    <div
                                                        class="flex flex-col md:flex-row justify-between md:items-center flex-1 gap-6"
                                                    >
                                                        <div
                                                            class="flex flex-col gap-2"
                                                        >
                                                            <span
                                                                class="font-medium text-surface-500 text-sm"
                                                                >{{
                                                                    item.class
                                                                }}</span
                                                            >
                                                            <div
                                                                class="text-lg font-semibold"
                                                            >
                                                                {{
                                                                    item.description ??
                                                                    item.memo
                                                                }}
                                                            </div>
                                                            <div
                                                                class="flex items-center gap-3 mt-2"
                                                            >
                                                                <label
                                                                    class="text-sm font-medium text-gray-600"
                                                                >
                                                                    Cantidad
                                                                    Entregada
                                                                </label>
                                                                <InputNumber
                                                                    v-model="
                                                                        form
                                                                            .cantidades[
                                                                            item
                                                                                .id
                                                                        ]
                                                                    "
                                                                    :min="0"
                                                                    :max="
                                                                        item.quantity -
                                                                        (item.deliveries_sum_amount ??
                                                                            0)
                                                                    "
                                                                    :step="1"
                                                                    inputClass="w-28 text-center border rounded-lg shadow-sm"
                                                                    showButtons
                                                                    buttonLayout="horizontal"
                                                                    decrementButtonClass="p-button-outlined p-button-secondary"
                                                                    incrementButtonClass="p-button-outlined p-button-secondary"
                                                                    incrementButtonIcon="pi pi-plus"
                                                                    decrementButtonIcon="pi pi-minus"
                                                                />
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex flex-col md:items-end gap-3"
                                                        >
                                                            <span
                                                                class="text-xl font-bold text-gray-700"
                                                            >
                                                                {{
                                                                    formatCurrency(
                                                                        item.amount
                                                                    )
                                                                }}
                                                            </span>

                                                            <span
                                                                class="text-sm text-gray-500"
                                                                >Cantidad
                                                                Solicitada:
                                                                {{
                                                                    item.quantity
                                                                }}</span
                                                            >
                                                            <span
                                                                class="text-sm text-gray-500"
                                                                >Cantidad
                                                                Entregada:
                                                                {{
                                                                    item.deliveries_sum_amount ??
                                                                    0
                                                                }}</span
                                                            >
                                                            <span
                                                                class="text-sm text-gray-500"
                                                            >
                                                                Faltan
                                                                {{
                                                                    item.quantity -
                                                                    (item.deliveries_sum_amount ??
                                                                        0)
                                                                }}
                                                                por entregar
                                                            </span>
                                                            <Tag
                                                                :value="
                                                                    item.type
                                                                "
                                                                severity="info"
                                                                class="uppercase font-bold"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </DataView>
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6"
                                >
                                    <div
                                        v-for="(invoice, index) in invoices"
                                        :key="index"
                                        class="bg-white border rounded-2xl shadow-md p-5 flex flex-col items-center justify-between hover:shadow-xl hover:-translate-y-1 transition transform"
                                    >
                                        <div
                                            class="w-14 h-14 flex items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-lg font-bold shadow-lg"
                                        >
                                            #{{ invoice.id }}
                                        </div>

                                        <div class="mt-3 text-center">
                                            <p class="text-sm text-gray-600">
                                                Proveedor:
                                                {{ invoice.supplier_id }}
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                {{
                                                    new Date(
                                                        invoice.created_at
                                                    ).toLocaleDateString()
                                                }}
                                            </p>
                                        </div>

                                        <div class="flex gap-6 mt-5">
                                            <a
                                                v-if="invoice.pdf_route"
                                                :href="`/${invoice.pdf_route}`"
                                                target="_blank"
                                                class="flex flex-col items-center text-red-500 hover:text-red-700 transition"
                                                title="Ver PDF"
                                            >
                                                <i
                                                    class="pi pi-file-pdf text-4xl"
                                                ></i>
                                                <span
                                                    class="text-sm mt-1 font-semibold"
                                                    >PDF</span
                                                >
                                            </a>
                                            <div
                                                v-else
                                                class="flex flex-col items-center text-gray-400"
                                            >
                                                <i
                                                    class="pi pi-file text-4xl"
                                                ></i>
                                                <span class="text-sm mt-1"
                                                    >Sin PDF</span
                                                >
                                            </div>

                                            <a
                                                v-if="invoice.xml_route"
                                                :href="`/${invoice.xml_route}`"
                                                target="_blank"
                                                class="flex flex-col items-center text-green-600 hover:text-green-800 transition"
                                                title="Ver XML"
                                            >
                                                <i
                                                    class="pi pi-code text-4xl"
                                                ></i>
                                                <span
                                                    class="text-sm mt-1 font-semibold"
                                                    >XML</span
                                                >
                                            </a>
                                            <div
                                                v-else
                                                class="flex flex-col items-center text-gray-400"
                                            >
                                                <i
                                                    class="pi pi-file text-4xl"
                                                ></i>
                                                <span class="text-sm mt-1"
                                                    >Sin XML</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex mt-2">
                                <div class="col-md-6">
                                    <div class="card p-4">
                                        <h3 class="text-lg font-semibold mb-3">
                                            📄 Subir Factura
                                        </h3>
                                        <FileUpload
                                            mode="basic"
                                            name="pdf"
                                            :auto="false"
                                            :multiple="false"
                                            accept=".pdf,image/*"
                                            :maxFileSize="2000000"
                                            @select="onFacturaSelect"
                                        >
                                            <template #empty>
                                                <span
                                                    >Arrastra tu Factura aquí o
                                                    haz clic para
                                                    seleccionarla.</span
                                                >
                                            </template>
                                        </FileUpload>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card p-4">
                                        <h3 class="text-lg font-semibold mb-3">
                                            📄 Subir XML
                                        </h3>
                                        <FileUpload
                                            mode="basic"
                                            name="xml"
                                            :auto="false"
                                            :multiple="false"
                                            accept=".xml"
                                            :maxFileSize="1000000"
                                            @select="onXmlSelect"
                                        >
                                            <template #empty>
                                                <span
                                                    >Arrastra tu archivo XML
                                                    aquí o haz clic para
                                                    seleccionarlo.</span
                                                >
                                            </template>
                                        </FileUpload>
                                    </div>
                                </div>
                            </div>
                            <template #footer>
                                <Button
                                    label="Cancelar"
                                    icon="pi pi-times"
                                    text
                                    @click="showOrder = false"
                                />
                                <Button
                                    label="Guardar"
                                    icon="pi pi-check"
                                    @click="store()"
                                />
                            </template>
                        </Dialog>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
