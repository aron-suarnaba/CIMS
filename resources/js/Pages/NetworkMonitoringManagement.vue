<script setup>
import HomeLayout from '@/Layouts/HomeLayout.vue';
import BackButton from '@/Components/BackButton.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';


defineOptions({ layout: HomeLayout });


const myBreadcrumb = [
    { label : 'Dashboard', url : route('dashboard') },
    { label : 'Network Monitoring and Management' },
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

                </div>
            </div>
            <div class="row mb-3 mt-5">

            </div>
        </div>
    </div>
</template>
