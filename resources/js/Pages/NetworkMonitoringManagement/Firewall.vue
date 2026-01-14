<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';

defineOptions({
    layout: HomeLayout,
});

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
        console.error('Failed:', error);
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
                                    v-for="device in deviceList"
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
