<script setup>
import SectionTitle from "./SectionTitle.vue";
</script>

<template>
    <div class="flex">
        <div class="p-4 sm:p-6 card shadow sm:rounded-lg w-100">
            <div
                class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-400"
            >
                <div class="flex items-center">
                    <SectionTitle>
                        <template #title>
                            <slot name="title" />
                        </template>
                        <template #description>
                            <slot name="description" />
                        </template>
                    </SectionTitle>
                </div>
                <div class="p-4 col-span-2">
                    <slot name="content" />
                </div>
            </div>
        </div>
    </div>
</template>
