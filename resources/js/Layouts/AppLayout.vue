<script setup>
import { onMounted, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Aside from '@/Components/Aside.vue';

defineProps({
    title: String,
});

const page = usePage();

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

console.log(page.props.auth.user.roles);

onMounted(() => {
    if (typeof KTApp !== 'undefined' && KTApp.init) {
            KTApp.init();

        }

        if (typeof KTMenu !== 'undefined') {
            KTMenu.init();
        }
        if (typeof KTScroll !== 'undefined') {
            KTScroll.init();
        }

        if (typeof KTLayoutAside !== 'undefined' && KTLayoutAside.init) {
            KTLayoutAside.init();
        }

        if (typeof KTToggle !== 'undefined' && KTToggle.init) {
            KTToggle.init()
        }

        if (typeof KTDrawer !== 'undefined') {
            KTDrawer.init();
        }

});

</script>

<template>
    <div>

        <Head :title="title" />

        <Aside />

        <main>
            <slot />
        </main>
    </div>
</template>
