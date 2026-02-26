<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import ListCard from '@/Components/ListCard.vue';
import Modals from '@/Components/Modals.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Pagination from '@/Components/Pagination.vue';

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

// helper that works with page numbers instead of URLs
const gotoPageNumber = (page) => {
    if (!page || page < 1) return;
    // compute URL using the route helper (assumes server understands ?page=)
    const url = route('computer.index', { page });
    gotoPage(url);
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
                    <BackButton @click.prevent="
                        router.get(route('AssetAndInventoryManagement'))
                        " />
                </div>
                <div class="col-sm-12 col-md-4 mb-2">
                    <div class="input-group">
                        <label for="AssetSearchInput" class="input-group-text"><i class="bi bi-search"></i></label>
                        <input id="AssetSearchInput" type="text" class="form-control" placeholder="Search"
                            autofocus="false" />
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 d-flex justify-content-end mb-2 gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#AddComputerModals">
                        <i class="bi bi-plus-lg"></i>
                        Add Workstation
                    </button>
                    <div class="dropdown">
                        <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-funnel"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li v-for="option in sortOption" :key="option.value">
                                <a href="#" class="dropdown-item" @click.prevent="
                                    applyFilter(filterBrand, option.value)
                                    " :class="{
                                        active: currentSort === option.value,
                                    }">
                                    Sort by: {{ option.label }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 g-4 justify-content-start">

                <div v-for="computer in props.computers.data" :key="computer.id" class="col">
                    <ListCard @click.prevent="gotoComputerDetails(computer.host_name)">

                        <div class="img-container mb-3 d-flex align-items-center justify-content-center">
                            <img :src="getComputerImagePath(computer)" class="img-fluid object-fit-contain"
                                :alt="computer.model" />
                        </div>

                        <div class="text-center flex-grow-1 w-100">
                            <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-1"
                                style="font-size: 0.65rem;">
                                {{ computer.manufacturer }}
                            </small>
                            <h6 class="fw-bold text-dark mb-1 text-truncate">
                                {{ computer.model }}
                            </h6>
                            <p class="text-muted fs-7 mb-3">
                                <i class="bi bi-geo-alt-fill me-1"></i>
                                {{ computer.current_transaction?.department || 'Unassigned' }}
                            </p>
                        </div>

                        <span class="badge rounded-pill px-3 py-2 w-100" :class="{
                            'bg-success-subtle text-success': computer.status === 'In Storage',
                            'bg-warning-subtle text-dark': computer.status === 'In Use',
                            'bg-info-subtle text-info-emphasis': computer.status === 'In Repair',
                            'bg-danger-subtle text-danger': computer.status === 'Pullout',
                            'bg-secondary-subtle text-dark': computer.status === 'Retired',
                        }">
                            {{ computer.status }}
                        </span>
                    </ListCard>
                </div>

                <div v-if="props.computers.data?.length === 0" class="col-12 py-5 text-center">
                    <div class="py-5 bg-light rounded-3 border border-dashed">
                        <i class="bi bi-cpu text-muted fs-1"></i>
                        <p class="text-muted mt-2">No computer records found.</p>
                    </div>
                </div>
            </div>

            <div class="text-muted d-flex justify-content-end align-items-center mb-2">
                {{ props.computers?.from || 0 }} -
                {{ props.computers?.to || 0 }} of
                {{ props.computers?.total || 0 }} computers
            </div>

            <div class="d-flex justify-content-end">
                <Pagination :current-page="props.computers.current_page" :last-page="props.computers.last_page"
                    @update:page="gotoPageNumber" />
            </div>
        </div>
    </div>

    <Modals id="AddComputerModals" title="Add new workstation" header-class="bg-primary text-white" layout="bento">
        <template #body>
            <form id="addComputerForm" @submit.prevent="submit">
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="manufacturerInput" class="form-label">Manufacturer</label>
                        <select class="form-select" aria-label="Manufacturer" v-model="addForm.manufacturer">
                            <option selected disabled>
                                Select Manufacturer
                            </option>
                            <option v-for="manufacturer in computerManufacturer" :key="manufacturer.id || manufacturer"
                                :value="manufacturer">
                                {{ manufacturer || 'Others' }}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="modelInput" class="form-label">Model</label>
                        <input type="text" id="modelInput" v-model="addForm.model" class="form-control" required />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="hostNameInput" class="form-label">Host Name</label>
                        <input type="text" id="hostNameInput" v-model="addForm.host_name" class="form-control"
                            required />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="serialNumInput" class="form-label">Serial Number</label>
                        <input type="text" id="serialNumInput" v-model="addForm.serial_number" class="form-control"
                            required />
                    </div>
                </div>
                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="osInput" class="form-label">OS Version</label>
                        <input type="text" id="osInput" v-model="addForm.os_version" class="form-control" required />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="cpuInput" class="form-label">CPU</label>
                        <input type="text" id="cpuInput" v-model="addForm.cpu" class="form-control" required />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="ramInput" class="form-label">RAM</label>
                        <input type="text" id="ramInput" v-model="addForm.ram_gb" class="form-control" required />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="romInput" class="form-label">ROM</label>
                        <input type="text" id="romInput" v-model="addForm.storage_gb" class="form-control" required />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="macInput" class="form-label">Mac Address</label>
                        <input type="text" id="macInput" v-model="addForm.mac_address" class="form-control" required />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="ipInput" class="form-label">IP Address</label>
                        <input type="text" id="ipInput" v-model="addForm.ip_address" class="form-control" required />
                    </div>
                </div>

                <div class="row d-flex align-items-center mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="purchaseDate" class="form-label">Purchase Date</label>
                        <input type="date" id="purchaseDate" v-model="addForm.purchase_date" class="form-control"
                            required />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="warrantyDate" class="form-label">Warranty Expiry</label>
                        <input type="date" id="warrantyDate" v-model="addForm.warranty_expiry" class="form-control"
                            required />
                    </div>
                </div>

                <div class="mb-3">
                    <label for="modelInput" class="form-label">Remarks</label>
                    <textarea id="modelInput" v-model="addForm.remarks" class="form-control" rows="3"></textarea>
                </div>
            </form>
        </template>
        <template #footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Close
            </button>
            <button type="submit" class="btn btn-primary" form="addComputerForm" :disabled="addForm.processing"
                @click="submit">
                <span v-if="addForm.processing" class="spinner-border spinner-border-sm me-1"></span>
                Add Asset
            </button>
        </template>
    </Modals>
</template>
