<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import PhoneCard from '@/Components/PhoneCard.vue';
import BackButton from '@/Components/BackButton.vue';
import { defineOptions, defineProps, ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    phones: {
        type: Object,
        required: true,
    },
});

import iPhoneImage from '/public/img/phone/iphone.png';
import OppoImage from '/public/img/phone/oppo.png';
import RedmiImage from '/public/img/phone/redmi.png';
import SamsungImage from '/public/img/phone/samsung.png';
import VivoImage from '/public/img/phone/vivo.png';
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
                            {{ filterBrand || 'Filter' }}
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

            <div class="row justify-content-start mb-3 mt-5">
                <div
                    class="col-6 col-sm-4 col-md-2 d-flex justify-content-center"
                    v-for="phone in props.phones.data"
                    :key="phone.id"
                >
                    <PhoneCard
                        @click.prevent="gotoPhoneDetails(phone.serial_num)"
                    >
                        <img
                            :src="getPhoneImagePath(phone)"
                            class="img-fluid"
                            style="height: 10rem"
                            :alt="phone.model"
                        />
                        <h4 class="card-title formal-font my-2">
                            {{ phone.model }}
                        </h4>
                        <h6 class="card-subtitle text-body-secondary mb-1">
                            {{ phone.issued_to }}
                        </h6>
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
        </div>
    </div>
</template>
