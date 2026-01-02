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

    router.get(url, {}, {
        preserveState: true, // Keeps your search/filter values in the URL
        preserveScroll: true, // Prevents jumping to the top of the page
    });
};

import iPhoneImage from '/public/img/phone/iphone.png';
import OppoImage from '/public/img/phone/oppo.png';
import RedmiImage from '/public/img/phone/redmi.png';
import SamsungImage from '/public/img/phone/samsung.png';
import VivoImage from '/public/img/phone/vivo.png';
import RealmeImage from '/public/img/phone/realme.png';
import XiaomiImage from '/public/img/phone/xiaomi.png';
import HonorImage from '/public/img/phone/honor.png';
import TechnoImage from '/public/img/phone/techno.png';
import DefaultImage from '/public/img/phone/default.png';

const filterBrand = ref(
    new URLSearchParams(window.location.search).get('brand') || '',
);
const availableBrands = [
    'All Brands',
    'Apple',
    'Oppo',
    'Redmi',
    'Samsung',
    'Realme',
    'Vivo',
];
const applyFilter = (brand) => {
    filterBrand.value = brand === 'All Brands' ? '' : brand;

    router.get(
        route('phone.index'),
        { brand: filterBrand.value },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
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
    const brand = phone.brand ? phone.brand.toLowerCase() : '';

    if (brand.includes('iphone') || brand.includes('apple')) {
        return iPhoneImage;
    }
    if (brand.includes('oppo')) {
        return OppoImage;
    }
    if (brand.includes('redmi')) {
        return RedmiImage;
    }
    if (brand.includes('samsung')) {
        return SamsungImage;
    }
    if (brand.includes('xiaomi')) {
        return XiaomiImage;
    }
    if (brand.includes('realme')) {
        return RealmeImage;
    }
    if (brand.includes('honor')) {
        return HonorImage;
    }
    if (brand.includes('techno')) {
        return TechnoImage;
    }
    if (brand.includes('vivo')) {
        return VivoImage;
    }

    return DefaultImage;
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
                            <a
                                :href="Home"
                                @click.prevent="gotoHome"
                                class="text-underline"
                                >Home</a
                            >
                        </li>
                        <li class="breadcrumb-item">
                            <a
                                :href="AssetInventoryManagementIndex"
                                @click.prevent="
                                    gotoAssetInventoryManagementIndex
                                "
                                class="text-underline"
                                >Asset & Inventory Management</a
                            >
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
                    <BackButton
                        @click.prevent="
                            router.get(route('AssetAndInventoryManagement'))
                        "
                    />
                </div>
                <div class="col-sm-12 col-md-4">
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
                    class="col-sm-12 col-md-4 d-flex justify-content-end gap-2"
                >
                    <button
                        type="button"
                        class="btn btn-success bg-gradient"
                        @click.prevent="gotoAddPhone"
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
                            <li v-for="brand in availableBrands" :key="brand">
                                <a
                                    href="#"
                                    class="dropdown-item"
                                    @click.prevent="applyFilter(brand)"
                                    :class="{
                                        active:
                                            brand === filterBrand ||
                                            (brand === 'All Brands' &&
                                                !filterBrand),
                                    }"
                                >
                                    {{ brand }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row justify-content-start mb-3 mt-5 px-5">
                <div
                    class="col-6 col-sm-4 col-md-2 d-flex justify-content-center mb-5"
                    v-for="phone in props.phones.data"
                    :key="phone.id"
                >
                    <PhoneCard
                        @click.prevent="gotoPhoneDetails(phone.serial_num)"
                    >
                        <img
                            :src="getPhoneImagePath(phone)"
                            class="img-fluid"
                            style="height: 8rem"
                            :alt="phone.model"
                        />
                        <h4 class="card-title formal-font my-2">
                            {{ phone.model }}
                        </h4>
                        <h6 class="card-subtitle text-body-secondary mb-1">
                            {{ phone.issued_to }}
                        </h6>
                        <span
                            :class="{
                                'badge bg-success':
                                    phone.status === 'available',
                                'badge bg-primary': phone.status === 'issued',
                                'badge bg-warning text-dark':
                                    phone.status === 'returned',
                            }"
                        >
                            {{ phone.status }}
                        </span>
                    </PhoneCard>
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
                    <div class="text-muted d-flex justify-content-center align-items-center mb-2">
                        {{ props.phones?.from || 0 }} -
                        {{ props.phones?.to || 0 }} of
                        {{ props.phones?.total || 0 }} phones
                    </div>
                    <nav aria-label="Phone pagination">
                        <ul class="pagination mb-0 d-flex gap-2 justify-content-center align-items-center">
                            <li v-for="(link, index) in props.phones.links"
                            :key="index"
                            class="page-item"
                            :class="{ 'active': link.active, 'disabled': !link.url }"
                            >
                                <button 
                                class="page-link"
                                @click.prevent="gotoPage(link.url)"
                                v-html="link.label"
                                :disabled="!link.url"
                                >

                                </button>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
