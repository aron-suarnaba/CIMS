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
    <MainSection id="mainSection" />
    <div class="long-divider"></div>
    <FeaturesSection id="featureSection" />
    <div class="long-divider"></div>
    <SocialProofSection id="proofSection" />
    <div class="long-divider"></div>
    <HowItWorksSection id="howItWorksSection" />
    <div class="long-divider"></div>
    <FAQsSection id="faqsSection" />
    <div class="long-divider"></div>
    <FinalCTASection id="ctaSection" />
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
