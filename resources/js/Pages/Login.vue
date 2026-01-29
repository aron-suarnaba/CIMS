<script setup>
import LoginFormComponent from '@/Components/Login.vue';
import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';

defineOptions({ layout: WelcomeLayout });
</script>

<template>
    <LoginFormComponent />
</template>
