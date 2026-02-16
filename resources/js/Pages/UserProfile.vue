<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import Modals from '@/Components/Modals.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3'; // Added useForm and usePage
import avatarPath from '/public/img/avatar.png';

defineOptions({ layout: HomeLayout });

const user = usePage().props.auth.user;

const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Profile' },
];

// Initialize the Inertia form helper
const form = useForm({
    first_name: user.first_name,
    last_name: user.last_name,
    employee_id: user.employee_id,
    position: user.position,
    department: user.department,
    email: user.email,
    password: '',
    password_confirmation: '',
});

const formatDate = (dateString, locale = 'en-US') => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(date);
};

const submitPersonalInfoEdit = () => {
    form.patch(route('user.update'), {
        onSuccess: () => {
            const closeButton = document.querySelector(
                '#personalInfoEditModals [data-bs-dismiss="modal"]',
            );
            if (closeButton) {
                closeButton.click();
            }
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
                    <div
                        class="row d-flex align-items-center justify-content-between border-bottom border-bottom-1 mb-3"
                    >
                        <div class="col-sm-12 col-md-10 mb-3">
                            <span
                                class="fw-bolder h4 text-primary mb-4 text-center"
                                >Personal information</span
                            >
                        </div>
                        <div class="col-sm-12 col-md-2 mb-3">
                            <button
                                type="button"
                                data
                                class="btn btn-outline-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#personalInfoEditModals"
                            >
                                Edit information
                            </button>
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
                                    {{
                                        formatDate(
                                            $page.props.auth.user.created_at,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Modals
        title="Edit Personal Information"
        id="personalInfoEditModals"
        headerClass="bg-warning bg-gradient text-white"
    >
        <template #body>
            <form
                id="personalInfoEditForms"
                @submit.prevent="submitPersonalInfoEdit"
            >
                <div class="mb-3">
                    <label for="employee_id" class="form-label"
                        >Employee ID</label
                    >
                    <input
                        v-model="form.employee_id"
                        type="text"
                        class="form-control"
                        id="employee_id"
                    />
                </div>
                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="first_name" class="form-label"
                            >First Name</label
                        >
                        <input
                            v-model="form.first_name"
                            type="text"
                            class="form-control"
                            id="first_name"
                        />
                        <div
                            v-if="form.errors.first_name"
                            class="text-danger small"
                        >
                            {{ form.errors.first_name }}
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="last_name" class="form-label"
                            >Last Name</label
                        >
                        <input
                            v-model="form.last_name"
                            type="text"
                            class="form-control"
                            id="last_name"
                        />
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-12 col-md-6 mb-3">
                        <label for="job_title" class="form-label"
                            >Job Title</label
                        >
                        <input
                            v-model="form.position"
                            type="text"
                            class="form-control"
                            id="job_title"
                        />
                    </div>
                    <div class="col-sm-12 col-md-6 mb-3">
                        <label for="department" class="form-label"
                            >Department</label
                        >
                        <input
                            v-model="form.department"
                            type="text"
                            class="form-control"
                            id="department"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="password" class="form-label"
                            >Password</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            class="form-control"
                            id="password"
                            placeholder="Leave blank to keep current"
                        />
                        <div
                            v-if="form.errors.password"
                            class="text-danger small"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="confirm_password" class="form-label"
                            >Confirm Password</label
                        >
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            class="form-control"
                            id="confirm_password"
                            placeholder="Confirm new password"
                        />
                    </div>
                </div>
                <div v-if="form.errors.employee_id" class="text-danger small">
                    {{ form.errors.employee_id }}
                </div>
                <div v-if="form.errors.email" class="text-danger small">
                    {{ form.errors.email }}
                </div>
            </form>
        </template>

        <template #footer>
            <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal"
            >
                Close
            </button>
            <button
                type="submit"
                form="personalInfoEditForms"
                class="btn btn-warning"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Saving...' : 'Save changes' }}
            </button>
        </template>
    </Modals>
</template>
