<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import { defineOptions } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import BackButton from '@/Components/BackButton.vue';

const form = useForm({
    brand: '',
    model: '',
    serial_number: '',
    purchase_date: '',
    ram: '',
    rom: '',
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

const PhoneBrands = ['Apple', 'Oppo', 'Redmi', 'Samsung', 'Vivo'];
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
                                :href="route('dashboard')"
                                @click.prevent="router.get(route('dashboard'))"
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
                                >Asset & Inventory</a
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
                        <li class="breadcrumb-item active">Add Smartphone</li>
                    </ol>
                </div>
            </div>
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
                <div class="col-sm-12 col-md-9 mx-auto">
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
                                            v-model="form.serial_number"
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

                                <div class="row g-3 mb-3">
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
                                            >IMEI 2</label
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

                                <div class="mt-4 text-center">
                                    <button
                                        type="submit"
                                        class="btn btn-success"
                                        :disabled="form.processing"
                                    >
                                        <i class="bi bi-plus"></i>
                                        {{
                                            form.processing
                                                ? 'Saving...'
                                                : 'Add Phone'
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
