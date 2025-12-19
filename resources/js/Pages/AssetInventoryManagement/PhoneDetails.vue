<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { defineOptions, defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';

defineOptions({ layout: HomeLayout });

const props = defineProps({
    phone: {
        type: Object,
        required: true,
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
</script>

<template>
    <div class="app-content-header">
        <div class="container">
            <div class="row my-4">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">
                        {{ props.phone.model }}
                    </h1>
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
                        <li class="breadcrumb-item">
                            <a
                                :href="PhoneIndex"
                                @click.prevent="gotoPhoneIndex"
                                class="text-underline"
                                >Smartphone</a
                            >
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ props.phone.model }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body pt-0">
                    <div class="row my-3">
                        <div class="col-sm-12 col-md-8">
                            <BackButton
                                @click.prevent="
                                    router.get(route('phone.index'))
                                "
                            />
                        </div>
                        <div
                            class="col-sm-12 col-md-4 d-flex justify-content-end gap-2"
                        >
                            <div
                                class="button-group d-flex justify-content-end gap-2"
                            >
                                <button
                                    type="button"
                                    class="btn btn-info bg-gradient"
                                    v-if="props.phone.status === 'available'"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                    Issue
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-warning bg-gradient d-flex justify-content-center align-items-center"
                                    v-else-if="props.phone.status === 'issued'"
                                >
                                    <i class="bi bi-arrow-return-left me-1"></i>
                                    Return
                                </button>
                                <button class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Delete Phone
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-auto mt-2">
                        <div class="col-sm-12 col-md-5">
                            <h2 class="card-title fw-bold fs-4 mb-2">
                                Smartphone Details
                            </h2>
                            <img
                                :src="getPhoneImagePath(props.phone)"
                                class="img-fluid my-3 rounded"
                                style="max-height: 250px"
                                :alt="props.phone.model"
                            />
                            <table
                                class="table-striped table-bordered table-hover bg-dark table-responsive table"
                            >
                                <tbody>
                                    <tr>
                                        <th scope="row">Model</th>
                                        <td>{{ props.phone.model }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" style="width: 30%">
                                            Serial Number
                                        </th>
                                        <td>{{ props.phone.serial_num }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Purchase Date</th>
                                        <td>
                                            {{
                                                formatDate(
                                                    props.phone.created_at,
                                                ) || 'N/A'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Status</th>
                                        <td>
                                            {{ props.phone.status || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">RAM</th>
                                        <td>
                                            {{ props.phone.ram || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">ROM</th>
                                        <td>
                                            {{ props.phone.rom || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">IMEI 1</th>
                                        <td>
                                            {{ props.phone.imei_one || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">IMEI 2</th>
                                        <td>
                                            {{ props.phone.imei_two || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Accessories</th>
                                        <td>
                                            {{
                                                props.phone
                                                    .issued_accessories || 'N/A'
                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-sm-12 col-md-5">
                            <h2 class="card-title fw-bold fs-4 mb-2">
                                Issuance
                            </h2>
                            <table
                                class="table-striped table-bordered table-hover table-responsive mb-3 table"
                            >
                                <tbody>
                                    <tr>
                                        <th scope="row">Issued By</th>
                                        <td>
                                            {{ props.phone.issued_by || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued To</th>
                                        <td>
                                            {{ props.phone.issued_to || 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued Department</th>
                                        <td>
                                            {{
                                                props.phone
                                                    .issued_to_department ||
                                                'N/A'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued Date</th>
                                        <td>
                                            {{
                                                formatDate(
                                                    props.phone.date_issued,
                                                ) || 'N/A'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Acknowledgement</th>
                                        <td
                                            class="d-flex justify-content-around gap-1"
                                        >
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    value=""
                                                    id="ITAcknowledgement"
                                                    :checked="
                                                        !!props.phone
                                                            .issuedAcknowledgementIT
                                                    "
                                                    disabled
                                                />
                                                <label
                                                    for="ITAcknowledgement"
                                                    class="form-check-label"
                                                    >IT</label
                                                >
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    value=""
                                                    id="PurchasingAcknowledgement"
                                                    :checked="
                                                        !!props.phone
                                                            .issuedAcknowledgementPurchasing
                                                    "
                                                    disabled
                                                />
                                                <label
                                                    for="PurchasingAcknowledgement"
                                                    class="form-check-label"
                                                    >Purchasing</label
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <h2 class="card-title fw-bold fs-4 mb-2">Return</h2>
                            <table
                                class="table-striped table-bordered table-hover table-responsive mb-3 table"
                            >
                                <tbody>
                                    <tr>
                                        <th scope="row">Returned Date</th>
                                        <td>
                                            {{
                                                formatDate(
                                                    props.phone.returned_date,
                                                ) || 'Not yet returned'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Returned By</th>
                                        <td>
                                            {{
                                                props.phone.returned_by ||
                                                'Not yet returned'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Returned To</th>
                                        <td>
                                            {{
                                                props.phone.returned_to ||
                                                'Not yet returned'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            Returned Accessories
                                        </th>
                                        <td>
                                            {{
                                                props.phone
                                                    .returned_accessories ||
                                                'Not yet returned'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Acknowledgement</th>
                                        <td
                                            class="d-flex justify-content-around gap-1"
                                        >
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    value=""
                                                    id="ITAcknowledgement"
                                                    :checked="
                                                        !!props.phone
                                                            .returnedAcknowledgementIT
                                                    "
                                                    disabled
                                                />
                                                <label
                                                    for="ITAcknowledgement"
                                                    class="form-check-label"
                                                    >IT</label
                                                >
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    value=""
                                                    id="PurchasingAcknowledgement"
                                                    :checked="
                                                        !!props.phone
                                                            .returnedAcknowledgementPurchasing
                                                    "
                                                    disabled
                                                />
                                                <label
                                                    for="PurchasingAcknowledgement"
                                                    class="form-check-label"
                                                    >Purchasing</label
                                                >
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
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
