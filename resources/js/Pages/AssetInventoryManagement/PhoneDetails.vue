<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

defineOptions({ layout: HomeLayout });

const { formatDate } = useDateFormatter();

const props = defineProps({
    phone: {
        type: Object,
        required: true,
    },
    phone_issuance: {
        type: [Object, null],
        default: null,
    },
    phone_return: {
        type: [Object, null],
        default: null,
    },
});

// Function for breadcrumb
const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Asset & Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Phone Units', url: route('phone.index') },
    { label: 'Details' },
];

// Function for the phone image path
const getPhoneImagePath = (phone) => {
    if (phone?.image_url) return phone.image_url;

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

// Deleting function
const deleteItem = (id) => {
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
            router.delete(route('phone.destroy', id), {
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
                onFinish: () => {
                    if (!Swal.isVisible()) return;
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
const itemsPerPage = 3;

const filteredHistory = computed(() => {
    if (!props.phone.issuances) return [];
    const searchTerm = historySearch.value.toLowerCase();

    return props.phone.issuances
        .map((issuance) => ({
            ...issuance,
            return: issuance.return,
        }))
        .filter((record) => {
            return (
                (record.issued_to?.toLowerCase() || '').includes(searchTerm) ||
                (record.issued_by?.toLowerCase() || '').includes(searchTerm) ||
                (record.return?.returned_by?.toLowerCase() || '').includes(
                    searchTerm,
                ) ||
                (record.department?.toLowerCase() || '').includes(searchTerm)
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

// Form for Issuance
const form = useForm({
    issued_by: '',
    issued_to: '',
    aknowledgement: '',
    department: '',
    date_issued: new Date().toISOString().substr(0, 10),
    issued_accessories: '',
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
    remarks: '',
});

// Form for update
const updateForm = useForm({
    image: null,
    brand: props.phone.brand || '',
    model: props.phone.model || '',
    serial_num: props.phone.serial_num || '',
    imei_one: props.phone.imei_one || '',
    imei_two: props.phone.imei_two || '',
    ram: props.phone.ram || '',
    rom: props.phone.rom || '',
    sim_no: props.phone.sim_no || '',
    purchase_date: props.phone.purchase_date || '',
    remarks: props.phone.remarks || '',
});
const updateSamplePic = ref(getPhoneImagePath(props.phone));
let updatePicObjectUrl = null;

// Listeners
watch(selectedAcc, (newVal) => {
    form.issued_accessories = newVal.join(', ');
});
watch(selectedReturnAcc, (newVal) => {
    returnform.returned_accessories = newVal.join(', ');
});

// Submit logic for the issue
const submit = () => {
    form.post(route('phone.issue', props.phone.id), {
        onSuccess: () => {
            const closeButton = document.querySelector(
                '#IssuePhoneModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
        },
    });
};

// Submit logic for the return
const returnSubmit = () => {
    returnform.post(route('phone.return', props.phone.id), {
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

// Submit logic for the update
const updateSubmit = () => {
    updateForm.put(route('phone.update', props.phone.id), {
        forceFormData: true,
        onSuccess: () => {
            updateForm.image = null;
            if (updatePicObjectUrl) {
                URL.revokeObjectURL(updatePicObjectUrl);
                updatePicObjectUrl = null;
            }
            const closeButton = document.querySelector(
                '#UpdatePhoneModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
        },
    });
};

// Logic to open Update Modals
const openUpdateModal = (phone) => {
    updateForm.id = phone.id;
    updateForm.brand = phone.brand;
    updateForm.model = phone.model;
    updateForm.serial_num = phone.serial_num;
    updateForm.imei_one = phone.imei_one;
    updateForm.imei_two = phone.imei_two;
    updateForm.ram = phone.ram;
    updateForm.rom = phone.rom;
    updateForm.sim_no = phone.sim_no;
    updateForm.purchase_date = phone.purchase_date;
    updateForm.remarks = phone.remarks;
    updateForm.image = null;
    updateSamplePic.value = phone?.image_url || getPhoneImagePath(phone);

    updateForm.clearErrors();

    const modalElement = document.getElementById('UpdatePhoneModal');
    if (modalElement) {
        const modalInstance =
            window.bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();
    } else {
        console.error('Modal element #UpdatePhoneModal not found');
    }
};

//Logic to handle image upload
const onUpdateFileSelect = (event) => {
    const file = event.target.files?.[0] || null;
    updateForm.image = file;

    if (updatePicObjectUrl) {
        URL.revokeObjectURL(updatePicObjectUrl);
        updatePicObjectUrl = null;
    }

    updateSamplePic.value = file
        ? (() => {
              updatePicObjectUrl = URL.createObjectURL(file);
              return updatePicObjectUrl;
          })()
        : getPhoneImagePath(props.phone);
};

const clearUpdateSelectedImage = () => {
    updateForm.image = null;
    if (updatePicObjectUrl) {
        URL.revokeObjectURL(updatePicObjectUrl);
        updatePicObjectUrl = null;
    }
    updateSamplePic.value = getPhoneImagePath(props.phone);

    const input = document.getElementById('updatePhoneImageDetailsInput');
    if (input) input.value = '';
};

const handleUpdateModalHidden = () => {
    clearUpdateSelectedImage();
};

// Logic to open Issue Modals
const openIssueModal = () => {
    const modalElement = document.getElementById('IssuePhoneModal');
    if (!modalElement) return;

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement, {
        backdrop: true,
        keyboard: true,
    });

    modal.show();
};

// Logic to open Return Modals
const openReturnModal = () => {
    const modalElement = document.getElementById('ReturnPhoneModal');
    if (!modalElement) {
        console.error('ReturnPhoneModal not found');
        return;
    }

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement, {
        backdrop: true,
        keyboard: true,
        focus: true,
    });

    modal.show();
};

const generateLogsheet = (id) => {
    window.open(
        `
         /AssetAndInventoryManagement/Phone/${id}/logsheet `,
        '_blank',
    );
};

onMounted(() => {
    window.Echo.channel('phoneInventory').listen('.AssetUpdated', (e) => {
        console.log('Update received:', e.message);

        router.reload({ only: ['phone', 'phone_issuance', 'phone_return'] });
    });

    document
        .getElementById('UpdatePhoneModal')
        ?.addEventListener('hidden.bs.modal', handleUpdateModalHidden);
});

onUnmounted(() => {
    window.Echo.leave('phoneInventory');

    document
        .getElementById('UpdatePhoneModal')
        ?.removeEventListener('hidden.bs.modal', handleUpdateModalHidden);
});
</script>

<template>
    <div class="app-content-header py-3">
        <!-- Breadcrumb -->
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>

    <div class="app-content mb-5">
        <div class="container px-3">
            <div class="row g-4">
                <!-- Navigation Menu -->
                <div class="col-12">
                    <div class="card mb-4 border-0 bg-transparent shadow-none">
                        <div class="card-body">
                            <div
                                class="row d-flex justify-content-between align-items-center"
                            >
                                <div class="col-sm-12 col-md-6 mb-3">
                                    <BackButton
                                        @click.prevent="
                                            router.get(route('phone.index'))
                                        "
                                    />
                                </div>
                                <div
                                    class="col-sm-12 col-md-6 d-flex justify-content-end align-items-center mb-3 gap-2"
                                >
                                    <button
                                        v-if="
                                            props.phone.status ===
                                                'available' ||
                                            props.phone.status === 'returned'
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
            </div>

            <div class="row g-3">
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
                                    {{
                                        props.phone.brand
                                            .charAt(0)
                                            .toUpperCase() +
                                        props.phone.brand.slice(1)
                                    }}
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
                                    {{
                                        props.phone.status
                                            .charAt(0)
                                            .toUpperCase() +
                                        props.phone.status.slice(1)
                                    }}
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
                                        props.phone.sim_no || 'N/A'
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
                                        >{{ props.phone.ram + ' GB' || 'N/A' }}
                                        /
                                        {{ props.phone.rom + ' GB' || 'N/A' }}
                                    </span>
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
                            class="card-footer d-flex justify-content-around align-items-center mb-3 border-0 bg-transparent pb-3 text-center"
                        >
                            <button
                                class="btn btn-outline-danger"
                                @click.prevent="deleteItem(props.phone.id)"
                            >
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                            <button
                                class="btn btn-outline-warning"
                                @click="openUpdateModal(props.phone)"
                            >
                                <i class="bi bi-pencil me-1"></i> Update
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-8 col-lg-7">
                    <!-- Issuance Card -->
                    <div class="card mb-3 border-0 shadow-sm">
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
                                <div class="col-md-4 border-end">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Recipient Info</label
                                    >
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.phone_issuance?.issued_to ||
                                            'Not yet issued'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        <span class="fw-bold"
                                            >Department:
                                        </span>
                                        {{
                                            props.phone_issuance?.department ||
                                            'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-4 border-end">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Issuance Logistics</label
                                    >
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.phone_issuance?.issued_by ||
                                            'Not yet issued'
                                        }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.phone_issuance
                                                    ?.date_issued,
                                            ) || 'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-4 px-md-4">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                    >
                                        <i
                                            class="bi bi-headphones text-primary me-2"
                                        ></i
                                        >Accessories:</label
                                    >
                                    <div class="d-flex align-items-center">
                                        <span class="text-muted fw-bold ms-2">{{
                                            props.phone_issuance
                                                ?.issued_accessories || 'None'
                                        }}</span>
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
                    <div class="card mb-3 border-0 shadow-sm">
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
                                            props.phone_return?.returned_by ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        <span class="fw-bold"
                                            >Department:
                                        </span>
                                        {{
                                            props.phone_return
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
                                            props.phone_return?.returned_to ||
                                            'Not yet returned'
                                        }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.phone_return
                                                    ?.date_returned,
                                            ) || 'Not yet returned'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-4 px-md-4">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                    >
                                        <i
                                            class="bi bi-headphones text-primary me-2"
                                        ></i
                                        >Accessories:</label
                                    >

                                    <p class="d-flex align-items-center">
                                        <span class="text-muted fw-bold ms-2">{{
                                            props.phone_return
                                                ?.returned_accessories || 'None'
                                        }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="card-body py-5 text-center"
                            v-else-if="props.phone.status === 'issued'"
                        >
                            <span class="fw-bold fs-5 text-dark text-muted mb-1"
                                >The asset is currently deploy (not yet
                                returned).
                            </span>
                        </div>
                        <div class="card-body py-5 text-center" v-else>
                            <span class="fw-bold fs-5 text-dark text-muted mb-1"
                                >The asset is not yet issued.
                            </span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <div
                                class="row d-flex justify-content-between align-items-center"
                            >
                                <div class="col-sm-12 col-md-8 mt-2">
                                    <h5 class="fw-bold text-primary mb-0">
                                        <i class="bi bi-clock-history me-2"></i
                                        >Phone Assignment History
                                    </h5>
                                </div>
                                <div class="col-sm-12 col-md-4 my-2">
                                    <div class="search-box">
                                        <div class="input-group input-group-sm">
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
                                            class="fs-8 text-uppercase text-muted border-top-0 text-wrap text-center"
                                        >
                                            <th class="ps-3" scope="col">
                                                Date Issued
                                            </th>
                                            <th scope="col">Issued To</th>
                                            <th scope="col">Issued By</th>
                                            <th scope="col">Issued Acc.</th>
                                            <th scope="col">Date Returned</th>
                                            <th scope="col">Returned By</th>
                                            <th scope="col">Returned To</th>
                                            <th scope="col" class="pe-3">
                                                Returned Acc.
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            class="fs-8 text-center"
                                            v-for="tx in paginatedHistory"
                                            :key="tx.id"
                                        >
                                            <td
                                                class="fw-medium mb-0 text-nowrap ps-3"
                                            >
                                                {{ formatDate(tx.date_issued) }}
                                            </td>

                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ tx.issued_to }}
                                                </div>
                                                <div
                                                    class="text-muted small ps-2"
                                                >
                                                    {{ tx.department }}
                                                </div>
                                            </td>

                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ tx.issued_by }}
                                                </div>
                                            </td>

                                            <td
                                                class="small text-wrap"
                                                style="max-width: 150px"
                                            >
                                                {{
                                                    tx.issued_accessories || '—'
                                                }}
                                            </td>

                                            <td class="text-nowrap">
                                                <span
                                                    v-if="
                                                        tx.return?.date_returned
                                                    "
                                                    class="fw-medium mb-0 text-nowrap ps-2"
                                                >
                                                    {{
                                                        formatDate(
                                                            tx.return
                                                                .date_returned,
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
                                                <div class="fw-bold text-dark">
                                                    {{
                                                        tx.return
                                                            ?.returned_by || '—'
                                                    }}
                                                </div>
                                                <div
                                                    class="text-muted small ps-2"
                                                >
                                                    {{
                                                        tx.return
                                                            ?.returnee_department
                                                    }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">
                                                    {{ tx.return?.returned_to }}
                                                </div>
                                            </td>

                                            <td
                                                class="small text-muted text-wrap pe-3"
                                                style="max-width: 150px"
                                            >
                                                {{
                                                    tx.return
                                                        ?.returned_accessories ||
                                                    '—'
                                                }}
                                            </td>
                                        </tr>
                                        <tr v-if="filteredHistory.length === 0">
                                            <td
                                                colspan="8"
                                                class="text-muted py-4 text-center"
                                            >
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
                                    <ul
                                        class="pagination pagination-sm mb-0 gap-2"
                                    >
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
                                                Previous
                                            </button>
                                        </li>

                                        <li
                                            v-for="page in totalPages"
                                            :key="page"
                                            class="page-item"
                                            :class="{
                                                active: currentPage === page,
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
                                                    currentPage === totalPages,
                                            }"
                                        >
                                            <button
                                                class="page-link"
                                                @click="nextPage"
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
                            >Issued To<i class="text-danger">*</i></label
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
                            >Issued By<i class="text-danger">*</i></label
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
                            >Department<i class="text-danger">*</i></label
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
                            >Date Issued<i class="text-danger">*</i></label
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
                    <label class="form-label"
                        >Select Accessories<i class="text-danger">*</i></label
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
                    <label class="form-label">Acknowledgement</label>
                    <div
                        class="d-flex justify-content-around align-items-center rounded border pb-2 pt-3"
                    >
                        <div class="form-check">
                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="acknowledgement"
                            />
                            <label for="acknowledgement" class="form-label"
                                >Information Technology</label
                            >
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block text-muted small fw-bold"
                        >Cashout Status<i class="text-danger">*</i></label
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

    <!-- Update Modal -->
    <Modals
        id="UpdatePhoneModal"
        title="Update Phone Asset"
        header-class="bg-warning text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="updateSubmit" id="updateForm">
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-12">
                        <img
                            :src="updateSamplePic"
                            alt="update-asset-image"
                            class="preview-image-fixed img-thumbnail rounded-circle d-block mx-auto border border-2 shadow-md"
                        />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <label
                                class="input-group-text"
                                for="updatePhoneImageDetailsInput"
                                >Upload</label
                            >
                            <input
                                type="file"
                                class="form-control"
                                id="updatePhoneImageDetailsInput"
                                accept="image/*"
                                @change="onUpdateFileSelect"
                            />
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="update_brand" class="form-label"
                            >Brand</label
                        >
                        <input
                            type="text"
                            id="update_brand"
                            v-model="updateForm.brand"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="update_model" class="form-label"
                            >Model</label
                        >
                        <input
                            type="text"
                            id="update_model"
                            v-model="updateForm.model"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="update_serial_num" class="form-label"
                            >Serial Number</label
                        >
                        <input
                            type="text"
                            id="update_serial_num"
                            v-model="updateForm.serial_num"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="update_sim_no" class="form-label"
                            >SIM Number</label
                        >
                        <input
                            type="text"
                            id="update_sim_no"
                            v-model="updateForm.sim_no"
                            class="form-control"
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="update_imei_one" class="form-label"
                            >IMEI One</label
                        >
                        <input
                            type="text"
                            id="update_imei_one"
                            v-model="updateForm.imei_one"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="update_imei_two" class="form-label"
                            >IMEI Two</label
                        >
                        <input
                            type="text"
                            id="update_imei_two"
                            v-model="updateForm.imei_two"
                            class="form-control"
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="update_ram" class="form-label">RAM</label>
                        <input
                            type="text"
                            id="update_ram"
                            v-model="updateForm.ram"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-md-6">
                        <label for="update_rom" class="form-label">ROM</label>
                        <input
                            type="text"
                            id="update_rom"
                            v-model="updateForm.rom"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="update_purchase_date" class="form-label"
                            >Purchase Date</label
                        >
                        <input
                            type="date"
                            id="update_purchase_date"
                            v-model="updateForm.purchase_date"
                            class="form-control"
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="update_remarks" class="form-label"
                        >Remarks</label
                    >
                    <textarea
                        v-model="updateForm.remarks"
                        id="update_remarks"
                        rows="3"
                        class="form-control"
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
                class="btn btn-warning"
                form="updateForm"
                :disabled="updateForm.processing"
            >
                <span
                    v-if="updateForm.processing"
                    class="spinner-border spinner-border-sm me-1"
                ></span>
                Update Asset
            </button>
        </template>
    </Modals>
</template>

<style scoped>
tr td {
    align-items: center;
}

.preview-image-fixed {
    width: 8rem;
    height: 8rem;
    object-fit: cover;
}
</style>
