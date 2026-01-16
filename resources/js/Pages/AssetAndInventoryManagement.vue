<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import InfoBox from '@/Components/InfoBox.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

defineOptions({ layout: HomeLayout });

const navigateTo = (url) => {
    if (url) router.get(url);
};

const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Inventory' },
];

const navCard = [
    {
        label: 'Company Phone',
        url: route('phone.index'),
        color: 'bg-primary bg-gradient text-white',
        icon: 'bi bi-phone',
    },
    {
        label: 'Computer',
        url: route('computer.index'),
        color: 'bg-info bg-gradient text-white',
        icon: 'bi bi-pc-display',
    },
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
                <div class="col-sm-12 col-md-4 mb-2">
                    <BackButton
                        @click.prevent="router.get(route('dashboard'))"
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
            </div>
            <div class="row mb-3 mt-5">
                <div
                    class="col-sm-12 col-md-2"
                    v-for="option in navCard"
                    :key="option.label"
                >
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
            </div>
        </div>
    </div>
</template>
