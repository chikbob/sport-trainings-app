import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from 'ziggy-js'

import dateFormatterPlugin from './plugins/dateFormatter'

createInertiaApp({
    resolve: async (name) => {
        return (await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))).default
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })

        vueApp.use(plugin)
        vueApp.use(ZiggyVue)
        vueApp.use(dateFormatterPlugin)

        vueApp.component('Link', Link)
        vueApp.mount(el)
    },
})
