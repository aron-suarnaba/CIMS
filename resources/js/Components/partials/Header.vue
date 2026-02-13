<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

// Use absolute paths for images in Laravel public folder
const logo = ref('/img/logo.png');

// Note: You don't actually need to wrap these in ref()
// unless you plan on changing the strings dynamically.
const HomePath = route('welcome');

const navItem = [
    {
        name: 'Home',
        url: '#mainSection',
    },
    {
        name: 'Login',
        url: route('login'),
    },
    {
        name: 'Features',
        url: '#featureSection',
    },
    {
        name: 'Proof',
        url: '#proofSection',
    },
    {
        name: 'How It Works',
        url: '#howItWorksSection',
    },
    {
        name: 'FAQs',
        url: '#faqsSection',
    },
    {
        name: 'CTA',
        url: '#ctaSection',
    },
];
</script>

<template>
    <div class="app-header">
        <div class="container-fluid">
            <header
                class="d-flex justify-content-center mb-4 flex-wrap px-5 pt-4"
            >
                <Link
                    :href="HomePath"
                    class="d-flex align-items-center mb-md-0 me-md-auto link-body-emphasis text-decoration-none mb-3"
                >
                    <img :src="logo" alt="logo" id="logo" class="me-2" />
                    <span class="fs-4 fw-bold text-primary">CIMS</span>
                </Link>

                <ul class="nav nav-pills">
                    <li class="nav-item" v-for="nav in navItem" :key="nav.name">
                        <a :href="nav.url" class="nav-link">{{ nav.name }}</a>
                    </li>
                </ul>
            </header>
        </div>
    </div>
</template>
<style scoped>
#logo {
    width: 38px;
    height: 42px;
}
</style>
