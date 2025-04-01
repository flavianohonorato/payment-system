<template>
  <q-page class="q-pa-md">
    <div class="text-h4 q-mb-lg text-center primary-color">Confirmação de Pagamento</div>

    <div class="row justify-center">
      <div class="col-12 col-sm-10 col-md-8">
        <q-card class="q-mb-md">
          <q-card-section>
            <div class="text-h5 primary-color">Resumo do Pedido</div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle1 q-mb-md">Informações do Cliente</div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div><strong>Nome:</strong> {{ payment.customer.name }}</div>
                <div><strong>Email:</strong> {{ payment.customer.email }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div><strong>Telefone:</strong> {{ payment.customer.phone }}</div>
                <div><strong>Documento:</strong> {{ payment.customer.document }}</div>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle1 q-mb-md">Método de Pagamento</div>
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <div>
                  <strong>Método:</strong> {{ getPaymentMethodName(payment.method) }}
                </div>

                <!-- Detalhes do cartão de crédito -->
                <template v-if="payment.method === 'credit'">
                  <div><strong>Número do Cartão:</strong> **** **** **** {{ payment.details.cardNumber.slice(-4) }}</div>
                  <div><strong>Nome no Cartão:</strong> {{ payment.details.cardholderName }}</div>
                  <div><strong>Validade:</strong> {{ payment.details.expiryDate }}</div>
                  <div><strong>Parcelas:</strong> {{ payment.details.installments }}x</div>
                </template>

                <!-- Detalhes do boleto -->
                <template v-if="payment.method === 'boleto'">
                  <div><strong>Data de Vencimento:</strong> {{ payment.details.dueDate }}</div>
                </template>

                <!-- Detalhes do PIX -->
                <template v-if="payment.method === 'pix'">
                  <div><strong>Pagamento imediato via QR Code ou código PIX</strong></div>
                </template>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle1 q-mb-md primary-color">Valor Total</div>
            <div class="text-h5 text-accent">R$ 100,00</div>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat color="primary" label="Voltar" @click="goBack" />
            <q-btn color="accent" text-color="white" label="Confirmar Pagamento" @click="confirmPayment" />
          </q-card-actions>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'PaymentConfirmationPage',
  data() {
    return {
      // Dados mockados - em produção viriam de um store ou query params
      payment: {
        customer: {
          name: 'João Silva',
          email: 'joao.silva@example.com',
          phone: '(11) 98765-4321',
          document: '123.456.789-00'
        },
        method: 'credit',
        details: {
          cardNumber: '4111 1111 1111 1111',
          cardholderName: 'JOAO SILVA',
          expiryDate: '12/25',
          cvv: '123',
          installments: 3
        }
      }
    }
  },
  methods: {
    getPaymentMethodName(method) {
      const methods = {
        credit: 'Cartão de Crédito',
        boleto: 'Boleto Bancário',
        pix: 'Pix'
      }
      return methods[method] || method
    },
    goBack() {
      this.$router.push('/payment')
    },
    confirmPayment() {
      // Simulando um ID de pagamento
      const paymentId = 'pay_' + Math.random().toString(36).substr(2, 9)

      // Redirecionar para a página de agradecimento
      this.$router.push(`/thank-you/${paymentId}`)
    }
  },
  // Em uma implementação real, buscaríamos os dados do payment state ou query params
  created() {
    // Exemplo de lógica para diferentes métodos de pagamento
    if (this.$route.query.method === 'boleto') {
      this.payment.method = 'boleto'
      this.payment.details = {
        dueDate: new Date(Date.now() + 3*24*60*60*1000).toLocaleDateString('pt-BR')
      }
    } else if (this.$route.query.method === 'pix') {
      this.payment.method = 'pix'
      this.payment.details = {
        pixCode: '00020126580014br.gov.bcb.pix0136a629532e-7693-4846-b028-f142674256015204000053039865802BR'
      }
    }
  }
}
</script>

<style scoped>
.primary-color {
  color: #174760 !important;
}

.text-accent {
  color: #E0730E !important;
}

.secondary-color {
  color: #017A7E !important;
}
</style>
