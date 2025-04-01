<template>
  <div>
    <div class="text-subtitle1 q-mb-md primary-color">Selecione como deseja pagar:</div>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-sm-4">
        <q-card class="payment-method-card cursor-pointer" clickable @click="selectMethod('credit')">
          <q-card-section class="text-center">
            <q-icon name="credit_card" size="4rem" color="accent" />
            <div class="text-h6 q-mt-sm primary-color">Cartão de Crédito</div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-4">
        <q-card class="payment-method-card cursor-pointer" clickable @click="selectMethod('boleto')">
          <q-card-section class="text-center">
            <q-icon name="receipt" size="4rem" color="secondary" />
            <div class="text-h6 q-mt-sm primary-color">Boleto Bancário</div>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-sm-4">
        <q-card class="payment-method-card cursor-pointer" clickable @click="selectMethod('pix')">
          <q-card-section class="text-center">
            <q-icon name="qr_code" size="4rem" color="primary" />
            <div class="text-h6 q-mt-sm primary-color">Pix</div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PaymentMethodSelector',
  methods: {
    selectMethod(method) {
      this.$emit('select', method)
    }
  }
}
</script>

<style scoped>
.payment-method-card {
  transition: all 0.3s;
}

.payment-method-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  border: 1px solid #E0730E;
}

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
