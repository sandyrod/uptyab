import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
        isInitialized: false
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        getUser: (state) => state.user,
    },

    actions: {
        async login(credentials) {
            try {
                console.log('Sending login request with:', credentials);
                const response = await axios.post('/usuarios/login', credentials);
                
                console.log('Full API response:', response);
                console.log('Response data:', response.data);
                console.log('User in response:', response.data.usuario);
                console.log('Token in response:', response.data.token);
                
                if (response.data.token) {
                    this.token = response.data.token;
                    this.user = response.data.usuario;
                    if (this.user === undefined) {
                        console.warn('USER IS UNDEFINED - checking response structure');
                        console.log('All keys in response.data:', Object.keys(response.data));
                    }
                    // Verificar que el usuario no sea undefined
                    if (!this.user) {
                        console.error('User data is undefined in API response');
                        throw new Error('No se recibieron datos del usuario');
                    }
                    
                    // Guardar en localStorage
                    localStorage.setItem('auth_token', this.token);
                    localStorage.setItem('user_data', JSON.stringify(this.user));
                    
                    console.log('Login successful - User saved:', this.user);
                    return response;
                }
            } catch (error) {
                console.error('Login error:', error);
                throw error;
            }
        },

        logout() {
            console.log('Logging out');
            this.token = null;
            this.user = null;
            this.isInitialized = false;
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_data');
        },

        initialize() {
            if (this.isInitialized) {
                console.log('Store already initialized');
                return;
            }
            
            const token = localStorage.getItem('auth_token');
            const userData = localStorage.getItem('user_data');
            
            console.log('Initializing store with:', { token, userData });
            
            if (token && userData) {
                try {
                    // Verificar si userData es la cadena "undefined"
                    if (userData === "undefined") {
                        console.error('Invalid user data: "undefined" string found');
                        this.logout();
                        return;
                    }
                    
                    this.token = token;
                    this.user = JSON.parse(userData);
                    console.log('Store initialized successfully - User:', this.user);
                } catch (error) {
                    console.error('Error parsing user data:', error);
                    this.logout();
                }
            } else {
                console.log('No auth data found in localStorage');
            }
            
            this.isInitialized = true;
        }
    },
});