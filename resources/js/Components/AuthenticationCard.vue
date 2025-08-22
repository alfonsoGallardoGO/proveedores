<script setup>
import bg from "@/assets/bg_login.png";
import logo from "@/assets/logo_prob.png";

</script>

<style scoped>
html,
body,
#app {
    height: 100%;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

.d-flex.flex-column.flex-fluid {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.d-flex.flex-column.flex-lg-row.flex-column-fluid {
    flex-grow: 1;
}

.menu.show {
    display: block;
}

.bgi-size-cover {
    background-size: cover !important;
}

#kt_auth_lang_menu {
    position: absolute;
}

@keyframes breathing {
    0%,
    100% {
        opacity: 0.9;
    }
    50% {
        opacity: 1;
    }
}

.breathing {
    animation: breathing 2s ease-in-out infinite;
}

@keyframes breathing {
    0%,
    100% {
        opacity: 0.9;
    }
    50% {
        opacity: 1;
    }
}

.breathing {
    animation: breathing 2s ease-in-out infinite;
}
</style>

<template>
    <Head title="Login" />
    <div class="d-flex flex-column flex-fluid">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div
                class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-y-center bgi-position-x-start bgi-no-repeat breathing"
                :style="{ backgroundImage: `url(${bg})` }"
            >
            </div>
            <div
                class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12"
            >
                <div
                    class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10 shadow"
                >
                    <div class="">
                        <p
                            class="text-gray-400 text-center fw-bolder mb-3 text-lg"
                        >
                            Portal Proveedores
                        </p>
                        <div
                            class="d-flex flex-column flex-center pb-0 pb-lg-3 p-3 w-100"
                        >
                            <img class="mx-auto h-100px" :src="logo" alt="" />
                        </div>
                    </div>
                    <div
                        class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px"
                    >
                        <div
                            class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20"
                        >

                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<!-- <template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div>
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template> -->
