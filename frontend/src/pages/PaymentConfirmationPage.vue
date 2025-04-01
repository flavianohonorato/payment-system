<template>
  <q-page class="q-pa-md">
    <div class="text-h4 q-mb-lg text-center primary-color">Confirmação de Pagamento</div>

    <div class="row justify-center">
      <div class="col-12 col-sm-10 col-md-8">
        <q-banner class="bg-primary text-white q-mb-md">
          <template v-slot:avatar>
            <q-icon name="info" />
          </template>
          Verifique os dados abaixo e clique em "Confirmar Pagamento" para finalizar sua compra.
        </q-banner>

        <q-card class="q-mb-md">
          <q-card-section>
            <div class="text-h5 primary-color">Resumo do Pedido</div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle1 q-mb-md">
              Informações do Cliente
              <q-btn
                flat
                round
                dense
                icon="edit"
                color="accent"
                size="sm"
                class="float-right"
                @click="editCustomerInfo"
              />
            </div>
            <div class="row q-col-gutter-md">
              <div class="col-12 col-md-6">
                <div><strong>Nome:</strong> {{ paymentStore.customer.name }}</div>
                <div><strong>Email:</strong> {{ paymentStore.customer.email }}</div>
              </div>
              <div class="col-12 col-md-6">
                <div><strong>Telefone:</strong> {{ paymentStore.customer.phone }}</div>
                <div><strong>Documento:</strong> {{ paymentStore.customer.document }}</div>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle1 q-mb-md">
              Método de Pagamento
              <q-btn
                flat
                round
                dense
                icon="edit"
                color="accent"
                size="sm"
                class="float-right"
                @click="editPaymentMethod"
              />
            </div>
            <div class="row q-col-gutter-md">
              <div class="col-12">
                <div>
                  <strong>Método:</strong> {{ getPaymentMethodName(paymentStore.paymentMethod) }}
                </div>

                <template v-if="paymentStore.paymentMethod === 'credit'">
                  <div><strong>Número do Cartão:</strong> **** **** **** {{ paymentStore.paymentDetails.cardNumber.slice(-4) }}</div>
                  <div><strong>Nome no Cartão:</strong> {{ paymentStore.paymentDetails.cardholderName }}</div>
                  <div><strong>Validade:</strong> {{ paymentStore.paymentDetails.expiryDate }}</div>
                  <div><strong>Parcelas:</strong> {{ paymentStore.paymentDetails.installments }}x</div>
                </template>

                <template v-if="paymentStore.paymentMethod === 'boleto'">
                  <div><strong>Data de Vencimento:</strong> {{ paymentStore.paymentDetails.dueDate }}</div>
                </template>

                <template v-if="paymentStore.paymentMethod === 'pix'">
                  <div><strong>Pagamento imediato via QR Code ou código PIX</strong></div>
                </template>
              </div>
            </div>
          </q-card-section>

          <q-separator />

          <q-card-section>
            <div class="text-subtitle1 q-mb-md primary-color">Valor Total</div>
            <div class="text-h5 text-accent">R$ 100,00</div>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat color="primary" label="Voltar" @click="goBack" />
            <q-btn
              color="accent"
              text-color="white"
              label="Confirmar Pagamento"
              @click="confirmPayment"
              :loading="loading"
              icon="check_circle"
            />
          </q-card-actions>
        </q-card>
      </div>
    </div>

    <q-dialog v-model="showBoletoDialog" persistent>
      <q-card style="width: 700px; max-width: 90vw;">
        <q-card-section class="row items-center">
          <div class="text-h6 primary-color">Boleto Gerado</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <div v-if="boletoData">
            <div class="text-subtitle1 q-mb-md">Informações do Boleto:</div>

            <div class="q-mb-md">
              <div><strong>Valor:</strong> R$ {{ formatValue(boletoData.value) }}</div>
              <div><strong>Vencimento:</strong> {{ formatDate(boletoData.due_date) }}</div>
              <div><strong>Status:</strong> {{ formatStatus(boletoData.status) }}</div>
            </div>

            <div class="text-center q-my-md">
              <q-btn
                v-if="boletoData.invoice_url"
                color="primary"
                label="Visualizar Fatura"
                icon="receipt"
                @click="openBoleto(boletoData.invoice_url)"
                class="q-mr-md"
              />
              <q-btn
                v-if="boletoData.bank_slip_url"
                color="accent"
                label="Download Boleto PDF"
                icon="download"
                @click="downloadBoleto(boletoData.bank_slip_url)"
              />
            </div>

            <!-- Iframe para visualização inline do boleto -->
            <div v-if="boletoData.bank_slip_url" class="boleto-iframe-container q-mt-md">
              <iframe :src="boletoData.bank_slip_url" frameborder="0" width="100%" height="400px"></iframe>
            </div>
          </div>
          <div v-else-if="loading" class="text-center">
            <q-spinner color="primary" size="3em" />
            <div class="q-mt-sm">Gerando boleto...</div>
          </div>
          <div v-else class="text-negative">
            Ocorreu um erro ao gerar o boleto. Por favor, tente novamente.
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat color="primary" label="Fechar" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="showPixDialog" persistent>
      <q-card style="width: 700px; max-width: 90vw;">
        <q-card-section class="row items-center">
          <div class="text-h6 primary-color">PIX Gerado</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          <div v-if="pixData">
            <div class="text-subtitle1 q-mb-md">Escaneie o QR Code abaixo:</div>

            <div class="row justify-center q-py-md">
              <div v-if="pixData.pix_qr_code" class="text-center">
                <img :src="'data:image/png;base64,' + pixData.pix_qr_code" style="width: 200px; height: 200px" />
              </div>
              <div v-else class="text-center">
                <q-icon name="qr_code" size="200px" color="grey-5" />
                <div class="text-caption q-mt-sm">QR Code não disponível</div>
              </div>
            </div>

            <div class="q-mt-md">
              <div class="text-subtitle2">Código PIX Copia e Cola:</div>
              <q-input
                readonly
                v-model="pixData.pix_copy_paste"
                filled
                type="textarea"
                autogrow
              >
                <template v-slot:append>
                  <q-btn
                    round
                    dense
                    flat
                    icon="content_copy"
                    @click="copyPixCode(pixData.pix_copy_paste)"
                  />
                </template>
              </q-input>
            </div>

            <div class="text-subtitle2 q-mt-md">Valor:</div>
            <div class="text-h6 text-accent">R$ {{ formatValue(pixData.value) }}</div>

            <div class="q-mt-md text-body2 text-grey-8">
              O pagamento será confirmado automaticamente após a transferência PIX.
            </div>
          </div>
          <div v-else-if="loading" class="text-center q-py-xl">
            <q-spinner color="primary" size="3em" />
            <div class="q-mt-sm">Gerando PIX...</div>
          </div>
          <div v-else class="text-negative text-center q-py-md">
            Ocorreu um erro ao gerar o PIX. Por favor, tente novamente.
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat color="primary" label="Fechar" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <error-dialog
      :visible="!!error"
      :title="'Erro ao processar pagamento'"
      :message="errorMessage"
      :details="errorDetails"
      @update:visible="error = null"
      @retry="confirmPayment"
    />
  </q-page>
