<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    suppliers: Array,
});

const supplierForm = useForm({
    id: null,
    external_id: null,
    nombre: "",
    descripcion: "",
    tipo: "",
    intervalo: null,
    dia_corte: null,
    condicionado_faltas: false,
    condicionado_seniority: false,
    condicionado_eficiencia: false,
    company_name: "",
    legal_name_company: "",
    tax: "",
    email: "",
    phone: "",
    address: "",
    currency: "",
    category: "",
});

const tipo = ref(null);
const intervalo = ref(null);
// const supplierForm = ref(null);


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
const supplierDialog = ref(false);
const deletesupplierDialog = ref(false);
const deleteBenefitsDialog = ref(false);
const product = ref({});
const selectedBenefits = ref();


const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const openNew = () => {
    supplierDialog.value = true;
};
const hideDialog = () => {
    supplierDialog.value = false;
    submitted.value = false;
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
const show = async (id) => {
    try {
        const response = await axios.get(`/suppliers/${id}`);
        const data = response.data;

        supplierForm.id                = data.id || null;
        supplierForm.external_id       = data.external_id || null;
        supplierForm.nombre            = data.name || data.legal_name_company || "";
        supplierForm.descripcion       = data.descripcion || "";
        supplierForm.tipo              = data.type || "";
        supplierForm.intervalo         = data.intervalo || null;
        supplierForm.dia_corte         = data.dia_corte || null;
        supplierForm.condicionado_faltas     = data.condicionado_faltas || false;
        supplierForm.condicionado_seniority  = data.condicionado_seniority || false;
        supplierForm.condicionado_eficiencia = data.condicionado_eficiencia || false;
        supplierForm.company_name      = data.company_name || "";
        supplierForm.legal_name_company= data.legal_name_company || "";
        supplierForm.tax               = data.tax || "";
        supplierForm.email             = data.email || "";
        supplierForm.phone             = data.phone || "";
        supplierForm.address           = data.address || "";
        supplierForm.currency          = data.currency || "";
        supplierForm.category          = data.category || "";

        supplierDialog.value = true; 
    } catch (error) {
        console.error("Error al cargar proveedor:", error);
    }
};



const confirmDeleteProduct = (prod) => {
    deletesupplierDialog.value = true;
};
const deleteProduct = () => {
    deletesupplierDialog.value = true;

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
    <AppLayout title="Proveedores">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'Proveedores'" />
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
                            :value="suppliers"
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
                                    <h4 class="m-0">Prestaciones</h4>
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
                            <Column header="Nombre" sortable style="min-width: 16rem">
                                <template #body="slotProps">
                                    {{ slotProps.data.legal_name_company || slotProps.data.name }}
                                </template>
                            </Column>
                            <Column
                                field="tax"
                                header="RFC"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                field="phone"
                                header="Telefono"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                field="email"
                                header="Correo"
                                sortable
                                style="min-width: 16rem"
                            ></Column>

                            <Column
                                :exportable="false"
                                style="min-width: 12rem"
                            >
                                <template #body="slotProps">
                                    <Button
                                        icon="pi pi-pencil"
                                        outlined
                                        rounded
                                        class="mr-2"
                                        @click="show(slotProps.data.id)"
                                    />
                                    <Button
                                        icon="pi pi-trash"
                                        outlined
                                        rounded
                                        severity="danger"
                                        @click="confirmDeleteProduct()"
                                    />
                                </template>
                            </Column>
                        </DataTable>
                        <Dialog
                            v-model:visible="supplierDialog"
                            :style="{ width: '450px' }"
                            header="Nueva Prestación"
                            :modal="true"
                        >
                            <div class="flex flex-col gap-6 p-6">   
                               <div class="flex flex-col gap-6">
                                
                                    <div class="flex flex-col">
                                        <label class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                            <i class="pi pi-user text-blue-500"></i> Nombre
                                        </label>
                                        <InputText v-model="supplierForm.nombre" placeholder="Nombre proveedor" class="w-full p-inputtext-sm" />
                                    </div>

                                    <div class="flex flex-col">
                                        <label class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                            <i class="pi pi-align-left text-purple-500"></i> Descripción
                                        </label>
                                        <InputText v-model="supplierForm.descripcion" placeholder="Descripción" class="w-full p-inputtext-sm" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                            <i class="pi pi-id-card text-green-500"></i> RFC
                                        </label>
                                        <InputText v-model="supplierForm.tax" placeholder="RFC del proveedor" class="w-full p-inputtext-sm" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                            <i class="pi pi-envelope text-red-400"></i> Correo
                                        </label>
                                        <InputText v-model="supplierForm.email" placeholder="correo@ejemplo.com" class="w-full p-inputtext-sm" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                            <i class="pi pi-phone text-teal-500"></i> Teléfono
                                        </label>
                                        <InputText v-model="supplierForm.phone" placeholder="+52 000 000 0000" class="w-full p-inputtext-sm" />
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="text-sm font-semibold text-gray-700 mb-1 flex items-center gap-2">
                                            <i class="pi pi-map-marker text-orange-500"></i> Dirección
                                        </label>
                                        <Textarea v-model="supplierForm.address" rows="3" placeholder="Calle, número, colonia, ciudad, CP" class="w-full p-inputtext-sm" />
                                    </div>
                                </div>
                            </div>

                            <template #footer>
                                <Button
                                    label="Cancel"
                                    icon="pi pi-times"
                                    text
                                    @click="hideDialog"
                                />
                                <Button
                                    label="Guardar"
                                    icon="pi pi-check"
                                    @click="saveBenefit()"
                                />
                            </template>
                        </Dialog>

                        <Dialog
                            v-model:visible="deletesupplierDialog"
                            :style="{ width: '450px' }"
                            header="Confirm"
                            :modal="true"
                        >
                            <div class="flex items-center gap-4">
                                <i
                                    class="pi pi-exclamation-triangle !text-3xl"
                                />
                                <span v-if="benefits.length"
                                    >¿Estás seguro de que deseas eliminar?</span
                                >
                            </div>
                            <template #footer>
                                <Button
                                    label="No"
                                    icon="pi pi-times"
                                    text
                                    @click="deletesupplierDialog = false"
                                    severity="secondary"
                                    variant="text"
                                />
                                <Button
                                    label="Si"
                                    icon="pi pi-check"
                                    text
                                    @click="deleteselectedBenefits"
                                    severity="danger"
                                />
                            </template>
                        </Dialog>

                        <Dialog
                            v-model:visible="deleteBenefitsDialog"
                            :style="{ width: '450px' }"
                            header="Confirmar"
                            :modal="true"
                        >
                            <div class="flex items-center gap-4">
                                <i
                                    class="pi pi-exclamation-triangle !text-3xl"
                                />
                                <span v-if="product"
                                    >¿Estás seguro de que deseas eliminar los
                                    registros seleccionados?</span
                                >
                            </div>
                            <template #footer>
                                <Button
                                    label="No"
                                    icon="pi pi-times"
                                    text
                                    @click="deleteBenefitsDialog = false"
                                    severity="secondary"
                                    variant="text"
                                />
                                <Button
                                    label="Si"
                                    icon="pi pi-check"
                                    text
                                    @click="deleteselectedBenefits"
                                    severity="danger"
                                />
                            </template>
                        </Dialog>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
