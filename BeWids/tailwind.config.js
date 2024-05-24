/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.css",
  ],
  theme: {
    extend: {
      colors:{
        'colorFondo': '#613C4C',
        'colorCabera':'#2B2C30',
        'colorMain': '#453745',
        'colorLetra':'white',
        'colorLetraOscura':'black',
      },
      screens: {
        'xsm':'70px',
      }
    },
  },
  plugins: [],
}

