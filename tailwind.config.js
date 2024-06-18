/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./app/**/*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        'accent': '#FD9E80',
        'primary': '#242633',
        'background': '#EBEBEB',
        'borderGrey': '#CDCDCF'
      },
      backgroundImage: {
        'hero-pattern': "url('/img/hero-pattern.svg')",
        'footer-texture': "url('/img/footer-texture.png')",
      },
      fontFamily: {
        'inter': ['Inter', 'sans-serif'],
        'serif4' : ['"Source Serif 4"', 'sans-serif']
      },
    },
  },
  plugins: [],
}