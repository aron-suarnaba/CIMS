<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';
import Modals from '@/Components/Modals.vue';
import { useForm } from '@inertiajs/vue3';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Swal from 'sweetalert2';
import { ref, watch, computed } from 'vue';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    phone: {
        type: Object,
        required: true,
    },
    phone_transaction: {
        type: [Object, null],
        required: true,
        default: null,
    },
});

// Function for breadcrumb
const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Smartphone Asset', url: route('phone.index') },
    { label: 'Smartphone Asset Details' },
];

// Function for the phone image path
const getPhoneImagePath = (phone) => {
    const defaultPath = '../../img/phone/default.png';
    if (!phone || !phone.brand) return defaultPath;

    const brand = phone.brand.toLowerCase();

    const supportedBrands = [
        'iphone',
        'apple',
        'oppo',
        'redmi',
        'samsung',
        'vivo',
        'realme',
        'xiaomi',
        'honor',
        'techno',
    ];

    // Find if the brand string contains any of our supported keywords
    const matched = supportedBrands.find((b) => brand.includes(b));

    if (matched) {
        // Handle the 'apple' keyword mapping to 'iphone.png'
        const fileName = matched === 'apple' ? 'iphone' : matched;
        return `../../img/phone/${fileName}.png`;
    }

    return defaultPath;
};

// Function for date formatting
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

// Deleting function
const deleteItem = (serial_num) => {
    Swal.fire({
        title: 'Confirm Delete Asset?',
        text: 'All the data including the table history, issuance, return information, etc., will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('phone.destroy', serial_num), {
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
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The asset record has been deleted.',
                        icon: 'success',
                    });
                },
                onError: () => {
                    Swal.close();
                },
            });
        }
    });
};

//Declaring selected accessories for issue and return
const selectedAcc = ref([]);
const selectedReturnAcc = ref([]);

