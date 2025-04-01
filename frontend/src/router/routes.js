const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue') },
      { path: 'payment', component: () => import('pages/PaymentPage.vue') },
      { path: 'payment/confirmation', component: () => import('pages/PaymentConfirmationPage.vue') },
      { path: 'thank-you/:paymentId', component: () => import('pages/ThankYouPage.vue') }
    ]
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
