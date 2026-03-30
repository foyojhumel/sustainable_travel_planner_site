/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.html",
    "./**/*.php",
    "./**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        "surface": "#fafaf5",
        "on-surface": "#1a1c19",
      },
      fontFamily: {
        body: ["Manrope"],
      }
    },
  },
  plugins: [],
}

