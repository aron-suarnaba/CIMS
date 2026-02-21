import '@/bootstrap';
import '@css/app.css';
import { library } from '@fortawesome/fontawesome-svg-core';
import { faHouse, faUser } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { createInertiaApp, router, usePage } from '@inertiajs/vue3';
import 'admin-lte/dist/js/adminlte.min.js';
import AOS from 'aos';
import 'aos/dist/aos.css';
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Swal from 'sweetalert2';
import { createApp, h, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { ZiggyVue } from 'ziggy-js';
window.bootstrap = bootstrap;

const appName = import.meta.env.VITE_APP_NAME || 'CIMS';
const resolveRoute = (name, fallback) => {
    try {
        return typeof route === 'function' ? route(name) : fallback;
    } catch {
        return fallback;
    }
};
const loginUrl = resolveRoute('login', '/login');
const refreshSessionUrl = resolveRoute('session.refresh', '/refresh-session');
const parseMs = (value, fallback) => {
    const parsed = Number.parseInt(value ?? `${fallback}`, 10);
    return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback;
};
const staleTabThresholdMs = parseMs(
    import.meta.env.VITE_STALE_TAB_THRESHOLD_MS,
    1000 * 60 * 30,
);
const heartbeatIntervalMs = parseMs(
    import.meta.env.VITE_SESSION_HEARTBEAT_MS,
    1000 * 60,
);
const minHeartbeatGapMs = parseMs(
    import.meta.env.VITE_SESSION_HEARTBEAT_MIN_GAP_MS,
    1000 * 45,
);
const hiddenAtStorageKey = 'cims_tab_hidden_at';
const sessionRefreshPath = '/refresh-session';
const activityEvents = ['input', 'keydown', 'click', 'scroll', 'touchstart'];
const modalDraftsStorageKey = 'cims_modal_drafts_v1';
const activeModalStorageKey = 'cims_active_modal_v1';

library.add(faUser, faHouse);

AOS.init({
    duration: 1700,
});

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        const requestUrl = error?.config?.url ?? '';
        const isSessionRefreshRequest =
            requestUrl.includes(refreshSessionUrl) ||
            requestUrl.includes(sessionRefreshPath);

        if (error.response && error.response.status === 419) {
            if (isSessionRefreshRequest) {
                return Promise.reject(error);
            }
            // Session is stale: do a quick reload without popup noise.
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            return Promise.reject(error);
        }
        if (error.response && error.response.status === 401) {
            window.location.href = loginUrl;
        }
        return Promise.reject(error);
    },
);

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(VueApexCharts)
            .component('font-awesome-icon', FontAwesomeIcon);
        const page = usePage();
        router.on('invalid', (event) => {
            if (event.detail.response.status === 419) {
                event.preventDefault(); // Replace modal/pop-up with quick silent reload
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        });

        watch(
            () => (page.props ? page.props?.flash : null),
            (flash) => {
                if (flash?.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: flash.success,
                        icon: 'success',
                        timer: 3000,
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                    });
                }
                if (flash?.error) {
                    Swal.fire('Error', flash.error, 'error');
                }
            },
            { deep: true },
        );

        let heartbeatTimer = null;
        let heartbeatInFlight = false;
        let lastHeartbeatAt = 0;
        let lastActivityAt = Date.now();

        const loadModalDrafts = () => {
            try {
                const raw = window.sessionStorage.getItem(modalDraftsStorageKey);
                return raw ? JSON.parse(raw) : {};
            } catch {
                return {};
            }
        };

        const saveModalDrafts = (drafts) => {
            try {
                window.sessionStorage.setItem(
                    modalDraftsStorageKey,
                    JSON.stringify(drafts),
                );
            } catch {
                // ignore storage exceptions
            }
        };

        const getFieldKey = (field, index) =>
            field.name || field.id || field.getAttribute('data-draft-key') || `field_${index}`;

        const fieldSelector =
            'input:not([type="file"]):not([type="submit"]):not([type="button"]):not([type="reset"]), select, textarea';

        const snapshotModal = (modalEl) => {
            if (!modalEl?.id) return;

            const payload = {};
            modalEl.querySelectorAll(fieldSelector).forEach((field, index) => {
                const key = getFieldKey(field, index);
                if (field.type === 'checkbox' || field.type === 'radio') {
                    payload[key] = !!field.checked;
                    return;
                }
                payload[key] = field.value;
            });

            const drafts = loadModalDrafts();
            drafts[modalEl.id] = payload;
            saveModalDrafts(drafts);
        };

        const applyFieldValue = (field, value) => {
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = !!value;
                field.dispatchEvent(new Event('change', { bubbles: true }));
                return;
            }

            field.value = value ?? '';
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        };

        const restoreModalDraft = (modalEl) => {
            if (!modalEl?.id) return;
            const drafts = loadModalDrafts();
            const draft = drafts[modalEl.id];
            if (!draft) return;

            modalEl.querySelectorAll(fieldSelector).forEach((field, index) => {
                const key = getFieldKey(field, index);
                if (!(key in draft)) return;
                applyFieldValue(field, draft[key]);
            });
        };

        const clearModalDraft = (modalId) => {
            if (!modalId) return;
            const drafts = loadModalDrafts();
            if (!(modalId in drafts)) return;
            delete drafts[modalId];
            saveModalDrafts(drafts);
        };

        const forceHardReload = () => {
            window.location.reload();
        };

        const sendHeartbeat = async ({ forceReloadOnAuthFailure = false } = {}) => {
            if (heartbeatInFlight) return;
            if (document.visibilityState !== 'visible') return;

            const now = Date.now();
            if (now - lastHeartbeatAt < minHeartbeatGapMs) return;

            heartbeatInFlight = true;
            try {
                await window.axios.get(refreshSessionUrl, {
                    params: { t: now },
                });
                lastHeartbeatAt = now;
            } catch (error) {
                const status = error?.response?.status;
                if (status === 401) {
                    if (forceReloadOnAuthFailure) {
                        forceHardReload();
                        return;
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    return;
                }
                if (status === 419) {
                    if (forceReloadOnAuthFailure) {
                        forceHardReload();
                        return;
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    return;
                }
            } finally {
                heartbeatInFlight = false;
            }
        };

        const markActivity = () => {
            lastActivityAt = Date.now();
        };

        const setHiddenTimestamp = () => {
            try {
                window.sessionStorage.setItem(
                    hiddenAtStorageKey,
                    `${Date.now()}`,
                );
            } catch {
                // ignore storage exceptions
            }
        };

        const consumeHiddenTimestamp = () => {
            try {
                const value = window.sessionStorage.getItem(hiddenAtStorageKey);
                window.sessionStorage.removeItem(hiddenAtStorageKey);
                return value ? Number.parseInt(value, 10) : null;
            } catch {
                return null;
            }
        };

        const isTabStale = () => {
            const hiddenAt = consumeHiddenTimestamp();
            if (!hiddenAt) return false;

            const tabWasIdleMs = Date.now() - hiddenAt;
            return tabWasIdleMs >= staleTabThresholdMs;
        };

        const onVisible = async () => {
            if (isTabStale()) {
                await sendHeartbeat({ forceReloadOnAuthFailure: true });
                return;
            }
            sendHeartbeat();
        };

        const handleVisibilityChange = () => {
            if (document.visibilityState === 'hidden') {
                setHiddenTimestamp();
                return;
            }
            onVisible();
        };

        document.addEventListener('visibilitychange', handleVisibilityChange);
        window.addEventListener('focus', onVisible);
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                forceHardReload();
                return;
            }
            onVisible();
        });

        // Persist modal drafts while users type so a reload does not lose in-progress data.
        document.addEventListener('input', (event) => {
            const modalEl = event.target?.closest?.('.modal');
            if (!modalEl) return;
            snapshotModal(modalEl);
        });
        document.addEventListener('change', (event) => {
            const modalEl = event.target?.closest?.('.modal');
            if (!modalEl) return;
            snapshotModal(modalEl);
        });
        document.addEventListener('shown.bs.modal', (event) => {
            const modalEl = event.target;
            if (!modalEl?.id) return;
            window.sessionStorage.setItem(activeModalStorageKey, modalEl.id);
            restoreModalDraft(modalEl);
        });
        document.addEventListener('hidden.bs.modal', () => {
            window.sessionStorage.removeItem(activeModalStorageKey);
        });
        // Clear drafts only when the user explicitly clicks Close (x or "Close" button).
        document.addEventListener('click', (event) => {
            const closeTrigger = event.target?.closest?.('[data-bs-dismiss="modal"]');
            if (!closeTrigger) return;

            const modalEl = closeTrigger.closest('.modal');
            if (!modalEl?.id) return;

            const buttonText = (closeTrigger.textContent || '').trim().toLowerCase();
            const isExplicitClose =
                closeTrigger.classList.contains('btn-close') || buttonText === 'close';

            if (isExplicitClose) {
                clearModalDraft(modalEl.id);
                if (window.sessionStorage.getItem(activeModalStorageKey) === modalEl.id) {
                    window.sessionStorage.removeItem(activeModalStorageKey);
                }
            }
        });
        const reopenModalAfterReload = () => {
            const modalId = window.sessionStorage.getItem(activeModalStorageKey);
            if (!modalId) return;
            const modalEl = document.getElementById(modalId);
            if (!modalEl || !window.bootstrap?.Modal) return;

            restoreModalDraft(modalEl);
            const instance = window.bootstrap.Modal.getOrCreateInstance(modalEl, {
                backdrop: 'static',
            });
            instance.show();
        };
        setTimeout(reopenModalAfterReload, 50);

        // Track activity only; heartbeats stay silent and timer-driven.
        activityEvents.forEach((eventName) => {
            window.addEventListener(eventName, markActivity, { passive: true });
        });

        watch(
            () => page.props?.auth?.user?.id,
            (userId) => {
                if (!userId) {
                    if (heartbeatTimer) {
                        clearInterval(heartbeatTimer);
                        heartbeatTimer = null;
                    }
                    return;
                }

                if (heartbeatTimer) return;

                heartbeatTimer = setInterval(() => {
                    const idleForMs = Date.now() - lastActivityAt;
                    if (idleForMs >= staleTabThresholdMs) return;
                    sendHeartbeat();
                }, heartbeatIntervalMs);

                if (document.visibilityState === 'visible') {
                    onVisible();
                }
            },
            { immediate: true },
        );

        return app.mount(el);
    },
    progress: { color: '#4B5563' },
});
