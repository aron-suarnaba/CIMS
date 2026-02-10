import { library } from '@fortawesome/fontawesome-svg-core';
import { faHouse, faLock, faUser } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { createInertiaApp, router, usePage } from '@inertiajs/vue3';
import { definePreset } from '@primeuix/themes'; // Add this import
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
import Calendar from 'primevue/calendar';
import Card from 'primevue/card';
import Checkbox from 'primevue/checkbox';
import Column from 'primevue/column';
import PrimeVue from 'primevue/config';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputGroup from 'primevue/inputgroup';
import InputGroupAddon from 'primevue/inputgroupaddon';
import InputNumber from 'primevue/inputnumber';
import Paginator from 'primevue/paginator';
import RadioButton from 'primevue/radiobutton';
import Tag from 'primevue/tag';
import Textarea from 'primevue/textarea';
import Swal from 'sweetalert2';
import { createApp, h, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '../css/app.css';
import './bootstrap'; // This usually defines window.axios
window.bootstrap = bootstrap;

const appName = import.meta.env.VITE_APP_NAME || 'CIMS';

library.add(faUser, faHouse, faLock);

const BootstrapBluePreset = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{blue.50}',
            100: '{blue.100}',
            200: '{blue.200}',
            300: '{blue.300}',
            400: '{blue.400}',
            500: '{blue.500}', // This is the main "Bootstrap Primary" shade
            600: '{blue.600}',
            700: '{blue.700}',
            800: '{blue.800}',
            900: '{blue.900}',
            950: '{blue.950}',
        },
    },
});

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
                    preset: BootstrapBluePreset, // Use our new custom blue preset
                    options: {
                        prefix: 'p',
                        darkModeSelector: 'none', // Keeps it consistent with standard AdminLTE light mode
                        cssLayer: false,
                    },
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
            .component('Badge', Badge)
            .component('Dialog', Dialog)
            .component('Checkbox', Checkbox)
            .component('RadioButton', RadioButton)
            .component('Paginator', Paginator)
            .component('Textarea', Textarea)
            .component('DataTable', DataTable)
            .component('Calendar', Calendar)
            .component('Column', Column)
            .component('InputNumber', InputNumber);
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
