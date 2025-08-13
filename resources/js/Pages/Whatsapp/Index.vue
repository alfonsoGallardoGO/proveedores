<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";

const props = defineProps({
    supplierWhatsapps: Array,
    supplierId: Number,
    supplierName: String,
});

console.log(props.supplierWhatsapps, props.supplierId, props.supplierName);

const whatsappList = ref(props.supplierWhatsapps);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const formatDateTime = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleString("en-GB", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        timeZone: "UTC",
    });
};

// Ejemplo
console.log(formatDateTime("2025-08-12T13:47:33.000000Z"));
</script>

<template>
    <AppLayout title="Whatsapps">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'Whatsapps'" />
            <div
                class="content d-flex flex-column flex-column-fluid"
                id="kt_content"
            >
                <div class="container-fluid" id="kt_content_container">
                    <h2 class="text-2xl font-semibold">
                        Lista de Whatsapps enviados del proveedor: #{{
                            props.supplierId
                        }}
                    </h2>
                    <h3 class="text-lg mt-10">
                        Proveedor: {{ props.supplierName }}
                    </h3>
                    <h3 class="text-lg mt-3">
                        Nombre de usuario: {{ $page.props.auth.user?.name }}
                    </h3>
                    <div class="card mt-10">
                        <DataTable
                            ref="dt"
                            :value="whatsappList"
                            dataKey="id"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} registros"
                        >
                            <Column
                                field="external_supplier_id"
                                header="Numero de proveedor"
                                sortable
                                style="min-width: 12rem"
                            ></Column>
                            <Column
                                field="supplier_name"
                                header="Proveedor"
                                sortable
                                style="min-width: 12rem"
                            ></Column>
                            <Column
                                field="phone"
                                header="Numero de telefono"
                                sortable
                                style="min-width: 12rem"
                            >
                            </Column>
                            <Column
                                field="updated_at"
                                header="Fecha de envio"
                                sortable
                                style="min-width: 12rem"
                            >
                                <template #body="slotProps">
                                    {{
                                        formatDateTime(
                                            slotProps.data.updated_at
                                        )
                                    }}
                                </template>
                            </Column>
                            <Column
                                field="pdf_rute"
                                header="Documento de recepcion"
                                style="min-width: 12rem"
                            >
                                <template #body="slotProps">
                                    <a
                                        :href="`https://proveedores.grupo-ortiz.site/${slotProps.data.pdf_rute}`"
                                        target="_blank"
                                        rounded
                                        class="mr-2"
                                    >
                                        <Button
                                            icon="pi pi-file-pdf"
                                            variant="outlined"
                                            rounded
                                            severity="warning"
                                        />
                                    </a>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
