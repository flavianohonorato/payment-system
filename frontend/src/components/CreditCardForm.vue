<template>
  <div>
    <q-form @submit="onSubmit" class="q-gutter-md">
      <q-input
        filled
        v-model="form.cardNumber"
        label="Número do Cartão *"
        mask="#### #### #### ####"
        :rules="[val => !!val || 'Número do cartão é obrigatório']"
      />

      <div class="row q-col-gutter-md">
        <div class="col-6">
          <q-input
            filled
            v-model="form.expiryDate"
            label="Data de Validade *"
            mask="##/##"
            hint="MM/AA"
            :rules="[val => !!val || 'Data de validade é obrigatória']"
          />
        </div>
        <div class="col-6">
          <q-input
            filled
            v-model="form.cvv"
            label="CVV *"
            mask="###"
            :rules="[val => !!val || 'CVV é obrigatório']"
          />
        </div>
      </div>

      <q-input
        filled
        v-model="form.cardholderName"
        label="Nome no Cartão *"
        :rules="[val => !!val || 'Nome no cartão é obrigatório']"
      />

      <q-select
        filled
        v-model="form.installments"
        :options="installmentOptions"
        label="Parcelas *"
        :rules="[val => !!val || 'Parcelas é obrigatório']"
      />

      <div>
        <q-btn label="Pagar Agora" type="submit" color="accent" text-color="white"/>
      </div>
    </q-form>
  </div>
</template>

<script>
export default {
  name: 'CreditCardForm',
  data() {
    return {
      form: {
        cardNumber: '',
        expiryDate: '',
        cvv: '',
        cardholderName: '',
        installments: 1
      },
      installmentOptions: [
        { label: '1x de R$ 100,00 sem juros', value: 1 },
        { label: '2x de R$ 50,00 sem juros', value: 2 },
        { label: '3x de R$ 33,33 sem juros', value: 3 },
        { label: '4x de R$ 25,00 sem juros', value: 4 },
        { label: '5x de R$ 20,00 sem juros', value: 5 },
        { label: '6x de R$ 16,67 sem juros', value: 6 }
      ]
    }
  },
  methods: {
    onSubmit() {
      this.$emit('submit', this.form)
    }
  }
}
</script>

<style lang="scss" scoped>
:deep(.q-field__label) {
  color: #174760;
}

:deep(.q-field--focused) {
  .q-field__control {
    border-color: #017A7E !important;
  }
}
</style>
