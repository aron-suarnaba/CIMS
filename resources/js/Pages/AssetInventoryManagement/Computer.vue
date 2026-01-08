<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import ListCard from '@/Components/ListCard.vue';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { router } from '@inertiajs/vue3';
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
const gotoAddComputer = () => {
    router.get(route('computer.create'));
};

const gotoComputerDetails = (host_name) => {
    router.get(route('computer.show', { phone: host_name }));
};

const getComputerImagePath = (computers) => {
    // Default fallback
    const defaultPath = '../img/phone/default.png';
    if (!computers || !computers.manufacturer) return defaultPath;

    const brand = computers.manufacturer.toLowerCase();

    // Define your supported brands
    const supportedBrands = [
        'dell',
        'hp',
        'lenovo',
        'apple',
        'asus',
        'acer',
    ];

    const matched = supportedBrands.find((b) => brand.includes(b));

    if (matched) {
        return `../img/computer/${matched}.png`;
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
            <div class="row mb-3 d-flex flex-wrap justify-content-center g-2">
                <div class="col-sm-12 col-md-4 mb-2">
                    <BackButton @click.prevent="
                        router.get(route('AssetAndInventoryManagement'))
                        " />
                </div>
                <div class="col-sm-12 col-md-4 mb-2">
                    <div class="input-group">
                        <label for="AssetSearchInput" class="input-group-text"><i class="bi bi-search"></i></label>
                        <input id="AssetSearchInput" type="text" class="form-control w-25" placeholder="Search"
                            autofocus="false" />
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 d-flex justify-content-end gap-2 mb-2">
                    <button type="button" class="btn btn-success bg-gradient" @click.prevent="gotoAddComputer">
                        <i class="bi bi-plus-lg"></i>
                        Add a phone
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

            <div class="row justify-content-start mb-3 mt-5 px-5">
                <div class="col-sm-12 col-md-2 col-xl-2 d-flex justify-content-center mb-4"
                    v-for="computers in props.computers.data" :key="computers.id">
                    <ListCard @click.prevent="gotoComputerDetails(computers.host_name)">
                        <img :src="getComputerImagePath(computers)" class="img-fluid" style="max-height: 8rem;"
                            :alt="computers.model" />
                        <div class="mb-0 gap-0 mt-2">
                            <h4 class="card-title formal-font text-wrap">
                            {{ computers.model }}
                        </h4>
                        <p class="text-muted">{{ computers.department }}</p>
                        </div>
                        <span :class="{
                            'badge bg-success':
                                computers.status === 'In Storage',
                            'badge bg-warning text-dark': computers.status === 'In Use',
                            'badge bg-info': computers.status === 'In Repair',
                            'badge bg-dark':
                                computers.status === 'Retired',
                        }">
                            {{ computers.status }}
                        </span>
                    </ListCard>
                </div>

                <div v-if="props.computers.data && props.computers.data.length === 0" class="col-12">
                    <p class="text-muted text-center">
                        No computer records found.
                    </p>
                </div>
            </div>

            <div class="text-muted d-flex justify-content-end align-items-center mb-2">
                {{ props.computers?.from || 0 }} -
                {{ props.computers?.to || 0 }} of
                {{ props.computers?.total || 0 }} computers
            </div>
            <nav aria-label="Computer pagination">
                <ul class="pagination d-flex justify-content-end align-items-center mb-0 gap-2">
                    <li v-for="(link, index) in props.computers.links" :key="index" class="page-item" :class="{
                        active: link.active,
                        disabled: !link.url,
                    }">
                        <button class="page-link" @click.prevent="gotoPage(link.url)" v-html="link.label"
                            :disabled="!link.url"></button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
