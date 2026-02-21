<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const title = ref('CIMS');
const logo = '/CIMS/public/img/logo.png';

const mobileBreakpoint = 992;
const tabletBreakpoint = 1200;

const handleCategoryClick = () => {
    const body = document.body;
    const width = window.innerWidth;

    if (width < mobileBreakpoint) {
        body.classList.remove('sidebar-open');
        return;
    }

    if (width < tabletBreakpoint) {
        body.classList.add('sidebar-collapse');
        body.classList.remove('sidebar-open');
    }
};
</script>

<template>
    <aside class="app-sidebar bg-secondary-subtle shadow" data-bs-theme="light">
        <div class="sidebar-brand">
            <a href="#" class="brand-link">
                <img
                    :src="logo"
                    alt="AdminLTE Logo"
                    class="brand-image rounded-circle opacity-75 shadow"
                />
                <span class="brand-text fw-bold text-primary">{{ title }}</span>
            </a>
        </div>
        <div class="sidebar-wrapper" data-overlayscrollbars="host">
            <div class="os-size-observer">
                <div class="os-size-observer-listener"></div>
            </div>
            <div
                class=""
                data-overlayscrollbars-viewport="scrollbarHidden overflowXHidden overflowYScroll"
                tabindex="-1"
                style="
                    margin-right: -16px;
                    margin-bottom: -16px;
                    margin-left: 0px;
                    top: -8px;
                    right: auto;
                    left: -8px;
                    width: calc(100% + 16px);
                    padding: 8px;
                "
            >
                <nav class="mt-2">
                    <ul
                        class="nav sidebar-menu flex-column"
                        role="navigation"
                        aria-label="Main navigation"
                        data-accordion="false"
                        id="navigation"
                    >
                        <li class="nav-item">
                            <Link
                                :href="route('dashboard')"
                                class="nav-link"
                                @click="handleCategoryClick"
                            >
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link
                                :href="route('AssetAndInventoryManagement')"
                                class="nav-link"
                                @click="handleCategoryClick"
                            >
                                <i class="bi bi-pc-display"></i>
                                <p>Assets & Inventory Management</p>
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link
                                :href="route('network.index')"
                                class="nav-link"
                                @click="handleCategoryClick"
                            >
                                <i class="bi bi-router-fill"></i>
                                <p>Network Monitoring & Management</p>
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link
                                :href="route('automation.index')"
                                class="nav-link"
                                @click="handleCategoryClick"
                            >
                                <i class="bi bi-gear-wide-connected"></i>
                                <p>Automation</p>
                            </Link>
                        </li>
                        <!--
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-database-fill"></i>
                                <p>Server & System Monitoring</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-wrench-adjustable"></i>
                                <p>Configuration & Patch Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-shield-shaded"></i>
                                <p>Security Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-file-bar-graph-fill"></i>
                                <p>Backup & Recovery Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-cloud-fill"></i>
                                <p>Cloud & Virtualization Management</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-journal-text"></i>
                                <p>Logging, Auditing, & Reporting</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-person-circle"></i>
                                <p>User & Access Management</p>
                            </a>
                        </li> -->
                    </ul>
                </nav>
            </div>
            <div
                class="os-scrollbar os-scrollbar-horizontal os-theme-light os-scrollbar-auto-hide os-scrollbar-handle-interactive os-scrollbar-track-interactive os-scrollbar-cornerless os-scrollbar-unusable os-scrollbar-auto-hide-hidden"
                style="--os-viewport-percent: 1; --os-scroll-direction: 0"
            >
                <div class="os-scrollbar-track">
                    <div class="os-scrollbar-handle"></div>
                </div>
            </div>
            <div
                class="os-scrollbar os-scrollbar-vertical os-theme-light os-scrollbar-auto-hide os-scrollbar-handle-interactive os-scrollbar-track-interactive os-scrollbar-visible os-scrollbar-cornerless os-scrollbar-auto-hide-hidden"
                style="--os-viewport-percent: 0.7723; --os-scroll-direction: 0"
            >
                <div class="os-scrollbar-track">
                    <div class="os-scrollbar-handle"></div>
                </div>
            </div>
        </div>
        <!--end::Sidebar Wrapper-->
    </aside>
</template>
