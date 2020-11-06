module.exports = {
  future: {
    // removeDeprecatedGapUtilities: true,
    // purgeLayersByDefault: true,
  },
  purge: ["./resources/**/*.html", "./resources/**/*.php"],
  theme: {
    extend: {},
  },
  variants: {
      textColor: ['responsive', 'hover', 'focus', 'group-hover'],
  },
  plugins: [
      require('@tailwindcss/custom-forms'),
  ],
}
