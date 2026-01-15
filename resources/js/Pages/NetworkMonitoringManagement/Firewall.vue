<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';

defineOptions({
    layout: HomeLayout,
});

const deviceList = ref([]);
const currentPage = ref(1);
const itemsPerPage = ref(25);

const fetchDevices = async () => {
    try {
        // We use the full path because of the XAMPP subfolder
        const response = await axios.get('/CIMS/public/api/fortigate/devices');

        // FortiGate returns a 'results' array for this endpoint
        if (response.data && response.data.results) {
            deviceList.value = response.data.results;
        }
    } catch (error) {
        console.error('Failed:', error);
    }
};

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

const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Network' },
];
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
                    <BackButton
                        @click.prevent="router.get(route('network.index'))"
                    />
                </div>
            </div>
            <div class="p-6">
                <div class="card shadow-lg">
                    <div
                        class="card-header bg-primary d-flex justify-content-between text-white"
                    >
                        <h5 class="mb-0">Network Device Inventory</h5>
                        <span class="badge bg-light text-dark"
                            >{{ deviceList.length }} Devices Found</span
                        >
                    </div>
                    <div class="card-body">
                        <table class="table-hover table">
                            <thead>
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
                                            'Unnamed Device'
                                        }}</strong>
                                    </td>
                                    <td>{{ device.ipv4_address }}</td>
                                    <td>
                                        <code>{{ device.mac }}</code>
                                    </td>
                                    <td>
                                        {{
                                            device.hardware_vendor ||
                                            'Unknown Vendor'
                                        }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{
                                                device.os_name ||
                                                'Network Device'
                                            }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="deviceList.length === 0">
                                    <td colspan="5" class="text-center">
                                        Loading devices...
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div
                            class="d-flex justify-content-between align-items-center mt-4"
                        >
                            <div class="text-muted small">
                                Showing
                                {{ (currentPage - 1) * itemsPerPage + 1 }} to
                                {{
                                    Math.min(
                                        currentPage * itemsPerPage,
                                        deviceList.length,
                                    )
                                }}
                                of {{ deviceList.length }} entries
                            </div>

                            <nav v-if="totalPages > 1">
                                <ul class="pagination mb-0">
                                    <li
                                        class="page-item"
                                        :class="{ disabled: currentPage === 1 }"
                                    >
                                        <a
                                            class="page-link"
                                            href="#"
                                            @click.prevent="
                                                setPage(currentPage - 1)
                                            "
                                            >Previous</a
                                        >
                                    </li>

                                    <li
                                        v-for="page in totalPages"
                                        :key="page"
                                        class="page-item"
                                        :class="{
                                            active: currentPage === page,
                                        }"
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
                                            disabled:
                                                currentPage === totalPages,
                                        }"
                                    >
                                        <a
                                            class="page-link"
                                            href="#"
                                            @click.prevent="
                                                setPage(currentPage + 1)
                                            "
                                            >Next</a
                                        >
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
