<script setup>
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { router, useForm } from '@inertiajs/vue3';

const myBreadcrumb = [
    { label: 'Home', url: route('dashboard') },
    { label: 'Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Smartphone Asset', url: route('dashboard') },
    { label: 'Add Smartphone' },
];

const form = useForm({
    brand: '',
    model: '',
    serial_num: '',
    purchase_date: '',
    ram: '',
    rom: '',
    sim_no: '',
    cashout: '0',
    imei_one: '',
    imei_two: '',
});

const submit = () => {
    form.post(route('phone.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

defineOptions({ layout: HomeLayout });

const PhoneBrands = [
    'Apple',
    'Honor',
    'Oppo',
    'Redmi',
    'Realme',
    'Samsung',
    'Techno',
    'Vivo',
    'Xiaomi',
    'Others',
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
            <div class="row mb-3">
                <div class="col-4">
                    <BackButton
                        @click.prevent="router.get(route('phone.index'))"
                    />
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-sm-12 col-md-12 mx-auto py-2">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <form @submit.prevent="submit">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label
                                            for="PhoneBrand"
                                            class="form-label fw-bold"
                                            ><i class="text-danger">*</i>
                                            Brand</label
                                        >
                                        <select
                                            class="form-select"
                                            v-model="form.brand"
                                            id="PhoneBrand"
                                            required
                                        >
                                            <option value="" disabled>
                                                Select Brand
                                            </option>
                                            <option
                                                v-for="brand in PhoneBrands"
                                                :value="brand"
                                                :key="brand"
                                            >
                                                {{ brand }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            for="PhoneModel"
                                            class="form-label fw-bold"
                                            ><i class="text-danger">*</i> Phone
                                            Model</label
                                        >
                                        <input
                                            type="text"
                                            id="PhoneModel"
                                            v-model="form.model"
                                            class="form-control"
                                            placeholder="Enter Phone Model"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label
                                            for="PurchaseDate"
                                            class="form-label fw-bold"
                                            >Purchase Date</label
                                        >
                                        <input
                                            type="date"
                                            id="PurchaseDate"
                                            v-model="form.purchase_date"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            for="PhoneSerialNumber"
                                            class="form-label fw-bold"
                                            ><i class="text-danger">*</i> Serial
                                            Number</label
                                        >
                                        <input
                                            type="text"
                                            id="PhoneSerialNumber"
                                            v-model="form.serial_num"
                                            class="form-control"
                                            placeholder="Enter Serial Number"
                                            required
                                        />
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label
                                            for="PhoneRam"
                                            class="form-label fw-bold"
                                            ><i class="text-danger">*</i>
                                            RAM</label
                                        >
                                        <input
                                            type="text"
                                            id="PhoneRam"
                                            v-model="form.ram"
                                            class="form-control"
                                            placeholder="e.g. 8GB"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            for="PhoneRom"
                                            class="form-label fw-bold"
                                            ><i class="text-danger">*</i>
                                            ROM</label
                                        >
                                        <input
                                            type="text"
                                            id="PhoneRom"
                                            v-model="form.rom"
                                            class="form-control"
                                            placeholder="e.g. 128GB"
                                            required
                                        />
                                    </div>
                                </div>

                                <div
                                    class="row g-3 d-flex align-items-center mb-3"
                                >
                                    <div class="col-md-6">
                                        <label
                                            for="ImeiOne"
                                            class="form-label fw-bold"
                                            ><i class="text-danger">*</i> IMEI
                                            1</label
                                        >
                                        <input
                                            type="text"
                                            id="ImeiOne"
                                            v-model="form.imei_one"
                                            class="form-control"
                                            placeholder="Enter IMEI 1"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            for="ImeiTwo"
                                            class="form-label fw-bold"
                                        >
                                            IMEI 2</label
                                        >
                                        <input
                                            type="text"
                                            id="ImeiTwo"
                                            v-model="form.imei_two"
                                            class="form-control"
                                            placeholder="Enter IMEI 2"
                                        />
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label
                                            for="sim_num"
                                            class="form-label fw-bold"
                                        >
                                            Sim Number
                                        </label>
                                        <input
                                            type="text"
                                            id="sim_num"
                                            v-model="form.sim_no"
                                            class="form-control"
                                            placeholder="Enter Sim Number"
                                        />
                                    </div>
                                </div>

                                <div class="mt-4 text-center">
                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                        :disabled="form.processing"
                                    >
                                        {{
                                            form.processing
                                                ? 'Saving...'
                                                : 'Submit'
                                        }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
