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
        'colorDetalles': '#BF1B4B',
        'colorComplem': '#4465B8',
        'colorBarra2':'#541530',
      },
      screens: {
        'xsm':'70px',
      }
    },
  },
  plugins: [],
}

