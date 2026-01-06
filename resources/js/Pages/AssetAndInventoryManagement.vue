<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import InfoBox from '@/Components/InfoBox.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';


defineOptions({ layout: HomeLayout });

const myBreadcrumb = [
    { label : 'Home', url : route('dashboard') },
    { label : 'Asset & Inventory' }
];

const navigateTo = (url) => {
    if (url) router.get(url);
}

const navCard = [
    { label : 'SmartPhone', url : route('phone.index'), color : 'bg-primary text-white', icon : 'bi bi-phone' },
];
</script>
<template>
    <div class="app-content-header">
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>
    <div class="app-content">
        <div class="container">
            <div class="row mb-5">
                <div class="col-sm-12 col-md-4 mb-3">
                    <BackButton
                        @click.prevent="router.get(route('dashboard'))"
                    />
                </div>
                <div class="col-sm-12 col-md-4 mb-3">
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
            </div>
            <div class="row mb-3 mt-5">
                <!-- <div class="col-sm-12 col-md-2">
                    <InfoBox boxClass="text-bg-success">
                        <template #header>
                            <i class="bi bi-pc-display"></i>
                        </template>
                        <template #content> Computer </template>
                    </InfoBox>
                </div>
                <div class="col-sm-12 col-md-2">
                    <InfoBox boxClass="text-bg-warning">
                        <template #header>
                            <i class="bi bi-printer"></i>
                        </template>
                        <template #content> Printer </template>
                    </InfoBox>
                </div> -->
                <div class="col-sm-12 col-md-2" v-for="option in navCard" :key="option.label">
                    <InfoBox
                        :boxClass="option.color"
                        @click.prevent="navigateTo(option.url)"
                    >
                        <template #header>
                            <i :class="option.icon"></i>
                        </template>
                        <template #content> {{ option.label }} </template>
                    </InfoBox>
                </div>
                <!-- <div class="col-sm-12 col-md-2">
                    <InfoBox boxClass="text-bg-info">
                        <template #header>
                            <i class="bi bi-windows"></i>
                        </template>
                        <template #content> License </template>
                    </InfoBox>
                </div>
                <div class="col-sm-12 col-md-2">
                    <InfoBox boxClass="text-bg-info">
                        <template #header>
                            <i class="bi bi-windows"></i>
                        </template>
                        <template #content> Others </template>
                    </InfoBox>
                </div> -->
            </div>
        </div>
    </div>
</template>
