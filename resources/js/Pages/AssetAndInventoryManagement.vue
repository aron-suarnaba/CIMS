<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import InfoBox from '@/Components/InfoBox.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

const navigateTo = (url) => {
    if (url) router.get(url);
};

const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Asset & Inventory' },
];

// Added more categories to fill out the UI
const navCard = [
    {
        label: 'Phone Units',
        url: route('phone.index'),
        icon: 'bi bi-phone-fill',
        color: 'primary',
        desc: 'Mobile & VoIP',
    },
    {
        label: 'Workstations',
        url: route('computer.index'),
        icon: 'bi bi-pc-display',
        color: 'success',
        desc: 'Desktops & Laptops',
    },
    {
        label: 'Mini PC',
        url: route('minipc.index'),
        icon: 'bi bi-pc-horizontal',
        color: 'dark',
        desc: 'Mini PC Units',
    },
    {
        label: 'Software Licenses',
        url: route('software-license.index'),
        icon: 'bi bi-key-fill',
        color: 'secondary',
        desc: 'License Inventory',
    },
    {
        label: 'Networking',
        url: '#', // Placeholder for future routes
        icon: 'bi bi-router',
        color: 'info',
        desc: 'Switches & APs',
    },
    {
        label: 'Peripherals',
        url: '#',
        icon: 'bi bi-printer',
        color: 'warning',
        desc: 'Printers & Scanners',
    },
];
</script>

<template>
    <div class="app-content-header bg-light py-3">
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>

    <div class="app-content mt-4">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-6">
                    <BackButton
                        @click.prevent="router.get(route('dashboard'))"
                    />
                </div>
                <div class="col-6 text-muted text-end">
                    <small>Select a category to manage assets</small>
                </div>
            </div>

            <div class="row g-4 justify-content-start">
                <div
                    class="col-12 col-sm-6 col-lg-3"
                    v-for="option in navCard"
                    :key="option.label"
                >
                    <div
                        class="nav-wrapper"
                        @click.prevent="navigateTo(option.url)"
                    >
                        <InfoBox
                            class="h-100 navigation-box border-0 shadow-sm"
                        >
                            <template #header>
                                <div
                                    :class="`icon-circle bg-${option.color}-subtle text-${option.color}`"
                                >
                                    <i :class="[option.icon, 'fs-3']"></i>
                                </div>
                            </template>
                            <template #content>
                                <div class="ms-2">
                                    <div class="fw-bold text-dark fs-5">
                                        {{ option.label }}
                                    </div>
                                    <small class="text-muted d-block">{{
                                        option.desc
                                    }}</small>
                                </div>
                            </template>
                        </InfoBox>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.nav-wrapper {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}

.nav-wrapper:hover {
    transform: translateY(-8px);
}

.navigation-box {
    padding: 1.5rem !important;
    background: #ffffff;
    border-radius: 15px !important;
}

/* Creating a clean circular icon container */
.icon-circle {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin-bottom: 1rem;
}

.nav-wrapper:hover .navigation-box {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1) !important;
}
</style>
