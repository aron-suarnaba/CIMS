<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import PhoneCard from '@/Components/PhoneCard.vue';
import BackButton from '@/Components/BackButton.vue';
import { router } from '@inertiajs/vue3';
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
            preserveState: true, // Keeps your search/filter values in the URL
            preserveScroll: true, // Prevents jumping to the top of the page
        },
    );
};

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
    // Ensure 'All Brands' matches what is in your template
    filterBrand.value = (brand === 'All Brands' || brand === 'All brand') ? '' : brand;
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
        }
    );
};
const AssetInventoryManagementIndex = route('AssetAndInventoryManagement');
const Home = route('dashboard');
const gotoAddPhone = () => {
    router.get(route('phone.create'));
};

const gotoPhoneDetails = (phoneSerialNumber) => {
    router.get(route('phone.show', { phone: phoneSerialNumber }));
};

const gotoAssetInventoryManagementIndex = () => {
    router.get(AssetInventoryManagementIndex);
};
const gotoHome = () => {
    router.get(Home);
};

const getPhoneImagePath = (phone) => {
    // Default fallback
    const defaultPath = '/img/phone/default.png';
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
        return `/img/phone/${fileName}.png`;
    }

    return defaultPath;
};
</script>

<template>
    <div class="app-content-header">
        <div class="container">
            <div class="row my-4">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">Smartphone</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a :href="Home" @click.prevent="gotoHome" class="text-underline">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a :href="AssetInventoryManagementIndex" @click.prevent="
                                gotoAssetInventoryManagementIndex
                            " class="text-underline">Asset & Inventory Management</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Smartphone
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container">
            <div class="row mb-5">
                <div class="col-sm-12 col-md-4">
                    <BackButton @click.prevent="
                        router.get(route('AssetAndInventoryManagement'))
                        " />
                </div>
                <div class="col-sm-12 col-md-4">
                    <div class="input-group">
                        <label for="AssetSearchInput" class="input-group-text"><i class="bi bi-search"></i></label>
                        <input id="AssetSearchInput" type="text" class="form-control" placeholder="Search"
                            autofocus="false" />
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-success bg-gradient" @click.prevent="gotoAddPhone">
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
                <div class="col-6 col-sm-4 col-md-2 d-flex justify-content-center mb-5"
                    v-for="phone in props.phones.data" :key="phone.id">
                    <PhoneCard @click.prevent="gotoPhoneDetails(phone.serial_num)">
                        <img :src="getPhoneImagePath(phone)" class="img-fluid" style="height: 8rem"
                            :alt="phone.model" />
                        <h4 class="card-title formal-font my-2">
                            {{ phone.model }}
                        </h4>
                        <h6 class="card-subtitle text-body-secondary mb-1">
                            {{ phone.issued_to }}
                        </h6>
                        <span :class="{
                            'badge bg-success':
                                phone.status === 'available',
                            'badge bg-primary': phone.status === 'issued',
                            'badge bg-warning text-dark':
                                phone.status === 'returned',
                        }">
                            {{ phone.status }}
                        </span>
                    </PhoneCard>
                </div>

                <div v-if="props.phones.data && props.phones.data.length === 0" class="col-12">
                    <p class="text-muted text-center">
                        No phone records found.
                    </p>
                </div>
            </div>

            <div class="row justify-content-end align-items-center mb-4">
                <div class="col-sm-12 col-xl-4 col-lg-4">
                    <div class="text-muted d-flex justify-content-center align-items-center mb-2">
                        {{ props.phones?.from || 0 }} -
                        {{ props.phones?.to || 0 }} of
                        {{ props.phones?.total || 0 }} phones
                    </div>
                    <nav aria-label="Phone pagination">
                        <ul class="pagination d-flex justify-content-center align-items-center mb-0 gap-2">
                            <li v-for="(link, index) in props.phones.links" :key="index" class="page-item" :class="{
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
        </div>
    </div>
</template>
