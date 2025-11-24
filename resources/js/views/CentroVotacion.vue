<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Centros de Votación</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Centro
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre, código, ubicación o responsable..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchCentrosVotacion"
        />
        <button
          @click="fetchCentrosVotacion"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando centros de votación...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="centrosVotacion.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron centros de votación.</p>
      </div>
    </div>

    <!-- Centros de Votación Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cant. Electores</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="centro in centrosFiltrados" :key="centro.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ centro.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ centro.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ centro.codigo }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ centro.cantidad_electores }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getUbicacionNombre(centro.ubicacion_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getPersonaNombre(centro.persona_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(centro.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(centro)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(centro)"
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
            {{ isEditing ? 'Editar Centro de Votación' : 'Nuevo Centro de Votación' }}
          </h2>
          
          <form @submit.prevent="saveCentroVotacion">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
                  Nombre *
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="codigo">
                  Código *
                </label>
                <input
                  id="codigo"
                  v-model="formData.codigo"
                  type="number"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.codigo}"
                  required
                />
                <p v-if="formErrors.codigo" class="text-red-500 text-xs italic mt-1">{{ formErrors.codigo[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cantidad_electores">
                  Cantidad de Electores *
                </label>
                <input
                  id="cantidad_electores"
                  v-model="formData.cantidad_electores"
                  type="number"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.cantidad_electores}"
                  required
                />
                <p v-if="formErrors.cantidad_electores" class="text-red-500 text-xs italic mt-1">{{ formErrors.cantidad_electores[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="ubicacion_id">
                  Ubicación *
                </label>
                <select
                  id="ubicacion_id"
                  v-model="formData.ubicacion_id"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.ubicacion_id}"
                  required
                >
                  <option value="">Seleccione una ubicación</option>
                  <option v-for="ubicacion in ubicaciones" :key="ubicacion.id" :value="ubicacion.id">
                    {{ ubicacion.nombre }} - {{ getComunidadNombre(ubicacion.comunidad_id) }}
                  </option>
                </select>
                <p v-if="formErrors.ubicacion_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.ubicacion_id[0] }}</p>
              </div>

              <div class="mb-4 md:col-span-2">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="persona_id">
                  Responsable *
                </label>
                <select
                  id="persona_id"
                  v-model="formData.persona_id"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.persona_id}"
                  required
                >
                  <option value="">Seleccione un responsable</option>
                  <option v-for="persona in personas" :key="persona.id" :value="persona.id">
                    {{ persona.nombres }} {{ persona.apellidos }} - {{ persona.cedula }}
                  </option>
                </select>
                <p v-if="formErrors.persona_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.persona_id[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el centro "{{ centroToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteCentroVotacion"
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
  name: 'CentroVotacion',
  setup() {
    const centrosVotacion = ref([]);
    const ubicaciones = ref([]);
    const personas = ref([]);
    const comunidades = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const centroToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      codigo: '',
      cantidad_electores: '',
      ubicacion_id: '',
      persona_id: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar centros en el frontend
    const centrosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return centrosVotacion.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return centrosVotacion.value.filter(centro => {
        const ubicacionNombre = getUbicacionNombre(centro.ubicacion_id).toLowerCase();
        const personaNombre = getPersonaNombre(centro.persona_id).toLowerCase();
        
        return (
          centro.nombre.toLowerCase().includes(query) ||
          centro.codigo.toString().includes(query) ||
          centro.cantidad_electores.toString().includes(query) ||
          ubicacionNombre.includes(query) ||
          personaNombre.includes(query)
        );
      });
    });

    // Método para obtener nombre de la ubicación
    const getUbicacionNombre = (ubicacionId) => {
      const ubicacion = ubicaciones.value.find(u => u.id === ubicacionId);
      return ubicacion?.nombre || 'N/A';
    };

    // Método para obtener nombre de la persona
    const getPersonaNombre = (personaId) => {
      const persona = personas.value.find(p => p.id === personaId);
      if (persona) {
        return `${persona.nombres} ${persona.apellidos}`;
      }
      return 'N/A';
    };

    // Método para obtener nombre de la comunidad
    const getComunidadNombre = (comunidadId) => {
      const comunidad = comunidades.value.find(c => c.id === comunidadId);
      return comunidad?.nombre || 'N/A';
    };

    const fetchCentrosVotacion = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        console.log('🔍 Fetching centros de votación from API...');
        
        const response = await axios.get('/votacion-centros');
        
        console.log('📦 Respuesta completa de la API:', response);
        console.log('📊 Datos recibidos:', response.data);
        
        // Diferentes formas en que la API podría devolver los datos
        if (Array.isArray(response.data)) {
          centrosVotacion.value = response.data;
          console.log('✅ Centros de votación cargados (array directo):', centrosVotacion.value.length);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          centrosVotacion.value = response.data.data;
          console.log('✅ Centros de votación cargados (con paginación):', centrosVotacion.value.length);
        } else if (response.data.centros && Array.isArray(response.data.centros)) {
          centrosVotacion.value = response.data.centros;
          console.log('✅ Centros de votación cargados (con clave centros):', centrosVotacion.value.length);
        } else {
          console.warn('❌ Estructura de respuesta desconocida:', response.data);
          centrosVotacion.value = [];
        }
        
        if (centrosVotacion.value.length === 0) {
          console.warn('⚠️  El array de centros de votación está vacío');
        } else {
          console.log('🎉 Centros de votación cargados exitosamente. Primer centro:', centrosVotacion.value[0]);
        }
        
      } catch (err) {
        console.error('💥 Error al cargar los centros de votación:', err);
        console.error('📞 Detalles del error:', err.response);
        
        error.value = `Error al cargar los centros de votación: ${err.response?.status || 'Sin conexión'}. ${err.response?.data?.message || ''}`;
        
        // Datos de prueba para debugging
        centrosVotacion.value = [
          {
            id: 1,
            nombre: 'Centro Escolar Nacional',
            codigo: 1001,
            cantidad_electores: 1500,
            ubicacion_id: 1,
            persona_id: 1,
            created_at: '2025-01-01T00:00:00.000Z',
            updated_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 2,
            nombre: 'Liceo Bolivariano',
            codigo: 1002,
            cantidad_electores: 2000,
            ubicacion_id: 2,
            persona_id: 2,
            created_at: '2025-01-01T00:00:00.000Z',
            updated_at: '2025-01-01T00:00:00.000Z'
          }
        ];
        
        console.log('🔄 Usando datos de prueba:', centrosVotacion.value);
      } finally {
        loading.value = false;
      }
    };

    const fetchUbicaciones = async () => {
      try {
        console.log('🔍 Fetching ubicaciones from API...');
        
        const response = await axios.get('/ubicaciones');
        
        if (Array.isArray(response.data)) {
          ubicaciones.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          ubicaciones.value = response.data.data;
        } else {
          ubicaciones.value = [];
        }
        
        console.log('✅ Ubicaciones cargadas:', ubicaciones.value.length);
        
        // Datos de prueba si no hay ubicaciones
        if (ubicaciones.value.length === 0) {
          ubicaciones.value = [
            {
              id: 1,
              nombre: 'Escuela Básica Nacional',
              comunidad_id: 1
            },
            {
              id: 2,
              nombre: 'Liceo Municipal',
              comunidad_id: 2
            }
          ];
          console.log('🔄 Usando ubicaciones de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar ubicaciones:', err);
        
        // Datos de prueba
        ubicaciones.value = [
          {
            id: 1,
            nombre: 'Escuela Básica Nacional',
            comunidad_id: 1
          },
          {
            id: 2,
            nombre: 'Liceo Municipal',
            comunidad_id: 2
          }
        ];
      }
    };

    const fetchPersonas = async () => {
      try {
        console.log('🔍 Fetching personas from API...');
        
        const response = await axios.get('/personas');
        
        if (Array.isArray(response.data)) {
          personas.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          personas.value = response.data.data;
        } else {
          personas.value = [];
        }
        
        console.log('✅ Personas cargadas:', personas.value.length);
        
        // Datos de prueba si no hay personas
        if (personas.value.length === 0) {
          personas.value = [
            {
              id: 1,
              nombres: 'Juan',
              apellidos: 'Pérez',
              cedula: '12345678'
            },
            {
              id: 2,
              nombres: 'María',
              apellidos: 'González',
              cedula: '87654321'
            }
          ];
          console.log('🔄 Usando personas de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar personas:', err);
        
        // Datos de prueba
        personas.value = [
          {
            id: 1,
            nombres: 'Juan',
            apellidos: 'Pérez',
            cedula: '12345678'
          },
          {
            id: 2,
            nombres: 'María',
            apellidos: 'González',
            cedula: '87654321'
          }
        ];
      }
    };

    const fetchComunidades = async () => {
      try {
        console.log('🔍 Fetching comunidades from API...');
        
        const response = await axios.get('/comunidades');
        
        if (Array.isArray(response.data)) {
          comunidades.value = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          comunidades.value = response.data.data;
        } else {
          comunidades.value = [];
        }
        
        console.log('✅ Comunidades cargadas:', comunidades.value.length);
        
        // Datos de prueba si no hay comunidades
        if (comunidades.value.length === 0) {
          comunidades.value = [
            {
              id: 1,
              nombre: 'Comunidad Norte'
            },
            {
              id: 2,
              nombre: 'Comunidad Sur'
            }
          ];
          console.log('🔄 Usando comunidades de prueba');
        }
        
      } catch (err) {
        console.error('💥 Error al cargar comunidades:', err);
        
        // Datos de prueba
        comunidades.value = [
          {
            id: 1,
            nombre: 'Comunidad Norte'
          },
          {
            id: 2,
            nombre: 'Comunidad Sur'
          }
        ];
      }
    };

    const openModal = (centro = null) => {
      if (centro) {
        isEditing.value = true;
        formData.value = {
          id: centro.id,
          nombre: centro.nombre,
          codigo: centro.codigo,
          cantidad_electores: centro.cantidad_electores,
          ubicacion_id: centro.ubicacion_id,
          persona_id: centro.persona_id
        };
      } else {
        isEditing.value = false;
        formData.value = {
          id: null,
          nombre: '',
          codigo: '',
          cantidad_electores: '',
          ubicacion_id: '',
          persona_id: ''
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
        codigo: '',
        cantidad_electores: '',
        ubicacion_id: '',
        persona_id: ''
      };
      formErrors.value = {};
    };

    const saveCentroVotacion = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        // Preparar los datos para enviar
        const dataToSend = {
          nombre: formData.value.nombre,
          codigo: formData.value.codigo,
          cantidad_electores: formData.value.cantidad_electores,
          ubicacion_id: formData.value.ubicacion_id,
          persona_id: formData.value.persona_id
        };

        console.log('📤 Enviando datos:', dataToSend);

        if (isEditing.value) {
          await axios.put(`/votacion-centros/${formData.value.id}`, dataToSend);
        } else {
          await axios.post('/votacion-centros', dataToSend);
        }
        
        closeModal();
        await fetchCentrosVotacion();
        
        console.log('✅ Centro de votación guardado exitosamente');
      } catch (err) {
        console.error('💥 Error al guardar centro de votación:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el centro de votación. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (centro) => {
      centroToDelete.value = centro;
      showDeleteModal.value = true;
    };

    const deleteCentroVotacion = async () => {
      if (!centroToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/votacion-centros/${centroToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchCentrosVotacion();
        
        console.log('✅ Centro de votación eliminado exitosamente');
      } catch (err) {
        console.error('💥 Error al eliminar centro de votación:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el centro de votación. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        centroToDelete.value = null;
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
      console.log('🚀 Componente CentroVotacion montado');
      fetchCentrosVotacion();
      fetchUbicaciones();
      fetchPersonas();
      fetchComunidades();
    });

    return {
      centrosVotacion,
      ubicaciones,
      personas,
      comunidades,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      centroToDelete,
      formData,
      formErrors,
      centrosFiltrados,
      fetchCentrosVotacion,
      openModal,
      closeModal,
      saveCentroVotacion,
      confirmDelete,
      deleteCentroVotacion,
      formatDate,
      getUbicacionNombre,
      getPersonaNombre,
      getComunidadNombre
    };
  }
};
</script>