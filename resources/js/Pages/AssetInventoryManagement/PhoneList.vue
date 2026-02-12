<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Swal from 'sweetalert2';
import { ref, watch } from 'vue';

const { formatDate } = useDateFormatter();
defineOptions({ layout: HomeLayout });

const props = defineProps({
    phones: { type: Object, required: true },
});

// --- STATE ---
const filterBrand = ref(
    new URLSearchParams(window.location.search).get('brand') || '',
);
const currentSort = ref(
    new URLSearchParams(window.location.search).get('sort') || 'availability',
);
const searchQuery = ref(
    new URLSearchParams(window.location.search).get('search') || '',
);

const samplePic = ref('../img/phone/default.png');
let samplePicObjectUrl = null;

//Searching
const debouncedSearch = debounce(() => {
    applyFilter();
}, 300);

// Watch the searchQuery ref for changes
watch(searchQuery, () => {
    debouncedSearch();
});

const getRowNumber = (index) => {
    return (
        (props.phones.current_page - 1) * props.phones.per_page + (index + 1)
    );
};
// --- FORM LOGIC ---
const addForm = useForm({
    image: '',
    brand: '',
    model: '',
    serial_num: '',
    imei_one: '',
    imei_two: '',
    ram: '',
    rom: '',
    purchase_date: '',
    sim_no: '',
    remarks: '',
});

const onFileSelect = (event) => {
    const file = event.target.files?.[0] || null;
    addForm.image = file;

    if (samplePicObjectUrl) {
        URL.revokeObjectURL(samplePicObjectUrl);
        samplePicObjectUrl = null;
    }

    if (file) {
        samplePicObjectUrl = URL.createObjectURL(file);
        samplePic.value = samplePicObjectUrl;
    } else {
        samplePic.value = '../img/phone/default.png';
    }
};

// Form for update
const updateForm = useForm({
    id: null,
    brand: props.phones.brand || '',
    model: props.phones.model || '',
    serial_num: props.phones.serial_num || '',
    imei_one: props.phones.imei_one || '',
    imei_two: props.phones.imei_two || '',
    ram: props.phones.ram || '',
    rom: props.phones.rom || '',
    sim_no: props.phones.sim_no || '',
    purchase_date: props.phones.purchase_date || '',
    remarks: props.phones.remarks || '',
});

const submitAddForm = () => {
    addForm.post(route('phone.store'), {
        forceFormData: true,
        onSuccess: () => {
            addForm.reset();
            if (samplePicObjectUrl) {
                URL.revokeObjectURL(samplePicObjectUrl);
                samplePicObjectUrl = null;
            }
            samplePic.value = '../img/phone/default.png';
            const modal = document.getElementById('AddPhoneModal');
            const bootstrapModal = bootstrap.Modal.getInstance(modal);
            if (bootstrapModal) bootstrapModal.hide();
        },
        onError: (errors) => {
            const errorDetails = Object.values(errors)
                .map((err) => `<li>${err}</li>`)
                .join('');
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                html: `<ul class="text-start mt-2">${errorDetails}</ul>`,
                confirmButtonColor: '#dc3545',
            });
        },
    });
};

