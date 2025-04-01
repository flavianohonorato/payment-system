<template>
  <div>
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-h6 primary-color">Pagamento via Boleto Bancário</div>
        <p>Para gerar seu boleto, clique em "Continuar" para revisar os detalhes do pagamento.</p>

        <div class="text-subtitle2 q-mt-md">Valor a pagar:</div>
        <div class="text-h5 text-accent">R$ 100,00</div>

        <div class="text-subtitle2 q-mt-md">Data de vencimento:</div>
        <div>{{ formatDate(dueDate) }}</div>

        <div class="q-mt-sm">
          <q-badge color="secondary">
            O prazo de compensação é de até 3 dias úteis
          </q-badge>
        </div>
      </q-card-section>
    </q-card>

    <div>
      <q-btn label="Continuar" @click="onSubmit" color="accent" text-color="white"/>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BoletoForm',
  computed: {
    dueDate() {
      const date = new Date()
      date.setDate(date.getDate() + 3)
      // Retorna no formato YYYY-MM-DD para a API
      return date.toISOString().split('T')[0]
    }
  },
  methods: {
    onSubmit() {
      this.$emit('submit', {
        type: 'boleto',
        dueDate: this.dueDate
      })
    },
    formatDate(dateString) {
      if (!dateString) return '';

      const parts = dateString.split('-');
      if (parts.length !== 3) return dateString;

      return `${parts[2]}/${parts[1]}/${parts[0]}`;
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
</style>
