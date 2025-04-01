<template>
  <q-page>
    <div class="hero-banner text-white">
      <div class="container">
        <div class="row items-center">
          <div class="col-12 col-md-6">
            <h1 class="text-h3 q-mb-md">Pagamentos fáceis e seguros</h1>
            <p class="text-h6 q-mb-xl">Escolha entre diversas formas de pagamento e receba em instantes.</p>
            <q-btn to="/payment" color="accent" text-color="white" label="Fazer Pagamento" class="q-px-xl" size="lg" />
          </div>
          <div class="col-12 col-md-6 flex justify-center">
            <img src="https://cdn.quasar.dev/img/parallax2.jpg" style="max-width: 100%; max-height: 300px; border-radius: 10px;" />
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-md q-my-lg">
      <div class="text-h4 text-center q-mb-lg primary-color">Vantagens de usar nosso sistema</div>
      <div class="row q-col-gutter-lg justify-center">
        <div class="col-12 col-sm-4">
          <q-card class="benefit-card">
            <q-card-section class="text-center">
              <q-icon name="security" color="secondary" size="3rem" />
              <div class="text-h6 q-mt-sm primary-color">Segurança Garantida</div>
              <div class="q-mt-xs">Seus dados e pagamentos são protegidos com a mais alta tecnologia.</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-4">
          <q-card class="benefit-card">
            <q-card-section class="text-center">
              <q-icon name="speed" color="accent" size="3rem" />
              <div class="text-h6 q-mt-sm primary-color">Rapidez</div>
              <div class="q-mt-xs">Pagamentos processados em segundos com confirmação instantânea.</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-4">
          <q-card class="benefit-card">
            <q-card-section class="text-center">
              <q-icon name="support_agent" color="primary" size="3rem" />
              <div class="text-h6 q-mt-sm primary-color">Suporte 24/7</div>
              <div class="q-mt-xs">Nossa equipe está sempre disponível para ajudar com qualquer dúvida.</div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>

    <div class="bg-grey-2 q-pa-md q-py-xl">
      <div class="container">
        <div class="text-h4 text-center q-mb-lg primary-color">Métodos de Pagamento</div>
        <div class="row q-col-gutter-lg justify-center">
          <div class="col-12 col-sm-4">
            <q-card flat bordered class="payment-card">
              <q-img src="https://cdn.quasar.dev/img/mountains.jpg" height="150px" />
              <q-card-section>
                <div class="text-h6 primary-color">Cartão de Crédito</div>
                <div class="q-mt-xs">Pague em até 12x com os principais cartões de crédito.</div>
              </q-card-section>
              <q-card-actions align="right">
                <q-btn to="/payment" flat color="accent" label="Escolher" />
              </q-card-actions>
            </q-card>
          </div>
          <div class="col-12 col-sm-4">
            <q-card flat bordered class="payment-card">
              <q-img src="https://cdn.quasar.dev/img/parallax1.jpg" height="150px" />
              <q-card-section>
                <div class="text-h6 primary-color">Boleto Bancário</div>
                <div class="q-mt-xs">Gere um boleto e pague em qualquer banco ou lotérica.</div>
              </q-card-section>
              <q-card-actions align="right">
                <q-btn to="/payment" flat color="accent" label="Escolher" />
              </q-card-actions>
            </q-card>
          </div>
          <div class="col-12 col-sm-4">
            <q-card flat bordered class="payment-card">
              <q-img src="https://cdn.quasar.dev/img/parallax2.jpg" height="150px" />
              <q-card-section>
                <div class="text-h6 primary-color">PIX</div>
                <div class="q-mt-xs">Transferência instantânea disponível 24h por dia.</div>
              </q-card-section>
              <q-card-actions align="right">
                <q-btn to="/payment" flat color="accent" label="Escolher" />
              </q-card-actions>
            </q-card>
          </div>
        </div>
      </div>
    </div>

    <div class="q-pa-lg text-center">
      <div class="text-h5 q-mb-md primary-color">Pronto para começar?</div>
      <q-btn to="/payment" color="accent" text-color="white" label="Fazer Pagamento Agora" class="q-px-xl" size="lg" />
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'PageIndex'
}
</script>

<style lang="scss" scoped>
.hero-banner {
  padding: 60px 20px;
  background: linear-gradient(135deg, #174760 0%, #017A7E 100%);
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.benefit-card {
  height: 100%;
  transition: transform 0.3s;

  &:hover {
    transform: translateY(-5px);
  }
}

.payment-card {
  height: 100%;
  transition: transform 0.3s, box-shadow 0.3s;

  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }
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

:deep(.q-btn) {
  &.bg-primary, &.text-primary {
    background-color: #174760 !important;
    color: white !important;
  }

  &.bg-accent, &.text-accent {
    background-color: #E0730E !important;
    color: white !important;
  }

  &.bg-secondary, &.text-secondary {
    background-color: #017A7E !important;
    color: white !important;
  }
}
</style>
