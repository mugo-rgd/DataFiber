console.log("Dark Mode Enabled:", import.meta.env.VITE_FEATURE_DARK_MODE === "true");

export const appConfig = {
    name: import.meta.env.VITE_APP_NAME,
    logo: import.meta.env.VITE_APP_LOGO,
    description: import.meta.env.VITE_APP_DESCRIPTION,
    features: {
        darkMode: import.meta.env.VITE_FEATURE_DARK_MODE === "true",
        multiTenancy: import.meta.env.VITE_FEATURE_MULTI_TENANCY === "true",
        betaPortal: import.meta.env.VITE_FEATURE_BETA_PORTAL === "true",
    },
};


import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});
