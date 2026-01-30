<script setup>
import { ref } from 'vue';

const appName = ref('CIMS');
</script>

<template>
    <footer class="app-footer bg-white py-5">
        <div class="container pt-4">
            <div class="row">
                <div class="col-12 col-md-4 mb-md-0 mb-4">
                    <h4 class="fw-bold text-primary mb-3">{{ appName }}</h4>
                    <p class="text-secondary small pr-md-5">
                        Centralized Infrastructure Management System.<br />
                        Modern tools for the modern IT professional.
                    </p>
                    <div class="d-flex align-items-center mt-3 gap-2">
                        <span class="status-dot"></span>
                        <span class="small text-muted fw-semibold uppercase"
                            >All Systems Operational</span
                        >
                    </div>
                </div>

                <div class="col-6 col-md-2 mb-3">
                    <h6 class="fw-bold">Platform</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a
                                href="#"
                                class="nav-link text-secondary small p-0"
                                >Features</a
                            >
                        </li>
                        <li class="nav-item mb-2">
                            <a
                                href="#"
                                class="nav-link text-secondary small p-0"
                                >Security</a
                            >
                        </li>
                        <li class="nav-item mb-2">
                            <a
                                href="#"
                                class="nav-link text-secondary small p-0"
                                >Integrations</a
                            >
                        </li>
                    </ul>
                </div>

                <div class="col-6 col-md-2 mb-3">
                    <h6 class="fw-bold">Resources</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a
                                href="#"
                                class="nav-link text-secondary small p-0"
                                >Documentation</a
                            >
                        </li>
                        <li class="nav-item mb-2">
                            <a
                                href="#"
                                class="nav-link text-secondary small p-0"
                                >API Reference</a
                            >
                        </li>
                        <li class="nav-item mb-2">
                            <a
                                href="#"
                                class="nav-link text-secondary small p-0"
                                >Support</a
                            >
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 text-md-end">
                    <h6 class="fw-bold">Connect</h6>
                    <div class="d-flex justify-content-md-end mb-3 gap-3">
                        <a href="#" class="text-secondary"
                            ><i class="bi bi-github fs-5"></i
                        ></a>
                        <a href="#" class="text-secondary"
                            ><i class="bi bi-linkedin fs-5"></i
                        ></a>
                        <a href="#" class="text-secondary"
                            ><i class="bi bi-twitter-x fs-5"></i
                        ></a>
                    </div>
                    <p class="text-muted small">
                        © {{ new Date().getFullYear() }} CIMS. All rights
                        reserved.
                    </p>
                </div>
            </div>

            <div
                class="d-flex flex-column flex-sm-row justify-content-between border-top my-4 py-4"
            >
                <div class="d-flex gap-4">
                    <a href="#" class="text-muted small text-decoration-none"
                        >Privacy Policy</a
                    >
                    <a href="#" class="text-muted small text-decoration-none"
                        >Terms of Service</a
                    >
                    <a href="#" class="text-muted small text-decoration-none"
                        >Cookie Policy</a
                    >
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.status-dot {
    height: 8px;
    width: 8px;
    background-color: #28a745; /* Success Green */
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 8px #28a745;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.4;
    }
    100% {
        opacity: 1;
    }
}

.nav-link:hover {
    color: #0d6efd !important;
    text-decoration: underline !important;
}
</style>
