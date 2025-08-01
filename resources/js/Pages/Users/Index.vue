<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useForm } from "@inertiajs/inertia-vue3";

const props = defineProps({
    users: Array,
});

const selectedUsers = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const userDialog = ref(false);
const deleteProductDialog = ref(false);
const user = useForm({
    name: "",
    email: "",
    password: "",
    rfc: "",
    username: "",
    phone_number: "",
    profile_photo_path: "",
});

const openNew = () => {
    user.reset();
    userDialog.value = true;
};

const confirmDeleteSelected = () => {
    if (selectedUsers.value.length) {
        console.log("Confirm delete selected users", selectedUsers.value);
        deleteProductDialog.value = true;
    } else {
        console.log("No users selected for deletion");
    }
};

console.log(props.users);
</script>

<template>
    <AppLayout title="Usuarios">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <Header :title="'Usuarios'" />
            <div
                class="content d-flex flex-column flex-column-fluid"
                id="kt_content"
            >
                <div class="container-fluid" id="kt_content_container">
                    <div class="card">
                        <Toolbar class="mb-6">
                            <template #start>
                                <Button
                                    label="Añadir Usuario"
                                    icon="pi pi-plus"
                                    class="mr-2"
                                    @click="openNew"
                                />
                                <Button
                                    label="Eliminar Seleccionados"
                                    icon="pi pi-trash"
                                    severity="danger"
                                    variant="outlined"
                                    @click="confirmDeleteSelected"
                                    :disabled="
                                        !selectedUsers || !selectedUsers.length
                                    "
                                />
                            </template>

                            <template #end>
                                <FileUpload
                                    mode="basic"
                                    accept="image/*"
                                    :maxFileSize="1000000"
                                    label="Import"
                                    customUpload
                                    chooseLabel="Import"
                                    class="mr-2"
                                    auto
                                    :chooseButtonProps="{
                                        severity: 'secondary',
                                    }"
                                />
                                <Button
                                    label="Export"
                                    icon="pi pi-upload"
                                    severity="secondary"
                                    @click="exportCSV($event)"
                                />
                            </template>
                        </Toolbar>
                        <DataTable
                            ref="dt"
                            v-model:selection="selectedUsers"
                            :value="props.users"
                            dataKey="id"
                            :paginator="true"
                            :rows="10"
                            :filters="filters"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                            :rowsPerPageOptions="[5, 10, 25]"
                            currentPageReportTemplate="Mostrando {first} a {last} de {totalRecords} usuarios"
                        >
                            <template #header>
                                <div
                                    class="flex flex-wrap gap-2 items-center justify-between"
                                >
                                    <h4 class="m-0">Administrar Usuarios</h4>
                                    <IconField>
                                        <InputIcon>
                                            <i class="pi pi-search" />
                                        </InputIcon>
                                        <InputText
                                            v-model="filters['global'].value"
                                            placeholder="Search..."
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
                                header="ID"
                                sortable
                                :filterPlaceholder="'Search by ID'"
                                :filter="true"
                                :filterField="'id'"
                            ></Column>
                            <Column header="Perfil" style="min-width: 5rem">
                                <template #body="slotProps">
                                    <img
                                        :src="`http://localhost:8000/storage/${slotProps.data.profile_photo_path}`"
                                        :alt="slotProps.data.image"
                                        class="rounded-full object-cover"
                                        style="width: 40px; height: 40px"
                                    />
                                </template>
                            </Column>
                            <Column
                                field="name"
                                header="Nombre"
                                sortable
                                style="min-width: 16rem"
                            ></Column>
                            <Column
                                header="RFC"
                                sortable
                                style="min-width: 16rem"
                            >
                                <template #body="slotProps">
                                    <Tag
                                        icon="pi pi-user"
                                        severity="info"
                                        :value="slotProps.data.rfc"
                                    ></Tag>
                                </template>
                            </Column>
                            <Column
                                field="username"
                                header="Nombre de Usuario"
                                sortable
                                style="min-width: 16rem"
                            ></Column>

                            <Column
                                field="email"
                                header="Correo Electronico"
                                sortable
                                style="min-width: 20rem"
                            ></Column>
                            <Column
                                field="phone_number"
                                header="Numero de Telefono"
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
                                        variant="outlined"
                                        rounded
                                        class="mr-2"
                                        @click="editProduct(slotProps.data)"
                                    />
                                    <Button
                                        icon="pi pi-eye"
                                        variant="outlined"
                                        rounded
                                        severity="info"
                                        class="mr-2"
                                        @click="
                                            confirmDeleteProduct(slotProps.data)
                                        "
                                    />
                                    <Button
                                        icon="pi pi-trash"
                                        variant="outlined"
                                        rounded
                                        severity="danger"
                                        @click="
                                            confirmDeleteProduct(slotProps.data)
                                        "
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                    <Dialog
                        v-model:visible="userDialog"
                        :style="{ width: '450px' }"
                        header="Detalles del Usuario"
                        :modal="true"
                    >
                        <div class="flex flex-col gap-6">
                            <!-- <img
                                v-if="product.image"
                                :src="``"
                                :alt="product.image"
                                class="block m-auto pb-4"
                            /> -->
                            <div>
                                <label for="name" class="block font-bold mb-3"
                                    >Name</label
                                >
                                <InputText
                                    id="name"
                                    v-model.trim="user.name"
                                    required="true"
                                    autofocus
                                    :invalid="submitted && !user.name"
                                    fluid
                                />
                                <small
                                    v-if="submitted && !user.name"
                                    class="text-red-500"
                                    >Name is required.</small
                                >
                            </div>
                            <div>
                                <label for="email" class="block font-bold mb-3"
                                    >Email</label
                                >
                                <InputText
                                    id="email"
                                    v-model="user.email"
                                    required="true"
                                    autofocus
                                    :invalid="submitted && !user.email"
                                    fluid
                                />
                            </div>
                            <div>
                                <label for="rfc" class="block font-bold mb-3"
                                    >RFC</label
                                >
                                <InputText
                                    id="rfc"
                                    v-model.trim="user.rfc"
                                    required="true"
                                    :invalid="submitted && !user.rfc"
                                    fluid
                                />
                            </div>

                            <div>
                                <span class="block font-bold mb-4"
                                    >Nombre de usuario</span
                                >
                                <InputText
                                    id="username"
                                    v-model.trim="user.username"
                                    required="true"
                                    :invalid="submitted && !user.username"
                                    fluid
                                />
                            </div>

                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-6">
                                    <label
                                        for="phone"
                                        class="block font-bold mb-3"
                                        >Numero de telefono</label
                                    >
                                    <InputNumber
                                        id="phone"
                                        v-model="user.phone_number"
                                        mode="currency"
                                        currency="USD"
                                        locale="en-US"
                                        fluid
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
                                label="Save"
                                icon="pi pi-check"
                                @click="saveProduct"
                            />
                        </template>
                    </Dialog>
                    <Dialog
                        v-model:visible="deleteProductDialog"
                        :style="{ width: '450px' }"
                        header="Confirm"
                        :modal="true"
                    >
                        <div class="flex items-center gap-4">
                            <i class="pi pi-exclamation-triangle !text-3xl" />
                            <span v-if="product"
                                >Are you sure you want to delete
                                <b>{{ product.name }}</b
                                >?</span
                            >
                        </div>
                        <template #footer>
                            <Button
                                label="No"
                                icon="pi pi-times"
                                text
                                @click="deleteProductDialog = false"
                                severity="secondary"
                                variant="text"
                            />
                            <Button
                                label="Yes"
                                icon="pi pi-check"
                                @click="deleteProduct"
                                severity="danger"
                            />
                        </template>
                    </Dialog>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
