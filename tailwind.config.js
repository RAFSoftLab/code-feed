/** @type {import('tailwindcss').Config} */
import forms from '@tailwindcss/forms';
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        './vendor/brandymedia/turbine-ui-core/**/*.php',
    ],
    theme: {
        extend: {},
    },
    plugins: [forms],
}
