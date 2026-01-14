<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import avatarPath from '/public/img/avatar.png';

defineOptions({ layout: HomeLayout });

const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Profile' },
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
            <div class="mb-5">
                <BackButton @click.prevent="router.get(route('dashboard'))" />
            </div>

            <div class="card bg-light-subtle mb-5 rounded shadow-sm">
                <div class="card-content">
                    <div class="row align-items-center mb-4 flex-wrap pt-4">
                        <div
                            class="col-sm-12 col-md-2 d-flex justify-content-center align-items-center"
                        >
                            <div class="ms-4">
                                <img
                                    :src="avatarPath"
                                    class="rounded-circle img-fluid"
                                    alt="User Image"
                                    style="max-height: 7rem"
                                />
                            </div>
                        </div>
                        <div
                            class="col-sm-12 col-md-2 d-flex justify-content-center align-items-center flex-column"
                        >
                            <h3 class="text-primary mb-0">
                                {{ $page.props.auth.user.first_name }}
                                {{ $page.props.auth.user.last_name }}
                            </h3>
                            <p class="text-muted">
                                {{ $page.props.auth.user.position }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-light-subtle mb-5 rounded shadow-sm">
                <div class="card-content p-3">
                    <div class="row mb-3  d-flex align-items-center justify-content-between">
                        <div class="col-sm-12 col-md-10 mb-3">
                            <span class="fw-bolder h4 text-primary mb-4 text-center"
                                >Personal information</span
                            >
                        </div>
                        <div class="col-sm-12 col-md-2 mb-3">
                            <button type="button" class="btn btn-warning">Edit information</button>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12 col-md-4">
                            <div class="mb-4 mt-3">
                                <p class="text-muted mb-0">Name</p>
                                <p class="fw-bold text-wrap">
                                    {{ $page.props.auth.user.first_name }}
                                    {{ $page.props.auth.user.last_name }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0">Employee ID</p>
                                <p class="fw-bold text-wrap">
                                    {{ $page.props.auth.user.employee_id }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="mb-4 mt-3">
                                <p class="text-muted mb-0">Position</p>
                                <p class="fw-bold text-wrap">
                                    {{ $page.props.auth.user.position }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0">Department</p>
                                <p class="fw-bold text-wrap">
                                    {{ $page.props.auth.user.first_name }}
                                    {{ $page.props.auth.user.department }}
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <div class="mb-4 mt-3">
                                <p class="text-muted mb-0">Email</p>
                                <p class="fw-bold text-wrap">
                                    {{ $page.props.auth.user.email }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <p class="text-muted mb-0">Created Date</p>
                                <p class="fw-bold text-wrap">
                                    {{ $page.props.auth.user.created_at }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
