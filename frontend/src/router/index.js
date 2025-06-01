import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '@/views/Login.vue';
import SignupView from '@/views/Signup.vue';
import PrescriptionUploadView from '@/views/customer/PrescriptionUpload.vue';
import CustomerQuotationsView from '@/views/customer/CustomerQuotations.vue';
import PrescriptionView from '@/views/pharmacy/ViewPrescriptions.vue';
import Quotationsview from '@/views/pharmacy/ViewQuotations.vue';
import CreateQuotationView  from '@/views/pharmacy/CreateQuotation.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
    path: '/',
    redirect: '/login',
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
        path:'/signup',
        name:'signup',
        component:SignupView
    },
    {
        path:'/upload-prescription',
        name:'upload-prescription',
        component:PrescriptionUploadView
    },
    {
        path:'/view-quotations',
        name:'view-customer-quotations',
        component:CustomerQuotationsView
    },
    {
        path:'/pharmacy/view-prescriptions',
        name:'view-prescriptions',
        component:PrescriptionView
    },
    {
        path:'/pharmacy/view-quotations',
        name:'view-quotations',
        component:Quotationsview
    },
    {
        path:'/pharmacy/create-quotation/:id',
        name:'create-quotation',
        component:CreateQuotationView
    }
  ],
});

export default router;
