<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, ref, watch } from 'vue';

defineOptions({ layout: HomeLayout });

const { formatDate } = useDateFormatter();

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

// history table state
const historySearch = ref('');
const currentPage = ref(1);
const itemsPerPage = 3;

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

            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5>Device Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Manufacturer/Model:</strong>
                            {{ props.minipc.manufacturer_model }}
                        </div>
                        <div class="col-md-4">
                            <strong>CPU:</strong> {{ props.minipc.cpu }}
                        </div>
                        <div class="col-md-4">
                            <strong>RAM:</strong> {{ props.minipc.ram }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <strong>ROM:</strong> {{ props.minipc.rom }}
                        </div>
                        <div class="col-md-4">
                            <strong>MAC:</strong> {{ props.minipc.mac }}
                        </div>
                        <div class="col-md-4">
                            <strong>IP:</strong> {{ props.minipc.ip }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <strong>Name:</strong> {{ props.minipc.name }}
                        </div>
                        <div class="col-md-4">
                            <strong>Purchase Date:</strong>
                            {{ formatDate(props.minipc.purchase_date) }}
                        </div>
                        <div class="col-md-4">
                            <strong>Warranty Expiry:</strong>
                            {{ formatDate(props.minipc.warranty_expiry) }}
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <strong>Remarks:</strong> {{ props.minipc.remarks }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="props.minipc_issuance" class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5>Current Issuance</h5>
                    <p>
                        <strong>Department:</strong>
                        {{ props.minipc_issuance.department }}
                    </p>
                    <p>
                        <strong>Location:</strong>
                        {{ props.minipc_issuance.location }}
                    </p>
                    <p>
                        <strong>Date Issued:</strong>
                        {{ formatDate(props.minipc_issuance.date_issued) }}
                    </p>
                </div>
            </div>

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
</template>

<style scoped>
/* custom styling can be added */
</style>
