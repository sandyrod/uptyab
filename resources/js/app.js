import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';
import App from './App.vue';
import Login from './views/Login.vue';
import Dashboard from './views/Dashboard.vue';
import Estados from './views/Estados.vue';
import Municipios from './views/Municipios.vue';
import Comunidades from './views/Comunidades.vue';
import Parroquias from './views/Parroquias.vue';
import Ubicaciones from './views/Ubicaciones.vue';
import Roles from './views/Roles.vue';
import Usuarios from './views/Usuarios.vue';
import Personas from './views/Personas.vue';
import CentrosVotacion from './views/CentroVotacion.vue';
import Eventos from './views/Eventos.vue';
import Afluencias from './views/Afluencias.vue';
import Comunas from './views/Comunas.vue';
import Partidos from './views/Partidos.vue';
import Consejos from './views/Consejos.vue';
import Proyectos from './views/Proyectos.vue';

import { useAuthStore } from './stores/auth';
import axios from 'axios';

// Configuración de Axios
axios.defaults.withCredentials = true;
axios.defaults.baseURL = '/api';

// Configuración del Router
const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/login',
            name: 'login',
            component: Login,
            meta: { requiresAuth: false }
        },
        {
            path: '/',
            name: 'dashboard',
            component: Dashboard,
            meta: { requiresAuth: true },
            children: [
                {
                    path: 'estados',
                    name: 'estados',
                    component: Estados
                },
                {
                    path: 'municipios',
                    name: 'municipios',
                    component: Municipios
                },
                {
                    path: 'parroquias',
                    name: 'parroquias',
                    component: Parroquias
                },
                {
                    path: 'comunidades',
                    name: 'comunidades',
                    component: Comunidades
                },
                {
                    path: 'ubicaciones',
                    name: 'ubicaciones',
                    component: Ubicaciones
                },
                {
                    path: 'roles',
                    name: 'roles',
                    component: Roles
                },
                {
                    path: 'usuarios',
                    name: 'usuarios',
                    component: Usuarios
                },
                {
                    path: 'personas',
                    name: 'personas',
                    component: Personas
                },
                {
                    path: 'centros-votacion',
                    name: 'centros-votacion',
                    component: CentrosVotacion
                },
                {
                    path: 'eventos',
                    name: 'eventos',
                    component: Eventos
                },
                {
                    path: 'afluencias',
                    name: 'afluencias',
                    component: Afluencias
                },
                {
                    path: 'comunas',
                    name: 'comunas',
                    component: Comunas
                },
                {
                    path: 'partidos',
                    name: 'partidos',
                    component: Partidos
                },
                {
                    path: 'consejos',
                    name: 'consejos',
                    component: Consejos
                },
                {
                    path: 'proyectos',
                    name: 'proyectos',
                    component: Proyectos
                },

            ]
        },
        {
            path: '/dashboard',
            redirect: '/'
        }
    ]
});

// Crear la aplicación
const app = createApp(App);
const pinia = createPinia();

// Usar plugins
app.use(pinia);
app.use(router);

// Inicializar el store de autenticación
const authStore = useAuthStore();
authStore.initialize();

// Guardia de navegación
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.name === 'login' && authStore.isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

// Interceptor para agregar el token a las peticiones
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

// Interceptor de respuestas para manejar tokens expirados
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            authStore.logout();
            router.push('/login');
        }
        return Promise.reject(error);
    }
);

// Montar la aplicación
app.mount('#app');