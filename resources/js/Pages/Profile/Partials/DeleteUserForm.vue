<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import ActionSection from "@/Components/ActionSection.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route("current-user.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <ActionSection>
        <template #title> Eliminar Cuenta </template>

        <template #description> Elimina permanentemente tu cuenta. </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600">
                Una vez que tu cuenta sea eliminada, todos sus recursos y datos
                serán eliminados permanentemente. Antes de eliminar tu cuenta,
                por favor descarga cualquier dato o información que desees
                conservar.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    Eliminar Cuenta
                </DangerButton>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title> Eliminar Cuenta </template>

                <template #content>
                    ¿Estás seguro de que deseas eliminar tu cuenta? Una vez que
                    tu cuenta sea eliminada, todos sus recursos y datos serán
                    eliminados permanentemente. Por favor, ingresa tu contraseña
                    para confirmar que deseas eliminar tu cuenta de forma
                    permanente.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Eliminar Cuenta
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
</template>
