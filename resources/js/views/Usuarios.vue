<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Usuario
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex space-x-4">
        <div class="flex-1">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Buscar por cédula, nombres, apellidos o email..."
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            @keyup.enter="fetchUsuarios"
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
          <select
            v-model="rolFilter"
            class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          >
            <option value="">Todos los roles</option>
            <option v-for="rol in roles" :key="rol.id" :value="rol.id">
              {{ rol.rol }}
            </option>
          </select>
          <button
            @click="fetchUsuarios"
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
      <p class="mt-2 text-gray-600">Cargando usuarios...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="usuarios.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron usuarios.</p>
      </div>
    </div>

    <!-- Usuarios Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cédula</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombres</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellidos</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="usuario in usuariosFiltrados" :key="usuario.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ usuario.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ usuario.cedula }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ usuario.nombres }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ usuario.apellidos }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ usuario.email }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ usuario.telefono || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getRolNombre(usuario.rol_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  usuario.estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]"
              >
                {{ usuario.estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(usuario.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(usuario)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(usuario)"
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
            {{ isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
          </h2>
          
          <form @submit.prevent="saveUsuario">
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
                  placeholder="Ej: V-12345678"
                />
                <p v-if="formErrors.cedula" class="text-red-500 text-xs italic mt-1">{{ formErrors.cedula[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rol_id">
                  Rol *
                </label>
                <select
                  id="rol_id"
                  v-model="formData.rol_id"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.rol_id}"
                  required
                >
                  <option value="">Seleccione un rol</option>
                  <option v-for="rol in roles" :key="rol.id" :value="rol.id">
                    {{ rol.rol }} (Nivel: {{ rol.nivel_acceso }})
                  </option>
                </select>
                <p v-if="formErrors.rol_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.rol_id[0] }}</p>
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                  Email *
                </label>
                <input
                  id="email"
                  v-model="formData.email"
                  type="email"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.email}"
                  required
                />
                <p v-if="formErrors.email" class="text-red-500 text-xs italic mt-1">{{ formErrors.email[0] }}</p>
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
                  placeholder="Ej: 0412-1234567"
                />
                <p v-if="formErrors.telefono" class="text-red-500 text-xs italic mt-1">{{ formErrors.telefono[0] }}</p>
              </div>
            </div>

            <!-- Solo mostrar campo de contraseña en creación -->
            <div v-if="!isEditing" class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Contraseña *
              </label>
              <input
                id="password"
                v-model="formData.password"
                type="password"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.password}"
                required
                placeholder="Mínimo 8 caracteres"
              />
              <p v-if="formErrors.password" class="text-red-500 text-xs italic mt-1">{{ formErrors.password[0] }}</p>
            </div>

            <div class="mb-4">
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
                  <span class="ml-2 text-sm text-gray-700">Usuario activo</span>
                </label>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar al usuario "{{ usuarioToDelete?.nombres }} {{ usuarioToDelete?.apellidos }}"?</p>
          <p class="mb-6 text-sm text-gray-600">Esta acción no se puede deshacer.</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteUsuario"
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
  name: 'Usuarios',
  setup() {
    const usuarios = ref([]);
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
    const rolFilter = ref('');
    const usuarioToDelete = ref(null);
    
    const formData = ref({
      id: null,
      cedula: '',
      nombres: '',
      apellidos: '',
      email: '',
      telefono: '',
      rol_id: '',
      password: '',
      estado: true
    });
    
    const formErrors = ref({});

    // Computed para filtrar usuarios en el frontend
    const usuariosFiltrados = computed(() => {
      let filtered = usuarios.value;

      // Filtrar por búsqueda de texto
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(usuario => 
          usuario.cedula.toLowerCase().includes(query) ||
          usuario.nombres.toLowerCase().includes(query) ||
          usuario.apellidos.toLowerCase().includes(query) ||
          usuario.email.toLowerCase().includes(query) ||
          (usuario.telefono?.toLowerCase().includes(query) || '')
        );
      }

      // Filtrar por estado
      if (estadoFilter.value !== '') {
        filtered = filtered.filter(usuario => usuario.estado === (estadoFilter.value === 'true'));
      }

      // Filtrar por rol
      if (rolFilter.value !== '') {
        filtered = filtered.filter(usuario => usuario.rol_id.toString() === rolFilter.value);
      }

      return filtered;
    });

    // Método para obtener nombre del rol
    const getRolNombre = (rolId) => {
      const rol = roles.value.find(r => r.id === rolId);
      return rol?.rol || 'N/A';
    };

    const fetchUsuarios = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        console.log('🔍 Fetching usuarios from API...');
        
        const response = await axios.get('/usuarios');
        
        console.log('📦 Respuesta completa de la API:', response);
        console.log('📊 Datos recibidos:', response.data);
        
        // Diferentes formas en que la API podría devolver los datos
        if (Array.isArray(response.data)) {
          usuarios.value = response.data;
          console.log('✅ Usuarios cargados (array directo):', usuarios.value.length);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          usuarios.value = response.data.data;
          console.log('✅ Usuarios cargados (con paginación):', usuarios.value.length);
        } else if (response.data.usuarios && Array.isArray(response.data.usuarios)) {
          usuarios.value = response.data.usuarios;
          console.log('✅ Usuarios cargados (con clave usuarios):', usuarios.value.length);
        } else {
          console.warn('❌ Estructura de respuesta desconocida:', response.data);
          usuarios.value = [];
        }
        
        if (usuarios.value.length === 0) {
          console.warn('⚠️  El array de usuarios está vacío');
        } else {
          console.log('🎉 Usuarios cargados exitosamente. Primer usuario:', usuarios.value[0]);
        }
        
      } catch (err) {
        console.error('💥 Error al cargar los usuarios:', err);
        console.error('📞 Detalles del error:', err.response);
        
        error.value = `Error al cargar los usuarios: ${err.response?.status || 'Sin conexión'}. ${err.response?.data?.message || ''}`;
        
        // Datos de prueba para debugging
        usuarios.value = [
          {
            id: 1,
            cedula: 'V-12345678',
            nombres: 'María',
            apellidos: 'González',
            email: 'maria@example.com',
            telefono: '0412-1234567',
            rol_id: 1,
            estado: true,
            created_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 2,
            cedula: 'V-87654321',
            nombres: 'Carlos',
            apellidos: 'Rodríguez',
            email: 'carlos@example.com',
            telefono: '0414-7654321',
            rol_id: 2,
            estado: true,
            created_at: '2025-01-01T00:00:00.000Z'
          }
        ];
        
        console.log('🔄 Usando datos de prueba:', usuarios.value);
      } finally {
        loading.value = false;
      }
    };

    const fetchRoles = async () => {
      try {
        console.log('🔍 Fetching roles from API...');
        
        const response = await axios.get('/roles');
        
        console.log('📦 Respuesta de roles:', response);
        
        if (Array.isArray(response.data)) {
          roles.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          roles.value = response.data.data;
        } else {
          roles.value = [];
        }
        
        console.log('✅ Roles cargados:', roles.value.length);
        
        // Datos de prueba si no hay roles
        if (roles.value.length === 0) {
          roles.value = [
            {
              id: 1,
              rol: 'Administrador',
              nivel_acceso: 100
            },
            {
              id: 2,
              rol: 'Usuario',
              nivel_acceso: 10
            }
          ];
          console.log('🔄 Usando roles de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar roles:', err);
        
        // Datos de prueba
        roles.value = [
          {
            id: 1,
            rol: 'Administrador',
            nivel_acceso: 100
          },
          {
            id: 2, 
            rol: 'Usuario',
            nivel_acceso: 10
          }
        ];
      }
    };

    const openModal = (usuario = null) => {
      if (usuario) {
        isEditing.value = true;
        formData.value = {
          id: usuario.id,
          cedula: usuario.cedula,
          nombres: usuario.nombres,
          apellidos: usuario.apellidos,
          email: usuario.email,
          telefono: usuario.telefono || '',
          rol_id: usuario.rol_id,
          password: '', // No mostramos la contraseña en edición
          estado: usuario.estado
        };
      } else {
        isEditing.value = false;
        formData.value = {
          id: null,
          cedula: '',
          nombres: '',
          apellidos: '',
          email: '',
          telefono: '',
          rol_id: '',
          password: '',
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
        cedula: '',
        nombres: '',
        apellidos: '',
        email: '',
        telefono: '',
        rol_id: '',
        password: '',
        estado: true
      };
      formErrors.value = {};
    };

    const saveUsuario = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        const dataToSend = {
          cedula: formData.value.cedula,
          nombres: formData.value.nombres,
          apellidos: formData.value.apellidos,
          email: formData.value.email,
          telefono: formData.value.telefono,
          rol_id: formData.value.rol_id,
          estado: formData.value.estado
        };

        // Solo incluir password si estamos creando un nuevo usuario o si se proporcionó uno
        if (!isEditing.value || formData.value.password) {
          dataToSend.password = formData.value.password;
        }

        if (isEditing.value) {
          await axios.put(`/usuarios/${formData.value.id}`, dataToSend);
        } else {
          await axios.post('/usuarios', dataToSend);
        }
        
        closeModal();
        await fetchUsuarios();
        
        console.log('✅ Usuario guardado exitosamente');
      } catch (err) {
        console.error('💥 Error al guardar usuario:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el usuario. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (usuario) => {
      usuarioToDelete.value = usuario;
      showDeleteModal.value = true;
    };

    const deleteUsuario = async () => {
      if (!usuarioToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/usuarios/${usuarioToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchUsuarios();
        
        console.log('✅ Usuario eliminado exitosamente');
      } catch (err) {
        console.error('💥 Error al eliminar usuario:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el usuario. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        usuarioToDelete.value = null;
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
      console.log('🚀 Componente Usuarios montado');
      fetchUsuarios();
      fetchRoles();
    });

    return {
      usuarios,
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
      rolFilter,
      usuarioToDelete,
      formData,
      formErrors,
      usuariosFiltrados,
      fetchUsuarios,
      openModal,
      closeModal,
      saveUsuario,
      confirmDelete,
      deleteUsuario,
      formatDate,
      getRolNombre
    };
  }
};
</script>