<template>
  <div class="text-center">
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-h6 primary-color">Pagamento via PIX</div>
        <p>Para realizar o pagamento via PIX, clique em "Continuar" para revisar os detalhes.</p>

        <div class="text-subtitle2 q-mt-md">Valor a pagar:</div>
        <div class="text-h5 text-accent">R$ 100,00</div>

        <div class="q-mt-sm">
          <q-badge color="secondary">
            O pagamento PIX é processado instantaneamente após a confirmação
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
  name: 'PixForm',
  data() {
    return {
      dueDate: new Date().toISOString().split('T')[0]
    }
  },
  methods: {
    onSubmit() {
      this.$emit('submit', {
        type: 'pix',
        dueDate: this.dueDate
      })
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
