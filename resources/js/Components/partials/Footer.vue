<script setup>
import { ref } from 'vue';

const appName = ref('CIMS');
const currentYear = ref(new Date().getFullYear());
</script>

<template>
    <footer class="app-footer">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-center text-secondary container px-4 py-3"
        >
            <div class="mb-md-0 text-md-start mb-2 text-center">
                &copy; {{ currentYear }} {{ appName.toUpperCase() }}. All rights
                reserved.
            </div>

            <nav class="mt-md-0 mt-2">
                <ul class="d-flex list-unstyled m-0 p-0">
                    <li class="px-2">
                        <a
                            href="/about"
                            class="text-decoration-none text-secondary link-secondary"
                            >About</a
                        >
                    </li>
                    <li class="px-2">
                        <a
                            href="/privacy"
                            class="text-decoration-none text-secondary link-secondary"
                            >Privacy Policy</a
                        >
                    </li>
                    <li class="px-2">
                        <a
                            href="/contact"
                            class="text-decoration-none text-secondary link-secondary"
                            >Contact</a
                        >
                    </li>
                </ul>
            </nav>
        </div>
    </footer>
</template>
