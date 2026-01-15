<script setup>
import { ref, onMounted, computed } from 'vue'; // Added computed
import axios from 'axios';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

defineOptions({
    layout: HomeLayout,
});

// State
const deviceList = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(25); // Adjust items per page here

// Breadcrumbs
const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Network Monitoring and Management' },
];

// Fetching Data
const fetchDevices = async () => {
    try {
        const response = await axios.get('/CIMS/public/api/fortigate/devices');

        // Handle different possible response structures from FortiGate API
        if (response.data && response.data.results) {
            deviceList.value = response.data.results;
        } else if (response.data && Array.isArray(response.data)) {
            deviceList.value = response.data;
        } else if (response.data && response.data.data) {
            deviceList.value = response.data.data;
        } else {
            console.warn('Unexpected API response structure:', response.data);
            deviceList.value = [];
        }
    } catch (error) {
        console.error('Failed to fetch devices:', error);
        deviceList.value = [];
    }
};

// Pagination Logic
const totalPages = computed(() =>
    Math.ceil(deviceList.value.length / itemsPerPage.value),
);

const paginatedDevices = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return deviceList.value.slice(start, end);
});

const setPage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
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
            <div class="row mb-4">
                <div class="col-sm-12 col-md-6">
                    <BackButton
                        @click.prevent="router.get(route('dashboard'))"
                    />
                </div>
                <div class="col-sm-12 col-md-6 text-end">
                    <button
                        @click.prevent="router.get(route('firewall.index'))"
                        class="btn btn-outline-primary"
                    >
                        <i class="bi bi-shield-lock me-1"></i> Firewall Settings
                    </button>
                </div>
            </div>

            <div class="row g-3 mb-4 text-center">
                <div class="col-6 col-md-3">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3>{{ deviceList.length || '0' }}</h3>
                            <p>Total Devices</p>
                        </div>
                        <i class="bi bi-cpu small-box-icon"></i>
                    </div>
                </div>
            </div>

            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-white">
                    <div
                        class="row d-flex justify-content-between align-items-center"
                    >
                        <div class="col-sm-12 col-md-4">
                            <h5 class="text-primary mb-0">Active Device</h5>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <input type="text" class="form-control" />
                            <i class="bi-bi-search form-label"></i>
                        </div>
                        <div class="col-sm-12 col-md-4 float-end text-end">
                            <span class="badge bg-primary fs-5"
                                >{{ deviceList.length }} Total</span
                            >
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-hover mb-0 table">
                            <thead class="table-light">
                                <tr>
                                    <th>Device / Hostname</th>
                                    <th>IP Address</th>
                                    <th>MAC Address</th>
                                    <th>Manufacturer</th>
                                    <th>Type/OS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="device in paginatedDevices"
                                    :key="device.mac"
                                >
                                    <td>
                                        <i class="bi bi-laptop me-2"></i>
                                        <strong>{{
                                            device.hostname ||
                                            device.alias ||
                                            'Unnamed'
                                        }}</strong>
                                    </td>
                                    <td>{{ device.ipv4_address || 'Unknown' }}</td>
                                    <td>
                                        <code>{{ device.mac }}</code>
                                    </td>
                                    <td>
                                        {{
                                            device.hardware_vendor || 'Unknown'
                                        }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">{{
                                            device.os_name || 'Network Device'
                                        }}</span>
                                    </td>
                                </tr>
                                <tr v-if="deviceList.length === 0">
                                    <td
                                        colspan="5"
                                        class="text-muted py-4 text-center"
                                    >
                                        Loading inventory...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="card-footer d-flex justify-content-between align-items-center bg-white py-3"
                >
                    <div class="text-muted small">
                        Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to
                        {{
                            Math.min(
                                currentPage * itemsPerPage,
                                deviceList.length,
                            )
                        }}
                        of {{ deviceList.length }}
                    </div>
                    <nav v-if="totalPages > 1" class="ms-auto">
                        <ul class="pagination pagination-sm mb-0 gap-1">
                            <li
                                class="page-item"
                                :class="{ disabled: currentPage === 1 }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="setPage(currentPage - 1)"
                                    >Previous</a
                                >
                            </li>
                            <li
                                v-for="page in totalPages"
                                :key="page"
                                class="page-item"
                                :class="{ active: currentPage === page }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="setPage(page)"
                                    >{{ page }}</a
                                >
                            </li>
                            <li
                                class="page-item"
                                :class="{
                                    disabled: currentPage === totalPages,
                                }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="setPage(currentPage + 1)"
                                    >Next</a
                                >
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
