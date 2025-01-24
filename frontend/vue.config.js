const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: true,

  pluginOptions: {
    vuetify: {
      // https://github.com/vuetifyjs/vuetify-loader/tree/next/packages/vuetify-loader
    }
  },

  devServer: {
    port: 3000,  // Set your desired port number here
    host: '0.0.0.0',  // Optional: to allow access from network (useful for testing on mobile)
  }
})
