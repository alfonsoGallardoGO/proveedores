<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Head title="Forgot Password" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <h1
            class="text-gray-900 fw-bolder mb-3 text-3xl"
        >
                Recuperar contraseña
        </h1>

        <div class="mb-4 text-sm text-gray-600 text-center">
            Olvidaste tu contraseña? No hay problema. Simplemente ingresa tu
            dirección de correo electrónico y te enviaremos un enlace para
            restablecer tu contraseña que te permitirá elegir una nueva.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Enviar enlace de restablecimiento de contraseña
                </PrimaryButton>
            </div>
        </form>

        <div class="flex justify-end w-full mt-4">
            <Button 
                label="Volver al login"
                icon="pi pi-arrow-left" 
                severity="secondary" 
                @click="$inertia.visit('/')"
                :class="{ 'opacity-25': form.processing }"
            />
        </div>

    </AuthenticationCard>
</template>
