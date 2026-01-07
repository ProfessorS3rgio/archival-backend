import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: () => import('../views/Auth/Login.vue'),
      meta: {
        title: 'Login',
      },
    },
    {
      path: '/',
      redirect: '/dashboard',
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: {
        title: 'Dashboard',
      },
    },
    {
      path: '/ordinance',
      name: 'Ordinance',
      component: () => import('../views/Archival/Ordinance.vue'),
      meta: {
        title: 'Ordinance',
      },
    },
    {
      path: '/resolution',
      name: 'Resolution',
      component: () => import('../views/Archival/Resolution.vue'),
      meta: {
        title: 'Resolution',
      },
    },
    {
      path: '/draft-ordinance',
      name: 'Draft Ordinance',
      component: () => import('../views/Archival/DraftOrdinance.vue'),
      meta: {
        title: 'Draft Ordinance',
      },
    },
    {
      path: '/draft-resolution',
      name: 'Draft Resolution',
      component: () => import('../views/Archival/DraftResolution.vue'),
      meta: {
        title: 'Draft Resolution',
      },
    },
    {
      path: '/municipalities-resolution',
      name: 'Municipalities Resolution',
      component: () => import('../views/Archival/MunicipalitiesResolution.vue'),
      meta: {
        title: 'Municipalities Resolution',
      },
    },
    {
      path: '/form-elements',
      name: 'Form Elements',
      component: () => import('../views/Forms/FormElements.vue'),
      meta: {
        title: 'Form Elements',
      },
    },
    {
      path: '/basic-tables',
      name: 'Basic Tables',
      component: () => import('../views/Tables/BasicTables.vue'),
      meta: {
        title: 'Basic Tables',
      },
    },
  ],
})

export default router

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.name !== 'Login' && !token) {
    next({ name: 'Login' })
  } else {
    document.title = `${to.meta.title} | Archival System`
    next()
  }
})
