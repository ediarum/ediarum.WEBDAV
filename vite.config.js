import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import dotenv from 'dotenv'

dotenv.config()
export default defineConfig({

    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    base: `"${process.env.APP_SUBPATH}"`,
});
