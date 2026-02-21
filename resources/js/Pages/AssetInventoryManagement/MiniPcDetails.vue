<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
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
    issued_to: '',
    issued_by: '',
    department: '',
    location: '',
    date_issued: new Date().toISOString().substr(0, 10),
    issued_accessories: '',
    acknowledgement: false,
});
const selectedAcc = ref([]);

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
    updateForm.purchase_date =
        formatDateForInput(props.minipc.purchase_date) || '';
    updateForm.warranty_expiry =
        formatDateForInput(props.minipc.warranty_expiry) || '';
    updateForm.remarks = props.minipc.remarks || '';

    const modalEl = document.getElementById('UpdateMiniPcModal');
    const bsModal = new window.bootstrap.Modal(modalEl);
    bsModal.show();
};

const openIssueModal = () => {
    const modalElement = document.getElementById('IssueMiniPcModal');
    if (!modalElement) return;

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement, {
        backdrop: true,
        keyboard: true,
    });

    modal.show();
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
                    Swal.fire(
                        'Deleted!',
                        'Mini PC has been removed.',
                        'success',
                    );
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
                (record.pullout?.reason?.toLowerCase() || '').includes(
                    searchTerm,
                )
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
    <div class="app-content-header py-3">
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

    <div class="app-content mb-5">
        <div class="container px-3">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card mb-4 border-0 bg-transparent shadow-none">
                        <div class="card-body">
                            <div
                                class="row d-flex justify-content-between align-items-center"
                            >
                                <div class="col-sm-12 col-md-6 mb-3">
                                    <BackButton
                                        @click.prevent="
                                            router.get(route('minipc.index'))
                                        "
                                    />
                                </div>
                                <div
                                    class="col-md-6 col-sm-12 d-flex justify-content-end align-items-center mb-3 gap-2"
                                >
                                    <button
                                        v-if="
                                            props.minipc.status ===
                                                'available' ||
                                            props.minipc.status === 'returned'
                                        "
                                        class="btn btn-primary"
                                        @click="openIssueModal"
                                    >
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Issue Asset
                                    </button>
                                    <button
                                        v-else-if="
                                            props.phone.status === 'issued'
                                        "
                                        class="btn btn-warning"
                                        @click="openReturnModal"
                                    >
                                        <i
                                            class="bi bi-arrow-return-left me-1"
                                        ></i>
                                        Process Return
                                    </button>
                                    <button
                                        @click="
                                            generateLogsheet(props.phone.id)
                                        "
                                        class="btn btn-secondary"
                                    >
                                        <i class="bi bi-receipt me-1"></i>
                                        Generate Logsheet
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-12 col-xl-4 col-lg-5">
                        <!-- Device Details Card -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary py-3 text-white">
                                <h5 class="fw-bold mb-0">
                                    Device Specification
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-4 text-center">
                                    <img
                                        src="/CIMS/public/img/miniPC/default.png"
                                        class="img-fluid bg-light rounded p-3"
                                        style="max-height: 220px"
                                        :alt="props.minipc.manufacturer_model"
                                    />
                                    <h3 class="fw-bold mb-0 mt-3">
                                        {{ props.minipc.name }}
                                    </h3>
                                    <p class="text-muted small mb-2">
                                        {{ props.minipc.manufacturer_model }}
                                    </p>
                                    <span
                                        :class="[
                                            'badge px-3 py-2',
                                            {
                                                'bg-success':
                                                    props.minipc.status ===
                                                    'available',
                                                'bg-primary':
                                                    props.minipc.status ===
                                                    'issued',
                                                'bg-danger':
                                                    props.minipc.status ===
                                                    'pullout',
                                            },
                                        ]"
                                    >
                                        {{ props.minipc.status.toUpperCase() }}
                                    </span>
                                </div>

                                <ul
                                    class="list-group list-group-flush border-top"
                                >
                                    <li
                                        class="list-group-item d-flex justify-content-between align-items-center"
                                    >
                                        <span class="text-muted">CPU</span>
                                        <span class="fw-bold text-end">{{
                                            props.minipc.cpu
                                        }}</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between"
                                    >
                                        <span class="text-muted"
                                            >RAM / ROM</span
                                        >
                                        <span class="fw-bold"
                                            >{{ props.minipc.ram }} /
                                            {{ props.minipc.rom }}</span
                                        >
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between"
                                    >
                                        <span class="text-muted"
                                            >Network (MAC/IP)</span
                                        >
                                        <div class="text-end">
                                            <div class="font-monospace small">
                                                {{ props.minipc.mac }}
                                            </div>
                                            <div class="text-primary small">
                                                {{ props.minipc.ip }}
                                            </div>
                                        </div>
                                    </li>
                                    <li
                                        v-if="props.minipc.status === 'issued'"
                                        class="list-group-item bg-light py-3"
                                    >
                                        <div
                                            class="d-flex justify-content-between"
                                        >
                                            <span
                                                class="text-muted small fw-bold uppercase"
                                                >Issued To</span
                                            >
                                            <span class="text-muted small">{{
                                                formatDate(
                                                    props.minipc.date_issuance,
                                                )
                                            }}</span>
                                        </div>
                                        <div class="fw-bold">
                                            {{ props.minipc.department }}
                                        </div>
                                        <div class="small">
                                            {{ props.minipc.location }}
                                        </div>
                                    </li>
                                    <li
                                        v-if="props.minipc.status === 'pullout'"
                                        class="list-group-item border-start border-danger border-4"
                                    >
                                        <span class="text-muted small d-block"
                                            >Pullout Details ({{
                                                formatDate(
                                                    props.minipc.pullout_date,
                                                )
                                            }})</span
                                        >
                                        <span class="text-danger fw-bold">{{
                                            props.minipc.reason_for_pullout
                                        }}</span>
                                    </li>
                                    <li
                                        class="list-group-item d-flex justify-content-between"
                                    >
                                        <span class="text-muted"
                                            >Warranty Expiry</span
                                        >
                                        <span>{{
                                            formatDate(
                                                props.minipc.warranty_expiry,
                                            )
                                        }}</span>
                                    </li>
                                    <li class="list-group-item border-bottom-0">
                                        <span class="text-muted d-block mb-1"
                                            >Remarks</span
                                        >
                                        <p class="small text-secondary mb-0">
                                            {{
                                                props.minipc.remarks ||
                                                'No additional notes'
                                            }}
                                        </p>
                                    </li>
                                </ul>
                            </div>
                            <div
                                class="card-footer d-flex justify-content-around align-items-center mb-3 border-0 bg-transparent pb-3 text-center"
                            >
                                <button
                                    class="btn btn-outline-danger"
                                    @click.prevent="confirmDelete()"
                                >
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                                <button
                                    class="btn btn-outline-warning"
                                    @click="openUpdateModal()"
                                >
                                    <i class="bi bi-pencil me-1"></i> Update
                                </button>
                            </div>
                        </div>

                        <!-- Pullout Details Card -->
                        <!-- <div
                            class="card mb-3 border-0 shadow-sm"
                            v-if="props.minipc_pullout"
                        >
                            <div
                                class="card-header bg-warning d-flex justify-content-start align-items-center text-dark"
                            >
                                <i class="bi bi-reply-fill fs-4 me-2"></i>
                                <h5 class="fw-bold mb-0">Pullout Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4 border-end">
                                        <label
                                            class="small text-muted text-uppercase fw-bold"
                                            >Date Pulled Out</label
                                        >
                                        <p class="fw-bold fs-5 text-dark mb-1">
                                            {{
                                                formatDate(
                                                    props.minipc_pullout
                                                        .pullout_date,
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div class="col-md-8 px-md-4">
                                        <label
                                            class="small text-muted text-uppercase fw-bold"
                                            >Reason</label
                                        >
                                        <p class="text-dark mb-0">
                                            {{
                                                props.minipc_pullout.reason ||
                                                'No reason provided.'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- action buttons when no issuance/pullout -->
                        <!-- <div v-if="!props.minipc_issuance" class="mb-3">
                            <button
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#IssueMiniPcModal"
                            >
                                Issue Device
                            </button>
                        </div> -->

                        <!-- <div
                            v-if="
                                props.minipc_issuance && !props.minipc_pullout
                            "
                            class="mb-3"
                        >
                            <button
                                class="btn btn-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#PulloutMiniPcModal"
                            >
                                Record Pullout
                            </button>
                        </div> -->
                    </div>

                    <div class="col-sm-12 col-xl-8 col-lg-7">
                        <div class="card mb-3 border-0 shadow-sm">
                            <div
                                class="card-header bg-primary d-flex align-items-center p-3 text-white"
                            >
                                <i class="bi bi-send-check fs-4 me-2"></i>
                                <h5 class="fw-bold mb-0">Current Issuance</h5>
                            </div>

                            <div class="card-body p-4">
                                <div
                                    class="row g-3"
                                    v-if="
                                        props.minipc?.status === 'available' ||
                                        props.minipc?.status === 'issued'
                                    "
                                >
                                    <div class="col-md-6 border-end">
                                        <label
                                            class="small text-muted text-uppercase fw-bold d-block mb-2"
                                            >Deployment Info</label
                                        >
                                        <div
                                            class="d-flex align-items-start mb-2"
                                        >
                                            <i
                                                class="bi bi-building text-primary me-2 mt-1"
                                            ></i>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    Department
                                                </div>
                                                <div
                                                    class="fs-6 text-secondary"
                                                >
                                                    {{
                                                        props.minipc_issuance
                                                            ?.department ||
                                                        'Not yet issued'
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <i
                                                class="bi bi-geo-alt text-danger me-2 mt-1"
                                            ></i>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    Location
                                                </div>
                                                <div
                                                    class="fs-6 text-secondary"
                                                >
                                                    {{
                                                        props.minipc_issuance
                                                            ?.location ||
                                                        'Not yet issued'
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 ps-md-4">
                                        <label
                                            class="small text-muted text-uppercase fw-bold d-block mb-2"
                                            >Logistics</label
                                        >
                                        <div class="d-flex align-items-start">
                                            <i
                                                class="bi bi-calendar-event text-success me-2 mt-1"
                                            ></i>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    Date Issued
                                                </div>
                                                <div
                                                    class="fs-6 text-secondary"
                                                >
                                                    {{
                                                        formatDate(
                                                            props
                                                                .minipc_issuance
                                                                ?.date_issued,
                                                        ) || 'Not yet issued'
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-5 text-center" v-else>
                                    <i
                                        class="bi bi-clipboard-x fs-1 text-muted d-block mb-3"
                                    ></i>
                                    <span class="fw-bold fs-5 text-muted"
                                        >The asset is currently not
                                        issued.</span
                                    >
                                </div>

                                <div class="col-md-6 ps-md-4"></div>
                            </div>
                        </div>

                        <div class="card mb-3 border-0 shadow-sm">
                            <div
                                class="card-header bg-warning d-flex justify-content-start align-items-center text-dark p-3"
                            >
                                <i
                                    class="bi bi-arrow-return-left fs-4 me-2"
                                ></i>
                                <h5 class="fw-bold mb-0">Pullout Details</h5>
                            </div>

                            <div
                                class="card-body p-4"
                                v-if="props.minipc?.status === 'pullout'"
                            >
                                <div class="row g-3">
                                    <div class="col-md-5 border-end">
                                        <label
                                            class="small text-muted text-uppercase fw-bold d-block mb-2"
                                            >Schedule Info</label
                                        >
                                        <div class="d-flex align-items-start">
                                            <i
                                                class="bi bi-calendar-x text-danger me-2 mt-1"
                                            ></i>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    Pullout Date
                                                </div>
                                                <div
                                                    class="fs-6 text-secondary"
                                                >
                                                    {{
                                                        formatDate(
                                                            props.minipc
                                                                ?.pullout_date,
                                                        ) || 'No date recorded'
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7 ps-md-4">
                                        <label
                                            class="small text-muted text-uppercase fw-bold d-block mb-2"
                                            >Reason & Remarks</label
                                        >
                                        <div class="d-flex align-items-start">
                                            <i
                                                class="bi bi-chat-left-dots text-primary me-2 mt-1"
                                            ></i>
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    Reason for Pullout
                                                </div>
                                                <div
                                                    class="fs-6 text-secondary italic"
                                                >
                                                    "{{
                                                        props.minipc?.reason ||
                                                        'No reason provided'
                                                    }}"
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="card-body py-5 text-center"
                                v-else-if="props.minipc?.status === 'issued'"
                            >
                                <i
                                    class="bi bi-info-circle text-muted fs-2 d-block mb-2"
                                ></i>
                                <span class="fw-bold fs-5 text-muted">
                                    The asset is currently deployed and has not
                                    been pulled out.
                                </span>
                            </div>

                            <div class="card-body py-5 text-center" v-else>
                                <i
                                    class="bi bi-box-seam text-muted fs-2 d-block mb-2"
                                ></i>
                                <span class="fw-bold fs-5 text-muted">
                                    The asset is in inventory (never issued).
                                </span>
                            </div>
                        </div>

                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div
                                    class="row d-flex justify-content-between align-items-center"
                                >
                                    <div class="col-sm-12 col-md-8 mt-2">
                                        <h5 class="fw-bold text-primary mb-0">
                                            <i
                                                class="bi bi-clock-history me-2"
                                            ></i
                                            >Mini PC Assignment History
                                        </h5>
                                    </div>
                                    <div class="col-sm-12 col-md-4 my-2">
                                        <div class="search-box">
                                            <div
                                                class="input-group input-group-sm"
                                            >
                                                <span
                                                    class="input-group-text bg-light border-end-0"
                                                >
                                                    <i
                                                        class="bi bi-search text-muted"
                                                    ></i>
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
                                    <table
                                        class="table-hover mb-0 table align-middle"
                                    >
                                        <thead class="table-light">
                                            <tr
                                                class="fs-7 text-uppercase text-muted border-top-0"
                                            >
                                                <th scope="col">Date Issued</th>
                                                <th scope="col">Department</th>
                                                <th scope="col">Location</th>
                                                <th scope="col">
                                                    Pullout Date
                                                </th>
                                                <th scope="col">Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr
                                                class="fs-7"
                                                v-for="tx in paginatedHistory"
                                                :key="tx.id"
                                            >
                                                <td
                                                    class="fw-medium mb-0 text-nowrap ps-3"
                                                >
                                                    {{
                                                        formatDate(
                                                            tx.date_issued,
                                                        )
                                                    }}
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
                                                        {{
                                                            tx.pullout
                                                                ? formatDate(
                                                                      tx.pullout
                                                                          .pullout_date,
                                                                  )
                                                                : '-'
                                                        }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-dark">
                                                        {{
                                                            tx.pullout
                                                                ?.reason || '-'
                                                        }}
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr
                                                v-if="
                                                    filteredHistory.length === 0
                                                "
                                            >
                                                <td
                                                    colspan="5"
                                                    class="text-muted py-4 text-center"
                                                >
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
                                <div
                                    class="d-flex justify-content-between align-items-center"
                                >
                                    <div class="text-muted small">
                                        Showing
                                        {{
                                            (currentPage - 1) * itemsPerPage + 1
                                        }}
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
                                        <ul
                                            class="pagination pagination-sm mb-0"
                                        >
                                            <li
                                                class="page-item"
                                                :class="{
                                                    disabled: currentPage === 1,
                                                }"
                                            >
                                                <button
                                                    class="page-link"
                                                    @click="currentPage = 1"
                                                >
                                                    &lt;&lt;
                                                </button>
                                            </li>
                                            <li
                                                class="page-item"
                                                :class="{
                                                    disabled: currentPage === 1,
                                                }"
                                            >
                                                <button
                                                    class="page-link"
                                                    @click="prevPage"
                                                >
                                                    previous
                                                </button>
                                            </li>

                                            <li class="page-item disabled">
                                                <span class="page-link">
                                                    {{ currentPage }}
                                                </span>
                                            </li>

                                            <li
                                                class="page-item"
                                                :class="{
                                                    disabled:
                                                        currentPage ===
                                                        totalPages,
                                                }"
                                            >
                                                <button
                                                    class="page-link"
                                                    @click="nextPage"
                                                >
                                                    next
                                                </button>
                                            </li>
                                            <li
                                                class="page-item"
                                                :class="{
                                                    disabled:
                                                        currentPage ===
                                                        totalPages,
                                                }"
                                            >
                                                <button
                                                    class="page-link"
                                                    @click="
                                                        currentPage = totalPages
                                                    "
                                                >
                                                    &gt;&gt;
                                                </button>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Issue Modal -->
    <Modals
        id="IssueMiniPcModal"
        title="Issue Mini PC Asset"
        header-class="bg-primary text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="submitIssue" id="issueMiniPcForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="issued_to" class="form-label">
                            Issued To <i class="text-danger">*</i>
                        </label>
                        <input
                            type="text"
                            id="issued_to"
                            v-model="form.issued_to"
                            class="form-control"
                            placeholder="Employee Name"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="issued_by" class="form-label">
                            Issued By <i class="text-danger">*</i>
                        </label>
                        <input
                            type="text"
                            id="issued_by"
                            v-model="form.issued_by"
                            class="form-control"
                            placeholder="IT Personnel"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department" class="form-label">
                            Department <i class="text-danger">*</i>
                        </label>
                        <input
                            type="text"
                            id="department"
                            v-model="form.department"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label">
                            Location <i class="text-danger">*</i>
                        </label>
                        <input
                            type="text"
                            id="location"
                            v-model="form.location"
                            class="form-control"
                            placeholder="e.g. 2nd Floor, Server Room"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="date_issued" class="form-label">
                            Date Issued <i class="text-danger">*</i>
                        </label>
                        <input
                            type="date"
                            id="date_issued"
                            v-model="form.date_issued"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label"
                        >Included Peripherals
                        <i class="text-danger">*</i></label
                    >
                    <div
                        class="d-flex justify-content-around gap-2 rounded border p-2"
                    >
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Power Adapter"
                                v-model="selectedAcc"
                                id="adapterCheck"
                            />
                            <label class="form-check-label" for="adapterCheck"
                                >Power Adapter</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="HDMI Cable"
                                v-model="selectedAcc"
                                id="hdmiCheck"
                            />
                            <label class="form-check-label" for="hdmiCheck"
                                >HDMI Cable</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Keyboard/Mouse"
                                v-model="selectedAcc"
                                id="kbCheck"
                            />
                            <label class="form-check-label" for="kbCheck"
                                >KB/Mouse</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="issued_accessories_summary" class="form-label"
                        >Additional Specs/Remarks</label
                    >
                    <textarea
                        id="issued_accessories_summary"
                        v-model="form.issued_accessories"
                        class="form-control"
                        rows="2"
                        placeholder="e.g. 16GB RAM, Monitor Included"
                    ></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Acknowledgement</label>
                    <div
                        class="d-flex justify-content-around align-items-center rounded border pb-2 pt-3"
                    >
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="acknowledgement"
                                v-model="form.acknowledgement"
                            />
                            <label
                                for="acknowledgement"
                                class="form-label text-primary fw-bold"
                                >Information Technology Verified</label
                            >
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                Cancel
            </button>
            <button
                type="submit"
                class="btn btn-primary px-4"
                form="issueMiniPcForm"
                :disabled="form.processing"
            >
                <span
                    v-if="form.processing"
                    class="spinner-border spinner-border-sm me-1"
                ></span>
                Issue Mini PC
            </button>
        </template>
    </Modals>

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
                                <label class="form-label"
                                    >Manufacturer / Model</label
                                >
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
                                <label class="form-label"
                                    >Warranty Expiry</label
                                >
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
