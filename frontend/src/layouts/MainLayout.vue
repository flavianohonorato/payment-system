<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-white text-primary">
      <q-toolbar>
        <q-toolbar-title class="row items-center no-wrap">
          <q-icon name="shopping_cart" size="28px" class="q-mr-sm accent-color" />
          <span class="text-weight-bold primary-color">Perfect Pay</span>
        </q-toolbar-title>

        <q-space />

        <div class="q-gutter-sm row items-center no-wrap">
          <q-btn flat round dense icon="search" class="primary-color" />
          <q-btn flat round dense icon="person" class="primary-color" />
          <q-btn flat round dense icon="shopping_cart" class="primary-color">
            <q-badge color="accent" floating>2</q-badge>
          </q-btn>
          <q-btn to="/" flat no-caps label="Home" class="desktop-only primary-color" />
          <q-btn to="/payment" flat no-caps label="Checkout" class="desktop-only accent-color" />
        </div>
      </q-toolbar>

      <!-- Menu para dispositivos móveis -->
      <q-toolbar class="mobile-only bg-primary text-white">
        <q-btn flat dense to="/" label="Home" />
        <q-space />
        <q-btn flat dense to="/payment" label="Checkout" />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'MainLayout'
}
</script>

<style lang="scss" scoped>
.desktop-only {
  @media (max-width: 600px) {
    display: none;
  }
}

.mobile-only {
  @media (min-width: 601px) {
    display: none;
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

.bg-primary {
  background-color: #174760 !important;
}

.bg-accent {
  background-color: #E0730E !important;
}

.bg-secondary {
  background-color: #017A7E !important;
}
</style>
