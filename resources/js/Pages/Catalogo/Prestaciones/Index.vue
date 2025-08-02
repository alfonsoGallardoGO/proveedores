<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, onMounted, computed } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useToast } from "primevue/usetoast";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    benefits: Array,
});

const form = useForm({
    nombre: "",
    descripcion: "",
    tipo: null,
    intervalo: null,
    dia_corte: null,
    condicionado_faltas: false,
    condicionado_seniority: false,
    condicionado_eficiencia: false,
});

const tipo = ref(null);
const intervalo = ref(null);

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
const benefitDialog = ref(false);
const deleteBenefitDialog = ref(false);
const deleteBenefitsDialog = ref(false);
const product = ref({});
const selectedBenefits = ref();

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
</script>

<template>
    <AppLayout title="Prestaciones">
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
                            :value="benefits"
                            dataKey="id"
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
                            <Column
                                field="name"
                                header="Prestación"
                                sortable
                                style="min-width: 16rem"
                            ></Column>

                            <Column
                                field="conditioned_seniority"
                                header="Condicionado Antiguedad"
                                sortable
                                style="min-width: 10rem"
                                bodyClass="ml-2"
                            >
                                <template #body="{ data }">
                                    <i
                                        class="pi"
                                        :class="{
                                            'pi-check-circle text-green-500':
                                                data.conditioned_seniority,
                                            'pi-times-circle text-red-400':
                                                !data.conditioned_seniority,
                                        }"
                                    ></i>
                                </template>
                            </Column>
                            <Column
                                field="conditioned_efficiency"
                                header="Condicionado Eficiencia"
                                sortable
                                style="min-width: 10rem"
                                bodyClass="ml-2"
                            >
                                <template #body="{ data }">
                                    <i
                                        class="pi"
                                        :class="{
                                            'pi-check-circle text-green-500':
                                                data.conditioned_efficiency,
                                            'pi-times-circle text-red-400':
                                                !data.conditioned_efficiency,
                                        }"
                                    ></i>
                                </template>
                            </Column>
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
                                        @click="editProduct()"
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
                            v-model:visible="benefitDialog"
                            :style="{ width: '450px' }"
                            header="Nueva Prestación"
                            :modal="true"
                        >
                            <div class="flex flex-col gap-6 p-6">
                                <div class="mt-2 w-full">
                                    <FloatLabel variant="on" class="w-full">
                                        <InputText
                                            id="nombre"
                                            v-model="form.nombre"
                                            class="w-full"
                                        />
                                        <label for="nombre">Nombre</label>
                                    </FloatLabel>
                                    <small
                                        v-if="submitted && !form.nombre"
                                        class="text-red-500"
                                        >El nombre es requerido.</small
                                    >
                                </div>
                                <div class="mt-2 w-full">
                                    <FloatLabel variant="on" class="w-full">
                                        <Textarea
                                            id="descripcion"
                                            v-model="form.descripcion"
                                            rows="5"
                                            cols="30"
                                            style="resize: none"
                                            class="w-full"
                                        />
                                        <label for="descripcion"
                                            >Descripcion</label
                                        >
                                    </FloatLabel>
                                    <small
                                        v-if="submitted && !form.descripcion"
                                        class="text-red-500"
                                        >La descripción es requerida.</small
                                    >
                                </div>

                                <FloatLabel class="w-full" variant="on">
                                    <Select
                                        v-model="form.tipo"
                                        inputId="tipo"
                                        :options="[
                                            { name: 'Mes(es)', value: 'mes' },
                                            { name: 'Día(s)', value: 'dia' },
                                        ]"
                                        showClear
                                        optionLabel="name"
                                        class="w-full"
                                        @change="showSelect"
                                    />
                                    <label for="tipo">Tipo</label>
                                </FloatLabel>
                                <small
                                    v-if="submitted && !form.tipo"
                                    class="text-red-500"
                                    >El tipo es requerido.</small
                                >

                                <FloatLabel class="w-full" variant="on">
                                    <InputNumber
                                        v-model="form.intervalo"
                                        inputId="intervalo"
                                        prefix="Cada "
                                        :suffix="intervaloSufijo"
                                        class="w-full"
                                    />
                                    <label for="intervalo">Intervalo</label>
                                </FloatLabel>
                                <small
                                    v-if="submitted && !form.intervalo"
                                    class="text-red-500"
                                    >El intervalo es requerido.</small
                                >

                                <FloatLabel class="w-full" variant="on">
                                    <InputNumber
                                        v-model="form.dia_corte"
                                        inputId="minmax"
                                        :min="0"
                                        :max="30"
                                        fluid
                                    />
                                    <label for="dia_corte">Día de corte</label>
                                </FloatLabel>
                                <small
                                    v-if="submitted && !form.dia_corte"
                                    class="text-red-500"
                                    >El dia de corte es requerido.</small
                                >

                                <div class="flex flex-col gap-4 mt-2">
                                    <div class="flex gap-2">
                                        <Checkbox
                                            v-model="form.condicionado_faltas"
                                            inputId="condicionado_faltas"
                                            name="size"
                                            value="Normal"
                                        />
                                        <label
                                            for="condicionado_faltas"
                                            class="text-base"
                                            >Condicionado Faltas</label
                                        >
                                    </div>

                                    <div class="flex gap-2">
                                        <Checkbox
                                            v-model="
                                                form.condicionado_seniority
                                            "
                                            inputId="condicionado_seniority"
                                            name="size"
                                            value="Normal"
                                        />
                                        <label
                                            for="condicionado_seniority"
                                            class="text-base"
                                            >Condicionado Antiguedad</label
                                        >
                                    </div>
                                    <div class="flex gap-2">
                                        <Checkbox
                                            v-model="
                                                form.condicionado_eficiencia
                                            "
                                            inputId="condicionado_eficiencia"
                                            name="size"
                                            value="Normal"
                                        />
                                        <label
                                            for="condicionado_eficiencia"
                                            class="text-base"
                                            >Condicionado Eficiencia</label
                                        >
                                    </div>
                                </div>
                                <Button
                                    label="Añadir Reglas de Eficiencia"
                                    severity="info"
                                    @click="agregarRegla"
                                />
                                <div
                                    v-for="(regla, index) in reglas"
                                    :key="index"
                                    class="flex gap-2 w-full"
                                >
                                    <div>
                                        <FloatLabel class="w-full" variant="on">
                                            <InputNumber
                                                v-model="regla.monto"
                                                inputId="monto_{{ index }}"
                                                fluid
                                                mode="decimal"
                                                :minFractionDigits="0"
                                                :maxFractionDigits="0"
                                            />
                                            <label for="integeronly"
                                                >Monto</label
                                            >
                                        </FloatLabel>

                                        <small
                                            v-if="
                                                reglas.length > 0 &&
                                                submitted &&
                                                !regla.monto
                                            "
                                            class="text-red-500"
                                            >El monto es requerido.</small
                                        >
                                    </div>

                                    <div>
                                        <InputNumber
                                            v-model="regla.otro"
                                            inputId="otro_{{ index }}"
                                            fluid
                                        />
                                    </div>
                                    <div>
                                        <FloatLabel class="w-full" variant="on">
                                            <InputNumber
                                                v-model="regla.porcentaje"
                                                inputId="porcentaje_{{ index }}"
                                                fluid
                                                mode="decimal"
                                                :minFractionDigits="0"
                                                :maxFractionDigits="0"
                                            />
                                            <label for="integeronly"
                                                >Porcentaje</label
                                            >
                                        </FloatLabel>

                                        <small
                                            v-if="
                                                reglas.length > 0 &&
                                                submitted &&
                                                !regla.porcentaje
                                            "
                                            class="text-red-500"
                                            >El porcentaje es requerido.</small
                                        >
                                    </div>
                                    <div>
                                        <Button
                                            icon="pi pi-trash"
                                            severity="danger"
                                            aria-label="Eliminar"
                                            @click="eliminarRegla(index)"
                                        />
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
                            v-model:visible="deleteBenefitDialog"
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
                                    @click="deleteBenefitDialog = false"
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
