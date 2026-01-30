<script setup>
defineProps({
    id: { type: String, default: '' },
    aosValue: { type: String, default: '' },
});
</script>
<template>
    <section class="text-dark bg-white py-5" :id="id" :data-aos="aosValue">
        <div class="container">
            <div
                class="mb-5 text-center"
                data-aos="fade-left"
                data-aos-duration="1800"
                data-aos-ease="ease-in-sine"
            >
                <h2 class="fw-bold text-body-emphasis">
                    Engineered for Performance
                </h2>
                <p class="text-muted mx-auto" style="max-width: 600px">
                    Built with a focus on speed, security, and reliability to
                    meet the demands of modern IT infrastructure.
                </p>
            </div>

            <div class="row row-cols-1 row-cols-md-3 g-4 text-center">
                <div
                    class="col"
                    data-aos="fade-up"
                    data-aos-duration="1400"
                    data-aos-ease="ease-in-sine"
                >
                    <div
                        class="border-light-subtle rounded-4 bg-light h-100 transition-hover border p-4 shadow-sm"
                    >
                        <div class="display-5 fw-bold text-primary mb-2">
                            99.9%
                        </div>
                        <h5 class="fw-bold text-dark">Uptime Target</h5>
                        <p class="text-secondary small mb-0">
                            High-availability architecture ensures your
                            monitoring stays online even during peak network
                            traffic.
                        </p>
                    </div>
                </div>

                <div
                    class="col"
                    data-aos="fade-up"
                    data-aos-duration="1800"
                    data-aos-ease="ease-in-sine"
                >
                    <div
                        class="border-light-subtle rounded-4 bg-light h-100 transition-hover border p-4 shadow-sm"
                    >
                        <div class="display-5 fw-bold text-primary mb-2">
                            256-bit
                        </div>
                        <h5 class="fw-bold text-dark">AES Encryption</h5>
                        <p class="text-secondary small mb-0">
                            Enterprise-grade encryption protecting all IT asset
                            data, credentials, and network logs at rest.
                        </p>
                    </div>
                </div>

                <div
                    class="col"
                    data-aos="fade-up"
                    data-aos-duration="2200"
                    data-aos-ease="ease"
                >
                    <div
                        class="border-light-subtle rounded-4 bg-light h-100 transition-hover border p-4 shadow-sm"
                    >
                        <div class="display-5 fw-bold text-primary mb-2">
                            &lt; 2s
                        </div>
                        <h5 class="fw-bold text-dark">Sync Latency</h5>
                        <p class="text-secondary small mb-0">
                            Proprietary polling technology provides near-instant
                            synchronization with Active Directory and firewalls.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.bg-light {
    background-color: #f8f9fa !important;
}

.text-primary {
    color: #0d6efd !important; /* Bootstrap Primary Blue */
}

.shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

/* Adds a nice subtle lift when the user interacts with the stats */
.transition-hover {
    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;
}

.transition-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    border-color: #0d6efd !important;
}
</style>
