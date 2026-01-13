<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

// const props = defineProps({
//     initialDevices: Array
// });

const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Network Monitoring and Management' },
];



// onMounted(() => {
//     if (window.Echo) {
//         window.Echo.channel('network-status')
//             .listen('DeviceStatusUpdated', (e) => {
//                 const index = devices.value.findIndex(d => d.id === e.device.id);
//                 if (index !== -1) {
//                     devices.value[index] = e.device; // Live update!
//                 }
//             });
//     } else {
//         console.warn("Laravel Echo not found. Real-time updates are disabled.");
//     }
// });

const deviceList = ref([]);

const fetchDevices = async () => {
    try {
        // We use the full path because of the XAMPP subfolder
        const response = await axios.get('/CIMS/public/api/fortigate/devices');

        // FortiGate returns a 'results' array for this endpoint
        if (response.data && response.data.results) {
            deviceList.value = response.data.results;
        }
    } catch (error) {
        console.error("Failed:", error);
    }

};

onMounted(fetchDevices);
</script>
<template>
    <div class="app-content-header">
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>
    <div class="app-content">
        <div class="container">
            <div class="row mb-5">
                <div class="col-sm-12 col-md-4 mb-2">
                    <BackButton @click.prevent="router.get(route('dashboard'))" />
                </div>
            </div>

            <div class="mt-0">
                <div class="row g-3 mb-4 text-center">
                    <div class=" col-6 col-md-3">
                        <div class="small-box text-bg-primary">
                            <div class="inner">
                                <h3 class="mb-0">{{ deviceList.length || "0" }}</h3>
                                <p class="mb-0">Device</p>
                            </div>
                            <!-- <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                                </path>
                            </svg> -->
                            <i class="bi bi-phone small-box-icon fs-1"></i>
                            <a href="#"
                                class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <div class=" col-6 col-md-3">
                        <div class="small-box text-bg-success">
                            <div class="inner">
                                <h3 class="mb-0">{{ deviceList.length || "0" }}</h3>
                                <p class="mb-0">Online</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                                </path>
                            </svg>
                            <a href="#"
                                class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <div class=" col-6 col-md-3">
                        <div class="small-box text-bg-danger">
                            <div class="inner">
                                <h3 class="mb-0">{{ deviceList.length || "0" }}</h3>
                                <p class="mb-0">Offline</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                                </path>
                            </svg>
                            <a href="#"
                                class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                    <div class=" col-6 col-md-3">
                        <div class="small-box text-bg-secondary">
                            <div class="inner">
                                <h3 class="mb-0">{{ deviceList.length || "0" }}</h3>
                                <p class="mb-0">Average Latency</p>
                            </div>
                            <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                                </path>
                            </svg>
                            <a href="#"
                                class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                                More info <i class="bi bi-link-45deg"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-9"></div>
                    <div class="col-sm-12 col-md-3">
                        <div class="card text-bg-light">
                            <div class="card-body">
                                <button @click.prevent="router.get(route('firewall.index'))" class="btn btn-outline-primary">
                                    Firewall
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</template>
