import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue'; // Correção: usar { createApp, h } em vez de Vue
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import store from '@/Pages/Store/index'; // Certifique-se de que o store está correto

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) }) // createApp é o correto para Vue 3
            .use(plugin)
            .use(store)  // Certifique-se de que o store está correto
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
