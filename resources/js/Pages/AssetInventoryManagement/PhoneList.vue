<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import { useDateFormatter } from '@/composables/useDateFormatter';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce'; // Import debounce
import Swal from 'sweetalert2';
import { ref, watch } from 'vue'; // Add watch here

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

//Searching
const debouncedSearch = debounce(() => {
    applyFilter();
}, 500); // 500ms delay

// Watch the searchQuery ref for changes
watch(searchQuery, () => {
    debouncedSearch();
});

// --- FORM LOGIC ---
const addForm = useForm({
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

const submitAddForm = () => {
    addForm.post(route('phone.store'), {
        onSuccess: () => {
            addForm.reset();
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
    const supported = [
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
    const brand = phone.brand?.toLowerCase() || '';
    const matched = supported.find((b) => brand.includes(b));
    return matched
        ? `../img/phone/${matched === 'apple' ? 'iphone' : matched}.png`
        : '../img/phone/default.png';
};

const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Asset Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Phone Assets' },
];

const sortOption = [
    { label: 'Name', value: 'name' },
    { label: 'Date Modified', value: 'date_modified' },
    { label: 'Availability', value: 'availability' },
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

            <div class="card border-0 shadow-sm">
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
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="phone in props.phones.data"
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
                                        #{{ phone.id }}
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
                                            >{{ phone.ram }}GB /
                                            {{ phone.rom }}GB</small
                                        >
                                    </td>
                                    <td>
                                        <span
                                            :class="
                                                phone.current_transaction
                                                    ? 'text-dark'
                                                    : 'text-muted fst-italic'
                                            "
                                        >
                                            {{
                                                phone.current_transaction
                                                    ?.department || 'Unassigned'
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
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer border-top-0 bg-white py-3">
                    <div
                        class="d-flex justify-content-between align-items-center"
                    >
                        <span class="text-muted small"
                            >Showing {{ props.phones.from }} to
                            {{ props.phones.to }} of
                            {{ props.phones.total }} results</span
                        >
                        <nav>
                            <ul class="pagination pagination-sm mb-0 gap-2">
                                <li
                                    v-for="link in props.phones.links"
                                    :key="link.label"
                                    class="page-item"
                                    :class="{
                                        active: link.active,
                                        disabled: !link.url,
                                    }"
                                >
                                    <button
                                        class="page-link"
                                        @click.prevent="router.get(link.url)"
                                        v-html="link.label"
                                    ></button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Modals
        id="AddPhoneModal"
        title="Add new phone"
        header-class="bg-success text-white bg-gradient"
    >
        <template #body>
            <form @submit.prevent="submitAddForm" id="addPhoneForm">
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="brandInput" class="form-label">Brand</label>
                        <input
                            type="text"
                            id="brandInput"
                            v-model="addForm.brand"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="modelInput" class="form-label">Model</label>
                        <input
                            type="text"
                            id="modelInput"
                            v-model="addForm.model"
                            class="form-control"
                            required
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="serialNumInput" class="form-label"
                            >Serial Number</label
                        >
                        <input
                            type="text"
                            id="serialNumInput"
                            v-model="addForm.serial_num"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="imeiOneInput" class="form-label"
                            >IMEI One</label
                        >
                        <input
                            type="text"
                            id="imeiOneInput"
                            v-model="addForm.imei_one"
                            class="form-control"
                            required
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
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
                    <div class="col-sm-12 col-md-6">
                        <label for="ramInput" class="form-label">RAM</label>
                        <input
                            type="text"
                            id="ramInput"
                            v-model="addForm.ram"
                            class="form-control"
                            required
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="romInput" class="form-label">ROM</label>
                        <input
                            type="text"
                            id="romInput"
                            v-model="addForm.rom"
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
                            v-model="addForm.sim_no"
                            class="form-control"
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
                Close
            </button>
            <button
                type="submit"
                form="addPhoneForm"
                class="btn btn-success bg-gradient"
                :disabled="addForm.processing"
            >
                <span
                    v-if="addForm.processing"
                    class="spinner-border spinner-border-sm me-1"
                ></span>
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
