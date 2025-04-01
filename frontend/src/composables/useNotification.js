import { useQuasar } from 'quasar'

export default function useNotification() {
  const $q = useQuasar()

  const showSuccess = (message, timeout = 3000) => {
    if ($q && $q.notify) {
      $q.notify({
        message,
        color: 'positive',
        position: 'top',
        timeout,
        actions: [{ icon: 'close', color: 'white' }]
      })
    } else {
      console.log('Notificação de sucesso:', message)
    }
  }

  const showError = (message, caption = null, timeout = 5000) => {
    if ($q && $q.notify) {
      $q.notify({
        message,
        caption,
        color: 'negative',
        position: 'top',
        timeout,
        actions: [{ icon: 'close', color: 'white' }]
      })
    } else {
      console.error('Notificação de erro:', message, caption)
    }
  }

  const showInfo = (message, timeout = 3000) => {
    if ($q && $q.notify) {
      $q.notify({
        message,
        color: 'primary',
        position: 'top',
        timeout,
        actions: [{ icon: 'close', color: 'white' }]
      })
    } else {
      console.info('Notificação info:', message)
    }
  }

  const showWarning = (message, timeout = 4000) => {
    if ($q && $q.notify) {
      $q.notify({
        message,
        color: 'warning',
        position: 'top',
        timeout,
        actions: [{ icon: 'close', color: 'white' }]
      })
    } else {
      console.warn('Notificação de aviso:', message)
    }
  }

  return {
    showSuccess,
    showError,
    showInfo,
    showWarning
  }
}
