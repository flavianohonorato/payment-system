<template>
  <q-dialog v-model="isVisible" persistent>
    <q-card style="min-width: 350px">
      <q-card-section class="bg-negative text-white">
        <div class="text-h6">
          <q-icon name="error" class="q-mr-sm" />
          {{ title }}
        </div>
      </q-card-section>

      <q-card-section class="q-pt-md">
        <div class="text-body1">{{ message }}</div>

        <div v-if="details" class="q-mt-md">
          <q-expansion-item
            switch-toggle-side
            dense
            label="Detalhes técnicos"
            header-class="text-grey-8"
          >
            <q-card>
              <q-card-section class="q-pa-sm">
                <pre class="error-details">{{ details }}</pre>
              </q-card-section>
            </q-card>
          </q-expansion-item>
        </div>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn flat label="Fechar" color="grey-7" v-close-popup />
        <q-btn
          v-if="showRetry"
          color="primary"
          label="Tentar Novamente"
          @click="$emit('retry'); isVisible = false"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'ErrorDialog',
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Erro no Processamento'
    },
    message: {
      type: String,
      default: 'Não foi possível processar sua solicitação.'
    },
    details: {
      type: String,
      default: ''
    },
    showRetry: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      isVisible: this.visible
    }
  },
  watch: {
    visible(newVal) {
      this.isVisible = newVal;
    },
    isVisible(newVal) {
      if (!newVal) {
        this.$emit('update:visible', false);
      }
    }
  }
}
</script>

<style scoped>
.error-details {
  white-space: pre-wrap;
  word-break: break-word;
  max-height: 200px;
  overflow-y: auto;
  background: #f8f8f8;
  padding: 8px;
  border-radius: 4px;
  font-size: 12px;
  color: #d32f2f;
  border-left: 3px solid #d32f2f;
  margin: 0;
}
</style>
