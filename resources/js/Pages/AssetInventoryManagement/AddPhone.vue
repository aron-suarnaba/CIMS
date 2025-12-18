<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { defineOptions } from 'vue';
import { router } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });
</script>

<template>
    <div class="app-content-header">
        <div class="container">
            <div class="row my-4">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">Add Smartphone</h1>
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
                                :href="route('AssetAndInventoryManagement')"
                                @click.prevent="
                                    router.get(
                                        route('AssetAndInventoryManagement'),
                                    )
                                "
                                class="text-underline"
                                >Asset & Inventory Management</a
                            >
                        </li>
                        <li class="breadcrumb-item">
                            <a
                                :href="route('phone.index')"
                                @click.prevent="
                                    router.get(route('phone.index'))
                                "
                                class="text-underline"
                                >Smartphone</a
                            >
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Add Smartphone
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container">
            <div class="row mb-3">
                <div class="col-sm-12 col-md-4">
                    <button
                        type="button"
                        class="btn btn-outline-secondary ms-2"
                        @click.prevent="router.get(route('phone.index'))"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Back
                    </button>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-12 col-12">
                    <div class="border-1 container border shadow-sm"></div>
                </div>
            </div>
        </div>
    </div>
</template>
