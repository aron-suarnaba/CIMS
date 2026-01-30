<script setup>
defineProps({
    id: { type: String, default: '' },
});
const features = [
    {
        title: 'Infrastructure Monitoring',
        desc: 'Provides real-time monitoring of all computers and servers. Tracks CPU, memory, and disk health.',
        iconPath:
            'M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z',
        aosDuration: '1700',
    },
    {
        title: 'Network & Firewall',
        desc: 'Monitors network activity and firewall performance by integrating with FortiGate devices.',
        iconPath:
            'M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z',
        aosDuration: '2000',
    },
    {
        title: 'Asset & Domain',
        desc: 'Manages IT assets and domain-joined devices by synchronizing with Active Directory.',
        iconPath:
            'M2.3 4.6c-1.3 0-2.3 1-2.3 2.3v13.1c0 1.3 1 2.3 2.3 2.3h15.4c1.3 0 2.3-1 2.3-2.3V9.2c0-1.3-1-2.3-2.3-2.3H8.5c0-1.3-1-2.3-2.3-2.3H2.3zm0 3.9h15.4c.4 0 .8.3.8.8v10.8c0 .4-.4.8-.8.8H2.3c-.4 0-.8-.3-.8-.8V9.2c0-.4.4-.8.8-.8zm7.7 1.6c-1.1 0-2.2.8-2.2 2.2 0 .9.6 1.9 1.2 2.5.2.6-.2.8-.3.9-1.2.4-2.5 1.2-2.5 2v.3c0 1 1.9 1.3 3.8 1.3s3.8-.3 3.8-1.3v-.3c0-.8-1.3-1.6-2.5-2-.1 0-.5-.2-.3-.9.6-.6 1.2-1.6 1.2-2.5 0-1.4-1.1-2.2-2.2-2.2z',
        aosDuration: '2300',
    },
    {
        title: 'Alerts & Analytics',
        desc: 'Generates alerts and reports based on system conditions to notify administrators of critical issues.',
        iconPath:
            'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z',
        aosDuration: '2500',
    },
];
</script>

<template>
    <section class="container px-4 py-5" :id="id">
        <h2
            class="border-bottom d-flex align-items-center justify-content-start pb-2"
            data-aos="fade-right"
            data-aos-duration="1500"
            data-aos-ease="ease-in-out"
        >
            <p class="text-primary fw-bold mb-0 me-2">CIMS</p>
            Features
        </h2>

        <div
            class="row row-cols-1 row-cols-md-2 align-items-md-center g-5 py-5"
        >
            <div
                class="col d-flex flex-column align-items-start gap-2"
                data-aos="fade-right"
                data-aos-duration="1500"
                data-aos-ease="ease"
            >
                <h2 class="fw-bold text-body-emphasis display-4">
                    IT Management Reimagined
                </h2>
                <p class="text-body-secondary">
                    A comprehensive suite of tools designed to monitor, secure,
                    and manage your entire digital infrastructure from a single
                    dashboard.
                </p>
                <a href="#" class="btn btn-primary btn-lg">Get Started</a>
            </div>

            <div class="col">
                <div class="row row-cols-1 row-cols-sm-2 g-4">
                    <div
                        v-for="feature in features"
                        :key="feature.title"
                        class="col d-flex flex-column gap-2"
                        data-aos="fade-right"
                        :data-aos-duration="aosDuration"
                        data-aos-ease="ease-in-out"
                    >
                        <div
                            class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-4 rounded-3"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path :d="feature.iconPath" />
                            </svg>
                        </div>
                        <h4 class="fw-semibold text-body-emphasis mb-0">
                            {{ feature.title }}
                        </h4>
                        <p class="text-body-secondary small">
                            {{ feature.desc }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.feature-icon-small {
    width: 48px;
    height: 48px;
}
</style>
