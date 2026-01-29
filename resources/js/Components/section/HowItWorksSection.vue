<script setup>
const stepCard = [
    {
        stepNumber: 1,
        description: 'Discover our products',
        icon: 'bi bi-search',
    },
    {
        stepNumber: 2,
        description: 'Discover our products',
        icon: 'bi bi-cart-plus',
    },
    {
        stepNumber: 3,
        description: 'Discover our products',
        icon: 'bi bi-credit-card',
    },
    {
        stepNumber: 4,
        description: 'Discover our products',
        icon: 'bi bi-truck',
    },
];
</script>
<template>
    <section class="steps-section bg-light mx-0 my-4 rounded-md p-5">
        <div class="container-fluid">
            <h2 class="fw-bold mb-5 text-center">How It Works</h2>
            <div class="row">
                <div
                    class="col-md-3 mb-md-0 mb-4"
                    v-for="step in stepCard"
                    :key="step.stepNumber"
                >
                    <div class="step-item text-center">
                        <div class="step-icon mb-3">
                            <span
                                class="bg-primary rounded-circle d-inline-block p-3 text-white"
                            >
                                <i class="fs-2" :class="step.icon"></i>
                            </span>
                        </div>
                        <h3 class="h5">Step {{ step.stepNumber }}</h3>
                        <p class="text-muted">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<style scoped>
.step-item {
    position: relative;
    padding: 20px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    min-height: 14rem;
}

.step-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.step-icon span {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.step-item::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -15px;
    width: 30px;
    height: 2px;
    background-color: #dee2e6;
    transform: translateY(-50%);
}

.step-item:last-child::after {
    display: none;
}

@media (max-width: 767.98px) {
    .step-item::after {
        display: none;
    }
}
</style>
