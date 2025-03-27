import { boot } from 'quasar/wrappers'
import axios from 'axios'

const apiUrl = process.env.API_URL || 'http://localhost:8000'

const api = axios.create({
  baseURL: apiUrl
})

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios
  app.config.globalProperties.$api = api
})

export { api }
