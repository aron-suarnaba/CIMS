<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { router, useForm } from '@inertiajs/vue3';

defineOptions({ layout: HomeLayout });

const form = useForm({
    manufacturer_model: '',
    cpu: '',
    ram: '',
    rom: '',
    mac: '',
    ip: '',
    name: '',
    purchase_date: '',
    warranty_expiry: '',
    remarks: '',
});

const submit = () => {
    form.post(route('minipc.store'), {
        onSuccess: () => form.reset(),
    });
};

const myBreadcrumb = [
    { label: 'Dashboard', url: route('dashboard') },
    { label: 'Asset & Inventory', url: route('AssetAndInventoryManagement') },
    { label: 'Mini PC Units', url: route('minipc.index') },
    { label: 'Add Mini PC' },
];
</script>

<template>
    <div class="app-content-header">
        <div class="container">
            <Breadcrumb :breadcrumbs="myBreadcrumb" />
        </div>
    </div>
    <div class="app-content mt-4">
        <div class="container">
            <BackButton @click.prevent="router.get(route('minipc.index'))" />
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <form @submit.prevent="submit">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Manufacturer / Model</label>
                                <input type="text" v-model="form.manufacturer_model" class="form-control" required />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CPU</label>
                                <input type="text" v-model="form.cpu" class="form-control" />
                            </div>
                        </div>
                        <!-- similar fields as in modal -->
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">RAM</label>
                                <input type="text" v-model="form.ram" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ROM</label>
                                <input type="text" v-model="form.rom" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">MAC</label>
                                <input type="text" v-model="form.mac" class="form-control" />
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">IP</label>
                                <input type="text" v-model="form.ip" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Name</label>
                                <input type="text" v-model="form.name" class="form-control" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Purchase Date</label>
                                <input type="date" v-model="form.purchase_date" class="form-control" />
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Warranty Expiry</label>
                                <input type="date" v-model="form.warranty_expiry" class="form-control" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Remarks</label>
                                <textarea v-model="form.remarks" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
