<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="flex justify-center">
                <img src="/images/cne.svg" alt="Logo CNE" class="h-24 w-auto">
            </div>
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Iniciar Sesión
                </h2>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="cedula" class="sr-only">Cedula</label>
                        <input 
                            id="cedula" 
                            name="cedula" 
                            type="text" 
                            autocomplete="cedula" 
                            required 
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="cedula"
                            v-model="form.cedula"
                        >
                    </div>
                    <div>
                        <label for="clave" class="sr-only">Contraseña</label>
                        <input 
                            id="clave" 
                            name="clave" 
                            type="password" 
                            autocomplete="current-clave" 
                            required 
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Contraseña"
                            v-model="form.clave"
                        >
                    </div>
                </div>

                <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ error }}
                </div>

                <div>
                    <button 
                        type="submit" 
                        :disabled="isLoading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        <span v-if="isLoading">Iniciando sesión...</span>
                        <span v-else>Iniciar Sesión</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

export default {
    name: 'Login',
    setup() {
        const router = useRouter();
        const authStore = useAuthStore();
        const form = ref({
            cedula: '',
            clave: ''
        });
        const isLoading = ref(false);
        const error = ref('');

        onMounted(() => {
            if (authStore.isAuthenticated) {
                router.push('/dashboard');
            }
        });

        const handleLogin = async () => {
            isLoading.value = true;
            error.value = '';
            
            try {
                const response = await authStore.login(form.value);
                
                // Verificar explícitamente que el usuario se guardó
                if (!authStore.user) {
                    throw new Error('No se pudieron cargar los datos del usuario');
                }
                
                console.log('Login completed, redirecting to dashboard');
                router.push({ name: 'dashboard' });
                
            } catch (err) {
                console.error('Login component error:', err);
                error.value = err.response?.data?.message || err.message || 'Error al iniciar sesión';
                
                // Si hay un problema con los datos, limpiar el localStorage
                if (err.message.includes('datos del usuario')) {
                    authStore.logout();
                }
            } finally {
                isLoading.value = false;
            }
        };

        return {
            form,
            isLoading,
            error,
            handleLogin
        };
    }
};
</script>