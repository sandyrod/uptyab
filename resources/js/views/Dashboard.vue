<template>
    <div v-if="user && isInitialized" class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-4">
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-600 text-sm mt-2">Bienvenido, {{ user.name }}</p>
            </div>
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <router-link 
                        to="/estados"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'estados'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Estados</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/municipios"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'municipios'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Municipios</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/parroquias"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'parroquias'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Parroquias</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/comunidades"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'comunidades'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Comunidades</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/ubicaciones"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'ubicaciones'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Ubicaciones</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/roles"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'roles'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Roles</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/usuarios"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'usuarios'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Usuarios</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/personas"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'personas'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Personas</span>
                    </router-link>
                </div>
                <div class="px-4 py-2">
                    <router-link 
                        to="/centros-votacion"
                        class="flex items-center w-full px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150 mb-2"
                        :class="{'bg-gray-200': $route.name === 'centros-votacion'}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Centros de Votación</span>
                    </router-link>
                </div>
                <!-- Other navigation items... -->
            </nav>
            <nav class="mt-6">
                <div class="px-4 py-2">
                    <button 
                        @click="handleLogout"
                        class="flex items-center w-full px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:bg-gray-200 transition duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Salir</span>
                    </button>
                </div>
            </nav>
        </div>

        <!-- Contenido principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">
                        <template v-if="$route.name === 'dashboard'">Panel Principal</template>
                        <template v-else-if="$route.name === 'estados'">Gestión de Estados</template>
                        <template v-else-if="$route.name === 'municipios'">Gestión de Municipios</template>
                        <template v-else-if="$route.name === 'parroquias'">Gestión de Parroquias</template>
                        <template v-else-if="$route.name === 'comunidades'">Gestión de Comunidades</template>
                        <template v-else-if="$route.name === 'ubicaciones'">Gestión de Ubicaciones</template>
                        <template v-else-if="$route.name === 'roles'">Gestión de Roles</template>
                        <template v-else-if="$route.name === 'usuarios'">Gestión de Usuarios</template>
                        <template v-else-if="$route.name === 'personas'">Gestión de Personas</template>
                        <template v-else-if="$route.name === 'centros-votacion'">Gestión de Centros de Votación</template>
                        <template v-else>{{ $route.name }}</template>
                    </h2>
                    <img src="/images/cne.svg" alt="Logo CNE" class="h-10 w-auto">
                </div>
            </header>
            
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                <!-- Aquí se renderizarán las vistas hijas -->
                <router-view></router-view>
                
                <!-- Contenido por defecto cuando no hay ruta hija activa -->
                <div v-if="$route.name === 'dashboard'" class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900">Bienvenido al sistema</h3>
                    <p class="mt-2 text-gray-600">
                        Has iniciado sesión correctamente. Aquí puedes ver tus roles:
                    </p>
                    <div class="mt-4">
                        <h4 class="font-medium text-gray-700">Tus roles:</h4>
                        <ul class="mt-2 list-disc list-inside">
                            <li v-for="role in user.roles" :key="role.id" class="text-gray-600">
                                {{ role.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <div v-else class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Cargando...</p>
            <p class="mt-2 text-sm text-gray-500">
                {{ !isInitialized ? 'Inicializando...' : 'Cargando usuario...' }}
            </p>
        </div>
    </div>
</template>

<script>
import { useAuthStore } from '../stores/auth';
import { computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

export default {
    name: 'Dashboard',
    setup() {
        const authStore = useAuthStore();
        const router = useRouter();
        
        const user = computed(() => authStore.user);
        const isInitialized = computed(() => authStore.isInitialized);

        const handleLogout = () => {
            authStore.logout();
            router.push('/login');
        };

        onMounted(() => {
            console.log('Dashboard mounted - Auth state:', {
                user: authStore.user,
                isInitialized: authStore.isInitialized,
                isAuthenticated: authStore.isAuthenticated,
                localStorageToken: localStorage.getItem('auth_token'),
                localStorageUser: localStorage.getItem('user_data')
            });

            if (!authStore.isInitialized) {
                authStore.initialize();
            }

            setTimeout(() => {
                if (!authStore.isAuthenticated) {
                    console.log('Not authenticated, redirecting to login');
                    router.push('/login');
                }
            }, 100);
        });

        return {
            user,
            isInitialized,
            handleLogout
        };
    }
};
</script>