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

const today = new Date().toLocaleDateString();

const props = defineProps({
    computer: {
        type: Object,
        required: true,
    },
    computer_transaction: {
        type: [Object, null],
        required: true,
        default: null,
    },
});

// Function for breadcrumb
const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Workstation', url: route('computer.index') },
    { label: 'Workstation Details' },
];

// Function for the computer image path
const getComputerImagePath = (computer) => {
    const defaultPath = '../../img/computer/default.png';
    if (!computer || !computer.manufacturer) return defaultPath;

    const manufacturer = computer.manufacturer.toLowerCase();

    const supportedManufacturer = [
        'dell',
        'hp',
        'lenovo',
        'apple',
        'asus',
        'acer',
    ];

    const matched = supportedManufacturer.find((b) => manufacturer.includes(b));

    if (matched) {
        const fileName = matched; // use manufacturer name directly for computer images
        return `../../img/computer/${fileName}.png`;
    }

    return defaultPath;
};

// Function for date formatting
const formatDate = (dateString, locale = 'en-US') => {
    if (!dateString) return '';
    const date = new Date(dateString);

    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(date);
};

const deleteItem = (host_name) => {
    Swal.fire({
        title: 'Confirm Delete Workstation?',
        text: 'All the data including the table history, issuance, return information, etc., will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('computer.destroy', host_name), {
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


// Everything about the history table variable declaration
const historySearch = ref('');
const currentPage = ref(1);
const itemsPerPage = 10;

const filteredHistory = computed(() => {
    if (!props.computer?.transactions) return [];
    const searchTerm = historySearch.value.toLowerCase();

    return props.computer.transactions.filter((tx) => {
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
const issueform = useForm({
    assigned_user: '',
    department: '',
    date_issued: new Date().toISOString().substr(0, 10),
    remarks: '',
});

// Form for return
const returnform = useForm({
    pullout_reason: '',
    pullout_date: new Date().toISOString().substr(0, 10),
    remarks: '',
});


//Submission
const submit = () => {
    issueform.post(route('computer.issue', props.computer.host_name), {
        onSuccess: () => {
            issueform.reset();

            const closeButton = document.querySelector(
                '#IssueComputerModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
        },
    });
};
const returnSubmit = () => {
    returnform.post(route('computer.return', props.computer.host_name), {
        onSuccess: () => {
            returnform.reset();

            const closeButton = document.querySelector(
                '#ReturnComputerModal [data-bs-dismiss="modal"]',
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
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <BackButton @click.prevent="
                                router.get(route('computer.index'))
                                " />
                            <div class="btn-group shadow-sm">
                                <button v-if="
                                    props.computer.status === 'In Storage' ||
                                    props.computer.status === 'In Repair'
                                " class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#IssueComputerModal">
                                    <i class="bi bi-person-plus me-1"></i> Deploy
                                    Workstation
                                </button>
                                <button v-else-if="props.computer.status === 'In Use'" class="btn btn-warning"
                                    data-bs-toggle="modal" data-bs-target="#ReturnComputerModal">
                                    <i class="bi bi-arrow-return-left me-1"></i>
                                    Pullout Workstation
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
                                <img :src="getComputerImagePath(props.computer)" class="img-fluid bg-light rounded p-3"
                                    style="max-height: 220px" :alt="props.computer.model" />
                                <h3 class="fw-bold mb-0 mt-3">
                                    {{ props.computer.manufacturer }}
                                    {{ props.computer.model }}
                                </h3>
                                <span :class="[
                                    'badge mt-2 px-3 py-2',
                                    {
                                        'badge bg-success': props.computer.status === 'In Storage',
                                        'badge bg-warning text-dark': props.computer.status === 'In Use',
                                        'badge bg-info': props.computer.status === 'In Repair',
                                        'badge bg-danger': props.computer.status === 'Pullout',
                                        'badge bg-dark': props.computer.status === 'Retired',
                                    },
                                ]"> {{ props.computer.status }}
                                </span>
                            </div>

                            <ul class="list-group list-group-flush border-top">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Serial Number</span>
                                    <span class="fw-bold">{{
                                        props.computer.serial_number || 'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Host Name</span>
                                    <span class="font-monospace small">{{
                                        props.computer.host_name || 'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">OS Version</span>
                                    <span>{{
                                        props.computer.os_version || 'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Mac Address</span>
                                    <span class="font-monospace small">{{
                                        props.computer.mac_address || 'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">IP Address</span>
                                    <span class="font-monospace small">{{
                                        props.computer.ip_address || 'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">CPU</span>
                                    <span>{{
                                        props.computer.cpu || 'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">RAM / Storage</span>
                                    <span>{{ props.computer.ram_gb || 'N/A' }} /
                                        {{ props.computer.storage_gb || 'N/A' }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Purchase Date</span>
                                    <span>{{
                                        formatDate(props.computer.purchase_date || props.computer.created_at) ||
                                        'N/A'
                                        }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Warranty</span>
                                    <span>{{ formatDate(props.computer.warranty_expiry) || 'N/A' }}
                                        {{ props.computer.warranty_expiry >= today ? '(The warranty is expired)' : '' }}
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between gap-2 align-items-center">
                                    <span class="text-muted">Remarks</span>
                                    <span class="p-2 container-fluid text-end">{{ props.computer.remarks || 'N/A' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <div class="card-footer border-0 bg-transparent pb-3 text-center">
                            <button class="btn btn-outline-danger btn-sm w-100" @click.prevent="
                                deleteItem(props.computer.host_name)
                                ">
                                <i class="bi bi-trash me-1"></i> Delete Asset
                                Record
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Issuance Card -->
                <div class="col-sm-12 col-xl-8 col-lg-7">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-primary d-flex justify-content-start align-items-center text-white">
                            <i class="bi bi-send-check fs-4 me-3"></i>
                            <h5 class="fw-bold mb-0">Deployment Asset</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3" v-if="
                                props.computer?.status === 'In Repair' ||
                                props.computer?.status === 'In Use' ||
                                props.computer?.status === 'In Storage' ||
                                props.computer?.status === 'Pull Out'
                            ">
                                <div class="col-md-6 border-end">
                                    <label class="small text-muted text-uppercase fw-bold">Recipient Info</label>
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.computer_transaction
                                                ?.assigned_user || 'Not yet issued'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        <span class="fw-bold">Department:
                                        </span>
                                        {{
                                            props.computer_transaction
                                                ?.department || 'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-6 px-md-4">
                                    <label class="small text-muted text-uppercase fw-bold">Issuance Logistics</label>
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.computer_transaction
                                                    ?.date_issued,
                                            ) || 'Not yet issued'
                                        }}
                                    </p>
                                    <!-- <p class="mb-0">
                                        <strong>By:</strong>
                                        {{
                                            props.computer_transaction
                                                ?.issued_by || 'Not yet issued'
                                        }}
                                    </p> -->
                                </div>
                                <!-- <div class="col-12 mt-4">
                                    <div class="bg-light d-flex align-items-center flex-wrap gap-4 rounded p-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-headphones text-primary me-2"></i>
                                            <strong>Accessories:</strong>
                                            <span class="text-muted ms-2">{{
                                                props.computer_transaction
                                                    ?.issued_accessories ||
                                                'None'
                                            }}</span>
                                        </div>
                                        <div class="vr d-none d-md-block mx-2"></div>
                                        <div class="d-flex align-items-center flex-wrap">
                                            <div class="g-1">
                                                <i class="bi bi-check-lg me-1"></i>
                                                <strong>Acknowledgement:</strong>
                                            </div>
                                            <span class="badge text-dark ms-2 border text-white" :class="props.phone_transaction
                                                    ?.it_ack_issued
                                                    ? 'bg-success'
                                                    : 'bg-danger'
                                                ">IT:
                                                {{
                                                    props.computer_transaction
                                                        ?.it_ack_issued
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                                <i :class="props.phone_transaction
                                                        ?.it_ack_issued
                                                        ? 'bi bi-check-circle-fill'
                                                        : 'bi-x-circle-fill'
                                                    "></i>
                                            </span>
                                            <span class="badge text-dark ms-2 border text-white" :class="props.phone_transaction
                                                    ?.purch_ack_issued
                                                    ? 'bg-success'
                                                    : 'bg-danger'
                                                ">Purchasing:
                                                {{
                                                    props.phone_transaction
                                                        ?.purch_ack_issued
                                                        ? 'Yes'
                                                        : 'No'
                                                }}
                                                <i :class="props.phone_transaction
                                                        ?.it_ack_issued
                                                        ? 'bi bi-check-circle-fill'
                                                        : 'bi-x-circle-fill'
                                                    "></i>
                                            </span>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                            <div class="card-body py-5 text-center" v-else>
                                <span class="fw-bold fs-5 text-muted">The asset is not yet issued.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Return Details -->

                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-warning d-flex justify-content-start align-items-center text-dark">
                            <i class="bi bi-reply-fill fs-4 me-2"></i>
                            <h5 class="fw-bold mb-0">Pullout Details</h5>
                        </div>
                        <div class="card-body"
                            v-if="props.computer?.status === 'In Storage' || props.computer?.status === 'In Repair' || props.computer?.status === 'Retired'">
                            <div class="row g-3">
                                <div class="col-md-4 border-end">
                                    <label class="small text-muted text-uppercase fw-bold">Recipient Info</label>
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.computer_transaction
                                                ?.assigned_user ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        <span class="fw-bold">Department:
                                        </span>
                                        {{
                                            props.computer_transaction
                                                ?.department ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                </div>

                                <div class="col-md-4 px-md-4">
                                    <label class="small text-muted text-uppercase fw-bold">Issuance Logistics</label>
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.computer_transaction
                                                    ?.pullout_date,
                                            ) || 'Not yet returned'
                                        }}
                                    </p>
                                </div>


                                <div class="col-12 mt-4">
                                    <div class="card bg-light border-0 shadow-sm">
                                        <div class="card-body">
                                            <h5
                                                class="card-title fw-bold text-secondary d-flex align-items-center mb-3 me-2">
                                                <i class="bi bi-sticky me-2"></i>Reason
                                            </h5>
                                            <p class="card-text text-dark d-flex align-items-center">
                                                {{
                                                    props.computer_transaction
                                                        ?.pullout_reason ||
                                                    'No reason provided for this pullout.'
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bg-light py-5 text-center" v-else-if="props.computer.status === 'In Use'">
                            <span class="fw-bold fs-5 text-dark text-muted mb-1">The asset is currently deploy (not yet
                                returned).
                            </span>
                        </div>
                        <div class="card-body bg-light py-5 text-center" v-else>
                            <span class="fw-bold fs-5 text-dark text-muted mb-1">The asset is not yet issued.
                            </span>
                        </div>
                    </div>

                    <!-- Table History -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div class="row d-flex justify-content-between align-items-center">
                                <div class="col-sm-12 col-md-8 mt-2">
                                    <h5 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-clock-history me-2"></i>Asset Transaction History
                                    </h5>
                                </div>
                                <div class="col-sm-12 col-md-4 my-2">
                                    <div class="search-box">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-search text-muted"></i>
                                            </span>
                                            <input type="text" v-model="historySearch"
                                                class="form-control bg-light border-start-0"
                                                placeholder="Search history..." />
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
                                            <th scope="col">Deployment Date</th>
                                            <th scope="col">Deploy To</th>
                                            <th scope="col">Department</th>
                                            <th scope="col">
                                                Date Pullout
                                            </th>
                                            <th scope="col">Reason</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fs-7" v-for="tx in paginatedHistory" :key="tx.id">
                                            <td class="fw-medium mb-0 text-nowrap ps-3">
                                                {{
                                                    formatDate(
                                                        tx.date_issued,
                                                    )
                                                }}
                                            </td>

                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ tx.assigned_user }}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="text-muted small ps-2">
                                                    {{ tx.department }}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ formatDate(tx.pullout_date) }}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ tx.pullout_reason }}
                                                </div>
                                            </td>



                                        </tr>
                                        <tr v-if="
                                            filteredHistory.length === 0
                                        ">
                                            <td colspan="8" class="text-muted py-4 text-center">
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
                        <div class="card-footer border-top-0 bg-white py-3" v-if="totalPages > 1">
                            <div class="d-flex justify-content-between align-items-center">
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
                                    <ul class="pagination pagination-sm mb-0">
                                        <li class="page-item" :class="{
                                            disabled: currentPage === 1,
                                        }">
                                            <button class="page-link" @click="currentPage--">
                                                Previous
                                            </button>
                                        </li>

                                        <li v-for="page in totalPages" :key="page" class="page-item" :class="{
                                            active:
                                                currentPage === page,
                                        }">
                                            <button class="page-link" @click="currentPage = page">
                                                {{ page }}
                                            </button>
                                        </li>

                                        <li class="page-item" :class="{
                                            disabled:
                                                currentPage ===
                                                totalPages,
                                        }">
                                            <button class="page-link" @click="currentPage++">
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

    <!-- Issuance Modal -->
    <Modals id="IssueComputerModal" title="Deploy Workstation" header-class="bg-primary text-white bg-gradient">
        <template #body>
            <form @submit.prevent="submit" id="deployComputerForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="assigned_user" class="form-label">Issued To</label>
                        <input type="text" id="assigned_user" v-model="issueform.assigned_user" class="form-control"
                            required />
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" id="department" v-model="issueform.department" class="form-control"
                            required />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="date_issued" class="form-label">Date Issued</label>
                    <input type="date" id="date_issued" v-model="issueform.date_issued" class="form-control" required />
                </div>

                <div class="mb-3">
                    <label for="deployment_remarks" class="form-label">Remarks</label>
                    <textarea v-model="issueform.remarks" id="deployment_remarks" class="form-control"
                        rows="3"></textarea>
                </div>

            </form>
        </template>

        <template #footer>
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary px-4" form="deployComputerForm"
                :disabled="issueform.processing">
                <span v-if="issueform.processing" class="spinner-border spinner-border-sm me-1"></span>
                Deploy Workstation
            </button>
        </template>
    </Modals>

    <!-- Return Modal -->
    <Modals id="ReturnComputerModal" title="Return Computer Asset" header-class="bg-warning text-white bg-gradient">
        <template #body>
            <form @submit.prevent="returnSubmit" id="returnForm">
                <div class="mb-3">
                    <label for="pullout_reason" class="form-label">Pullout Reason</label>
                    <select v-model="returnform.pullout_reason" id="pullout_reason" class="form-select">
                        <option selected disabled>Select an option</option>
                        <option value="In Repair">For Repair</option>
                        <option value="In Storage">Dispose to Storage</option>
                        <option value="Retired">Workstation Retirement</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="date_returned" class="form-label">Date</label>
                    <input type="date" id="date_returned" v-model="returnform.pullout_date" class="form-control"
                        required />
                </div>


                <div class="mb-3">
                    <label for="remarksTextarea" class="form-label">Remarks</label>
                    <textarea v-model="returnform.remarks" id="remarksTextarea" class="form-control"
                        rows="3"></textarea>
                </div>
            </form>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Cancel
            </button>
            <button type="submit" class="btn btn-primary" form="returnForm" :disabled="returnform.processing">
                <span v-if="returnform.processing" class="spinner-border spinner-border-sm me-1"></span>
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
