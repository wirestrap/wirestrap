import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        outDir: resolve(__dirname, 'Browser/dist'),
        emptyOutDir: true,
        lib: {
            entry: resolve(__dirname, '../resources/js/wirestrap.js'),
            formats: ['iife'],
            name: 'Wirestrap',
            fileName: () => 'wirestrap.js',
        },
    },
});
