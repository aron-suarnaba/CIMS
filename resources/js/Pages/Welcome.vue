<script setup>
import FAQsSection from '@/Components/section/FAQsSection.vue';
import FeaturesSection from '@/Components/section/FeaturesSection.vue';
import FinalCTASection from '@/Components/section/FinalCTASection.vue';
import HowItWorksSection from '@/Components/section/HowItWorksSection.vue';
import MainSection from '@/Components/section/MainSection.vue';
import SocialProofSection from '@/Components/section/SocialProofSection.vue';

import WelcomeLayout from '@/Layouts/WelcomeLayout.vue';
defineOptions({ layout: WelcomeLayout });
</script>

<template>
    <div class="short-divider"></div>
    <MainSection />
    <div class="long-divider"></div>
    <FeaturesSection />
    <div class="long-divider"></div>
    <SocialProofSection />
    <div class="long-divider"></div>
    <HowItWorksSection />
    <div class="long-divider"></div>
    <FAQsSection />
    <div class="long-divider"></div>
    <FinalCTASection />
    <div class="long-divider"></div>
</template>
<style scoped>
.long-divider {
    margin: 5rem 0;
}
.short-divider {
    margin: 1.5rem 0;
}
</style>