// --- NAVIGATION & FILTERING ---
const applyFilter = () => {
    router.get(
        route('phone.index'),
        {
            brand: filterBrand.value,
            sort: currentSort.value,
            search: searchQuery.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const getPhoneImagePath = (phone) => {
    if (phone?.image_url) {
        return phone.image_url;
    }

    if (phone?.image_path) {
        const baseUrl =
            typeof route === 'function'
                ? route('welcome').replace(/\/$/, '')
                : window.location.origin;

        return `${baseUrl}/${String(phone.image_path).replace(/^\/+/, '')}`;
    }

    const supported = [
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
    const brand = phone.brand?.toLowerCase() || '';
    const matched = supported.find((b) => brand.includes(b));
    return matched
        ? `../img/phone/${matched === 'apple' ? 'iphone' : matched}.png`
        : '../img/phone/default.png';
};

const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Asset & Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Phone Units' },
];

const sortOption = [
    { label: 'Name', value: 'name' },
    { label: 'Date Modified', value: 'date_modified' },
    { label: 'Availability', value: 'availability' },
];
// const updateForm = useForm({
//     brand: '',
//     model: '',
//     serial_num: '',
//     imei_one: '',
//     imei_two: '',
//     ram: '',
//     rom: '',
//     sim_no: '',
//     purchase_date: '',
//     remarks: '',
// });
const openUpdateModal = (phone) => {
    updateForm.id = phone.id;
    updateForm.brand = phone.brand || '';
    updateForm.model = phone.model || '';
    updateForm.serial_num = phone.serial_num || '';
    updateForm.imei_one = phone.imei_one || '';
    updateForm.imei_two = phone.imei_two || '';
    updateForm.ram = phone.ram || '';
    updateForm.rom = phone.rom || '';
    updateForm.sim_no = phone.sim_no || '';
    updateForm.purchase_date = phone.purchase_date || '';
    updateForm.remarks = phone.remarks || '';
};

const updateSubmit = () => {
    updateForm.put(route('phone.update', updateForm.id), {
        preserveScroll: true,
        onSuccess: () => {
            // Closes modal using the data-bs-dismiss trigger
            const modalElement = document.getElementById('UpdatePhoneModal');
            const closeBtn = modalElement.querySelector(
                '[data-bs-dismiss="modal"]',
            );
            closeBtn?.click();
        },
    });
};

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

const brandsOption = [
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
    'other',
];
</script>

<template>
    <div class="app-content-header py-3">
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>

    <div class="app-content">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <BackButton
                    @click.prevent="
                        router.get(route('AssetAndInventoryManagement'))
                    "
                />
            </div>

            <div class="card mb-5 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <h5 class="fw-bold text-primary mb-0">
                                Phone Inventory
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <div class="input-group">
                                <span
                                    class="input-group-text bg-light border-end-0"
                                >
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    class="form-control bg-light border-start-0"
                                    placeholder="Search model or serial..."
                                    @input="debouncedSearch"
                                />
                            </div>
                        </div>

                        <div
                            class="col-md-4 d-flex justify-content-md-end gap-2"
                        >
                            <button
                                class="btn btn-success shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#AddPhoneModal"
                            >
                                <i class="bi bi-plus-lg me-1"></i> Add Phone
                            </button>

                            <div class="dropdown">
                                <button
                                    class="btn btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                >
                                    <i class="bi bi-filter-right me-1"></i> Sort
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li
                                        v-for="opt in sortOption"
                                        :key="opt.value"
                                    >
                                        <a
                                            href="#"
                                            class="dropdown-item"
                                            :class="{
                                                active:
                                                    currentSort === opt.value,
                                            }"
                                            @click.prevent="
                                                applyFilter(
                                                    filterBrand,
                                                    opt.value,
                                                )
                                            "
                                        >
                                            {{ opt.label }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-hover mb-0 table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Brand & Model</th>
                                    <th>Status</th>
                                    <th>Specs (RAM/ROM)</th>
                                    <th>Assigned To</th>
                                    <th>Date Issued</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(phone, i) in props.phones.data"
                                    :key="phone.id"
                                    @click="
                                        router.get(
                                            route('phone.show', {
                                                id: phone.id,
                                            }),
                                        )
                                    "
                                    style="cursor: pointer"
                                >
                                    <td class="text-muted ps-4">
                                        {{ getRowNumber(i) }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img
                                                :src="getPhoneImagePath(phone)"
                                                class="me-3"
                                                style="
                                                    width: 24px;
                                                    height: 24px;
                                                    object-fit: contain;
                                                "
                                            />
                                            <span
                                                class="fw-semibold text-capitalize"
                                                >{{ phone.brand }}
                                                {{ phone.model }}</span
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="{
                                                'text-bg-success':
                                                    phone.status ===
                                                    'available',
                                                'text-bg-warning':
                                                    phone.status === 'issued',
                                                'text-bg-danger':
                                                    phone.status === 'return',
                                            }"
                                        >
                                            {{
                                                phone.status
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                phone.status.slice(1)
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted"
                                            >{{ phone.ram }} GB /
                                            {{ phone.rom }} GB</small
                                        >
                                    </td>
                                    <td>
                                        <span
                                            :class="
                                                phone.phone?.issued_to
                                                    ? 'text-dark'
                                                    : 'text-muted fst-italic'
                                            "
                                        >
                                            {{
                                                phone.phone?.issued_to ||
                                                'Not yet issued'
                                            }}
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        {{
                                            formatDate(
                                                phone.current_transaction
                                                    ?.date_issued,
                                            ) || 'Not yet Issued'
                                        }}
                                    </td>
                                    <td
                                        class="d-flex justify-content-center align-items-center gap-2"
                                    >
                                        <button
                                            type="button"
                                            class="btn btn-outline-warning"
                                            data-bs-toggle="modal"
                                            data-bs-target="#UpdatePhoneModal"
                                            @click.stop="openUpdateModal(phone)"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger"
                                            @click.stop="deleteItem(phone.id)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="props.phones.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-muted py-5 text-center"
                                    >
                                        No phones found matching your criteria.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer border-top-0 bg-white py-3">
                    <div
                        class="d-flex align-items-center justify-content-between p-3"
                    >
                        <div class="text-secondary small">
                            Showing
                            <span class="fw-bold text-dark">{{
                                props.phones.from ?? 0
                            }}</span>
                            to
                            <span class="fw-bold text-dark">{{
                                props.phones.to ?? 0
                            }}</span>
                            of
                            <span class="fw-bold text-dark">{{
                                props.phones.total
                            }}</span>
                            entries
                        </div>

                        <nav aria-label="Phone pagination">
                            <ul class="pagination pagination-sm mb-0 gap-1">
                                <li
                                    class="page-item"
                                    :class="{
                                        disabled:
                                            props.phones.current_page === 1,
                                    }"
                                >
                                    <button
                                        class="page-link text-dark border-0 px-3"
                                        @click="
                                            router.get(
                                                route('phone.index'),
                                                {
                                                    ...$page.props.filters,
                                                    page:
                                                        props.phones
                                                            .current_page - 1,
                                                },
                                                { preserveScroll: true },
                                            )
                                        "
                                        :disabled="
                                            props.phones.current_page === 1
                                        "
                                    >
                                        <span aria-hidden="true">&larr;</span>
                                        <span class="d-none d-sm-inline ms-1"
                                            >Previous</span
                                        >
                                    </button>
                                </li>

                                <li class="page-item disabled">
                                    <span
                                        class="page-link text-dark fw-medium border-0 bg-transparent px-3"
                                    >
                                        Page {{ props.phones.current_page }} of
                                        {{ props.phones.last_page }}
                                    </span>
                                </li>

                                <li
                                    class="page-item"
                                    :class="{
                                        disabled:
                                            props.phones.current_page ===
                                            props.phones.last_page,
                                    }"
                                >
                                    <button
                                        class="page-link text-dark border-0 px-3"
                                        @click="
                                            router.get(
                                                route('phone.index'),
                                                {
                                                    ...$page.props.filters,
                                                    page:
                                                        props.phones
                                                            .current_page + 1,
                                                },
                                                { preserveScroll: true },
                                            )
                                        "
                                        :disabled="
                                            props.phones.current_page ===
                                            props.phones.last_page
                                        "
                                    >
                                        <span class="d-none d-sm-inline me-1"
                                            >Next</span
                                        >
                                        <span aria-hidden="true">&rarr;</span>
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modals -->
    <Modals
        id="UpdatePhoneModal"
        title="Update Phone Asset"
        header-class="bg-warning text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="updateSubmit" id="updateForm">
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

    <Modals
        id="AddPhoneModal"
        title="Add new phone"
        header-class="bg-primary text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="submitAddForm" id="addPhoneForm">
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-12">
                        <img
                            :src="samplePic"
                            alt="asset-image"
                            class="img-thumbnail rounded-circle d-block mx-auto border border-2 shadow-md"
                            style="width: 8rem; height: auto"
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <label
                                class="input-group-text"
                                for="inputGroupFile01"
                                >Upload</label
                            >
                            <input
                                type="file"
                                class="form-control"
                                id="inputGroupFile01"
                                @change="onFileSelect"
                                accept="image/*"
                            />
                        </div>
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="brandInput" class="form-label"
                            >Brand<i class="text-danger">*</i></label
                        >

                        <select
                            class="form-select"
                            aria-label="Brand"
                            id="brandInput"
                            v-model="addForm.brand"
                            required
                        >
                            <option selected disabled>Select Brand</option>
                            <option
                                :value="brand"
                                :key="brand"
                                v-for="brand in brandsOption"
                            >
                                {{
                                    brand.charAt(0).toUpperCase() +
                                    brand.slice(1)
                                }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="modelInput" class="form-label"
                            >Model<i class="text-danger">*</i></label
                        >
                        <input
                            type="text"
                            id="modelInput"
                            v-model="addForm.model"
                            placeholder="(e.g. iPhone 17)"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="serialNumInput" class="form-label"
                            >Serial Number<i class="text-danger">*</i></label
                        >
                        <input
                            type="text"
                            id="serialNumInput"
                            v-model="addForm.serial_num"
                            placeholder="e.g. C39F2V9JCL"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="simNoInput" class="form-label"
                            >SIM Number</label
                        >
                        <input
                            type="text"
                            id="simNoInput"
                            placeholder="(e.g. 09072853112)"
                            v-model="addForm.sim_no"
                            class="form-control"
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="imeiOneInput" class="form-label"
                            >IMEI One<i class="text-danger">*</i></label
                        >
                        <input
                            type="text"
                            id="imeiOneInput"
                            placeholder="e.g. 356938035643809"
                            v-model="addForm.imei_one"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="imeiTwoInput" class="form-label"
                            >IMEI Two</label
                        >
                        <input
                            type="text"
                            id="imeiTwoInput"
                            v-model="addForm.imei_two"
                            class="form-control"
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="ramInput" class="form-label"
                            >RAM<i class="text-danger">*</i> (GB)</label
                        >
                        <input
                            type="text"
                            id="ramInput"
                            v-model="addForm.ram"
                            placeholder="e.g. 8"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="romInput" class="form-label"
                            >ROM<i class="text-danger">*</i> (GB)</label
                        >
                        <input
                            type="text"
                            id="romInput"
                            v-model="addForm.rom"
                            placeholder="e.g. 256"
                            class="form-control"
                            required
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="purchaseDate" class="form-label"
                            >Purchase Date</label
                        >
                        <input
                            type="date"
                            id="purchaseDate"
                            v-model="addForm.purchase_date"
                            class="form-control"
                        />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="remarksInput" class="form-label">Remarks</label>
                    <textarea
                        id="remarksInput"
                        v-model="addForm.remarks"
                        class="form-control"
                        rows="2"
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
                Close
            </button>
            <button
                type="submit"
                form="addPhoneForm"
                class="btn btn-primary bg-gradient"
                :disabled="addForm.processing"
            >
                <span
                    v-if="addForm.processing"
                    class="spinner-border spinner-border-sm me-1"
                ></span>
                <i class="bi bi-plus-lg me-1"></i>
                Add Asset
            </button>
        </template>
    </Modals>
</template>

<style scoped>
.table thead th {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>
