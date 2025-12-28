<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { defineOptions, defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';
import Modals from '@/Components/Modals.vue';
import { useForm } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    phone: {
        type: Object,
        required: true,
    },
    phone_transaction: {
        type: Object,
        required: false,
    },
});

const AssetInventoryManagementIndex = route('AssetAndInventoryManagement');
const Home = route('dashboard');
const PhoneIndex = route('phone.index');

const gotoAssetInventoryManagementIndex = () => {
    router.get(AssetInventoryManagementIndex);
};
const gotoHome = () => {
    router.get(Home);
};
const gotoPhoneIndex = () => {
    router.get(PhoneIndex);
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

const formatDate = (dateString, locale = 'en-US') => {
    if (!dateString) return '';
    const date = new Date(dateString);

    return new Intl.DateTimeFormat(locale, {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(date);
};

const form = useForm({
    issued_by: '',
    issued_to: '',
    department: '',
    date_issued: new Date().toISOString().substr(0, 10),
    issued_accessories: [],
    it_ack_issued: false,
    purch_ack_issued: false,
});

const submit = () => {
    form.post(route('phone.issue', props.phone.id), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <div class="app-content-header border-bottom bg-white py-3">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a :href="Home" @click.prevent="gotoHome">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a
                                :href="PhoneIndex"
                                @click.prevent="gotoPhoneIndex"
                                >Inventory</a
                            >
                        </li>
                        <li class="breadcrumb-item active">
                            {{ props.phone.model }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex gap-2">
                    <BackButton
                        @click.prevent="router.get(route('phone.index'))"
                    />
                </div>
            </div>
        </div>
    </div>

    <div class="app-content mt-4">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card bg-light mb-4 border-0 shadow-sm">
                        <div
                            class="card-body d-flex justify-content-between align-items-center"
                        >
                            <h5 class="fw-bold text-secondary mb-0">
                                Asset Management
                            </h5>
                            <div class="btn-group shadow-sm">
                                <button
                                    v-if="props.phone.status === 'available'"
                                    class="btn btn-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#IssuePhoneModal"
                                >
                                    <i class="bi bi-person-plus me-1"></i> Issue
                                    Asset
                                </button>
                                <button
                                    v-else-if="props.phone.status === 'issued'"
                                    class="btn btn-warning"
                                >
                                    <i class="bi bi-arrow-return-left me-1"></i>
                                    Process Return
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-sm-12 col-xl-4 col-lg-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-dark py-3 text-white">
                            <h5 class="fw-bold mb-0">Device Specifications</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4 text-center">
                                <img
                                    :src="getPhoneImagePath(props.phone)"
                                    class="img-fluid bg-light rounded p-3"
                                    style="max-height: 220px"
                                    :alt="props.phone.model"
                                />
                                <h3 class="fw-bold mb-0 mt-3">
                                    {{ props.phone.brand }}
                                    {{ props.phone.model }}
                                </h3>
                                <span
                                    :class="[
                                        'badge mt-2 px-3 py-2',
                                        props.phone.status === 'available'
                                            ? 'bg-success'
                                            : 'bg-primary',
                                    ]"
                                >
                                    Status: {{ props.phone.status }}
                                </span>
                            </div>

                            <ul class="list-group list-group-flush border-top">
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted"
                                        >Serial Number</span
                                    >
                                    <span class="fw-bold">{{
                                        props.phone.serial_num
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">IMEI 1</span>
                                    <span class="font-monospace small">{{
                                        props.phone.imei_one || 'N/A'
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">IMEI 2</span>
                                    <span class="font-monospace small">{{
                                        props.phone.imei_two || 'N/A'
                                    }}</span>
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted">RAM / ROM</span>
                                    <span
                                        >{{ props.phone.ram || 'N/A' }} /
                                        {{ props.phone.rom || 'N/A' }}</span
                                    >
                                </li>
                                <li
                                    class="list-group-item d-flex justify-content-between"
                                >
                                    <span class="text-muted"
                                        >Purchase Date</span
                                    >
                                    <span>{{
                                        formatDate(props.phone.created_at) ||
                                        'N/A'
                                    }}</span>
                                </li>
                            </ul>
                        </div>
                        <div
                            class="card-footer border-0 bg-transparent pb-3 text-center"
                        >
                            <button class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i> Delete Asset
                                Record
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-xl-8 col-lg-7">
                    <div class="card mb-4 border-0 shadow-sm">
                        <div
                            class="card-header bg-primary d-flex justify-content-between align-items-center text-white"
                        >
                            <h5 class="fw-bold mb-0">
                                Current / Last Issuance
                            </h5>
                            <i class="bi bi-send-check fs-4"></i>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 border-end">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Recipient Info</label
                                    >
                                    <p class="fw-bold fs-5 text-dark mb-1">
                                        {{
                                            props.phone_transaction
                                                ?.issued_to || 'Not yet issued'
                                        }}
                                    </p>
                                    <p class="text-secondary small mb-0">
                                        <i class="bi bi-building me-1"></i>
                                        {{
                                            props.phone_transaction
                                                ?.department || 'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-md-6 px-md-4">
                                    <label
                                        class="small text-muted text-uppercase fw-bold"
                                        >Issuance Logistics</label
                                    >
                                    <p class="mb-1">
                                        <strong>Date:</strong>
                                        {{
                                            formatDate(
                                                props.phone_transaction
                                                    ?.date_issued,
                                            ) || 'Not yet issued'
                                        }}
                                    </p>
                                    <p class="mb-0">
                                        <strong>By:</strong>
                                        {{
                                            props.phone_transaction
                                                ?.issued_by || 'Not yet issued'
                                        }}
                                    </p>
                                </div>
                                <div class="col-12 mt-4">
                                    <div
                                        class="bg-light d-flex align-items-center flex-wrap gap-4 rounded p-3"
                                    >
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bi bi-headphones text-primary me-2"
                                            ></i>
                                            <strong>Accessories:</strong>
                                            <span class="text-muted ms-2">{{
                                                props.phone_transaction
                                                    ?.issued_accessories ||
                                                'None'
                                            }}</span>
                                        </div>
                                        <div
                                            class="vr d-none d-md-block mx-2"
                                        ></div>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="bi bi-check-circle-fill text-success me-2"
                                            ></i>
                                            <strong>Ack:</strong>
                                            <span
                                                class="badge text-dark ms-2 border bg-white"
                                                >IT:
                                                {{
                                                    props.phone
                                                        .issuedAcknowledgementIT
                                                        ? 'Yes'
                                                        : 'No'
                                                }}</span
                                            >
                                            <span
                                                class="badge text-dark ms-2 border bg-white"
                                                >Purchasing:
                                                {{
                                                    props.phone
                                                        .issuedAcknowledgementPurchasing
                                                        ? 'Yes'
                                                        : 'No'
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="card border-start border-0 border-4 shadow-sm"
                        :class="
                            props.phone.returned_date
                                ? 'border-warning'
                                : 'border-secondary'
                        "
                    >
                        <div class="card-header bg-white">
                            <h5
                                class="fw-bold mb-0"
                                :class="
                                    props.phone.returned_date
                                        ? 'text-warning'
                                        : 'text-muted'
                                "
                            >
                                Return Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div
                                v-if="props.phone.returned_date"
                                class="row g-3"
                            >
                                <div class="col-md-4">
                                    <label class="small text-muted d-block"
                                        >Returned Date</label
                                    >
                                    <span class="fw-bold">{{
                                        formatDate(props.phone.returned_date)
                                    }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted d-block"
                                        >Returned By</label
                                    >
                                    <span class="fw-bold">{{
                                        props.phone.returned_by
                                    }}</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted d-block"
                                        >Returned To</label
                                    >
                                    <span class="fw-bold">{{
                                        props.phone.returned_to
                                    }}</span>
                                </div>
                                <div class="col-12">
                                    <div
                                        class="bg-light small rounded border p-2"
                                    >
                                        <strong
                                            >Return Note / Accessories:</strong
                                        >
                                        {{
                                            props.phone.returned_accessories ||
                                            'None specified'
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-muted py-3 text-center">
                                <i class="bi bi-info-circle me-1"></i> No return
                                history for the current transaction.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
tr td {
    align-items: center;
}
</style>
