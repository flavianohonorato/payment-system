import paymentService from 'src/services/paymentService'

const state = {
  customer: {
    id: null,
    name: '',
    email: '',
    phone: '',
    document: ''
  },
  paymentMethod: null,
  paymentDetails: null,
  processingPayment: false,
  paymentError: null,
  paymentResult: null
}

const mutations = {
  SET_CUSTOMER(state, customer) {
    state.customer = customer
  },
  SET_PAYMENT_METHOD(state, method) {
    state.paymentMethod = method
  },
  SET_PAYMENT_DETAILS(state, details) {
    state.paymentDetails = details
  },
  SET_PROCESSING_PAYMENT(state, isProcessing) {
    state.processingPayment = isProcessing
  },
  SET_PAYMENT_ERROR(state, error) {
    state.paymentError = error
  },
  SET_PAYMENT_RESULT(state, result) {
    state.paymentResult = result
  },
  RESET_PAYMENT(state) {
    state.paymentMethod = null
    state.paymentDetails = null
    state.processingPayment = false
    state.paymentError = null
    state.paymentResult = null
  }
}

const actions = {
  setCustomer({ commit }, customer) {
    commit('SET_CUSTOMER', customer)
  },
  setPaymentMethod({ commit }, method) {
    commit('SET_PAYMENT_METHOD', method)
  },
  setPaymentDetails({ commit }, details) {
    commit('SET_PAYMENT_DETAILS', details)
  },
  resetPayment({ commit }) {
    commit('RESET_PAYMENT')
  },
  async processPayment({ commit, state }) {
    commit('SET_PROCESSING_PAYMENT', true)
    commit('SET_PAYMENT_ERROR', null)

    try {
      const billingTypeMap = {
        'credit': 'CREDIT_CARD',
        'boleto': 'BOLETO',
        'pix': 'PIX'
      }

      const customerData = {
        name: state.customer.name,
        email: state.customer.email,
        document: state.customer.document.replace(/[^0-9]/g, ''),
        phone: state.customer.phone
      }

      let customerId = null

      customerId = state.customer.id

      const paymentData = {
        customer_id: customerId,
        billing_type: billingTypeMap[state.paymentMethod],
        value: 100.00,
        description: 'Pagamento de produtos na Perfect Pay',
        due_date: state.paymentMethod === 'boleto'
          ? state.paymentDetails.dueDate
          : new Date().toISOString().split('T')[0],
        customer: customerData
      }

      const response = await paymentService.processPayment(paymentData)

      commit('SET_PAYMENT_RESULT', response.data)
      return response.data
    } catch (error) {
      const errorMessage = error.response?.data?.message || 'Erro ao processar pagamento'
      commit('SET_PAYMENT_ERROR', errorMessage)
      throw error
    } finally {
      commit('SET_PROCESSING_PAYMENT', false)
    }
  }
}

const getters = {
  getCustomer: state => state.customer,
  getPaymentMethod: state => state.paymentMethod,
  getPaymentDetails: state => state.paymentDetails,
  isProcessingPayment: state => state.processingPayment,
  getPaymentError: state => state.paymentError,
  getPaymentResult: state => state.paymentResult
}

export default {
  namespaced: true,
  state,
  mutations,
  actions,
  getters
}
