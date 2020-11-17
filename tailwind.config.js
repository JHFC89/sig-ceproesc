module.exports = {
  future: {
    // removeDeprecatedGapUtilities: true,
    // purgeLayersByDefault: true,
  },
  purge: ["./resources/views/html", "./resources/views/*.blade.php", "./storage/framework/views/*.php"],
  theme: {
    extend: {},
  },
  variants: {
      textColor: ['responsive', 'hover', 'focus', 'group-hover'],
      opacity: ['responsive', 'hover', 'focus', 'disabled'],
  },
  plugins: [
      require('@tailwindcss/custom-forms'),
      require('@tailwindcss/ui'),
  ],
}
