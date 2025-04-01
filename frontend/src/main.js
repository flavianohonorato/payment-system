import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia'
import { Quasar, Notify } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/dist/quasar.css'

const pinia = createPinia()
const app = createApp(App)

app.use(Quasar, {
  plugins: {
    Notify
  },
  config: {
    notify: {
      position: 'top',
      timeout: 2500,
      textColor: 'white',
      actions: [{ icon: 'close', color: 'white' }]
    }
  }
})

app.use(router)
app.use(pinia)
app.mount('#app')
