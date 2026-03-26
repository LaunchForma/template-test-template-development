import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './shared/hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Forma';
const templates = import.meta.glob(['./templates/*/pages/*.{tsx,jsx}']);
const templatePaths = Object.keys(templates).filter((fn) => {
    return fn.startsWith('./templates/');
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            [
                `./shared/pages/**/${name}.tsx`,
                `./shared/pages/**/${name}.jsx`,
                `./shared/pages/${name}.tsx`,
                `./shared/pages/${name}.jsx`,
                `${templatePaths}`,
            ],
            import.meta.glob([
                './shared/pages/**/*.{tsx,jsx}',
                './shared/pages/*.{tsx,jsx}',
                './templates/*/pages/*.{tsx,jsx}',
            ]),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <StrictMode>
                <App {...props} />
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
