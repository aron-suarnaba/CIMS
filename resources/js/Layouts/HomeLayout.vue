<script setup>
import Sidebar from '@/Components/partials/Aside.vue';
import Nav from '@/Components/partials/Nav.vue';
import Footer from '@/Components/partials/Footer.vue';
</script>

<template>
    <div class="app-wrapper">
        <Nav />

        <Sidebar />

        <main class="app-main bg-body-tertiary" id="main">
            <slot />
        </main>

        <Footer />
    </div>
</template>
