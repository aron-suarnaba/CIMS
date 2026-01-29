<script setup>
// Logic goes here
</script>

<template>
    <div class="container px-4 py-5">
        <h2 class="border-bottom pb-2">Features with title</h2>

        <div
            class="row row-cols-1 row-cols-md-2 align-items-md-center g-5 py-5"
        >
            <div class="col d-flex flex-column align-items-start gap-2">
                <h2 class="fw-bold text-body-emphasis">
                    Left-aligned title explaining these awesome features
                </h2>
                <p class="text-body-secondary">
                    Paragraph of text beneath the heading to explain the
                    heading. We'll add onto it with another sentence and
                    probably just keep going until we run out of words.
                </p>
                <a href="#" class="btn btn-primary btn-lg">Primary button</a>
            </div>

            <div class="col">
                <div class="row row-cols-1 row-cols-sm-2 g-4">
                    <div class="col d-flex flex-column gap-2">
                        <div
                            class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-4 rounded-3 p-2"
                            style="width: 40px; height: 40px"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"
                                />
                            </svg>
                        </div>
                        <h4 class="fw-semibold text-body-emphasis mb-0">
                            Infrastructure Monitoring
                        </h4>
                        <p class="text-body-secondary small">
                            Real-time monitoring of computers and servers,
                            tracking CPU, memory, and disk health.
                        </p>
                    </div>

                    <div class="col d-flex flex-column gap-2">
                        <div
                            class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-4 rounded-3 p-2"
                            style="width: 40px; height: 40px"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"
                                />
                            </svg>
                        </div>
                        <h4 class="fw-semibold text-body-emphasis mb-0">
                            Network Management
                        </h4>
                        <p class="text-body-secondary small">
                            Monitors FortiGate firewalls, bandwidth usage, and
                            active sessions for stability.
                        </p>
                    </div>

                    <div class="col d-flex flex-column gap-2">
                        <div
                            class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-4 rounded-3 p-2"
                            style="width: 40px; height: 40px"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M15 11V5l-3-3-3 3v6H3v11h18V11h-6zm-4-4h2v4h-2V7zm-6 13h2v-2H5v2zm0-4h2v-2H5v2zm0-4h2V9H5v2zm6 8h2v-2h-2v2zm0-4h2v-2h-2v2zm0-4h2v-2h-2v2zm6 8h2v-2h-2v2zm0-4h2v-2h-2v2zm0-4h2V9h-2v2z"
                                />
                            </svg>
                        </div>
                        <h4 class="fw-semibold text-body-emphasis mb-0">
                            Asset & Domain
                        </h4>
                        <p class="text-body-secondary small">
                            Synchronizes with Active Directory to maintain
                            updated inventory of users and devices.
                        </p>
                    </div>

                    <div class="col d-flex flex-column gap-2">
                        <div
                            class="feature-icon-small d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-4 rounded-3 p-2"
                            style="width: 40px; height: 40px"
                        >
                            <svg
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"
                                />
                            </svg>
                        </div>
                        <h4 class="fw-semibold text-body-emphasis mb-0">
                            Alerts & Analytics
                        </h4>
                        <p class="text-body-secondary small">
                            Generates logs and critical notifications for
                            resource overloads or downtime.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Scoped styles belong here, not in the script tag */
.feature-icon-small {
    width: 3rem;
    height: 3rem;
}
</style>
