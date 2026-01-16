<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ListCard from '@/Components/ListCard.vue';
import Modals from '@/Components/Modals.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    phones: {
        type: Object,
        required: true,
    },
});

const gotoPage = (url) => {
    if (!url) return;

    router.get(
        url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};

const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    {
        label: 'Asset Inventory and Management',
        url: route('AssetAndInventoryManagement'),
    },
    { label: 'Phone Asset' },
];

const filterBrand = ref(
    new URLSearchParams(window.location.search).get('brand') || '',
);
const currentSort = ref(
    new URLSearchParams(window.location.search).get('sort') || 'availability',
);

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
            // Close modal
            const closeButton = document.querySelector(
                '#AddPhoneModal [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
        },
    });
};

const sortOption = [
    { label: 'Name', value: 'name' },
    { label: 'Date Modified', value: 'date_modified' },
    { label: 'Availability', value: 'availability' },
];

const applyFilter = (brand = filterBrand.value, sort = currentSort.value) => {
    filterBrand.value =
        brand === 'All Brands' || brand === 'All brand' ? '' : brand;
    currentSort.value = sort;

    router.get(
        route('phone.index'),
        {
            brand: filterBrand.value,
            sort: currentSort.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
};

const gotoPhoneDetails = (phoneSerialNumber) => {
    router.get(route('phone.show', { phone: phoneSerialNumber }));
};

const getPhoneImagePath = (phone) => {
    // Default fallback
    const defaultPath = '../img/phone/default.png';
    if (!phone || !phone.brand) return defaultPath;

    const brand = phone.brand.toLowerCase();

    // Define your supported brands
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
        return `../img/phone/${fileName}.png`;
    }

    return defaultPath;
};
</script>

<template>
    <div class="app-content-header">
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>
    <div class="app-content">
        <div class="container">
            <div class="row d-flex justify-content-center g-2 mb-3 flex-wrap">
                <div class="col-sm-12 col-md-4 mb-2">
                    <BackButton
                        @click.prevent="
                            router.get(route('AssetAndInventoryManagement'))
                        "
                    />
                </div>
                <div class="col-sm-12 col-md-4 mb-2">
                    <div class="input-group">
                        <label for="AssetSearchInput" class="input-group-text"
                            ><i class="bi bi-search"></i
                        ></label>
                        <input
                            id="AssetSearchInput"
                            type="text"
                            class="form-control w-25"
                            placeholder="Search"
                            autofocus="false"
                        />
                    </div>
                </div>
                <div
                    class="col-sm-12 col-md-4 d-flex justify-content-end mb-2 gap-2"
                >
                    <button
                        type="button"
                        class="btn btn-success bg-gradient"
                        data-bs-toggle="modal"
                        data-bs-target="#AddPhoneModal"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Add a phone
                    </button>
                    <div class="dropdown">
                        <button
                            type="button"
                            class="btn btn-secondary dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <i class="bi bi-funnel"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li
                                v-for="option in sortOption"
                                :key="option.value"
                            >
                                <a
                                    href="#"
                                    class="dropdown-item"
                                    @click.prevent="
                                        applyFilter(filterBrand, option.value)
                                    "
                                    :class="{
                                        active: currentSort === option.value,
                                    }"
                                >
                                    Sort by: {{ option.label }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row justify-content-start mb-3 mt-5 px-5">
                <div
                    class="col-sm-12 col-md-2 col-xl-2 d-flex justify-content-center mb-5"
                    v-for="phone in props.phones.data"
                    :key="phone.id"
                >
                    <ListCard
                        @click.prevent="gotoPhoneDetails(phone.serial_num)"
                    >
                        <img
                            :src="getPhoneImagePath(phone)"
                            class="img-fluid"
                            style="max-height: 8rem"
                            :alt="phone.model"
                        />
                        <h4 class="card-title formal-font my-2 text-wrap">
                            {{ phone.model }}
                        </h4>
                        <span
                            :class="{
                                'badge bg-success':
                                    phone.status === 'available',
                                'badge bg-primary': phone.status === 'issued',
                                'badge bg-warning text-dark':
                                    phone.status === 'returned',
                            }"
                        >
                            {{
                                phone.status[0].toUpperCase() +
                                phone.status.slice(1)
                            }}
                        </span>
                    </ListCard>
                </div>

                <div
                    v-if="props.phones.data && props.phones.data.length === 0"
                    class="col-12"
                >
                    <p class="text-muted text-center">
                        No phone records found.
                    </p>
                </div>
            </div>

            <div class="row justify-content-end align-items-center mb-4">
                <div class="col-sm-12 col-xl-4 col-lg-4">
                    <div
                        class="text-muted d-flex justify-content-center align-items-center mb-2"
                    >
                        {{ props.phones?.from || 0 }} -
                        {{ props.phones?.to || 0 }} of
                        {{ props.phones?.total || 0 }} phones
                    </div>
                    <nav aria-label="Phone pagination">
                        <ul
                            class="pagination d-flex justify-content-center align-items-center mb-0 gap-2"
                        >
                            <li
                                v-for="(link, index) in props.phones.links"
                                :key="index"
                                class="page-item"
                                :class="{
                                    active: link.active,
                                    disabled: !link.url,
                                }"
                            >
                                <button
                                    class="page-link"
                                    @click.prevent="gotoPage(link.url)"
                                    v-html="link.label"
                                    :disabled="!link.url"
                                ></button>
                            </li>
                        </ul>
                    </nav>
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
                class="btn btn-success bg-gradient"
                form="addPhoneForm"
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
