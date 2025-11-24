<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Roles</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Rol
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex space-x-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar por rol o descripción..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @keyup.enter="fetchRoles"
          />
        </div>
        <div class="flex space-x-2">
          <select
            v-model="estadoFilter"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todos los estados</option>
            <option value="true">Activo</option>
            <option value="false">Inactivo</option>
          </select>
          <button
            @click="fetchRoles"
            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
          >
            Buscar
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando roles...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="roles.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron roles.</p>
      </div>
    </div>

    <!-- Roles Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel Acceso</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="rol in rolesFiltrados" :key="rol.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rol.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ rol.rol }}</td>
            <td class="px-6 py-4 text-sm text-gray-500">{{ rol.descripcion || 'Sin descripción' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ rol.nivel_acceso }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  rol.estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
              >
                {{ rol.estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(rol.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(rol)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(rol)"
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
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-4">
            {{ isEditing ? 'Editar Rol' : 'Nuevo Rol' }}
          </h2>
          
          <form @submit.prevent="saveRol">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="rol">
                Rol *
              </label>
              <input
                id="rol"
                v-model="formData.rol"
                type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.rol}"
                required
                placeholder="Ej: Administrador, Usuario, etc."
              />
              <p v-if="formErrors.rol" class="text-red-500 text-xs italic mt-1">{{ formErrors.rol[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="descripcion">
                Descripción
              </label>
              <textarea
                id="descripcion"
                v-model="formData.descripcion"
                rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.descripcion}"
                placeholder="Descripción del rol y sus permisos..."
              ></textarea>
              <p v-if="formErrors.descripcion" class="text-red-500 text-xs italic mt-1">{{ formErrors.descripcion[0] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nivel_acceso">
                  Nivel de Acceso *
                </label>
                <input
                  id="nivel_acceso"
                  v-model.number="formData.nivel_acceso"
                  type="number"
                  min="0"
                  max="999"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.nivel_acceso}"
                  required
                />
                <p v-if="formErrors.nivel_acceso" class="text-red-500 text-xs italic mt-1">{{ formErrors.nivel_acceso[0] }}</p>
              </div>

              <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="estado">
                  Estado
                </label>
                <div class="mt-2">
                  <label class="inline-flex items-center">
                    <input
                      type="checkbox"
                      v-model="formData.estado"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-700">Activo</span>
                  </label>
                </div>
              </div>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el rol "{{ rolToDelete?.rol }}"?</p>
          <p class="mb-6 text-sm text-gray-600">Esta acción no se puede deshacer.</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteRol"
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
  name: 'Roles',
  setup() {
    const roles = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const estadoFilter = ref('');
    const rolToDelete = ref(null);
    
    const formData = ref({
      id: null,
      rol: '',
      descripcion: '',
      nivel_acceso: 1,
      estado: true
    });
    
    const formErrors = ref({});

    // Computed para filtrar roles en el frontend
    const rolesFiltrados = computed(() => {
      let filtered = roles.value;

      // Filtrar por búsqueda de texto
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(rol => 
          rol.rol.toLowerCase().includes(query) ||
          (rol.descripcion?.toLowerCase().includes(query) || '')
        );
      }

      // Filtrar por estado
      if (estadoFilter.value !== '') {
        filtered = filtered.filter(rol => rol.estado === (estadoFilter.value === 'true'));
      }

      return filtered;
    });

    const fetchRoles = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        console.log('🔍 Fetching roles from API...');
        
        const response = await axios.get('/roles');
        
        console.log('📦 Respuesta completa de la API:', response);
        console.log('📊 Datos recibidos:', response.data);
        
        // Diferentes formas en que la API podría devolver los datos
        if (Array.isArray(response.data)) {
          roles.value = response.data;
          console.log('✅ Roles cargados (array directo):', roles.value.length);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          roles.value = response.data.data;
          console.log('✅ Roles cargados (con paginación):', roles.value.length);
        } else if (response.data.roles && Array.isArray(response.data.roles)) {
          roles.value = response.data.roles;
          console.log('✅ Roles cargados (con clave roles):', roles.value.length);
        } else {
          console.warn('❌ Estructura de respuesta desconocida:', response.data);
          roles.value = [];
        }
        
        if (roles.value.length === 0) {
          console.warn('⚠️  El array de roles está vacío');
        } else {
          console.log('🎉 Roles cargados exitosamente. Primer rol:', roles.value[0]);
        }
        
      } catch (err) {
        console.error('💥 Error al cargar los roles:', err);
        console.error('📞 Detalles del error:', err.response);
        
        error.value = `Error al cargar los roles: ${err.response?.status || 'Sin conexión'}. ${err.response?.data?.message || ''}`;
        
        // Datos de prueba para debugging
        roles.value = [
          {
            id: 1,
            rol: 'Administrador',
            descripcion: 'Acceso completo al sistema',
            nivel_acceso: 100,
            estado: true,
            created_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 2,
            rol: 'Usuario', 
            descripcion: 'Acceso básico al sistema',
            nivel_acceso: 10,
            estado: true,
            created_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 3,
            rol: 'Invitado',
            descripcion: 'Acceso limitado de solo lectura',
            nivel_acceso: 1,
            estado: false,
            created_at: '2025-01-01T00:00:00.000Z'
          }
        ];
        
        console.log('🔄 Usando datos de prueba:', roles.value);
      } finally {
        loading.value = false;
      }
    };

    const openModal = (rol = null) => {
      if (rol) {
        isEditing.value = true;
        formData.value = {
          id: rol.id,
          rol: rol.rol,
          descripcion: rol.descripcion || '',
          nivel_acceso: rol.nivel_acceso,
          estado: rol.estado
        };
      } else {
        isEditing.value = false;
        formData.value = {
          id: null,
          rol: '',
          descripcion: '',
          nivel_acceso: 1,
          estado: true
        };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formData.value = {
        id: null,
        rol: '',
        descripcion: '',
        nivel_acceso: 1,
        estado: true
      };
      formErrors.value = {};
    };

    const saveRol = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          await axios.put(`/roles/${formData.value.id}`, {
            rol: formData.value.rol,
            descripcion: formData.value.descripcion,
            nivel_acceso: formData.value.nivel_acceso,
            estado: formData.value.estado
          });
        } else {
          await axios.post('/roles', {
            rol: formData.value.rol,
            descripcion: formData.value.descripcion,
            nivel_acceso: formData.value.nivel_acceso,
            estado: formData.value.estado
          });
        }
        
        closeModal();
        await fetchRoles();
        
        console.log('✅ Rol guardado exitosamente');
      } catch (err) {
        console.error('💥 Error al guardar rol:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el rol. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (rol) => {
      rolToDelete.value = rol;
      showDeleteModal.value = true;
    };

    const deleteRol = async () => {
      if (!rolToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/roles/${rolToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchRoles();
        
        console.log('✅ Rol eliminado exitosamente');
      } catch (err) {
        console.error('💥 Error al eliminar rol:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el rol. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        rolToDelete.value = null;
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
      console.log('🚀 Componente Roles montado');
      fetchRoles();
    });

    return {
      roles,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      estadoFilter,
      rolToDelete,
      formData,
      formErrors,
      rolesFiltrados,
      fetchRoles,
      openModal,
      closeModal,
      saveRol,
      confirmDelete,
      deleteRol,
      formatDate
    };
  }
};
</script>