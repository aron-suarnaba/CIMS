<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref, watch } from 'vue';

defineOptions({ layout: HomeLayout });

const { formatDate, formatDateForInput } = useDateFormatter();

const props = defineProps({
    minipc: { type: Object, required: true },
    minipc_issuance: { type: [Object, null], default: null },
    minipc_pullout: { type: [Object, null], default: null },
});

const form = useForm({
    department: '',
    location: '',
    date_issued: new Date().toISOString().substr(0, 10),
});

const pulloutForm = useForm({
    pullout_date: new Date().toISOString().substr(0, 10),
    reason: '',
});

const closeAllModals = () => {
    document.querySelectorAll('.modal.show').forEach((element) => {
        const instance = window.bootstrap?.Modal?.getInstance(element);
        instance?.hide();
    });
};

// history table state
const historySearch = ref('');
const currentPage = ref(1);
const itemsPerPage = 3;

// update form (same structure as list)
const updateForm = useForm({
    id: null,
    manufacturer_model: '',
    cpu: '',
    ram: '',
    rom: '',
    mac: '',
    ip: '',
    name: '',
    purchase_date: '',
    warranty_expiry: '',
    remarks: '',
});

const submitUpdate = () => {
    updateForm.put(route('minipc.update', updateForm.id), {
        onSuccess: () => {
            closeAllModals();
        },
        onError: (errors) => {
            const errMsg = Object.values(errors).join(', ');
            Swal.fire('Error', errMsg, 'error');
        },
    });
};

const openUpdateModal = () => {
    updateForm.id = props.minipc.id;
    updateForm.manufacturer_model = props.minipc.manufacturer_model || '';
    updateForm.cpu = props.minipc.cpu || '';
    updateForm.ram = props.minipc.ram || '';
    updateForm.rom = props.minipc.rom || '';
    updateForm.mac = props.minipc.mac || '';
    updateForm.ip = props.minipc.ip || '';
    updateForm.name = props.minipc.name || '';
    updateForm.purchase_date = formatDateForInput(props.minipc.purchase_date) || '';
    updateForm.warranty_expiry = formatDateForInput(props.minipc.warranty_expiry) || '';
    updateForm.remarks = props.minipc.remarks || '';

    const modalEl = document.getElementById('UpdateMiniPcModal');
    const bsModal = new window.bootstrap.Modal(modalEl);
    bsModal.show();
};

const confirmDelete = () => {
    Swal.fire({
        title: 'Confirm Delete Asset?',
        text: 'All data including history and transactions will be removed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('minipc.destroy', props.minipc.id), {
                onBefore: () => {
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                    });
                },
                onSuccess: () => {
                    Swal.fire('Deleted!', 'Mini PC has been removed.', 'success');
                    router.get(route('minipc.index'));
                },
                onError: () => {
                    Swal.close();
                },
            });
        }
    });
};
const filteredHistory = computed(() => {
    if (!props.minipc.issuances) return [];
    const searchTerm = historySearch.value.toLowerCase();

    return props.minipc.issuances
        .map((issuance) => ({
            ...issuance,
            pullout: issuance.pullout,
        }))
        .filter((record) => {
            return (
                (record.department?.toLowerCase() || '').includes(searchTerm) ||
                (record.location?.toLowerCase() || '').includes(searchTerm) ||
                (record.pullout?.reason?.toLowerCase() || '').includes(searchTerm)
            );
        });
});

const paginatedHistory = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredHistory.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredHistory.value.length / itemsPerPage);
});

const nextPage = () => {
    if (currentPage.value < totalPages.value) currentPage.value++;
};

const prevPage = () => {
    if (currentPage.value > 1) currentPage.value--;
};

watch(historySearch, () => {
    currentPage.value = 1;
});

const submitIssue = () => {
    form.post(route('minipc.issue', props.minipc.id), {
        onSuccess: () => {
            form.reset();
            const close = document.querySelector(
                '#IssueMiniPcModal [data-bs-dismiss="modal"]',
            );
            if (close) close.click();
            router.reload({
                only: ['minipc', 'minipc_issuance', 'minipc_pullout'],
            });
        },
        onError: (errors) => {
            let msg = 'Error issuing mini pc.';
            if (errors && Object.keys(errors).length) {
                msg = Object.values(errors).join(', ');
            }
            Swal.fire('Error', msg, 'error');
        },
    });
};

