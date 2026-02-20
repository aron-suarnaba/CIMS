<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

defineOptions({ layout: HomeLayout });

const { formatDate, formatDateForInput } = useDateFormatter();

const props = defineProps({
    license: { type: Object, required: true },
});

const statusOptions = ['active', 'inactive', 'expired'];
const licenseTypeOptions = [
    'Per User',
    'Per Device',
    'Subscription',
    'Enterprise',
];

const updateForm = useForm({
    software_name: props.license.software_name || '',
    vendor: props.license.vendor || '',
    license_type: props.license.license_type || 'Per User',
    total_licenses: props.license.total_licenses ?? 1,
    used_licenses: props.license.used_licenses ?? 0,
    license_key: props.license.license_key || '',
    purchase_date: formatDateForInput(props.license.purchase_date) || '',
    expiry_date: formatDateForInput(props.license.expiry_date) || '',
    status: props.license.status || 'active',
    remarks: props.license.remarks || '',
});

const available = () =>
    Math.max(
        0,
        Number(updateForm.total_licenses) - Number(updateForm.used_licenses),
    );

const submitUpdate = () => {
    updateForm.put(route('software-license.update', props.license.id), {
        preserveScroll: true,
        onSuccess: () => {
            const closeButton = document.querySelector(
                '#UpdateSoftwareLicenseModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) closeButton.click();
        },
    });
};

const deleteItem = () => {
    Swal.fire({
        title: 'Delete software license?',
        text: 'This record will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('software-license.destroy', props.license.id));
        }
    });
};
</script>

<template>
    <div class="app-content-header py-3">
        <div class="container">
            <Breadcrumb
                :breadcrumbs="[
                    { label: 'Dashboard', url: route('dashboard') },
                    {
                        label: 'Asset & Inventory',
                        url: route('AssetAndInventoryManagement'),
                    },
                    {
                        label: 'Software Licenses',
                        url: route('software-license.index'),
                    },
                    { label: 'Details' },
                ]"
            />
        </div>
    </div>

    <div class="app-content mb-5">
        <div class="container px-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <BackButton
                    @click.prevent="router.get(route('software-license.index'))"
                />
                <div class="d-flex gap-2">
                    <button
                        class="btn btn-outline-warning"
                        data-bs-toggle="modal"
                        data-bs-target="#UpdateSoftwareLicenseModal"
                    >
                        <i class="bi bi-pencil me-1"></i> Update
                    </button>
                    <button class="btn btn-outline-danger" @click="deleteItem">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-primary py-3 text-white">
                            <h5 class="fw-bold mb-0">License Overview</h5>
                        </div>
                        <div class="card-body">
                            <h4 class="fw-bold mb-1">
                                {{ props.license.software_name }}
                            </h4>
                            <p class="text-muted mb-3">
                                {{ props.license.vendor || 'N/A Vendor' }}
                            </p>

                            <span class="badge bg-secondary mb-3">
                                {{ props.license.license_type }}
                            </span>

                            <ul class="list-group list-group-flush">
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span>Total</span>
                                    <strong>{{
                                        props.license.total_licenses
                                    }}</strong>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span>Used</span>
                                    <strong>{{
                                        props.license.used_licenses
                                    }}</strong>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span>Available</span>
                                    <strong>{{
                                        props.license.available_licenses ??
                                        Math.max(
                                            0,
                                            props.license.total_licenses -
                                                props.license.used_licenses,
                                        )
                                    }}</strong>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span>Status</span>
                                    <strong class="text-capitalize">{{
                                        props.license.status
                                    }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-light py-3">
                            <h5 class="fw-bold mb-0">License Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small"
                                        >Purchase Date</label
                                    >
                                    <div class="fw-semibold">
                                        {{
                                            props.license.purchase_date
                                                ? formatDate(
                                                      props.license
                                                          .purchase_date,
                                                  )
                                                : 'N/A'
                                        }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small"
                                        >Expiry Date</label
                                    >
                                    <div class="fw-semibold">
                                        {{
                                            props.license.expiry_date
                                                ? formatDate(
                                                      props.license.expiry_date,
                                                  )
                                                : 'N/A'
                                        }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small"
                                        >License Key</label
                                    >
                                    <pre
                                        class="bg-light small mb-0 rounded p-3"
                                        >{{
                                            props.license.license_key || 'N/A'
                                        }}</pre
                                    >
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small"
                                        >Remarks</label
                                    >
                                    <p class="mb-0">
                                        {{
                                            props.license.remarks ||
                                            'No remarks provided.'
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Modals
        id="UpdateSoftwareLicenseModal"
        title="Update Software License"
        size="modal-lg"
    >
        <template #body>
            <form @submit.prevent="submitUpdate" id="updateSoftwareLicenseForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Software Name</label>
                        <input
                            v-model="updateForm.software_name"
                            type="text"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vendor</label>
                        <input
                            v-model="updateForm.vendor"
                            type="text"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">License Type</label>
                        <select
                            v-model="updateForm.license_type"
                            class="form-select"
                        >
                            <option
                                v-for="opt in licenseTypeOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Total Licenses</label>
                        <input
                            v-model.number="updateForm.total_licenses"
                            type="number"
                            min="1"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Used Licenses</label>
                        <input
                            v-model.number="updateForm.used_licenses"
                            type="number"
                            min="0"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-12">
                        <small class="text-muted">
                            Available licenses:
                            <strong>{{ available() }}</strong>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Purchase Date</label>
                        <input
                            v-model="updateForm.purchase_date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date</label>
                        <input
                            v-model="updateForm.expiry_date"
                            type="date"
                            class="form-control"
                        />
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">License Key</label>
                        <textarea
                            v-model="updateForm.license_key"
                            rows="2"
                            class="form-control"
                        ></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select v-model="updateForm.status" class="form-select">
                            <option
                                v-for="opt in statusOptions"
                                :key="opt"
                                :value="opt"
                            >
                                {{ opt }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Remarks</label>
                        <textarea
                            v-model="updateForm.remarks"
                            rows="2"
                            class="form-control"
                        ></textarea>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
            >
                Cancel
            </button>
            <button
                type="submit"
                form="updateSoftwareLicenseForm"
                class="btn btn-warning"
                :disabled="updateForm.processing"
            >
                Update
            </button>
        </template>
    </Modals>
</template>
