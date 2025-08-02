<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useForm, usePage } from "@inertiajs/vue3";
import { Button } from "primevue";

const props = defineProps({
    users: Array,
});

const page = usePage();

console.log("Page Props:", page.props);
console.log("Users:", props.users);

const selectedUsers = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);
const userDialog = ref(false);
const deleteUserDialog = ref(false);
const previewImageUrl = ref(null);
const passwordUser = ref("");
const user = useForm({
    id: null,
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
    previewImageUrl.value = null;
    userDialog.value = true;
};

const confirmDeleteSelected = () => {
    if (selectedUsers.value.length) {
        console.log("Confirm delete selected users", selectedUsers.value);
        deleteUserDialog.value = true;
    } else {
        console.log("No users selected for deletion");
    }
};

const sendUser = () => {
    if (user.id) {
        delete user.password;
        user.phone_number = user.phone_number
            ? user.phone_number.replace(/\D/g, "")
            : null;
        user.put(route("users.update", user.id), {
            onSuccess: () => {
                userDialog.value = false;
                user.reset();
                previewImageUrl.value = null;
            },
        });
    } else {
        user.phone_number = user.phone_number
            ? user.phone_number.replace(/\D/g, "")
            : null;
        user.post(route("users.store"), {
            onSuccess: () => {
                userDialog.value = false;
                user.reset();
                previewImageUrl.value = null;
            },
        });
    }
};

const hideDialog = () => {
    userDialog.value = false;
    user.reset();
};

const editUser = (data) => {
    user.id = data.id;
    user.name = data.name;
    user.email = data.email;
    user.rfc = data.rfc;
    user.username = data.username;
    user.phone_number = data.phone_number;
    user.profile_photo_path = data.profile_photo_path || null;
    previewImageUrl.value = data.profile_photo_path
        ? `http://localhost:8000/storage/${data.profile_photo_path}`
        : null;
    submitted.value = false;
    userDialog.value = true;
};

const getInitials = (username) => {
    if (!username) {
        return "";
    }
    const names = username.split(" ");
    let initials = "";

    if (names.length > 0) {
        initials += names[0].charAt(0);
    }
    if (names.length > 1) {
        initials += names[1].charAt(0);
    }

    return initials.toUpperCase();
};

const deleteUser = (userId) => {
    user.put(route("users.destroy", userId), {
        onSuccess: () => {
            deleteUserDialog.value = false;
        },
    });
};

const confirmDeleteUser = (data) => {
    user.name = data.name;
    user.id = data.id;
    deleteUserDialog.value = true;
};

const setProfilePhotoPath = (event) => {
    previewImageUrl.value = URL.createObjectURL(event.files[0]);
    user.profile_photo_path = event.files[0];
};

const removeProfilePhoto = () => {
    if (previewImageUrl.value && previewImageUrl.value.startsWith("blob:")) {
        URL.revokeObjectURL(previewImageUrl.value);
    }
    previewImageUrl.value = null;
    user.profile_photo_path = null;
};
</script>

<style scoped>
.initials-avatar {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e0e0e0;
    color: #757575;
    font-weight: bold;
    font-size: 1rem;
}
</style>

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
                                    <div
                                        v-if="slotProps.data.profile_photo_path"
                                    >
                                        <img
                                            :src="`http://localhost:8000/storage/${slotProps.data.profile_photo_path}`"
                                            :alt="slotProps.data.image"
                                            class="rounded-full object-cover"
                                            style="width: 40px; height: 40px"
                                        />
                                    </div>
                                    <div v-else class="initials-avatar">
                                        {{ getInitials(slotProps.data.name) }}
                                    </div>
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
                                        @click="editUser(slotProps.data)"
                                    />
                                    <Button
                                        icon="pi pi-eye"
                                        variant="outlined"
                                        rounded
                                        severity="info"
                                        class="mr-2"
                                        @click="
                                            confirmDeleteUser(slotProps.data)
                                        "
                                    />
                                    <Button
                                        icon="pi pi-trash"
                                        variant="outlined"
                                        rounded
                                        severity="danger"
                                        @click="
                                            confirmDeleteUser(slotProps.data)
                                        "
                                    />
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                    <Dialog
                        v-model:visible="userDialog"
                        :style="{ width: '450px' }"
                        :header="user.id ? 'Editar Usuario' : 'Añadir Usuario'"
                        :modal="true"
                    >
                        <div class="flex flex-col gap-6">
                            <div>
                                <img
                                    v-if="previewImageUrl"
                                    :src="previewImageUrl"
                                    :alt="
                                        user.profile_photo_path
                                            ? user.profile_photo_path.name ||
                                              user.profile_photo_path
                                            : 'Foto de perfil'
                                    "
                                    class="block m-auto pb-4 rounded-full object-cover"
                                    style="width: 100px; height: 100px"
                                />
                                <img
                                    v-else
                                    src="https://definicion.de/wp-content/uploads/2019/07/perfil-de-usuario.png"
                                    alt="Perfil de usuario por defecto"
                                    class="block m-auto pb-4 rounded-full object-cover"
                                    style="width: 100px; height: 100px"
                                />
                                <label
                                    for="profile_photo_path"
                                    class="block font-bold mb-3"
                                    >Foto de Perfil</label
                                >
                                <div class="flex items-center gap-2 mb-4">
                                    <FileUpload
                                        ref="fileupload"
                                        mode="basic"
                                        name="profile_photo_path"
                                        accept="image/*"
                                        :auto="true"
                                        :maxFileSize="1000000"
                                        @select="setProfilePhotoPath"
                                        chooseLabel="Seleccionar Imagen"
                                    />
                                    <Button
                                        icon="pi pi-trash"
                                        severity="danger"
                                        label="Eliminar Imagen"
                                        @click="removeProfilePhoto"
                                        v-if="previewImageUrl"
                                    />
                                </div>
                                <label for="name" class="block font-bold mb-3"
                                    >Nombre</label
                                >
                                <InputText
                                    id="name"
                                    v-model.trim="user.name"
                                    required="true"
                                    placeholder="Nombre del usuario"
                                    autofocus
                                    :invalid="submitted && !user.name"
                                    autocomplete="off"
                                    fluid
                                />
                                <small
                                    v-if="!user.name && submitted"
                                    class="text-red-500"
                                    >El nombre es requerido.</small
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
                                    placeholder="Email del usuario"
                                    :invalid="submitted && !user.email"
                                    autocomplete="off"
                                    fluid
                                />
                                <small
                                    v-if="
                                        (submitted && !user.email) ||
                                        page.props.errors.email
                                    "
                                    class="text-red-500"
                                    >{{
                                        page.props.errors.email ===
                                        "The email has already been taken."
                                            ? "El email ya está en uso."
                                            : "Email es requerido."
                                    }}</small
                                >
                            </div>
                            <div>
                                <label
                                    for="password"
                                    class="block font-bold mb-3"
                                    >Contraseña</label
                                >

                                <Password
                                    fluid
                                    v-model="user.password"
                                    id="password"
                                    placeholder="Contraseña"
                                    toggleMask
                                    promptLabel="Escoge una contraseña"
                                    weakLabel="Demasiado simple"
                                    mediumLabel="Complejidad promedio"
                                    strongLabel="Contraseña compleja"
                                >
                                    <template #header>
                                        <div class="font-semibold text-xm mb-4">
                                            Introduzca una contraseña
                                        </div>
                                    </template>
                                </Password>
                                <small
                                    v-if="
                                        (submitted && !user.password) ||
                                        page.props.errors.password
                                    "
                                    class="text-red-500"
                                    >{{
                                        page.props.errors.password ===
                                        "The password must be at least 8 characters."
                                            ? "La contraseña debe tener al menos 8 caracteres."
                                            : "La contraseña es requerida."
                                    }}</small
                                >
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
                                    autocomplete="off"
                                    placeholder="RFC del usuario"
                                    fluid
                                />
                                <small
                                    v-if="
                                        (submitted && !user.rfc) ||
                                        page.props.errors.rfc
                                    "
                                    class="text-red-500"
                                    >{{
                                        page.props.errors.rfc ===
                                        "The rfc has already been taken."
                                            ? "El RFC ya está en uso."
                                            : "El RFC es requerido."
                                    }}</small
                                >
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
                                    placeholder="Nombre de usuario"
                                    autocomplete="off"
                                    fluid
                                />
                                <small
                                    v-if="
                                        (submitted && !user.username) ||
                                        page.props.errors.username
                                    "
                                    class="text-red-500"
                                    >{{
                                        page.props.errors.username ===
                                        "The username has already been taken."
                                            ? "El nombre de usuario ya está en uso."
                                            : "El nombre de usuario es requerido."
                                    }}</small
                                >
                            </div>

                            <div>
                                <label for="phone" class="block font-bold mb-3"
                                    >Numero de telefono</label
                                >
                                <InputMask
                                    id="phone"
                                    v-model="user.phone_number"
                                    mask="(999) 999-9999"
                                    placeholder="(999) 999-9999"
                                    autocomplete="off"
                                    fluid
                                />
                                <small
                                    v-if="
                                        (submitted && !user.phone_number) ||
                                        page.props.errors.phone_number
                                    "
                                    class="text-red-500"
                                    >El número de teléfono es requerido.</small
                                >
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
                                @click="sendUser"
                            />
                        </template>
                    </Dialog>
                    <Dialog
                        v-model:visible="deleteUserDialog"
                        :style="{ width: '450px' }"
                        header="Confirmación de Eliminación"
                        :modal="true"
                    >
                        <div class="flex items-center gap-4">
                            <i class="pi pi-exclamation-triangle !text-3xl" />
                            <span v-if="user.name">
                                Estás seguro de que deseas eliminar el usuario
                                <b>{{ user.name }}</b
                                >?</span
                            >
                        </div>
                        <template #footer>
                            <Button
                                label="Cancelar"
                                icon="pi pi-times"
                                text
                                @click="deleteUserDialog = false"
                                severity="secondary"
                                variant="text"
                            />
                            <Button
                                label="Si, eliminar"
                                icon="pi pi-check"
                                @click="deleteUser(user.id)"
                                severity="danger"
                            />
                        </template>
                    </Dialog>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
