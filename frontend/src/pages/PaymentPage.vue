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
          ref="stepper"
        >
          <q-step
            :name="1"
            title="Informações do Cliente"
            icon="person"
            :done="step > 1"
          >
            <customer-info-form
              @update:customer="updateCustomer"
              :customer="paymentStore.customer"
            />

            <q-stepper-navigation>
              <q-btn
                color="accent"
                @click="nextStep"
                label="Continuar"
                :disable="!isCustomerValid"
              />
            </q-stepper-navigation>
          </q-step>

          <q-step
            :name="2"
            title="Método de Pagamento"
            icon="payment"
            :done="step > 2"
          >
            <payment-method-selector @select="selectPaymentMethod" />

            <q-stepper-navigation>
              <q-btn flat @click="prevStep" color="primary" label="Voltar" class="q-mr-sm" />
              <q-btn
                color="accent"
                @click="nextStep"
                label="Continuar"
                :disable="!paymentStore.paymentMethod"
              />
            </q-stepper-navigation>
          </q-step>

          <q-step
            :name="3"
            title="Detalhes do Pagamento"
            icon="attach_money"
          >
            <div v-if="paymentStore.paymentMethod">
              <div class="row items-center q-mb-md">
                <div class="col">
                  <q-chip
                    class="payment-method-chip"
                    :color="getMethodColor(paymentStore.paymentMethod)"
                    text-color="white"
                    icon="payment"
                  >
                    {{ getMethodName(paymentStore.paymentMethod) }}
                  </q-chip>
                </div>
                <div class="col-auto">
                  <q-btn
                    flat
                    color="accent"
                    label="Trocar método"
                    icon="swap_horiz"
                    @click="changePaymentMethod"
                  />
                </div>
              </div>

              <div v-if="paymentStore.paymentMethod === 'credit'">
                <credit-card-form
                  @submit="processPayment"
                  :initial-data="paymentStore.paymentDetails"
                />
              </div>
              <div v-else-if="paymentStore.paymentMethod === 'boleto'">
                <boleto-form @submit="processPayment" />
              </div>
              <div v-else-if="paymentStore.paymentMethod === 'pix'">
                <pix-form @submit="processPayment" />
              </div>

              <q-stepper-navigation>
                <q-btn flat @click="prevStep" color="primary" label="Voltar" class="q-mr-sm" />
              </q-stepper-navigation>
            </div>
          </q-step>
        </q-stepper>
      </div>
    </div>
  </q-page>
</template>

<script>
import { usePaymentStore } from 'src/stores/paymentStore'
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
      isCustomerValid: false
    }
  },
  computed: {
    paymentStore() {
      return usePaymentStore()
    }
  },
  methods: {
    updateCustomer(customerData) {
      this.paymentStore.setCustomer(customerData)
      this.isCustomerValid = !!(
        customerData.name &&
        customerData.email &&
        customerData.phone &&
        customerData.document
      )
    },
    selectPaymentMethod(method) {
      this.paymentStore.setPaymentMethod(method)
      this.nextStep()
    },
    processPayment(paymentData) {
      console.log('Processando pagamento:', {
        customer: this.paymentStore.customer,
        method: this.paymentStore.paymentMethod,
        paymentData
      })

      this.paymentStore.setPaymentDetails(paymentData)

      this.$router.push({
        path: '/payment/confirmation'
      })
    },
    changePaymentMethod() {
      this.step = 2
    },
    getMethodName(method) {
      const methods = {
        credit: 'Cartão de Crédito',
        boleto: 'Boleto Bancário',
        pix: 'PIX'
      }
      return methods[method] || method
    },
    getMethodColor(method) {
      const colors = {
        credit: 'primary',
        boleto: 'secondary',
        pix: 'accent'
      }
      return colors[method] || 'primary'
    },
    nextStep() {
      this.$refs.stepper.next()
    },
    prevStep() {
      this.$refs.stepper.previous()
    }
  },
  created() {
    if (this.paymentStore.paymentMethod) {
      this.step = 3
      this.isCustomerValid = this.paymentStore.isCustomerInfoComplete
    } else if (this.paymentStore.isCustomerInfoComplete) {
      this.step = 2
      this.isCustomerValid = true
    }

    const stepParam = this.$route.query.step
    if (stepParam) {
      this.step = parseInt(stepParam)
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

.payment-method-chip {
  font-weight: bold;
}
</style>
