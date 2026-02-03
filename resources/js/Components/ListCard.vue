<script setup></script>
<template>
    <div class="card phone-card clickable-card rounded-3">
        <div class="card-body d-flex flex-column align-items-center text-wrap">
            <slot />
        </div>
    </div>
</template>
<style scoped>
.phone-card {
    width: 150px;
    height: 250px;

    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
    transition: 0.3s;
    border-radius: 5px;
    text-align: center;
}
.phone-card:hover {
    box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
    transform: translate(1, 1);
}

.card-body {
    height: 100%;
    justify-content: space-between;
}
</style>
