import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Function to get current CSRF token
const getCSRFToken = () => {
    const token = document.head.querySelector('meta[name="csrf-token"]');
    return token ? token.content : null;
};

// Set CSRF token header from meta tag when available
try {
    const token = getCSRFToken();
    if (token) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }
} catch (e) {
    // document may be undefined during SSR/build step; ignore silently
}

// Request interceptor to ensure CSRF token is always current
window.axios.interceptors.request.use(
    (config) => {
        const token = getCSRFToken();
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Ensure cookies (session) are sent with requests on same-origin setups
window.axios.defaults.withCredentials = true;

// Only initialize Echo if broadcasting is enabled
// if (import.meta.env.VITE_BROADCAST_CONNECTION && import.meta.env.VITE_BROADCAST_CONNECTION !== 'null') {
//     import('laravel-echo').then(({ default: Echo }) => {
//         import('pusher-js').then(({ default: Pusher }) => {
//             window.Pusher = Pusher;
//             window.Echo = new Echo({
//                 broadcaster: 'reverb',
//                 key: import.meta.env.VITE_REVERB_APP_KEY,
//                 wsHost: import.meta.env.VITE_REVERB_HOST,
//                 wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//                 wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//                 forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
//                 enabledTransports: ['ws', 'wss'],
//             });
//         });
//     });
// }

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
