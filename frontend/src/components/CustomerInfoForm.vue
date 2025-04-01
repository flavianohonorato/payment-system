<template>
  <div>
    <q-form @submit="onSubmit" class="q-gutter-md">
      <q-input
        filled
        v-model="form.name"
        label="Nome Completo *"
        hint="Nome e sobrenome"
        :rules="[val => !!val || 'Nome é obrigatório']"
      />

      <q-input
        filled
        v-model="form.email"
        label="Email *"
        hint="exemplo@email.com"
        :rules="[
          val => !!val || 'Email é obrigatório',
          val => /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(val) || 'Email inválido'
        ]"
      />

      <q-input
        filled
        v-model="form.phone"
        label="Telefone *"
        mask="(##) #####-####"
        hint="(00) 00000-0000"
        :rules="[val => !!val || 'Telefone é obrigatório']"
      />

      <q-input
        filled
        v-model="form.document"
        label="CPF/CNPJ *"
        hint="Apenas números"
        :rules="[val => !!val || 'Documento é obrigatório']"
      />

      <div>
        <q-btn label="Continuar" type="submit" color="accent" text-color="white"/>
      </div>
    </q-form>
  </div>
</template>

<script>
export default {
  name: 'CustomerInfoForm',
  props: {
    customer: {
      type: Object,
      default: () => ({
        name: '',
        email: '',
        phone: '',
        document: ''
      })
    }
  },
  data() {
    return {
      form: { ...this.customer }
    }
  },
  methods: {
    onSubmit() {
      this.$emit('update:customer', this.form)
      this.$emit('submit')
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