</template>

<script>
import { usePaymentStore } from 'src/stores/paymentStore'
import paymentService from 'src/services/paymentService'
import ErrorDialog from 'components/ErrorDialog.vue'
import useNotification from 'src/composables/useNotification'

export default {
  name: 'PaymentConfirmationPage',
  components: {
    ErrorDialog
  },
  setup() {
    try {
      const notification = useNotification()
      return { notification }
    } catch (error) {
      console.error('Erro ao inicializar notificações:', error)
      return {
        notification: {
          showSuccess: (msg) => console.log('Sucesso:', msg),
          showError: (msg) => console.error('Erro:', msg),
          showInfo: (msg) => console.info('Info:', msg),
          showWarning: (msg) => console.warn('Aviso:', msg)
        }
      }
    }
  },
  data() {
    return {
      loading: false,
      error: null,
      errorDetails: null,
      showBoletoDialog: false,
      showPixDialog: false,
      boletoData: null,
      pixData: null
    }
  },
  computed: {
    paymentStore() {
      return usePaymentStore()
    },
    errorMessage() {
      if (!this.error) return '';

      if (this.errorDetails && this.errorDetails.includes('Integrity constraint violation')) {
        if (this.errorDetails.includes("Column 'customer_id' cannot be null")) {
          return 'Não foi possível associar o cliente no sistema. Por favor, tente novamente mais tarde ou entre em contato com o suporte.';
        }
      }

      return this.error;
    }
  },
  methods: {
    getPaymentMethodName(method) {
      const methods = {
        credit: 'Cartão de Crédito',
        boleto: 'Boleto Bancário',
        pix: 'Pix'
      }
      return methods[method] || method
    },
    goBack() {
      this.$router.push('/payment')
    },
    editCustomerInfo() {
      this.$router.push('/payment?step=1')
    },
    editPaymentMethod() {
      this.$router.push('/payment?step=2')
    },
    async confirmPayment() {
      this.loading = true
      this.error = null
      this.errorDetails = null

      try {
        const billingTypeMap = {
          'credit': 'CREDIT_CARD',
          'boleto': 'BOLETO',
          'pix': 'PIX'
        }

        if (!this.paymentStore.isCustomerInfoComplete) {
          throw new Error('Dados do cliente incompletos. Por favor, retorne e preencha todos os campos.')
        }

        const cleanDocument = this.paymentStore.customer.document.replace(/[^0-9]/g, '')
        if (!cleanDocument) {
          throw new Error('CPF/CNPJ é obrigatório')
        }

        const paymentData = {
          customer: {
            name: this.paymentStore.customer.name,
            email: this.paymentStore.customer.email,
            cpf_cnpj: cleanDocument
          },
          billing_type: billingTypeMap[this.paymentStore.paymentMethod],
          value: 100.00,
          description: "Pagamento de produto na Perfect Pay",
          due_date: this.formatDueDate(this.paymentStore.paymentDetails?.dueDate)
        }

        if (this.paymentStore.paymentMethod === 'credit') {
          const expiryParts = this.paymentStore.paymentDetails.expiryDate.split('/')
          paymentData.card_data = {
            holderName: this.paymentStore.paymentDetails.cardholderName,
            number: this.paymentStore.paymentDetails.cardNumber.replace(/\s/g, ''),
            expiryMonth: expiryParts[0],
            expiryYear: `20${expiryParts[1]}`,
            ccv: this.paymentStore.paymentDetails.cvv
          }
        }

        try {
          this.notification.showInfo('Processando pagamento...')
        } catch (e) {
          console.warn('Falha ao mostrar notificação:', e)
          alert('Processando pagamento...')
        }

        const response = await paymentService.processPayment(paymentData)

        console.log('Resposta da API:', response.data)

        if (this.paymentStore.paymentMethod === 'boleto' && response.data && response.data.success) {
          this.boletoData = response.data.data;
          this.showBoletoDialog = true

          try {
            this.notification.showSuccess('Boleto gerado com sucesso!')
          } catch (e) {
            console.warn('Falha ao mostrar notificação:', e)
          }
          return
        } else if (this.paymentStore.paymentMethod === 'pix' && response.data && response.data.success) {
          this.pixData = response.data.data;
          this.showPixDialog = true

          try {
            this.notification.showSuccess('PIX gerado com sucesso!')
          } catch (e) {
            console.warn('Falha ao mostrar notificação:', e)
          }
          return
        }

        const paymentId = response.data.data?.id || response.data.id || 'success'

        this.paymentStore.resetPayment()

        this.$router.push(`/thank-you/${paymentId}`)
      } catch (err) {
        console.error('Erro ao processar pagamento:', err);
        this.errorDetails = err.response?.data?.error || JSON.stringify(err.response?.data) || err.message;
        this.error = err.response?.data?.message || err.message || 'Erro ao processar pagamento. Tente novamente.';

        try {
          this.notification.showError(this.error, 'Verifique os dados e tente novamente')
        } catch (e) {
          console.warn('Falha ao mostrar notificação de erro:', e)
          alert('Erro: ' + this.error)
        }
      } finally {
        this.loading = false
      }
    },
    formatDueDate(dateStr) {
      if (!dateStr) return ''
      if (/^\d{4}-\d{2}-\d{2}$/.test(dateStr)) return dateStr
      if (/^\d{2}\/\d{2}\/\d{4}$/.test(dateStr)) {
        const parts = dateStr.split('/')
        return `${parts[2]}-${parts[1]}-${parts[0]}`
      }
      const date = new Date()
      date.setDate(date.getDate() + 3)
      return date.toISOString().split('T')[0]
    },
    formatValue(value) {
      return parseFloat(value).toFixed(2).replace('.', ',');
    },
    formatDate(dateString) {
      if (!dateString) return '';

      try {
        // Remove a parte de timezone se existir
        if (dateString.includes('T')) {
          dateString = dateString.split('T')[0];
        }

        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
          return dateString;
        }
        return date.toLocaleDateString('pt-BR');
      } catch (e) {
        console.error('Erro ao formatar data:', e);
        return dateString;
      }
    },
    formatStatus(status) {
      const statusMap = {
        'PENDING':    'Pendente',
        'CONFIRMED':  'Confirmado',
        'RECEIVED':   'Recebido',
        'OVERDUE':    'Vencido',
        'REFUNDED':   'Reembolsado',
        'CANCELLED':  'Cancelado'
      };
      return statusMap[status] || status;
    },
    openBoleto(url) {
      window.open(url, '_blank');
    },
    downloadBoleto(url) {
      window.open(url, '_blank');
    },
    copyPixCode(code) {
      if (!code) return;

      navigator.clipboard.writeText(code)
        .then(() => {
          try {
            this.notification.showSuccess('Código PIX copiado!')
          } catch (e) {
            console.warn('Falha ao mostrar notificação:', e)
            alert('Código PIX copiado!')
          }
        })
        .catch(err => {
          console.error('Erro ao copiar o código:', err)
          try {
            this.notification.showError('Não foi possível copiar o código')
          } catch (e) {
            console.warn('Falha ao mostrar notificação:', e)
            alert('Não foi possível copiar o código')
          }
        })
    }
  },
  created() {
    if (!this.paymentStore.paymentMethod || !this.paymentStore.customer.name) {
      this.$router.push('/payment')
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

.secondary-color {
  color: #017A7E !important;
}

.barcode-number {
  font-family: monospace;
  font-size: 1.1em;
  word-break: break-all;
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 4px;
  margin-top: 5px;
}

.boleto-iframe-container {
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
}
</style>
