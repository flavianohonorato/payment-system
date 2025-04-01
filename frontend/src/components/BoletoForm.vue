<template>
  <div>
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-h6 primary-color">Instruções para Pagamento via Boleto</div>
        <p>Após confirmar, você receberá um boleto para pagamento. O prazo de compensação é de até 3 dias úteis.</p>

        <div class="text-subtitle2 q-mt-md">Valor a pagar:</div>
        <div class="text-h5 text-accent">R$ 100,00</div>

        <div class="text-subtitle2 q-mt-md">Data de vencimento:</div>
        <div>{{ dueDate }}</div>
      </q-card-section>
    </q-card>

    <div>
      <q-btn label="Gerar Boleto" @click="onSubmit" color="accent" text-color="white"/>
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
      return date.toLocaleDateString('pt-BR')
    }
  },
  methods: {
    onSubmit() {
      this.$emit('submit', { type: 'boleto', dueDate: this.dueDate })
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
