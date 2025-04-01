<template>
  <q-page class="q-pa-md">
    <div class="text-h4 q-mb-lg text-center primary-color">Escolha o Método de Pagamento</div>

    <div class="row justify-center q-col-gutter-md">
      <div class="col-12 col-sm-10 col-md-8">
        <q-stepper
          v-model="step"
          vertical
          color="accent"
          animated
        >
          <q-step
            :name="1"
            title="Informações do Cliente"
            icon="person"
            :done="step > 1"
          >
            <customer-info-form @submit="step = 2" :customer="customer" @update:customer="updateCustomer" />
          </q-step>

          <q-step
            :name="2"
            title="Método de Pagamento"
            icon="payment"
            :done="step > 2"
          >
            <payment-method-selector @select="selectPaymentMethod" />
          </q-step>

          <q-step
            :name="3"
            title="Detalhes do Pagamento"
            icon="attach_money"
          >
            <div v-if="paymentMethod === 'credit'">
              <credit-card-form @submit="processPayment" />
            </div>
            <div v-else-if="paymentMethod === 'boleto'">
              <boleto-form @submit="processPayment" />
            </div>
            <div v-else-if="paymentMethod === 'pix'">
              <pix-form @submit="processPayment" />
            </div>
          </q-step>
        </q-stepper>
      </div>
    </div>
  </q-page>
</template>

<script>
import CustomerInfoForm from 'components/CustomerInfoForm.vue'
import PaymentMethodSelector from 'components/PaymentMethodSelector.vue'
import CreditCardForm from 'components/CreditCardForm.vue'
import BoletoForm from 'components/BoletoForm.vue'
import PixForm from 'components/PixForm.vue'

export default {
  name: 'PaymentPage',
  components: {
    CustomerInfoForm,
    PaymentMethodSelector,
    CreditCardForm,
    BoletoForm,
    PixForm
  },
  data() {
    return {
      step: 1,
      paymentMethod: null,
      customer: {
        name: '',
        email: '',
        phone: '',
        document: ''
      }
    }
  },
  methods: {
    updateCustomer(customerData) {
      this.customer = { ...customerData }
    },
    selectPaymentMethod(method) {
      this.paymentMethod = method
      this.step = 3
    },
    processPayment(paymentData) {
      console.log('Processando pagamento:', {
        customer: this.customer,
        method: this.paymentMethod,
        paymentData
      })

      this.$router.push({
        path: '/payment/confirmation',
        query: {
          method: this.paymentMethod
        }
      })
    }
  }
}
</script>

<style lang="scss" scoped>
.primary-color {
  color: #174760 !important;
}

.accent-color {
  color: #E0730E !important;
}

.secondary-color {
  color: #017A7E !important;
}

:deep(.q-stepper) {
  .q-stepper__dot {
    background-color: #E0730E !important;
  }

  .q-stepper__title {
    color: #174760 !important;
  }
}
</style>
