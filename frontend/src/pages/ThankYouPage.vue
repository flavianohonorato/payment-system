<template>
  <q-page class="flex column items-center justify-center q-pa-md">
    <q-icon name="check_circle" color="secondary" size="6rem" />
    <div class="text-h4 q-mt-md primary-color">Pagamento Processado com Sucesso!</div>
    <div class="text-subtitle1 q-mt-sm">Obrigado pela sua compra</div>

    <q-card class="q-mt-lg" style="width: 100%; max-width: 500px;">
      <q-card-section>
        <div class="text-h6 primary-color">Detalhes do Pagamento</div>
        <div class="q-mt-sm">
          <div><strong>ID do Pagamento:</strong> {{ paymentId }}</div>
          <div><strong>Data:</strong> {{ currentDate }}</div>
          <div><strong>Método:</strong> {{ paymentMethodName }}</div>
        </div>
      </q-card-section>

      <q-card-actions align="center">
        <q-btn color="accent" text-color="white" label="Voltar ao início" to="/" />
      </q-card-actions>
    </q-card>
  </q-page>
</template>

<script>
export default {
  name: 'ThankYouPage',
  computed: {
    paymentId() {
      return this.$route.params.paymentId
    },
    currentDate() {
      return new Date().toLocaleString()
    },
    paymentMethodName() {
      // Aqui estamos mockando, mas idealmente seria obtido do store ou da API
      const methods = {
        credit: 'Cartão de Crédito',
        boleto: 'Boleto Bancário',
        pix: 'Pix'
      }

      // Vamos fingir que temos essa informação (normalmente seria armazenada no estado)
      const mockMethod = this.paymentId.includes('1') ? 'credit' :
                         this.paymentId.includes('2') ? 'boleto' : 'pix'

      return methods[mockMethod]
    }
  }
}
</script>

<style scoped>
.primary-color {
  color: #174760 !important;
}

.accent-color {
  color: #E0730E !important;
}

.secondary-color {
  color: #017A7E !important;
}
</style>
