import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    safelist: [
        // Red
        'bg-red-100',
        'text-red-700',
        'text-red-500',
        'dark:bg-red-900/40',
        'dark:text-red-300',

        // Blue
        'bg-blue-100',
        'text-blue-700',
        'text-blue-500',
        'dark:bg-blue-900/40',
        'dark:text-blue-300',

        // Green
        'bg-green-100',
        'text-green-700',
        'text-green-500',
        'dark:bg-green-900/40',
        'dark:text-green-300',

        // Yellow
        'bg-yellow-100',
        'text-yellow-700',
        'text-yellow-500',
        'dark:bg-yellow-900/40',
        'dark:text-yellow-300',

        // Purple
        'bg-purple-100',
        'text-purple-700',
        'text-purple-500',
        'dark:bg-purple-900/40',
        'dark:text-purple-300',
    ],

    plugins: [forms],

    darkMode: 'class'
};
