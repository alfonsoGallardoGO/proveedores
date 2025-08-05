<script setup>
import Header from "@/Components/Header.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { FilterMatchMode } from "@primevue/core/api";
import { useForm, usePage } from "@inertiajs/vue3";
import { Button, useToast } from "primevue";
import { router as Inertia } from "@inertiajs/vue3";

const props = defineProps({
    users: Array,
    suppliers: Array,
});

const pageUrl = import.meta.env.VITE_APP_URL;

const page = usePage();
const toast = useToast();

console.log("Page Props:", page.props);
console.log("Users:", props.users);
console.log("Suppliers:", props.suppliers);

const selectedUsers = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);
const userDialog = ref(false);
const deleteUserDialog = ref(false);
const previewImageUrl = ref(null);
const passwordUser = ref("");
const selectedSupplier = ref(null);

const user = useForm({
    id: null,
    name: "",
    email: "",
    password: "",
    rfc: "",
    username: "",
    phone_number: "",
    profile_photo_path: "",
    supplier_id: "",
});

const openNew = () => {
    user.reset();
    previewImageUrl.value = null;
    userDialog.value = true;
};

const confirmDeleteSelected = () => {
    deleteUserDialog.value = true;
};

const deleteSelectedUsers = () => {
    submitted.value = true;
    Inertia.delete(route("users.destroySelected"), {
        data: {
            users: selectedUsers.value.map((user) => user.id),
        },
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Usuarios eliminados",
                detail: "Los usuarios seleccionados han sido eliminados correctamente.",
                life: 3000,
            });
            selectedUsers.value = [];
            deleteUserDialog.value = false;
        },
    });
};

const sendUser = () => {
    const formData = new FormData();
    submitted.value = true;
    if (user.profile_photo_path instanceof File) {
        formData.append("profile_photo_path", user.profile_photo_path);
    } else if (user.profile_photo_path === null) {
        formData.append("profile_photo_path", "");
    }
    formData.append("name", user.name);
    formData.append("email", user.email);
    formData.append("username", user.username);
    formData.append("rfc", user.rfc || "");
    formData.append(
        "phone_number",
        user.phone_number?.replace(/\D/g, "") || ""
    );
    formData.append("supplier_id", user.supplier_id || "");

    if (user.password) {
        formData.append("password", user.password);
    }

    if (user.id) {
        formData.append("_method", "PUT");
        Inertia.post(route("users.update", user.id), formData, {
            forceFormData: true,
            preserveScroll: true,
            onError: () => {
                submitted.value = true;
                console.log("Error creating/updating user");
                toast.add({
                    severity: "error",
                    summary: "Error al actualizar el usuario",
                    detail: "Por favor, corrige los errores y vuelve a intentarlo.",
                    life: 3000,
                });
            },
            onSuccess: () => {
                userDialog.value = false;
                user.reset();
                previewImageUrl.value = null;
                toast.add({
                    severity: "success",
                    summary: "Usuario actualizado",
                    detail: "El usuario ha sido actualizado correctamente.",
                    life: 3000,
                });
                submitted.value = false;
            },
        });
    } else {
        console.log("Creating new user with formData:", formData);
        Inertia.post(route("users.store"), formData, {
            forceFormData: true,
            onSuccess: () => {
                userDialog.value = false;
                user.reset();
                previewImageUrl.value = null;
                toast.add({
                    severity: "success",
                    summary: "Usuario creado",
                    detail: "El usuario ha sido creado correctamente.",
                    life: 3000,
                });
                submitted.value = false;
            },
            onError: () => {
                submitted.value = true;
                console.log("Error creating user");
                toast.add({
                    severity: "error",
                    summary: "Error al crear el usuario",
                    detail: "Por favor, corrige los errores y vuelve a intentarlo.",
                    life: 3000,
                });
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
        ? `${pageUrl}/storage/${data.profile_photo_path}`
        : null;
    submitted.value = false;
    userDialog.value = true;
    user.supplier_id = data.supplier_id;
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
                        <Toast />
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
                                            :src="`${pageUrl}/storage/${slotProps.data.profile_photo_path}`"
                                            :alt="slotProps.data.image"
                                            class="rounded-full object-cover"
                                            style="width: 40px; height: 40px"
                                        />
                                    </div>
                                    <div v-else>
                                        <img
                                            :src="`https://ui-avatars.com/api/?name=${getInitials(
                                                slotProps.data.name
                                            )}&color=7F9CF5&background=EBF4FF`"
                                            :alt="`${getInitials(
                                                slotProps.data.name
                                            )} Avatar`"
                                            class="rounded-full object-cover"
                                            style="width: 40px; height: 40px"
                                        />
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
                                    :disabled="user.id !== null"
                                >
                                    <template #header>
                                        <div class="font-semibold text-xm mb-4">
                                            Introduzca una contraseña
                                        </div>
                                    </template>
                                </Password>
                                <small
                                    v-if="page.props.errors.password"
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
                                    style="text-transform: uppercase"
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
                            <div>
                                <label
                                    for="supplier_id"
                                    class="block font-bold mb-3"
                                    >Proveedor</label
                                >
                                <Select
                                    v-model="user.supplier_id"
                                    :options="props.suppliers"
                                    filter
                                    optionLabel="company_name"
                                    placeholder="Seleccione un Proveedor"
                                    class="w-full"
                                    :showClear="true"
                                    id="supplier_id"
                                    name="supplier_id"
                                    optionValue="id"
                                >
                                    <template #option="slotProps">
                                        <div class="flex items-center">
                                            <div>
                                                {{
                                                    slotProps.option
                                                        .company_name
                                                }}
                                            </div>
                                        </div>
                                    </template>
                                </Select>
                            </div>
                        </div>

                        <template #footer>
                            <Button
                                label="Cancelar"
                                icon="pi pi-times"
                                text
                                :disabled="submitted"
                                @click="hideDialog"
                            />
                            <Button
                                label="Guardar"
                                icon="pi pi-check"
                                :loading="submitted"
                                :disabled="submitted"
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
                            <span v-if="user.name && !selectedUsers.length">
                                Estás seguro de que deseas eliminar el usuario
                                <b>{{ user.name }}</b
                                >?</span
                            >
                            <span v-else-if="selectedUsers.length">
                                Estás seguro de que deseas eliminar estos
                                usuarios ?
                            </span>
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
                                :loading="submitted"
                                :disabled="submitted"
                                @click="
                                    selectedUsers.length === 0
                                        ? deleteUser(user.id)
                                        : deleteSelectedUsers()
                                "
                                severity="danger"
                            />
                        </template>
                    </Dialog>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
