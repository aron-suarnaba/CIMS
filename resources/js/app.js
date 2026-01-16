import { createInertiaApp, usePage } from '@inertiajs/vue3';
import 'admin-lte';
import 'admin-lte/dist/js/adminlte.min.js';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import Swal from 'sweetalert2';
import { createApp, h, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '../css/app.css';
import './bootstrap';

const appName = import.meta.env.VITE_APP_NAME || 'CIMS';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    alias: {
        '@': '/resources/js',
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(VueApexCharts);
        const page = usePage();

        watch(
            () => (page.props ? page.props?.flash : null),
            (flash) => {
                if (flash?.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: flash.success,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top',
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
    progress: {
        color: '#4B5563',
    },
    onSuccess: () => {
        if (window.AdminLTE && window.AdminLTE.Layout) {
            window.AdminLTE.Layout.fixLayoutHeight();
        }
    },
});
