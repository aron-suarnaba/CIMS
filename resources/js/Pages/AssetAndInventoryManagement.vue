<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import InfoBox from '@/Components/InfoBox.vue';
import { defineOptions } from 'vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';

defineOptions({ layout: HomeLayout });
const phoneIndex = route('phone.index');
const gotoHome = () => {
    router.get(route('dashboard'));
};
const goToPhoneIndex = () => {
    router.get(phoneIndex);
};
</script>
<template>
    <div class="app-content-header">
        <div class="container">
            <div class="row my-4">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">Asset & Inventory Management</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a
                                :href="gotoHome"
                                class="text-underline text-primary"
                                >Home</a
                            >
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Asset & Inventory Management
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
                        @click.prevent="router.get(route('dashboard'))"
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
                <div class="col-sm-12 col-md-4"></div>
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
                <div class="col-sm-12 col-md-2">
                    <InfoBox
                        boxClass="text-bg-primary"
                        @click.prevent="goToPhoneIndex"
                    >
                        <template #header>
                            <i class="bi bi-phone"></i>
                        </template>
                        <template #content> Smartphone </template>
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
