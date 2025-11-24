<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Personas</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Persona
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por cédula, nombres, apellidos, cuenta, teléfono o cargo..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchPersonas"
        />
        <button
          @click="fetchPersonas"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando personas...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="personas.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron personas.</p>
      </div>
    </div>

    <!-- Personas Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cédula</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellidos</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cuenta</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado por</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="persona in personasFiltradas" :key="persona.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ persona.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ persona.cedula }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ persona.nombres }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ persona.apellidos }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ persona.cuenta || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ persona.telefono || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ persona.cargo || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getUsuarioNombreCompleto(persona.usuario_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(persona.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(persona)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(persona)"
                class="text-red-600 hover:text-red-900"
              >
                Eliminar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-4">
            {{ isEditing ? 'Editar Persona' : 'Nueva Persona' }}
          </h2>
          
          <form @submit.prevent="savePersona">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cedula">
                  Cédula *
                </label>
                <input
                  id="cedula"
                  v-model="formData.cedula"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.cedula}"
                  required
                />
                <p v-if="formErrors.cedula" class="text-red-500 text-xs italic mt-1">{{ formErrors.cedula[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombres">
                  Nombres *
                </label>
                <input
                  id="nombres"
                  v-model="formData.nombres"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.nombres}"
                  required
                />
                <p v-if="formErrors.nombres" class="text-red-500 text-xs italic mt-1">{{ formErrors.nombres[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="apellidos">
                  Apellidos *
                </label>
                <input
                  id="apellidos"
                  v-model="formData.apellidos"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.apellidos}"
                  required
                />
                <p v-if="formErrors.apellidos" class="text-red-500 text-xs italic mt-1">{{ formErrors.apellidos[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cuenta">
                  Cuenta
                </label>
                <input
                  id="cuenta"
                  v-model="formData.cuenta"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.cuenta}"
                />
                <p v-if="formErrors.cuenta" class="text-red-500 text-xs italic mt-1">{{ formErrors.cuenta[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="telefono">
                  Teléfono
                </label>
                <input
                  id="telefono"
                  v-model="formData.telefono"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.telefono}"
                />
                <p v-if="formErrors.telefono" class="text-red-500 text-xs italic mt-1">{{ formErrors.telefono[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cargo">
                  Cargo
                </label>
                <input
                  id="cargo"
                  v-model="formData.cargo"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.cargo}"
                />
                <p v-if="formErrors.cargo" class="text-red-500 text-xs italic mt-1">{{ formErrors.cargo[0] }}</p>
              </div>
            </div>

            <!-- Información del usuario que realiza la acción -->
            <div class="mb-4 p-3 bg-blue-50 rounded-md">
              <p class="text-sm text-blue-700">
                <span class="font-semibold">Usuario:</span> {{ currentUser.nombres }} {{ currentUser.apellidos }}
                <span class="text-xs text-blue-600 ml-2">(ID: {{ currentUser.id }})</span>
              </p>
              <p class="text-xs text-blue-600 mt-1">
                Esta persona será {{ isEditing ? 'actualizada' : 'creada' }} bajo su usuario.
              </p>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="saving"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 transition-colors"
              >
                <span v-if="saving">Guardando...</span>
                <span v-else>Guardar</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
          <h2 class="text-xl font-semibold text-red-600 mb-4">Confirmar Eliminación</h2>
          <p class="mb-6">¿Estás seguro de que deseas eliminar a "{{ personaToDelete?.nombres }} {{ personaToDelete?.apellidos }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deletePersona"
              :disabled="deleting"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 disabled:opacity-50 transition-colors"
            >
              <span v-if="deleting">Eliminando...</span>
              <span v-else>Eliminar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

export default {
  name: 'Personas',
  setup() {
    const personas = ref([]);
    const usuarios = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const personaToDelete = ref(null);
    
    // Información del usuario actual
    const currentUser = ref({
      id: null,
      nombres: '',
      apellidos: ''
    });
    
    const formData = ref({
      id: null,
      cedula: '',
      nombres: '',
      apellidos: '',
      cuenta: '',
      telefono: '',
      cargo: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar personas en el frontend
    const personasFiltradas = computed(() => {
      if (!searchQuery.value) {
        return personas.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return personas.value.filter(persona => {
        const usuarioNombre = getUsuarioNombreCompleto(persona.usuario_id).toLowerCase();
        
        return (
          persona.cedula.toLowerCase().includes(query) ||
          persona.nombres.toLowerCase().includes(query) ||
          persona.apellidos.toLowerCase().includes(query) ||
          (persona.cuenta?.toLowerCase().includes(query) || '') ||
          (persona.telefono?.toLowerCase().includes(query) || '') ||
          (persona.cargo?.toLowerCase().includes(query) || '') ||
          usuarioNombre.includes(query)
        );
      });
    });

    // Método para obtener nombre completo del usuario
    const getUsuarioNombreCompleto = (usuarioId) => {
      const usuario = usuarios.value.find(u => u.id === usuarioId);
      if (usuario) {
        return `${usuario.nombres || usuario.nombre || ''} ${usuario.apellidos || usuario.apellido || ''}`.trim();
      }
      return `Usuario ${usuarioId}`;
    };

    // Obtener información del usuario actual
    const fetchCurrentUser = async () => {
      try {
        // Implementa según tu sistema de autenticación
        // Ejemplo con localStorage:
        const userData = localStorage.getItem('user');
        if (userData) {
          const user = JSON.parse(userData);
          currentUser.value = {
            id: user.id,
            nombres: user.nombres || user.nombre || '',
            apellidos: user.apellidos || user.apellido || ''
          };
        } else {
          // Datos de ejemplo - reemplaza con tu implementación real
          currentUser.value = {
            id: 1,
            nombres: 'Admin',
            apellidos: 'Sistema'
          };
        }
        
        console.log('👤 Usuario actual:', currentUser.value);
      } catch (err) {
        console.error('Error al obtener usuario actual:', err);
        // Datos de fallback
        currentUser.value = {
          id: 1,
          nombres: 'Usuario',
          apellidos: 'Sistema'
        };
      }
    };

    const fetchPersonas = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        console.log('🔍 Fetching personas from API...');
        
        const response = await axios.get('/personas');
        
        console.log('📦 Respuesta completa de la API:', response);
        console.log('📊 Datos recibidos:', response.data);
        
        // Diferentes formas en que la API podría devolver los datos
        if (Array.isArray(response.data)) {
          personas.value = response.data;
          console.log('✅ Personas cargadas (array directo):', personas.value.length);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          personas.value = response.data.data;
          console.log('✅ Personas cargadas (con paginación):', personas.value.length);
        } else if (response.data.personas && Array.isArray(response.data.personas)) {
          personas.value = response.data.personas;
          console.log('✅ Personas cargadas (con clave personas):', personas.value.length);
        } else {
          console.warn('❌ Estructura de respuesta desconocida:', response.data);
          personas.value = [];
        }
        
        if (personas.value.length === 0) {
          console.warn('⚠️  El array de personas está vacío');
        } else {
          console.log('🎉 Personas cargadas exitosamente. Primera persona:', personas.value[0]);
        }
        
      } catch (err) {
        console.error('💥 Error al cargar las personas:', err);
        console.error('📞 Detalles del error:', err.response);
        
        error.value = `Error al cargar las personas: ${err.response?.status || 'Sin conexión'}. ${err.response?.data?.message || ''}`;
        
        // Datos de prueba para debugging
        personas.value = [
          {
            id: 1,
            usuario_id: 1,
            cedula: '12345678',
            nombres: 'Juan',
            apellidos: 'Pérez',
            cuenta: 'juan.perez',
            telefono: '0412-1234567',
            cargo: 'Analista',
            created_at: '2025-01-01T00:00:00.000Z',
            updated_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 2,
            usuario_id: 1,
            cedula: '87654321',
            nombres: 'María',
            apellidos: 'González',
            cuenta: 'maria.gonzalez',
            telefono: '0414-7654321',
            cargo: 'Supervisor',
            created_at: '2025-01-01T00:00:00.000Z',
            updated_at: '2025-01-01T00:00:00.000Z'
          }
        ];
        
        console.log('🔄 Usando datos de prueba:', personas.value);
      } finally {
        loading.value = false;
      }
    };

    const fetchUsuarios = async () => {
      try {
        console.log('🔍 Fetching usuarios from API...');
        
        const response = await axios.get('/usuarios');
        
        if (Array.isArray(response.data)) {
          usuarios.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          usuarios.value = response.data.data;
        } else {
          usuarios.value = [];
        }
        
        console.log('✅ Usuarios cargados:', usuarios.value.length);
        
        // Datos de prueba si no hay usuarios
        if (usuarios.value.length === 0) {
          usuarios.value = [
            {
              id: 1,
              nombres: 'Admin',
              apellidos: 'Sistema',
              email: 'admin@sistema.com'
            },
            {
              id: 2,
              nombres: 'Carlos',
              apellidos: 'Rodríguez',
              email: 'carlos@empresa.com'
            }
          ];
          console.log('🔄 Usando usuarios de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar usuarios:', err);
        
        // Datos de prueba
        usuarios.value = [
          {
            id: 1,
            nombres: 'Admin',
            apellidos: 'Sistema',
            email: 'admin@sistema.com'
          },
          {
            id: 2,
            nombres: 'Carlos',
            apellidos: 'Rodríguez', 
            email: 'carlos@empresa.com'
          }
        ];
      }
    };

    const openModal = (persona = null) => {
      if (persona) {
        isEditing.value = true;
        formData.value = {
          id: persona.id,
          cedula: persona.cedula,
          nombres: persona.nombres,
          apellidos: persona.apellidos,
          cuenta: persona.cuenta || '',
          telefono: persona.telefono || '',
          cargo: persona.cargo || ''
        };
      } else {
        isEditing.value = false;
        formData.value = {
          id: null,
          cedula: '',
          nombres: '',
          apellidos: '',
          cuenta: '',
          telefono: '',
          cargo: ''
        };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formData.value = {
        id: null,
        cedula: '',
        nombres: '',
        apellidos: '',
        cuenta: '',
        telefono: '',
        cargo: ''
      };
      formErrors.value = {};
    };

    const savePersona = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        // Preparar los datos para enviar
        const dataToSend = {
          cedula: formData.value.cedula,
          nombres: formData.value.nombres,
          apellidos: formData.value.apellidos,
          cuenta: formData.value.cuenta,
          telefono: formData.value.telefono,
          cargo: formData.value.cargo,
          usuario_id: currentUser.value.id // Se asigna automáticamente
        };

        console.log('📤 Enviando datos:', dataToSend);

        if (isEditing.value) {
          await axios.put(`/personas/${formData.value.id}`, dataToSend);
        } else {
          await axios.post('/personas', dataToSend);
        }
        
        closeModal();
        await fetchPersonas();
        
        console.log('✅ Persona guardada exitosamente');
      } catch (err) {
        console.error('💥 Error al guardar persona:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar la persona. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (persona) => {
      personaToDelete.value = persona;
      showDeleteModal.value = true;
    };

    const deletePersona = async () => {
      if (!personaToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/personas/${personaToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchPersonas();
        
        console.log('✅ Persona eliminada exitosamente');
      } catch (err) {
        console.error('💥 Error al eliminar persona:', err);
        error.value = err.response?.data?.message || 'Error al eliminar la persona. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        personaToDelete.value = null;
      }
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      try {
        const options = { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric'
        };
        return new Date(dateString).toLocaleDateString('es-ES', options);
      } catch (error) {
        return dateString;
      }
    };

    onMounted(() => {
      console.log('🚀 Componente Personas montado');
      fetchCurrentUser();
      fetchPersonas();
      fetchUsuarios();
    });

    return {
      personas,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      personaToDelete,
      currentUser,
      formData,
      formErrors,
      personasFiltradas,
      fetchPersonas,
      openModal,
      closeModal,
      savePersona,
      confirmDelete,
      deletePersona,
      formatDate,
      getUsuarioNombreCompleto
    };
  }
};
</script>