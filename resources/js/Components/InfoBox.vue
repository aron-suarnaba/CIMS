<script setup></script>
<template>
    <span class="info-box bg-gradient clickable-card">
        <span class="info-box-icon">
            <slot name="header" />
        </span>
        <div class="info-box-content">
            <span
                class="info-box-text d-flex justify-content-center align-items-center flex-wrap text-wrap"
            >
                <slot name="content" />
            </span>
        </div>
    </span>
</template>
