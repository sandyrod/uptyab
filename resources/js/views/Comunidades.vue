<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Comunidades</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Comunidad
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchComunidades"
        />
        <button
          @click="fetchComunidades"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando comunidades...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="comunidades.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron comunidades.</p>
      </div>
    </div>

    <!-- Comunidades Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parroquia</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="comunidad in comunidadesFiltradas" :key="comunidad.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ comunidad.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ comunidad.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getParroquiaNombre(comunidad.parroquia_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getMunicipioNombre(comunidad.parroquia_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getEstadoNombre(comunidad.parroquia_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(comunidad.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(comunidad)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(comunidad)"
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
            {{ isEditing ? 'Editar Comunidad' : 'Nueva Comunidad' }}
          </h2>
          
          <form @submit.prevent="saveComunidad">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
                Nombre
              </label>
              <input
                id="nombre"
                v-model="formData.nombre"
                type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.nombre}"
                required
              />
              <p v-if="formErrors.nombre" class="text-red-500 text-xs italic mt-1">{{ formErrors.nombre[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="parroquia_id">
                Parroquia
              </label>
              <select
                id="parroquia_id"
                v-model="formData.parroquia_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.parroquia_id}"
                required
              >
                <option value="">Seleccione una parroquia</option>
                <option v-for="parroquia in parroquias" :key="parroquia.id" :value="parroquia.id">
                  {{ parroquia.nombre }} - {{ getMunicipioNombre(parroquia.municipio_id) }} - {{ getEstadoNombre(parroquia.municipio_id) }}
                </option>
              </select>
              <p v-if="formErrors.parroquia_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.parroquia_id[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar la comunidad "{{ comunidadToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteComunidad"
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
  name: 'Comunidades',
  setup() {
    const comunidades = ref([]);
    const parroquias = ref([]);
    const municipios = ref([]);
    const estados = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const comunidadToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      parroquia_id: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar comunidades en el frontend
    const comunidadesFiltradas = computed(() => {
      if (!searchQuery.value) {
        return comunidades.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return comunidades.value.filter(comunidad => {
        const parroquiaNombre = getParroquiaNombre(comunidad.parroquia_id).toLowerCase();
        const municipioNombre = getMunicipioNombre(comunidad.parroquia_id).toLowerCase();
        const estadoNombre = getEstadoNombre(comunidad.parroquia_id).toLowerCase();
        
        return (
          comunidad.nombre.toLowerCase().includes(query) ||
          parroquiaNombre.includes(query) ||
          municipioNombre.includes(query) ||
          estadoNombre.includes(query)
        );
      });
    });

    // Método para obtener nombre de la parroquia
    const getParroquiaNombre = (parroquiaId) => {
      const parroquia = parroquias.value.find(p => p.id === parroquiaId);
      return parroquia?.nombre || 'N/A';
    };

    // Método para obtener nombre del municipio a través de la parroquia
    const getMunicipioNombre = (parroquiaId) => {
      const parroquia = parroquias.value.find(p => p.id === parroquiaId);
      if (!parroquia) return 'N/A';
      
      const municipio = municipios.value.find(m => m.id === parroquia.municipio_id);
      return municipio?.nombre || 'N/A';
    };

    // Método para obtener nombre del estado a través de la parroquia y municipio
    const getEstadoNombre = (parroquiaId) => {
      const parroquia = parroquias.value.find(p => p.id === parroquiaId);
      if (!parroquia) return 'N/A';
      
      const municipio = municipios.value.find(m => m.id === parroquia.municipio_id);
      if (!municipio) return 'N/A';
      
      const estado = estados.value.find(e => e.id === municipio.estado_id);
      return estado?.nombre || 'N/A';
    };

    const fetchComunidades = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        console.log('🔍 Fetching comunidades from API...');
        
        const response = await axios.get('/comunidades');
        
        console.log('📦 Respuesta completa de la API:', response);
        console.log('📊 Datos recibidos:', response.data);
        
        // Diferentes formas en que la API podría devolver los datos
        if (Array.isArray(response.data)) {
          comunidades.value = response.data;
          console.log('✅ Comunidades cargadas (array directo):', comunidades.value.length);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          comunidades.value = response.data.data;
          console.log('✅ Comunidades cargadas (con paginación):', comunidades.value.length);
        } else if (response.data.comunidades && Array.isArray(response.data.comunidades)) {
          comunidades.value = response.data.comunidades;
          console.log('✅ Comunidades cargadas (con clave comunidades):', comunidades.value.length);
        } else {
          console.warn('❌ Estructura de respuesta desconocida:', response.data);
          comunidades.value = [];
        }
        
        if (comunidades.value.length === 0) {
          console.warn('⚠️  El array de comunidades está vacío');
        } else {
          console.log('🎉 Comunidades cargadas exitosamente. Primera comunidad:', comunidades.value[0]);
        }
        
      } catch (err) {
        console.error('💥 Error al cargar las comunidades:', err);
        console.error('📞 Detalles del error:', err.response);
        
        error.value = `Error al cargar las comunidades: ${err.response?.status || 'Sin conexión'}. ${err.response?.data?.message || ''}`;
        
        // Datos de prueba para debugging
        comunidades.value = [
          {
            id: 1,
            nombre: 'Comunidad de Prueba 1',
            parroquia_id: 1,
            created_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 2,
            nombre: 'Comunidad de Prueba 2', 
            parroquia_id: 2,
            created_at: '2025-01-01T00:00:00.000Z'
          }
        ];
        
        console.log('🔄 Usando datos de prueba:', comunidades.value);
      } finally {
        loading.value = false;
      }
    };

    const fetchParroquias = async () => {
      try {
        console.log('🔍 Fetching parroquias from API...');
        
        const response = await axios.get('/parroquias');
        
        console.log('📦 Respuesta de parroquias:', response);
        
        if (Array.isArray(response.data)) {
          parroquias.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          parroquias.value = response.data.data;
        } else {
          parroquias.value = [];
        }
        
        console.log('✅ Parroquias cargadas:', parroquias.value.length);
        
        // Datos de prueba si no hay parroquias
        if (parroquias.value.length === 0) {
          parroquias.value = [
            {
              id: 1,
              nombre: 'Parroquia Ejemplo 1',
              municipio_id: 1
            },
            {
              id: 2,
              nombre: 'Parroquia Ejemplo 2',
              municipio_id: 2
            }
          ];
          console.log('🔄 Usando parroquias de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar parroquias:', err);
        
        // Datos de prueba
        parroquias.value = [
          {
            id: 1,
            nombre: 'Parroquia Ejemplo 1',
            municipio_id: 1
          },
          {
            id: 2, 
            nombre: 'Parroquia Ejemplo 2',
            municipio_id: 2
          }
        ];
      }
    };

    const fetchMunicipios = async () => {
      try {
        console.log('🔍 Fetching municipios from API...');
        
        const response = await axios.get('/municipios');
        
        console.log('📦 Respuesta de municipios:', response);
        
        if (Array.isArray(response.data)) {
          municipios.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          municipios.value = response.data.data;
        } else {
          municipios.value = [];
        }
        
        console.log('✅ Municipios cargados:', municipios.value.length);
        
        // Datos de prueba si no hay municipios
        if (municipios.value.length === 0) {
          municipios.value = [
            {
              id: 1,
              nombre: 'Municipio Ejemplo 1',
              estado_id: 1
            },
            {
              id: 2,
              nombre: 'Municipio Ejemplo 2',
              estado_id: 2
            }
          ];
          console.log('🔄 Usando municipios de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar municipios:', err);
        
        // Datos de prueba
        municipios.value = [
          {
            id: 1,
            nombre: 'Municipio Ejemplo 1',
            estado_id: 1
          },
          {
            id: 2, 
            nombre: 'Municipio Ejemplo 2',
            estado_id: 2
          }
        ];
      }
    };

    const fetchEstados = async () => {
      try {
        console.log('🔍 Fetching estados from API...');
        
        const response = await axios.get('/estados');
        
        console.log('📦 Respuesta de estados:', response);
        
        if (Array.isArray(response.data)) {
          estados.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          estados.value = response.data.data;
        } else {
          estados.value = [];
        }
        
        console.log('✅ Estados cargados:', estados.value.length);
        
        // Datos de prueba si no hay estados
        if (estados.value.length === 0) {
          estados.value = [
            {
              id: 1,
              nombre: 'Amazonas'
            },
            {
              id: 2,
              nombre: 'Anzoátegui'
            }
          ];
          console.log('🔄 Usando estados de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar estados:', err);
        
        // Datos de prueba
        estados.value = [
          {
            id: 1,
            nombre: 'Amazonas'
          },
          {
            id: 2, 
            nombre: 'Anzoátegui'
          }
        ];
      }
    };

    const openModal = (comunidad = null) => {
      if (comunidad) {
        isEditing.value = true;
        formData.value = {
          id: comunidad.id,
          nombre: comunidad.nombre,
          parroquia_id: comunidad.parroquia_id
        };
      } else {
        isEditing.value = false;
        formData.value = {
          id: null,
          nombre: '',
          parroquia_id: ''
        };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formData.value = {
        id: null,
        nombre: '',
        parroquia_id: ''
      };
      formErrors.value = {};
    };

    const saveComunidad = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          await axios.put(`/comunidades/${formData.value.id}`, {
            nombre: formData.value.nombre,
            parroquia_id: formData.value.parroquia_id
          });
        } else {
          await axios.post('/comunidades', {
            nombre: formData.value.nombre,
            parroquia_id: formData.value.parroquia_id
          });
        }
        
        closeModal();
        await fetchComunidades();
        
        console.log('✅ Comunidad guardada exitosamente');
      } catch (err) {
        console.error('💥 Error al guardar comunidad:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar la comunidad. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (comunidad) => {
      comunidadToDelete.value = comunidad;
      showDeleteModal.value = true;
    };

    const deleteComunidad = async () => {
      if (!comunidadToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/comunidades/${comunidadToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchComunidades();
        
        console.log('✅ Comunidad eliminada exitosamente');
      } catch (err) {
        console.error('💥 Error al eliminar comunidad:', err);
        error.value = err.response?.data?.message || 'Error al eliminar la comunidad. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        comunidadToDelete.value = null;
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
      console.log('🚀 Componente Comunidades montado');
      fetchComunidades();
      fetchParroquias();
      fetchMunicipios();
      fetchEstados();
    });

    return {
      comunidades,
      parroquias,
      municipios,
      estados,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      comunidadToDelete,
      formData,
      formErrors,
      comunidadesFiltradas,
      fetchComunidades,
      openModal,
      closeModal,
      saveComunidad,
      confirmDelete,
      deleteComunidad,
      formatDate,
      getParroquiaNombre,
      getMunicipioNombre,
      getEstadoNombre
    };
  }
};
</script>