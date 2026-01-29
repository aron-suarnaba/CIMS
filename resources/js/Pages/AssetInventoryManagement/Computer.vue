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
    computers: {
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
    { label: 'Home', url: route('dashboard') },
    { label: 'Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Workstation Asset' },
];

const filterBrand = ref(
    new URLSearchParams(window.location.search).get('brand') || '',
);
const currentSort = ref(
    new URLSearchParams(window.location.search).get('sort') || 'availability',
);

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
        route('computer.index'),
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

const gotoComputerDetails = (host_name) => {
    router.get(route('computer.show', { computer: host_name }));
};

const getComputerImagePath = (computers) => {
    // Default fallback
    const defaultPath = '../img/phone/default.png';
    if (!computers || !computers.manufacturer) return defaultPath;

    const brand = computers.manufacturer.toLowerCase();

    // Define your supported brands
    const supportedBrands = ['dell', 'hp', 'lenovo', 'apple', 'asus', 'acer'];

    const matched = supportedBrands.find((b) => brand.includes(b));

    if (matched) {
        return `../img/computer/${matched}.png`;
    }

    return defaultPath;
};

const computerManufacturer = [
    'Lenovo',
    'HP',
    'Dell Technologies',
    'Apple',
    'ASUS',
    'Acer',
    'Microsoft',
    'Samsung',
    'MSI',
    'Razer',
    'Gigabyte',
    'Huawei',
    'LG',
    'Panasonic',
    'Fujitsu',
    'Others',
];

const addForm = useForm({
    host_name: '',
    serial_number: '',
    manufacturer: '',
    model: '',
    os_version: '',
    cpu: '',
    ram_gb: '',
    storage_gb: '',
    mac_address: '',
    ip_address: '',
    purchase_date: '',
    po_number: '',
    warranty_expiry: '',
    remarks: '',
});

const submit = () => {
    addForm.post(route('computer.store'), {
        onSuccess: () => {
            addForm.reset();
            const closeButton = document.querySelector(
                '#AddComputerModals [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
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
                            class="form-control"
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
                        data-bs-target="#AddComputerModals"
                    >
                        <i class="bi bi-plus-lg"></i>
                        Add Workstation
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
                    class="col-sm-12 col-md-2 col-xl-2 d-flex justify-content-center mb-4"
                    v-for="computers in props.computers.data"
                    :key="computers.id"
                >
                    <ListCard
                        @click.prevent="
                            gotoComputerDetails(computers.host_name)
                        "
                    >
                        <img
                            :src="getComputerImagePath(computers)"
                            class="img-fluid"
                            style="max-height: 8rem"
                            :alt="computers.model"
                        />

                        <div class="mb-0 mt-2 gap-0">
                            <h4 class="card-title formal-font text-wrap">
                                {{ computers.manufacturer }}
                                {{ computers.model }}
                            </h4>

                            <p class="text-muted fw-bold fs-7">
                                {{
                                    computers.current_transaction?.department ||
                                    'Unassigned'
                                }}
                            </p>
                        </div>

                        <span
                            :class="{
                                'badge bg-success':
                                    computers.status === 'In Storage',
                                'badge bg-warning text-dark':
                                    computers.status === 'In Use',
                                'badge bg-info':
                                    computers.status === 'In Repair',
                                'badge bg-danger':
                                    computers.status === 'Pullout',
                                'badge bg-dark': computers.status === 'Retired',
                            }"
                        >
                            {{ computers.status }}
                        </span>
                    </ListCard>
                </div>

                <div
                    v-if="
                        props.computers.data &&
                        props.computers.data.length === 0
                    "
                    class="col-12"
                >
                    <p class="text-muted text-center">
                        No computer records found.
                    </p>
                </div>
            </div>

            <div
                class="text-muted d-flex justify-content-end align-items-center mb-2"
            >
                {{ props.computers?.from || 0 }} -
                {{ props.computers?.to || 0 }} of
                {{ props.computers?.total || 0 }} computers
            </div>
            <nav aria-label="Computer pagination">
                <ul
                    class="pagination d-flex justify-content-end align-items-center mb-0 gap-2"
                >
                    <li
                        v-for="(link, index) in props.computers.links"
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

    <Modals
        id="AddComputerModals"
        title="Add new workstation"
        header-class="bg-success text-white bg-gradient"
    >
        <template #body>
            <form id="addComputerForm" @submit.prevent="submit">
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="manufacturerInput" class="form-label"
                            >Manufacturer</label
                        >
                        <select
                            class="form-select"
                            aria-label="Manufacturer"
                            v-model="addForm.manufacturer"
                        >
                            <option selected disabled>
                                Select Manufacturer
                            </option>
                            <option
                                v-for="manufacturer in computerManufacturer"
                                :key="manufacturer.id || manufacturer"
                                :value="manufacturer"
                            >
                                {{ manufacturer || 'Others' }}
                            </option>
                        </select>
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
                        <label for="hostNameInput" class="form-label"
                            >Host Name</label
                        >
                        <input
                            type="text"
                            id="hostNameInput"
                            v-model="addForm.host_name"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="serialNumInput" class="form-label"
                            >Serial Number</label
                        >
                        <input
                            type="text"
                            id="serialNumInput"
                            v-model="addForm.serial_number"
                            class="form-control"
                            required
                        />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="osInput" class="form-label"
                            >OS Version</label
                        >
                        <input
                            type="text"
                            id="osInput"
                            v-model="addForm.os_version"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="cpuInput" class="form-label">CPU</label>
                        <input
                            type="text"
                            id="cpuInput"
                            v-model="addForm.cpu"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="ramInput" class="form-label">RAM</label>
                        <input
                            type="text"
                            id="ramInput"
                            v-model="addForm.ram_gb"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="romInput" class="form-label">ROM</label>
                        <input
                            type="text"
                            id="romInput"
                            v-model="addForm.storage_gb"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="macInput" class="form-label"
                            >Mac Address</label
                        >
                        <input
                            type="text"
                            id="macInput"
                            v-model="addForm.mac_address"
                            class="form-control"
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="ipInput" class="form-label"
                            >IP Address</label
                        >
                        <input
                            type="text"
                            id="ipInput"
                            v-model="addForm.ip_address"
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
                            required
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="warrantyDate" class="form-label"
                            >Warranty Expiry</label
                        >
                        <input
                            type="date"
                            id="warrantyDate"
                            v-model="addForm.warranty_expiry"
                            class="form-control"
                            required
                        />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="modelInput" class="form-label">Remarks</label>
                    <textarea
                        id="modelInput"
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
                form="addComputerForm"
                :disabled="addForm.processing"
                @click="submit"
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
