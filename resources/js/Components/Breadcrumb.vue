<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
const props = defineProps({
    title: {
        type: String,
        default: null,
    },
    breadcrumbs: {
        type: Array,
        required: true,
    },
    auth: Object,
    error: Object,
});

const displayedTitle = computed(() => {
    if (props.title) return props.title;
    return props.breadcrumbs[props.breadcrumbs.length - 1]?.label || 'CIMS';
});
</script>
<template>
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-2"
    >
        <div class="d-flex gap-2">
            <h5 class="fw-bold text-secondary mb-0">{{ displayedTitle }}</h5>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 flex-wrap">
                <li
                    class="breadcrumb-item"
                    v-for="(link, index) in breadcrumbs"
                    :key="index"
                    :class="{
                        active: index === breadcrumbs.length - 1,
                    }"
                >
                    <Link
                        v-if="index !== breadcrumbs.length - 1"
                        :href="link.url"
                    >
                        {{ link.label }}
                    </Link>
                    <span v-else>{{ displayedTitle }}</span>
                </li>
            </ol>
        </nav>
    </div>
</template>
