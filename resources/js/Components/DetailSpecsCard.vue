<script setup>
defineProps({
    device: { type: Object, required: true },
    title: { type: String, default: 'Device Specifications' },
    getImage: {
        type: Function,
        default: (item) => item.image_path || '/images/default-device.png',
    },
});

const emit = defineEmits(['delete', 'update']);

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString();
};

const statusClass = (status) => {
    switch (status?.toLowerCase()) {
        case 'available':
            return 'bg-success';
        case 'issued':
            return 'bg-primary';
        case 'returned':
            return 'bg-warning text-dark';
        case 'broken':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
};
</script>

<template>
    <div class="card h-100 border-0 shadow-sm">
        <div class="card-header bg-dark py-3 text-white">
            <h5 class="fw-bold mb-0">{{ title }}</h5>
        </div>

        <div class="card-body">
            <div class="mb-4 text-center">
                <img
                    :src="getImage(device)"
                    class="img-fluid bg-light device-image-render rounded p-3"
                    :alt="device.model"
                />
                <h3 class="fw-bold mb-0 mt-3">
                    {{ device.brand }} {{ device.model }}
                </h3>

                <span
                    v-if="device.status"
                    :class="[
                        'badge text-capitalize mt-2 px-3 py-2',
                        statusClass(device.status),
                    ]"
                >
                    {{ device.status }}
                </span>
            </div>

            <ul class="list-group list-group-flush border-top">
                <li
                    v-if="device.serial_num"
                    class="list-group-item d-flex justify-content-between"
                >
                    <span class="text-muted">Serial Number</span>
                    <span class="fw-bold">{{ device.serial_num }}</span>
                </li>

                <li
                    v-if="device.sim_no"
                    class="list-group-item d-flex justify-content-between"
                >
                    <span class="text-muted">Sim Number</span>
                    <span class="fw-bold">{{ device.sim_no }}</span>
                </li>

                <li
                    v-if="device.imei_one"
                    class="list-group-item d-flex justify-content-between"
                >
                    <span class="text-muted">IMEI 1</span>
                    <span class="font-monospace small">{{
                        device.imei_one
                    }}</span>
                </li>

                <li
                    v-if="device.ram || device.rom"
                    class="list-group-item d-flex justify-content-between"
                >
                    <span class="text-muted">Hardware Specs</span>
                    <span
                        >{{ device.ram || 'N/A' }} /
                        {{ device.rom || 'N/A' }}</span
                    >
                </li>

                <li class="list-group-item d-flex justify-content-between">
                    <span class="text-muted">Date Logged</span>
                    <span>{{ formatDate(device.created_at) }}</span>
                </li>

                <li v-if="device.remarks" class="list-group-item">
                    <div class="text-muted small mb-1">Remarks</div>
                    <div class="small text-secondary">{{ device.remarks }}</div>
                </li>
            </ul>
        </div>

        <div
            class="card-footer d-flex justify-content-around border-0 bg-transparent pb-3"
        >
            <button
                class="btn btn-outline-danger btn-sm px-4"
                @click="emit('delete', device.id)"
            >
                <i class="bi bi-trash"></i>
            </button>
            <button
                class="btn btn-outline-warning btn-sm px-4"
                @click="emit('update', device)"
            >
                <i class="bi bi-pencil"></i> Edit
            </button>
        </div>
    </div>
</template>

<style scoped>
.device-image-render {
    max-height: 220px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
    display: block;
    margin: 0 auto;
}
</style>
