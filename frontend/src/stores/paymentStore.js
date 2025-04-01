import { defineStore } from 'pinia'

export const usePaymentStore = defineStore('payment', {
  state: () => ({
    customer: {
      name: '',
      email: '',
      phone: '',
      document: ''
    },
    paymentMethod: null,
    paymentDetails: null
  }),

  actions: {
    setCustomer(customer) {
      this.customer = customer
    },
    setPaymentMethod(method) {
      this.paymentMethod = method
    },
    setPaymentDetails(details) {
      this.paymentDetails = details
    },
    resetPayment() {
      this.customer = {
        name: '',
        email: '',
        phone: '',
        document: ''
      }
      this.paymentMethod = null
      this.paymentDetails = null
    }
  },

  getters: {
    isCustomerInfoComplete: (state) => {
      return !!state.customer.name &&
             !!state.customer.email &&
             !!state.customer.phone &&
             !!state.customer.document
    }
  }
})
