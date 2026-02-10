import { library } from '@fortawesome/fontawesome-svg-core';
import { faHouse, faLock, faUser } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { createInertiaApp, router, usePage } from '@inertiajs/vue3';
import Aura from '@primeuix/themes/aura';
import 'admin-lte/dist/js/adminlte.min.js';
import AOS from 'aos';
import 'aos/dist/aos.css';
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { InputText } from 'primevue';
import Badge from 'primevue/badge';
import Breadcrumb from 'primevue/breadcrumb';
import Button from 'primevue/button';
import Card from 'primevue/card';
import PrimeVue from 'primevue/config';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import Swal from 'sweetalert2';
import Tag from 'primevue/tag';
import { createApp, h, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '../css/app.css';
import './bootstrap'; // This usually defines window.axios
window.bootstrap = bootstrap;

const appName = import.meta.env.VITE_APP_NAME || 'CIMS';

library.add(faUser, faHouse, faLock);

AOS.init({
    duration: 1700,
});

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 419) {
            window.location.reload();
        }
        if (error.response && error.response.status === 401) {
            window.location.href = '/login';
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
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                },
            })
            .component('font-awesome-icon', FontAwesomeIcon)
            .component('Button', Button)
            .component('InputGroup', InputGroup)
            .component('InputGroupAddon', InputGroupAddon)
            .component('InputText', InputText)
            .component('Card', Card)
            .component('Breadcrumb', Breadcrumb)
            .component('Tag', Tag)
            .component('Badge', Badge);
        const page = usePage();
        router.on('invalid', (event) => {
            if (event.detail.response.status === 419) {
                event.preventDefault(); // Prevent Inertia's default modal
                Swal.fire('Session Expired', 'Refreshing page...', 'info');
                window.location.reload();
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

        return app.mount(el);
    },
    progress: { color: '#4B5563' },
});

// Heartbeat: Keep session alive
setInterval(() => {
    // Use window.axios to ensure it's the configured instance
    window.axios.get('/refresh-session').catch(() => {
        console.log('Session refresh failed.');
    });
}, 300000);
