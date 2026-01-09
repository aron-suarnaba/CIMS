<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

// Use absolute paths for images in Laravel public folder
const logo = ref('./img/logo.png'); 

// Note: You don't actually need to wrap these in ref() 
// unless you plan on changing the strings dynamically.
const HomePath = route('welcome');
const LoginPath = route('login');
</script>

<template>
    <div class="app-header">
        <div class="container-fluid">
            <header class="d-flex justify-content-center mb-4 flex-wrap px-5 pt-4">
                <Link
                    :href="HomePath"
                    class="d-flex align-items-center mb-md-0 me-md-auto link-body-emphasis text-decoration-none mb-3"
                >
                    <img
                        :src="logo"
                        alt="logo"
                        id="logo"
                        class="rounded-circle me-2 shadow"
                    />
                    <span class="fs-4">CIMS</span>
                </Link>

                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <Link
                            :href="HomePath"
                            class="nav-link active"
                            aria-current="page"
                        >Home</Link>
                    </li>
                    <li class="nav-item">
                        <Link :href="LoginPath" class="nav-link">Login</Link>
                    </li>
                    <li class="nav-item"><a href="#" class="nav-link">Features</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Pricing</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">FAQs</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">About</a></li>
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
