<template>
    <Card v-bind="$attrs" class="asset-card">
        <template #header>
            <div class="card-header bg-dark py-3 text-white">
                <h5 class="fw-bold mb-0 text-center">
                    <slot name="header-title">Device Specifications</slot>
                </h5>
            </div>
        </template>
        <template #content></template>
        <template #footer></template>
    </Card>
</template>

<script setup></script>

<style scoped>
.asset-card {
    border: 1px solid #dee2e6;
}
.font-monospace {
    font-family: monospace;
}
</style>