// Everything about the history table variable declaration
const historySearch = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const filteredHistory = computed(() => {
    if (!props.phone.transactions) return [];
    const searchTerm = historySearch.value.toLowerCase();

    return props.phone.transactions.filter((tx) => {
        return (
            (tx.issued_to?.toLowerCase() || '').includes(searchTerm) ||
            (tx.issued_by?.toLowerCase() || '').includes(searchTerm) ||
            (tx.returned_by?.toLowerCase() || '').includes(searchTerm) ||
            (tx.department?.toLowerCase() || '').includes(searchTerm)
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

watch(historySearch, () => {
    currentPage.value = 1;
});

// Forms
// Form for Issuance
const form = useForm({
    issued_by: '',
    issued_to: '',
    department: '',
    date_issued: new Date().toISOString().substr(0, 10),
    issued_accessories: '',
    it_ack_issued: false,
    purch_ack_issued: false,
    cashout: false,
    remarks: '',
});

// Form for return
const returnform = useForm({
    returned_by: '',
    returned_to: '',
    returnee_department: '',
    date_returned: new Date().toISOString().substr(0, 10),
    returned_accessories: '',
    it_ack_returned: false,
    purch_ack_returned: false,
    remarks: '',
});

// Listeners

watch(selectedAcc, (newVal) => {
    form.issued_accessories = newVal.join(', ');
});
watch(selectedReturnAcc, (newVal) => {
    returnform.returned_accessories = newVal.join(', ');
});

//Submission
const submit = () => {
    form.post(route('phone.issue', props.phone.serial_num), {
        onSuccess: () => {
            form.reset();
            selectedAcc.value = [];

            const closeButton = document.querySelector(
                '#IssuePhoneModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }

            // const modalElement = document.getElementById('IssuePhoneModal');

            // const modalInstance =  bootstrap.Modal.getInstance(modalElement);
            // if(modalInstance){
            //     modalInstance.hide();
            // }
        },
    });
};
const returnSubmit = () => {
    returnform.post(route('phone.return', props.phone.serial_num), {
        onSuccess: () => {
            returnform.reset();
            selectedReturnAcc.value = [];

            const closeButton = document.querySelector(
                '#ReturnPhoneModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
        },
    });
};
</script>

<template>
    <div class="app-content-header border-bottom bg-white py-3">
        <!-- Breadcrumb -->
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>

    <div class="app-content mt-4">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Navigation Menu -->
                <div class="col-12">
                    <div class="card bg-light mb-4 border-0 shadow-sm">
                        <div
                            class="card-body d-flex justify-content-between align-items-center"
                        >
                            <BackButton
                                @click.prevent="
                                    router.get(route('phone.index'))
                                "
                            />
                            <div class="btn-group shadow-sm">
                                <button
                                    v-if="
                                        props.phone.status === 'available' ||
                                        props.phone.status === 'returned'
                                    "
                                    class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#IssuePhoneModal"
                                >
                                    <i class="bi bi-person-plus me-1"></i> Issue
                                    Asset
                                </button>
                                <button
                                    v-else-if="props.phone.status === 'issued'"
                                    class="btn btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#ReturnPhoneModal"
                                >
                                    <i class="bi bi-arrow-return-left me-1"></i>
                                    Process Return
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Asset Details -->
                <div class="col-sm-12 col-xl-4 col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark py-3 text-white">
                            <h5 class="fw-bold mb-0">Device Specifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 text-center">
                                <img
                                    :src="getPhoneImagePath(props.phone)"
                                    class="img-fluid bg-light rounded p-3"
                                    style="max-height: 220px"
                                    :alt="props.phone.model"
                                />
                                <h3 class="fw-bold mb-0 mt-3">
                                    {{ props.phone.brand }}
                                    {{ props.phone.model }}
                                </h3>
                                <span
                                    :class="[
                                        'badge mt-2 px-3 py-2',
                                        {
                                            'bg-success':
                                                props.phone.status ===
                                                'available',
                                            'bg-primary':
                                                props.phone.status === 'issued',
                                            'bg-warning text-dark':
                                                props.phone.status ===
                                                'returned',
                                        },
                                    ]"
                                >
                                    Status: {{ props.phone.status }}
                                </span>
                            </div>

                            <ul class="list-group list-group-flush border-top">
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted"
                                        >Serial Number</span
                                    >
                                    <span class="fw-bold">{{
                                        props.phone.serial_num
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">Sim Number</span>
                                    <span class="fw-bold">{{
                                        props.phone.sim_no
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">IMEI 1</span>
                                    <span class="font-monospace small">{{
                                        props.phone.imei_one || 'N/A'
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">IMEI 2</span>
                                    <span class="font-monospace small">{{
                                        props.phone.imei_two || 'N/A'
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">RAM / ROM</span>
                                    <span
                                        >{{ props.phone.ram || 'N/A' }} /
                                        {{ props.phone.rom || 'N/A' }}</span
                                    >
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted"
                                        >Purchase Date</span
                                    >
                                    <span>{{
                                        formatDate(props.phone.created_at) ||
                                        'N/A'
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">Remarks</span>
                                    <span>{{
                                        props.phone.remarks || 'N/A'
                                    }}</span>
                                </li>
                            </ul>
                        </div>
                        <div
                            class="card-footer d-flex justify-content-around align-items-center gap-2 border-0 bg-transparent pb-3 text-center"
                        >
                            <button
                                class="btn btn-outline-danger"
                                @click.prevent="
                                    deleteItem(props.phone.serial_num)
                                "
                            >
                                <i class="bi bi-trash me-1"></i> Delete Asset
                                Record
                            </button>
                            <button class="btn btn-outline-warning">
                                <i class="bi bi-trash me-1"></i> Update Asset
                                Record
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-8 col-lg-7">
                    <!-- Issuance Card -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div
                            class="card-header bg-primary d-flex justify-content-start align-items-center text-white"
                        >
                            <i class="bi bi-send-check fs-4 me-3"></i>
                            <h5 class="fw-bold mb-0">Current Issuance</h5>
                        </div>
                        <div class="card-body">
                            <div
                                class="row g-3"
                                v-if="
                                    props.phone?.status === 'available' ||
                                    props.phone.status === 'issued'
                                "
                            >
                                <div class="col-md-6 border-end">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Recipient Info</label
                                    >
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.phone_transaction
                                                ?.issued_to || 'Not yet issued'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        <span class="fw-bold"
                                            >Department:
                                        </span>
                                        {{
                                            props.phone_transaction
                                                ?.department || 'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-6 px-md-4">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Issuance Logistics</label
                                    >
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.phone_transaction
                                                    ?.date_issued,
                                            ) || 'Not yet issued'
                                        }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>By:</strong>
                                        {{
                                            props.phone_transaction
                                                ?.issued_by || 'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-12 mt-4">
                                    <div
                                        class="bg-light d-flex align-items-center flex-wrap gap-4 rounded p-3"
                                    >
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bi bi-headphones text-primary me-2"
                                            ></i>
                                            <strong>Accessories:</strong>
                                            <span class="text-muted ms-2">{{
                                                props.phone_transaction
                                                    ?.issued_accessories ||
                                                'None'
                                            }}</span>
                                        </div>
                                        <div
                                            class="vr d-none d-md-block mx-2"
                                        ></div>
                                        <div
                                            class="d-flex align-items-center flex-wrap"
                                        >
                                            <div class="g-1">
                                                <i
                                                    class="bi bi-check-lg me-1"
                                                ></i>
                                                <strong
                                                    >Acknowledgement:</strong
                                                >
                                            </div>
                                            <span
                                                class="badge text-dark ms-2 border text-white"
                                                :class="
                                                    props.phone_transaction
                                                        ?.it_ack_issued
                                                        ? 'bg-success'
                                                        : 'bg-danger'
                                                "
                                                >IT:
                                                {{
                                                    props.phone_transaction
                                                        ?.it_ack_issued
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                                <i
                                                    :class="
                                                        props.phone_transaction
                                                            ?.it_ack_issued
                                                            ? 'bi bi-check-circle-fill'
                                                            : 'bi-x-circle-fill'
                                                    "
                                                ></i>
                                            </span>
                                            <span
                                                class="badge text-dark ms-2 border text-white"
                                                :class="
                                                    props.phone_transaction
                                                        ?.purch_ack_issued
                                                        ? 'bg-success'
                                                        : 'bg-danger'
                                                "
                                                >Purchasing:
                                                {{
                                                    props.phone_transaction
                                                        ?.purch_ack_issued
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                                <i
                                                    :class="
                                                        props.phone_transaction
                                                            ?.it_ack_issued
                                                            ? 'bi bi-check-circle-fill'
                                                            : 'bi-x-circle-fill'
                                                    "
                                                ></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body py-5 text-center" v-else>
                                <span class="fw-bold fs-5 text-muted"
                                    >The asset is not yet issued.</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Return Details -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div
                            class="card-header bg-warning d-flex justify-content-start align-items-center text-dark"
                        >
                            <i class="bi bi-reply-fill fs-4 me-2"></i>
                            <h5 class="fw-bold mb-0">Return Details</h5>
                        </div>
                        <div
                            class="card-body"
                            v-if="props.phone?.status === 'available'"
                        >
                            <div class="row g-3">
                                <div class="col-md-4 border-end">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Recipient Info</label
                                    >
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.phone_transaction
                                                ?.returned_by ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        <span class="fw-bold"
                                            >Department:
                                        </span>
                                        {{
                                            props.phone_transaction
                                                ?.returnee_department ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-4 border-end">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Return To:</label
                                    >
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.phone_transaction
                                                ?.returned_to ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-4 px-md-4">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Issuance Logistics</label
                                    >
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.phone_transaction
                                                    ?.date_returned,
                                            ) || 'Not yet returned'
                                        }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>By:</strong>
                                        {{
                                            props.phone_transaction
                                                ?.issued_by ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                </div>

                                <div class="col-12 mt-4">
                                    <div
                                        class="bg-light d-flex align-items-center flex-wrap gap-4 rounded p-3"
                                    >
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bi bi-headphones text-primary me-2"
                                            ></i>
                                            <strong>Accessories:</strong>
                                            <span class="text-muted ms-2">{{
                                                props.phone_transaction
                                                    ?.returned_accessories ||
                                                'None'
                                            }}</span>
                                        </div>
                                        <div
                                            class="vr d-none d-md-block mx-2"
                                        ></div>
                                        <div
                                            class="d-flex align-items-center flex-wrap"
                                        >
                                            <strong>Acknowledgement:</strong>

                                            <span
                                                class="badge ms-2 border text-white"
                                                :class="
                                                    props.phone_transaction
                                                        ?.it_ack_returned
                                                        ? 'bg-success'
                                                        : 'bg-danger'
                                                "
                                            >
                                                IT:
                                                {{
                                                    props.phone_transaction
                                                        ?.it_ack_returned
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                                <i
                                                    class="bi ms-1"
                                                    :class="
                                                        props.phone_transaction
                                                            ?.it_ack_returned
                                                            ? 'bi-check-circle-fill'
                                                            : 'bi-x-circle-fill'
                                                    "
                                                ></i>
                                            </span>

                                            <span
                                                class="badge ms-2 border text-white"
                                                :class="
                                                    props.phone_transaction
                                                        ?.purch_ack_returned
                                                        ? 'bg-success'
                                                        : 'bg-danger'
                                                "
                                            >
                                                Purchasing:
                                                {{
                                                    props.phone_transaction
                                                        ?.purch_ack_returned
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                                <i
                                                    class="bi ms-1"
                                                    :class="
                                                        props.phone_transaction
                                                            ?.purch_ack_returned
                                                            ? 'bi-check-circle-fill'
                                                            : 'bi-x-circle-fill'
                                                    "
                                                ></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="card-body bg-light py-5 text-center"
                            v-else-if="props.phone.status === 'issued'"
                        >
                            <span class="fw-bold fs-5 text-dark text-muted mb-1"
                                >The asset is currently deploy (not yet
                                returned).
                            </span>
                        </div>
                        <div class="card-body bg-light py-5 text-center" v-else>
                            <span class="fw-bold fs-5 text-dark text-muted mb-1"
                                >The asset is not yet issued.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="row g-4 mb-5">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <div
                                    class="row d-flex justify-content-between align-items-center"
                                >
                                    <div class="col-sm-12 col-md-8 mt-2">
                                        <h5 class="fw-bold text-primary mb-0">
                                            <i
                                                class="bi bi-clock-history me-2"
                                            ></i
                                            >Asset Transaction History
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
                                                <th class="ps-3" scope="col">
                                                    Date Issued
                                                </th>
                                                <th scope="col">Issued To</th>
                                                <th scope="col">Issued By</th>
                                                <th scope="col">Issued Acc.</th>
                                                <th scope="col">
                                                    Date Returned
                                                </th>
                                                <th scope="col">Returned By</th>
                                                <th scope="col">Returned To</th>
                                                <th scope="col" class="pe-3">
                                                    Returned Acc.
                                                </th>
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
                                                    <div
                                                        class="fw-bold text-dark"
                                                    >
                                                        {{ tx.issued_to }}
                                                    </div>
                                                    <div
                                                        class="text-muted small ps-2"
                                                    >
                                                        {{ tx.department }}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div
                                                        class="fw-bold text-dark"
                                                    >
                                                        {{ tx.issued_by }}
                                                    </div>
                                                </td>

                                                <td
                                                    class="small text-wrap"
                                                    style="max-width: 150px"
                                                >
                                                    {{
                                                        tx.issued_accessories ||
                                                        '—'
                                                    }}
                                                </td>

                                                <td class="text-nowrap">
                                                    <span
                                                        v-if="tx.date_returned"
                                                        class="fw-medium mb-0 text-nowrap ps-2"
                                                    >
                                                        {{
                                                            formatDate(
                                                                tx.date_returned,
                                                            )
                                                        }}
                                                    </span>
                                                    <span
                                                        v-else
                                                        class="badge rounded-pill bg-warning text-dark"
                                                        >In Use</span
                                                    >
                                                </td>

                                                <td>
                                                    <div
                                                        class="fw-bold text-dark"
                                                    >
                                                        {{
                                                            tx.returned_by ||
                                                            '—'
                                                        }}
                                                    </div>
                                                    <div
                                                        class="text-muted small ps-2"
                                                    >
                                                        {{
                                                            tx.returnee_department
                                                        }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div
                                                        class="fw-bold text-dark"
                                                    >
                                                        {{ tx.returned_to }}
                                                    </div>
                                                </td>

                                                <td
                                                    class="small text-muted text-wrap pe-3"
                                                    style="max-width: 150px"
                                                >
                                                    {{
                                                        tx.returned_accessories ||
                                                        '—'
                                                    }}
                                                </td>
                                            </tr>
                                            <tr
                                                v-if="
                                                    filteredHistory.length === 0
                                                "
                                            >
                                                <td
                                                    colspan="8"
                                                    class="text-muted py-4 text-center"
                                                >
                                                    No transaction
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
                                                    @click="currentPage--"
                                                >
                                                    Previous
                                                </button>
                                            </li>

                                            <li
                                                v-for="page in totalPages"
                                                :key="page"
                                                class="page-item"
                                                :class="{
                                                    active:
                                                        currentPage === page,
                                                }"
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
                                                :class="{
                                                    disabled:
                                                        currentPage ===
                                                        totalPages,
                                                }"
                                            >
                                                <button
                                                    class="page-link"
                                                    @click="currentPage++"
                                                >
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
            </div>
        </div>
    </div>

    <!-- Issuance Modal -->
    <Modals
        id="IssuePhoneModal"
        title="Issue Phone Asset"
        header-class="bg-primary text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="submit" id="issueForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="issued_to" class="form-label"
                            >Issued To</label
                        >
                        <input
                            type="text"
                            id="issued_to"
                            v-model="form.issued_to"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="issued_by" class="form-label"
                            >Issued By</label
                        >
                        <input
                            type="text"
                            id="issued_by"
                            v-model="form.issued_by"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="department" class="form-label"
                            >Department</label
                        >
                        <input
                            type="text"
                            id="department"
                            v-model="form.department"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="date_issued" class="form-label"
                            >Date Issued</label
                        >
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
                    <label class="form-label text-muted small fw-bold"
                        >Acknowledgement</label
                    >
                    <div
                        class="d-flex justify-content-around gap-4 rounded border p-2"
                    >
                        <div class="form-check">
                            <input
                                type="checkbox"
                                v-model="form.it_ack_issued"
                                id="ITAcknowledgement"
                                class="form-check-input"
                            />
                            <label
                                for="ITAcknowledgement"
                                class="form-check-label"
                                >IT Dept</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                type="checkbox"
                                v-model="form.purch_ack_issued"
                                id="PurchAcknowledgement"
                                class="form-check-input"
                            />
                            <label
                                for="PurchAcknowledgement"
                                class="form-check-label"
                                >Purchasing</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold"
                        >Select Accessories</label
                    >
                    <div
                        class="d-flex justify-content-around gap-2 rounded border p-2"
                    >
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Charger"
                                v-model="selectedAcc"
                                id="chargerCheckInput"
                            />
                            <label
                                class="form-check-label"
                                for="chargerCheckInput"
                                >Charger</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Headphones"
                                v-model="selectedAcc"
                                id="headphonesCheckInput"
                            />
                            <label
                                class="form-check-label"
                                for="headphonesCheckInput"
                                >Headphones</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="issued_accessories_summary" class="form-label"
                        >Other / All Accessories (Summary)</label
                    >
                    <textarea
                        id="issued_accessories_summary"
                        v-model="form.issued_accessories"
                        class="form-control"
                        rows="2"
                        placeholder="e.g. Charger, USB-C Cable"
                    ></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block text-muted small fw-bold"
                        ><i class="text-danger me-1">*</i>Cashout Status</label
                    >

                    <div
                        class="d-flex justify-content-center align-items-center gap-2 rounded border py-2"
                    >
                        <div class="form-check form-check-inline mb-2">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="with_cashout"
                                value="1"
                                v-model="form.cashout"
                            />
                            <label class="form-check-label" for="with_cashout"
                                >With Cashout</label
                            >
                        </div>

                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="without_cashout"
                                value="0"
                                v-model="form.cashout"
                            />
                            <label
                                class="form-check-label"
                                for="without_cashout"
                                >Without Cashout</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="issueRemarks" class="form-label">Remarks</label>
                    <textarea
                        v-model="form.remarks"
                        id="issueRemarks"
                        rows="3"
                        class="form-control"
                    ></textarea>
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
                form="issueForm"
                :disabled="form.processing"
            >
                <span
                    v-if="form.processing"
                    class="spinner-border spinner-border-sm me-1"
                ></span>
                Issue Asset
            </button>
        </template>
    </Modals>

    <!-- Return Modal -->
    <Modals
        id="ReturnPhoneModal"
        title="Return Phone Asset"
        header-class="bg-warning text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="returnSubmit" id="returnForm">
                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="returned_to" class="form-label"
                            >Returned To</label
                        >
                        <input
                            type="text"
                            class="form-control"
                            id="returned_to"
                            v-model="returnform.returned_to"
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="date_returned" class="form-label"
                            >Date Returned</label
                        >
                        <input
                            type="date"
                            id="date_returned"
                            v-model="returnform.date_returned"
                            class="form-control"
                            required
                        />
                    </div>
                </div>
                <div class="row d-flex justify-content-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="returned_by" class="form-label"
                            >Returned By</label
                        >
                        <input
                            type="text"
                            id="returned_by"
                            v-model="returnform.returned_by"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="returnee_department" class="form-label"
                            >Department</label
                        >
                        <input
                            type="text"
                            id="returnee_department"
                            v-model="returnform.returnee_department"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Acknowledgement</label>
                    <div
                        class="d-flex justify-content-around align-items-center g-2 rounded border p-2"
                    >
                        <div class="form-check">
                            <input
                                type="checkbox"
                                v-model="returnform.it_ack_returned"
                                id="ITReturnAcknowledgement"
                                class="form-check-input"
                            />
                            <label
                                for="ITReturnAcknowledgement"
                                class="form-check-label"
                                >IT</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                type="checkbox"
                                v-model="returnform.purch_ack_returned"
                                id="PurchReturnAcknowledgement"
                                class="form-check-input"
                            />
                            <label
                                for="PurchReturnAcknowledgement"
                                class="form-check-label"
                                >Purchasing</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Accessories</label>
                    <div
                        class="d-flex justify-content-around align-items-center rounded border p-2"
                    >
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Charger"
                                v-model="selectedReturnAcc"
                                id="chargerReturnCheckInput"
                            />
                            <label
                                class="form-check-label"
                                for="chargerReturnCheckInput"
                                >Charger</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Headphones"
                                v-model="selectedReturnAcc"
                                id="headphonesReturnCheckInput"
                            />
                            <label
                                class="form-check-label"
                                for="headphonesReturnCheckInput"
                                >Headphones</label
                            >
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="Case"
                                v-model="selectedReturnAcc"
                                id="caseReturnCheckInput"
                            />
                            <label
                                class="form-check-label"
                                for="caseReturnCheckInput"
                                >Case</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="returned_accessories_summary" class="form-label"
                        >Other / All Accessories (Summary)</label
                    >
                    <input
                        type="text"
                        id="returned_accessories_summary"
                        v-model="returnform.returned_accessories"
                        class="form-control"
                        placeholder="e.g. Charger, USB-C Cable"
                    />
                </div>

                <div class="mb-3">
                    <label for="remarksTextarea" class="form-label"
                        >Remarks</label
                    >
                    <textarea
                        v-model="returnform.remarks"
                        id="remarksTextarea"
                        class="form-control"
                        rows="3"
                    ></textarea>
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
                class="btn btn-primary"
                form="returnForm"
                :disabled="returnform.processing"
            >
                <span
                    v-if="returnform.processing"
                    class="spinner-border spinner-border-sm me-1"
                ></span>
                Return
            </button>
        </template>
    </Modals>
</template>

<style scoped>
tr td {
    align-items: center;
}
</style>
