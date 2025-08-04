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
//     console.log('Datos de orders:', props.orders); //mostrar en consola loss datos del array "orders"
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
const showInvoice = ref(false);
const benefitDialog = ref(false);
const showFiltrer = ref(false);
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
    showInvoice.value = false;
    showFiltrer.value = false;
    selectedOrder.value = null;
};


const selectedOrder = ref(null);

const show = async (id, supplier) => {
    form.supplier_purchase_order_id = id;
    // form.supplier_id = supplier;
    // console.log(supplier.data)
    try {
        showInvoice.value = true;
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

const filtrer = (prod) => {
    showFiltrer.value = true;
};

const selectedStatus = ref(null);
const selectedDate = ref(null);

const statuses = ref([
    { name: 'Cerrada', code: 'CLOSED' },
    { name: 'Factura pendiente', code: 'INVOICE_PENDING' },
    { name: 'Facturación pendiente/parcialmente recibido', code: 'PARTIALLY_RECEIVED_INVOICE_PENDING' },
    { name: 'Parcialmente recibida', code: 'PARTIALLY_RECEIVED' },
    { name: 'Recepción pendiente', code: 'RECEIVE_PENDING' },
    { name: 'Totalmente facturada', code: 'FULLY_INVOICED' }
]);

const filteredOrders = computed(() => {
    if (!selectedStatus.value && !selectedDate.value) {
        return props.orders;
    }

    return props.orders.filter(order => {
        let statusMatch = true;
        let dateMatch = true;

        if (selectedStatus.value) {
            statusMatch = order.status === selectedStatus.value.code;
        }

        if (selectedDate.value) {
            const orderDate = new Date(order.date);
            const selectedMonth = selectedDate.value.getMonth();
            const selectedYear = selectedDate.value.getFullYear();

            dateMatch = (orderDate.getMonth() === selectedMonth) && (orderDate.getFullYear() === selectedYear);
        }
        return statusMatch && dateMatch;
    });
});


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
                                    label="Filtros"
                                    icon="pi pi-filter-fill"
                                    severity="help" 
                                    outlined
                                    @click="
                                            filtrer()
                                        "
                                />
                                <!-- <Button
                                    label="Eliminar Seleccionados"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    outlined
                                    @click="confirmDeleteSelected"
                                    :disabled="
                                        !selectedBenefits ||
                                        !selectedBenefits.length
                                    "
                                /> -->
                            </template>
                        </Toolbar>
                        <DataTable
                            ref="dt"
                            v-model:selection="selectedBenefits"
                            :value="filteredOrders"
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

                            <!-- <Column
                                selectionMode="multiple"
                                style="width: 3rem"
                                :exportable="false"
                            ></Column> -->
                            <Column
                                field="id"
                                header="Id"
                                sortable
                                style="min-width: 12rem"
                            ></Column>
                            <Column
                                field="status"
                                header="Estatus"
                                sortable
                                style="min-width: 16rem"
                                bodyClass="ml-2"
                            ></Column>
                            <Column
                                field="date"
                                header="Fecha"
                                sortable
                                style="min-width: 10rem"
                                bodyClass="ml-2"
                            ></Column>
                            <Column
                                :exportable="false"
                                header="Ver Factura"
                                style="min-width: 2rem"
                            >
                                <template #body="slotProps">
                                    <Button
                                        icon="pi pi-file-pdf"
                                        outlined
                                        rounded
                                        severity="danger"
                                        class="mr-2"
                                        @click="
                                            show(
                                                slotProps.data.id,
                                                slotProps.data
                                            )
                                        "
                                    />
                                </template>
                            </Column>
                        </DataTable>

                        <Dialog 
                            v-model:visible="showInvoice"
                            :style="{ width: '80%' }"
                            header="FACTURA FINALIZADA"
                            :modal="true"
                        >
                            <div class="card flex justify-center w-full h-full">
                                <iframe 
                                v-if="invoice.pdf_route"
                                :href="`/${invoice.pdf_route}`"
                                width="100%" 
                                height="600px" 
                                style="border: none;"></iframe>
                            </div>
                        </Dialog>
                        <Dialog 
                            v-model:visible="showFiltrer" 
                            :modal="true"
                            header="AÑADIR FILTROS DE BÚSQUEDA"
                            :style="{ width: '25rem' }"
                        >
                            <div class="flex items-center gap-4 mb-4">
                                <label for="estatus" class="font-semibold w-24">Estatus</label>
                                <Dropdown 
                                    v-model="selectedStatus" 
                                    :options="statuses" 
                                    optionLabel="name" 
                                    placeholder="Selecciona un estatus" 
                                    class="w-full md:w-14rem" 
                                />
                            </div>
                            <div class="flex items-center gap-4 mb-8">
                                <label for="date" class="font-semibold w-24">Fecha</label>
                                <Calendar 
                                    v-model="selectedDate" 
                                    view="month" 
                                    dateFormat="mm/yy" 
                                    placeholder="Selecciona un mes" 
                                    class="w-full"
                                    :showIcon="true"
                                />
                            </div>
                            <div class="flex justify-end gap-2">
                                <Button type="button" severity="contrast" @click="visible = false"><i class="pi pi-times-circle"> Cerrar</i></Button>
                                <Button type="button" severity="warn" @click="visible = false"><i class="pi pi-filter-slash"> Eliminar Filtros</i></Button>
                                <Button type="button" label="Filtrar" @click="visible = false"><i class="pi pi-check-circle"> Filtrar</i></Button>
                            </div>
                        </Dialog>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

