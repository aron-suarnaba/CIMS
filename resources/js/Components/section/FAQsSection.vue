<script setup></script>
<template>
    <section class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="mb-4">Frequently Asked Questions</h2>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq1"
                                aria-expanded="true"
                                aria-controls="faq1"
                            >
                                How does CIMS ensure the security of my
                                infrastructure data?
                            </button>
                        </h2>
                        <div
                            id="faq1"
                            class="accordion-collapse show collapse"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                CIMS employs a multi-layered security approach.
                                All data transmitted between your local agents
                                and the dashboard is encrypted using TLS 1.3.
                                Sensitive information, such as asset credentials
                                and network logs, is stored at rest using
                                AES-256 encryption. Furthermore, CIMS operates
                                on a "least-privilege" principle, ensuring the
                                application only accesses the specific data
                                points required for monitoring.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq2"
                                aria-expanded="false"
                                aria-controls="faq2"
                            >
                                Does CIMS support real-time integration with
                                FortiGate firewalls?
                            </button>
                        </h2>
                        <div
                            id="faq2"
                            class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                Yes. CIMS integrates directly with FortiOS APIs
                                to pull real-time telemetry. This allows you to
                                monitor firewall health, active sessions, and
                                security policy status directly from your CIMS
                                dashboard. You can also configure automated
                                alerts to trigger if the firewall detects
                                high-risk traffic patterns or hardware
                                anomalies.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq3"
                                aria-expanded="false"
                                aria-controls="faq3"
                            >
                                How does the Active Directory (AD)
                                synchronization work?
                            </button>
                        </h2>
                        <div
                            id="faq3"
                            class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                CIMS uses a lightweight synchronization agent
                                that communicates with your Domain Controller.
                                It maps your AD structure including
                                Organizational Units (OUs), users, and
                                domain-joined devices into the CIMS asset tree.
                                This ensures that when a new device joins your
                                domain, it is automatically detected and
                                categorized within the CIMS environment without
                                manual entry.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button
                                class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faq4"
                                aria-expanded="false"
                                aria-controls="faq4"
                            >
                                What are the system requirements for the CIMS
                                monitoring agent?
                            </button>
                        </h2>
                        <div
                            id="faq4"
                            class="accordion-collapse collapse"
                            data-bs-parent="#faqAccordion"
                        >
                            <div class="accordion-body">
                                The CIMS agent is designed to be extremely
                                lightweight to avoid impacting production
                                performance. It requires a minimum of 512MB RAM
                                and less than 1% CPU utilization under normal
                                operating conditions. It is compatible with
                                Windows Server 2016+, Ubuntu 20.04+, and most
                                modern RHEL-based distributions.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<style scoped>
body {
    background-color: #ffffff;
    color: #333;
    font-family: Arial, sans-serif;
    padding: 50px 0;
}

.accordion-item {
    border: none;
    border-bottom: 1px solid #e0e0e0;
}

.accordion-button {
    padding: 20px 0;
    font-weight: 600;
    color: #333;
    background-color: transparent;
}

.accordion-button:not(.collapsed) {
    color: #333;
    background-color: transparent;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23333'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-body {
    padding: 0 0 20px 0;
}
</style>
