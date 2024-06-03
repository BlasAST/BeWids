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
        'colorCaberaTras':'rgba(43,44,48,0.5)',
        'colorCaberaTras2':'rgba(43,44,48,0.9)',
        'colorMain': '#453745',
        'colorLetra':'white',
        'colorLetraOscura':'black',
        'colorDetalles': '#BF1B4B',
        'colorComplem': '#4465B8',
        'colorBarra2':'#541530',
        'colorSecundario':'#0b191f',
      },
      screens: {
        'xsm':'70px',
      }
    },
  },
  plugins: [],
}

