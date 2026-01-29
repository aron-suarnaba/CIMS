<script setup>
import MainSection from '@/Components/section/MainSection.vue';
import FeaturesSection from '@/Components/section/FeaturesSection.vue';

import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';
defineOptions({ layout: WelcomeLayout });
</script>

<template>
    <MainSection />
    <div class="b-example-divider"></div>
    <FeaturesSection />
</template>
