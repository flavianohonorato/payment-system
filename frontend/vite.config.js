import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    host: '0.0.0.0',
    port: 8080,
    strictPort: true,
    hmr: {
      host: 'localhost',
      clientPort: 8080
    },
    watch: {
      usePolling: true
    },
    allowedHosts: ['localhost', '.orb.local', 'frontend.payment-system.orb.local'],
  },
})
