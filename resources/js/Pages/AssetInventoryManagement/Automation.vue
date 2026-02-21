<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    actions: {
        type: Array,
        default: () => [],
    },
});

const runAction = (action) => {
    router.post(route('automation.run'), { action }, { preserveScroll: true });
};
</script>

<template>
    <div class="app-content-header py-3">
        <div class="container">
            <Breadcrumb
                :breadcrumbs="[
                    { label: 'Dashboard', url: route('dashboard') },
                    {
                        label: 'Asset & Inventory',
                        url: route('AssetAndInventoryManagement'),
                    },
                    { label: 'Automation' },
                ]"
            />
        </div>
    </div>

    <div class="app-content mt-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <BackButton
                    @click.prevent="
                        router.get(route('AssetAndInventoryManagement'))
                    "
                />
                <small class="text-muted"
                    >Run maintenance automation safely</small
                >
            </div>

            <div class="row g-3">
                <div
                    class="col-12 col-md-6 col-xl-4"
                    v-for="action in props.actions"
                    :key="action.key"
                >
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i
                                    :class="[
                                        action.icon,
                                        'fs-3 text-primary me-2',
                                    ]"
                                ></i>
                                <h5 class="fw-bold mb-0">{{ action.label }}</h5>
                            </div>
                            <p class="text-muted mb-4">
                                {{ action.description }}
                            </p>
                            <button
                                class="btn btn-primary mt-auto"
                                @click="runAction(action.key)"
                            >
                                Run Action
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
