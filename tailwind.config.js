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
        "primary-container": "#0047ab",
        "on-primary-container": "a5bdff",
        "primary": "#00327d",
        "on-primary": "#ffffff",
        "on-surface-variant": "#434653",
        "secondary": "#1b6d24",
        "outline-variant": "#c3c6d5",
        "surface-container-high": "#e8e8e3",
        "surface-container-low": "#f4f4ef",
        "surface-container-highest": "#e3e3de",
        "outline": "#737784",
        "secondary-container": "#a0f399",
        "tertiary": "#562c00",
      },
      fontFamily: {
        "headline": ["Plus Jakarta Sans"],
        "body": ["Manrope"],
        "footer": ["Manrope"],
        "label": ["Manrope",]
      },
      borderRadius: {
        "DEFAULT": "0.25rem",
        "lg": "0.5rem",
        "xl": "0.75rem",
        "full": "9999px",
      },
    },
  },
  plugins: [],
}

