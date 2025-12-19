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
                                    data-bs-toggle="modal"
                                    data-bs-target="#IssuePhoneModal"
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
                                            {{
                                                props.phone_transaction
                                                    .issued_by ||
                                                'Not yet issued'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued To</th>
                                        <td>
                                            {{
                                                props.phone_transaction
                                                    .issued_to ||
                                                'Not yet issued'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued Department</th>
                                        <td>
                                            {{
                                                props.phone_transaction
                                                    .department ||
                                                'Not yet issued'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued Date</th>
                                        <td>
                                            {{
                                                formatDate(
                                                    props.phone_transaction
                                                        .date_issued,
                                                ) || 'Not yet issued'
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Issued Accessories</th>
                                        <td>
                                            {{
                                                formatDate(
                                                    props.phone_transaction
                                                        .issued_accessories,
                                                ) || 'Not yet issued'
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
    <Modals id="IssuePhoneModal" title="Issue Phone">
        <template #body>
            <form @submit.prevent="submit">
                <div class="row border-bottom border-1 mb-3 pb-4">
                    <div class="col-sm-12 col-md-6">
                        <label for="IssuedBy" class="form-label"
                            >Issued By</label
                        >
                        <input
                            type="text"
                            id="IssuedBy"
                            class="form-control"
                            v-model="form.issued_by"
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="IssuedTo" class="form-label"
                            >Issued Date</label
                        >
                        <input
                            type="date"
                            id="IssuedTo"
                            class="form-control"
                            v-model="form.date_issued"
                        />
                    </div>
                </div>
                <div class="row border-bottom border-1 mb-3 pb-4">
                    <div class="col-sm-12 col-md-6">
                        <label for="IssuedTo" class="form-label"
                            >Issued To</label
                        >
                        <input
                            type="text"
                            id="IssuedTo"
                            class="form-control"
                            v-model="form.issued_to"
                        />
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="Department" class="form-label"
                            >Issued To</label
                        >
                        <input
                            type="text"
                            id="Department"
                            class="form-control"
                            v-model="form.department"
                        />
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6">
                        <label for="IssuedAccessories" class="form-label"
                            >Issued Accessories</label
                        >
                        <div class="form-check" id="IssuedAccessories">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="earphones"
                                id="earphonesOptions"
                            />
                            <label class="form-check-label" for="earphones">
                                Earphones
                            </label>
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="earphones"
                                id="chargerOptions"
                            />
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="charger"
                                id="checkChecked"
                                checked
                            />
                            <label class="form-check-label" for="checkChecked">
                                Charger
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <label for="IssueAcknowledgement" class="form-label"
                            >Acknowledgement</label
                        >
                        <div class="form-check" id="IssuedAccessories">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="true"
                                id="ITAcknowledgementRadio"
                            />
                            <label
                                class="form-check-label"
                                for="ITAcknowledgementRadio"
                            >
                                IT
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                value="true"
                                id="PurchAcknowledgementRadio"
                            />
                            <label
                                class="form-check-label"
                                for="PurchAcknowledgementRadio"
                            >
                                Purchasing
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <button type="submit" class="btn btn-success">Submit</button>
        </template>
    </Modals>
</template>
<style scoped>
tr td {
    align-items: center;
}
</style>
