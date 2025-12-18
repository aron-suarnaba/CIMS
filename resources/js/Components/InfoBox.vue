<script setup>
import { defineProps } from 'vue';
const props = defineProps({
    boxClass: {
        type: String,
        default: 'text-bg-primary',
    },
});
</script>
<template>
    <span class="info-box bg-gradient clickable-card" :class="props.boxClass">
        <span class="info-box-icon">
            <slot name="header" />
        </span>
        <div class="info-box-content">
            <span class="info-box-text">
                <slot name="content" />
            </span>
        </div>
    </span>
</template>
