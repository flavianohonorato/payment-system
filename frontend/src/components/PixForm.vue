<template>
  <div class="text-center">
    <q-card class="q-mb-md">
      <q-card-section>
        <div class="text-h6 primary-color">Pagamento via PIX</div>
        <p>Escaneie o QR Code abaixo com o aplicativo do seu banco ou copie o código PIX.</p>

        <div class="flex justify-center q-py-md">
          <q-img
            src="https://www.qrcode-monkey.com/img/default-preview-qr.png"
            style="width: 200px; height: 200px"
          />
        </div>

        <div class="q-mt-md">
          <q-input
            readonly
            v-model="pixCode"
            label="Código PIX"
            filled
          >
            <template v-slot:append>
              <q-btn
                round
                dense
                flat
                icon="content_copy"
                @click="copyPixCode"
              />
            </template>
          </q-input>
        </div>

        <div class="text-subtitle2 q-mt-md">Valor a pagar:</div>
        <div class="text-h5 text-accent">R$ 100,00</div>

        <div class="q-mt-sm">
          <q-badge color="secondary">
            O pagamento PIX é processado instantaneamente
          </q-badge>
        </div>
      </q-card-section>
    </q-card>

    <div>
      <q-btn label="Confirmar Pagamento" @click="onSubmit" color="accent" text-color="white"/>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PixForm',
  data() {
    return {
      pixCode: '00020126580014br.gov.bcb.pix0136a629532e-7693-4846-b028-f142674256015204000053039865802BR5925SISTEMA DE PAGAMENTOS SA6009SAO PAULO62070503***6304F28C'
    }
  },
  methods: {
    copyPixCode() {
      navigator.clipboard.writeText(this.pixCode)
      this.$q.notify({
        message: 'Código PIX copiado!',
        color: 'positive'
      })
    },
    onSubmit() {
      this.$emit('submit', { type: 'pix' })
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
