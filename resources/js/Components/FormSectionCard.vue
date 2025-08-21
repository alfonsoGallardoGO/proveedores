<script setup>
import { computed, useSlots } from "vue";
import SectionTitle from "./SectionTitle.vue";

defineEmits(["submitted"]);

const hasActions = computed(() => !!useSlots().actions);
</script>

<template>
    <div class="md:grid md:grid-cols-2 md:gap-6">
        <div class="mt-5 p-5 md:col-span-2 card shadow">
            <SectionTitle>
                <template #title>
                    <slot name="title" />
                </template>
                <template #description>
                    <slot name="description" />
                </template>
            </SectionTitle>
            <div class="px-4">
                <hr class="h-px mt-4 bg-gray-200 border-0 dark:bg-gray-700" />
            </div>

            <form @submit.prevent="$emit('submitted')">
                <div
                    class="px-4 py-5 sm:p-6"
                    :class="
                        hasActions
                            ? 'sm:rounded-tl-md sm:rounded-tr-md'
                            : 'sm:rounded-md'
                    "
                >
                    <div class="grid grid-cols-6 gap-6">
                        <slot name="form" />
                    </div>
                </div>
                <div class="px-4">
                    <hr
                        class="h-px mb-4 bg-gray-200 border-0 dark:bg-gray-700"
                    />
                </div>
                <div
                    v-if="hasActions"
                    class="flex flex-wrap items-start justify-end gap-2 px-4 text-end sm:px-6 sm:rounded-bl-md sm:rounded-br-md mb-4"
                >
                    <slot name="actions" />
                </div>
            </form>
        </div>
    </div>
</template>
