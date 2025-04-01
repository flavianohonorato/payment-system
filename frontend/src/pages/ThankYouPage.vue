<template>
  <q-page class="flex column items-center justify-center q-pa-md">
    <q-spinner v-if="loading" color="primary" size="3rem" />

    <template v-else>
      <q-icon name="check_circle" color="secondary" size="6rem" />
      <div class="text-h4 q-mt-md primary-color">Pagamento Processado com Sucesso!</div>
      <div class="text-subtitle1 q-mt-sm">Obrigado pela sua compra</div>

      <q-card class="q-mt-lg" style="width: 100%; max-width: 500px;">
        <q-card-section>
          <div class="text-h6 primary-color">Detalhes do Pagamento</div>
          <div class="q-mt-sm" v-if="paymentDetails">
            <div><strong>ID do Pagamento:</strong> {{ paymentDetails.id || paymentId }}</div>
            <div><strong>Data:</strong> {{ formatDate(paymentDetails.created_at) || currentDate }}</div>
            <div><strong>Método:</strong> {{ formatBillingType(paymentDetails.billing_type) }}</div>
            <div><strong>Status:</strong> {{ formatStatus(paymentDetails.status) }}</div>
            <div><strong>Valor:</strong> R$ {{ formatValue(paymentDetails.value) }}</div>
          </div>
          <div class="q-mt-sm" v-else>
            <div><strong>ID do Pagamento:</strong> {{ paymentId }}</div>
            <div><strong>Data:</strong> {{ currentDate }}</div>
            <div><strong>Método:</strong> {{ paymentMethodName }}</div>
          </div>
        </q-card-section>

        <q-card-actions align="center">
          <q-btn color="accent" text-color="white" label="Voltar ao início" to="/" />
        </q-card-actions>
      </q-card>
    </template>
  </q-page>
</template>

<script>
import paymentService from 'src/services/paymentService'

export default {
  name: 'ThankYouPage',
  data() {
    return {
      paymentDetails: null,
      loading: true,
      error: null
    }
  },
  computed: {
    paymentId() {
      return this.$route.params.paymentId
    },
    currentDate() {
      return new Date().toLocaleString()
    },
    paymentMethodName() {
      if (this.paymentId.includes('1')) return 'Cartão de Crédito';
      if (this.paymentId.includes('2')) return 'Boleto Bancário';
      return 'PIX';
    }
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR');
    },
    formatBillingType(billingType) {
      const types = {
        'CREDIT_CARD': 'Cartão de Crédito',
        'BOLETO': 'Boleto Bancário',
        'PIX': 'PIX'
      };
      return types[billingType] || billingType;
    },
    formatStatus(status) {
      const statusMap = {
        'PENDING': 'Pendente',
        'CONFIRMED': 'Confirmado',
        'RECEIVED': 'Recebido',
        'OVERDUE': 'Vencido',
        'REFUNDED': 'Reembolsado',
        'CANCELLED': 'Cancelado'
      };
      return statusMap[status] || status;
    },
    formatValue(value) {
      return parseFloat(value).toFixed(2).replace('.', ',');
    }
  },
  async created() {
    if (this.paymentId === 'success') {
      this.loading = false;
      return;
    }

    try {
      const response = await paymentService.getPaymentDetails(this.paymentId);

      if (response.data && response.data.data) {
        this.paymentDetails = response.data.data;
      } else if (response.data && response.data.payment) {
        this.paymentDetails = response.data.payment;
      } else {
        this.paymentDetails = response.data;
      }
    } catch (err) {
      console.error('Erro ao obter detalhes do pagamento:', err);
      this.error = 'Não foi possível obter os detalhes do pagamento';
    } finally {
      this.loading = false;
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
