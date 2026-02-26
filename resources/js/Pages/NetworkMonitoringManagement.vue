<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref } from 'vue'; // Added computed

defineOptions({
    layout: HomeLayout,
});

// State
const deviceList = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(25);

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
                    <BackButton @click.prevent="router.get(route('dashboard'))" />
                </div>
                <div class="col-sm-12 col-md-6 text-end">
                    <button @click.prevent="router.get(route('firewall.index'))" class="btn btn-outline-primary">
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
                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="col-sm-12 col-md-4">
                            <h5 class="text-primary mb-0">Active Device</h5>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="input-group">
                                <i class="bi bi-search input-group-text"></i>
                                <input type="text" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4 float-end text-end">
                            <span class="badge bg-primary fs-5">{{ deviceList.length }} Total</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-hover table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Device / Hostname</th>
                                    <th>IP Address</th>
                                    <th>MAC Address</th>
                                    <th>Manufacturer</th>
                                    <th>Type / OS</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="device in paginatedDevices" :key="device.mac" class="transition-all">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3"
                                                style="width: 35px; height: 35px;">
                                                <i class="bi bi-laptop text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    {{ device.hostname || device.alias || 'Unnamed Device' }}
                                                </div>
                                                <small class="text-muted">ID: {{ device.id || 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-monospace">{{ device.ipv4_address }}</span>
                                    </td>
                                    <td>
                                        <code class="text-uppercase bg-light px-2 py-1 rounded text-secondary"
                                            style="font-size: 0.85rem;">
                                    {{ device.mac }}
                                </code>
                                    </td>
                                    <td>
                                        <span class="text-secondary">{{ device.hardware_vendor || 'Unknown Vendor'
                                        }}</span>
                                    </td>
                                    <td>
                                        <span :class="[
                                            'badge rounded-pill px-3 py-2',
                                            device.os_name ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary'
                                        ]">
                                            <i class="bi bi-cpu me-1"></i>
                                            {{ device.os_name || 'Network Device' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-primary border-0">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="deviceList.length === 0">
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <span>No devices found in the network.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top p-4">
                        <div class="text-muted small">
                            Showing
                            <span class="fw-semibold">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to
                            <span class="fw-semibold">{{ Math.min(currentPage * itemsPerPage, deviceList.length)
                            }}</span>
                            of <span class="fw-semibold">{{ deviceList.length }}</span> entries
                        </div>

                        <Pagination
                        :current-page="currentPage"
                        :last-page="totalPages"
                        @update:page="setPage"
                    />
                    </div>
                </div>


            </div>
        </div>
    </div>
</template>