const submitPullout = () => {
    pulloutForm.post(route('minipc.pullout', props.minipc.id), {
        onSuccess: () => {
            pulloutForm.reset();
            const close = document.querySelector(
                '#PulloutMiniPcModal [data-bs-dismiss="modal"]',
            );
            if (close) close.click();
            router.reload({
                only: ['minipc', 'minipc_issuance', 'minipc_pullout'],
            });
        },
        onError: (errors) => {
            let msg = 'Error recording pullout.';
            if (errors && Object.keys(errors).length) {
                msg = Object.values(errors).join(', ');
            }
            Swal.fire('Error', msg, 'error');
        },
    });
};
</script>

<template>
    <div class="app-content-header bg-light border-bottom py-3">
        <div class="container">
            <Breadcrumb
                :breadcrumbs="[
                    { label: 'Dashboard', url: route('dashboard') },
                    {
                        label: 'Asset & Inventory',
                        url: route('AssetAndInventoryManagement'),
                    },
                    { label: 'Mini PC Units', url: route('minipc.index') },
                    { label: 'Details' },
                ]"
            />
        </div>
    </div>

    <div class="app-content mt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-4">
                    <BackButton
                        @click.prevent="router.get(route('minipc.index'))"
                    />
                </div>
            </div>

            <!-- two-column layout like phone details -->
            <div class="row g-3">
                <!-- left: device spec -->
                <div class="col-sm-12 col-xl-4 col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary py-3 text-white d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Device Specifications</h5>
                            <div>
                                <button
                                    class="btn btn-outline-warning btn-sm me-1"
                                    @click="openUpdateModal()"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="btn btn-outline-danger btn-sm"
                                    @click="confirmDelete()"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p>
                                <span class="badge"
                                    :class="{
                                        'bg-success': props.minipc.status === 'available',
                                        'bg-warning text-dark': props.minipc.status === 'issued',
                                        'bg-danger': props.minipc.status === 'pullout',
                                    }"
                                >
                                    {{ props.minipc.status }}
                                </span>
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Manufacturer/Model:</strong><br />
                                    {{ props.minipc.manufacturer_model }}
                                </div>
                                <div class="col-md-6">
                                    <strong>CPU:</strong><br />{{ props.minipc.cpu }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>RAM:</strong><br />{{ props.minipc.ram }}
                                </div>
                                <div class="col-md-6">
                                    <strong>ROM:</strong><br />{{ props.minipc.rom }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>MAC:</strong><br />{{ props.minipc.mac }}
                                </div>
                                <div class="col-md-6">
                                    <strong>IP:</strong><br />{{ props.minipc.ip }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <strong>Name:</strong><br />{{ props.minipc.name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Purchase Date:</strong><br />
                                    {{ formatDate(props.minipc.purchase_date) }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <strong>Warranty Expiry:</strong><br />
                                    {{ formatDate(props.minipc.warranty_expiry) }}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <strong>Remarks:</strong><br />
                                    {{ props.minipc.remarks }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- right: issuance / pullout cards -->
                <div class="col-sm-12 col-xl-8 col-lg-7">
                    <!-- Issuance Card -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-primary d-flex justify-content-start align-items-center text-white">
                            <i class="bi bi-send-check fs-4 me-3"></i>
                            <h5 class="fw-bold mb-0">Current Issuance</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3" v-if="props.minipc_issuance">
                                <div class="col-md-4 border-end">
                                    <label class="small text-muted text-uppercase fw-bold">Recipient</label>
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{ props.minipc_issuance.department }}
                                    </p>
                                </div>
                                <div class="col-md-4 border-end">
                                    <label class="small text-muted text-uppercase fw-bold">Location</label>
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{ props.minipc_issuance.location }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{ formatDate(props.minipc_issuance.date_issued) }}
                                    </p>
                                </div>
                                <div class="col-md-4 px-md-4">
                                    <!-- spacer for future accessories/info -->
                                </div>
                            </div>
                            <div class="card-body py-5 text-center" v-else>
                                <span class="fw-bold fs-5 text-muted">
                                    The device is not yet issued.
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Pullout Details Card -->
                    <div class="card mb-3 border-0 shadow-sm" v-if="props.minipc_pullout">
                        <div class="card-header bg-warning d-flex justify-content-start align-items-center text-dark">
                            <i class="bi bi-reply-fill fs-4 me-2"></i>
                            <h5 class="fw-bold mb-0">Pullout Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4 border-end">
                                    <label class="small text-muted text-uppercase fw-bold">Date Pulled Out</label>
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{ formatDate(props.minipc_pullout.pullout_date) }}
                                    </p>
                                </div>
                                <div class="col-md-8 px-md-4">
                                    <label class="small text-muted text-uppercase fw-bold">Reason</label>
                                    <p class="text-dark mb-0">
                                        {{ props.minipc_pullout.reason || 'No reason provided.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- action buttons when no issuance/pullout -->
                    <div v-if="!props.minipc_issuance" class="mb-3">
                        <button
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#IssueMiniPcModal"
                        >
                            Issue Device
                        </button>
                    </div>

                    <div
                        v-if="props.minipc_issuance && !props.minipc_pullout"
                        class="mb-3"
                    >
                        <button
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#PulloutMiniPcModal"
                        >
                            Record Pullout
                        </button>
                    </div>
                </div>
            </div>

            <!-- history table -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white py-3">
                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="col-sm-12 col-md-8 mt-2">
                            <h5 class="fw-bold text-primary mb-0">
                                <i class="bi bi-clock-history me-2"></i
                                >Mini PC Assignment History
                            </h5>
                        </div>
                        <div class="col-sm-12 col-md-4 my-2">
                            <div class="search-box">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        v-model="historySearch"
                                        class="form-control bg-light border-start-0"
                                        placeholder="Search history..."
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-hover mb-0 table align-middle">
                            <thead class="table-light">
                                <tr class="fs-7 text-uppercase text-muted border-top-0">
                                    <th scope="col">Date Issued</th>
                                    <th scope="col">Department</th>
                                    <th scope="col">Location</th>
                                    <th scope="col">Pullout Date</th>
                                    <th scope="col">Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    class="fs-7"
                                    v-for="tx in paginatedHistory"
                                    :key="tx.id"
                                >
                                    <td class="fw-medium mb-0 text-nowrap ps-3">
                                        {{ formatDate(tx.date_issued) }}
                                    </td>
                                    <td>
                                        <div class="text-dark">
                                            {{ tx.department }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark">
                                            {{ tx.location }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark">
                                            {{ tx.pullout
                                                ? formatDate(tx.pullout.pullout_date)
                                                : '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark">
                                            {{ tx.pullout?.reason || '-' }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="filteredHistory.length === 0">
                                    <td colspan="5" class="text-muted py-4 text-center">
                                        No transactions
                                        {{
                                            historySearch
                                                ? 'No matches found for "' +
                                                  historySearch +
                                                  '"'
                                                : 'No transaction history found.'
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div
                    class="card-footer border-top-0 bg-white py-3"
                    v-if="totalPages > 1"
                >
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Showing
                            {{ (currentPage - 1) * itemsPerPage + 1 }}
                            to
                            {{
                                Math.min(
                                    currentPage * itemsPerPage,
                                    filteredHistory.length,
                                )
                            }}
                            of {{ filteredHistory.length }} entries
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li
                                    class="page-item"
                                    :class="{ disabled: currentPage === 1 }"
                                >
                                    <button class="page-link" @click="prevPage">
                                        Previous
                                    </button>
                                </li>

                                <li
                                    v-for="page in totalPages"
                                    :key="page"
                                    class="page-item"
                                    :class="{ active: currentPage === page }"
                                >
                                    <button
                                        class="page-link"
                                        @click="currentPage = page"
                                    >
                                        {{ page }}
                                    </button>
                                </li>

                                <li
                                    class="page-item"
                                    :class="{ disabled: currentPage === totalPages }"
                                >
                                    <button class="page-link" @click="nextPage">
                                        Next
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue Modal -->
    <div
        class="modal fade"
        id="IssueMiniPcModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Issue Mini PC</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitIssue">
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="form.department"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="form.location"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Issued</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="form.date_issued"
                                required
                            />
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pullout Modal -->
    <div
        class="modal fade"
        id="PulloutMiniPcModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Pullout</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitPullout">
                        <div class="mb-3">
                            <label class="form-label">Pullout Date</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="pulloutForm.pullout_date"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea
                                class="form-control"
                                v-model="pulloutForm.reason"
                            ></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Mini PC Modal -->
    <div
        class="modal fade"
        id="UpdateMiniPcModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Mini PC</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitUpdate">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Manufacturer / Model</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.manufacturer_model"
                                    required
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CPU</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.cpu"
                                />
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">RAM</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.ram"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ROM</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.rom"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">MAC</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.mac"
                                />
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">IP</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.ip"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="updateForm.name"
                                />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Purchase Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="updateForm.purchase_date"
                                />
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Warranty Expiry</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="updateForm.warranty_expiry"
                                />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <textarea
                                    class="form-control"
                                    v-model="updateForm.remarks"
                                ></textarea>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* custom styling can be added */
</style>
