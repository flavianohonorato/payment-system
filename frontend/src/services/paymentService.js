import api from './api'

export default {
  processPayment(paymentData) {
    return api.post('/payments/process', paymentData)
      .then(response => {
        if (response.data) {
          console.log('Estrutura de resposta:', {
            success: response.data.success,
            message: response.data.message,
            hasData: !!response.data.data,
            hasPayment: !!response.data.payment
          });
        }

        return response;
      })
      .catch(error => {
        console.error('Erro na API de pagamento:', error.response?.data || error.message);
        throw error;
      });
  },

  getPaymentDetails(paymentId) {
    return api.get(`/thank-you/${paymentId}`)
  },

  getBoleto(boletoUrl) {
    return api.get(boletoUrl, {
      responseType: 'blob'
    })
  }
}
