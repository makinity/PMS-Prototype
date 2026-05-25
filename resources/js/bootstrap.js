import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
window.Pusher = Pusher;

// Enable Pusher debug logging in development
if (import.meta.env.DEV) {
    Pusher.logToConsole = true;
}

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
    enabledTransports: ['ws', 'wss'],
});

console.info('[PMS RT] Echo initialized', {
    key: import.meta.env.VITE_REVERB_APP_KEY ? '***set***' : '⚠️ MISSING',
    host: import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1',
    port: import.meta.env.VITE_REVERB_PORT ?? 8080,
});
