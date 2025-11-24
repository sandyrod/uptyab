<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Ubicaciones</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Ubicación
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre, calle, avenida o referencia..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchUbicaciones"
        />
        <button
          @click="fetchUbicaciones"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando ubicaciones...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="ubicaciones.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron ubicaciones.</p>
      </div>
    </div>

    <!-- Ubicaciones Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calle</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avenida</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referencia</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comunidad</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parroquia</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="ubicacion in ubicacionesFiltradas" :key="ubicacion.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ubicacion.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ ubicacion.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ubicacion.calle || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ubicacion.avenida || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ubicacion.referencia || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getComunidadNombre(ubicacion.comunidad_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getParroquiaNombre(ubicacion.comunidad_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getMunicipioNombre(ubicacion.comunidad_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ getEstadoNombre(ubicacion.comunidad_id) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(ubicacion)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(ubicacion)"
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
            {{ isEditing ? 'Editar Ubicación' : 'Nueva Ubicación' }}
          </h2>
          
          <form @submit.prevent="saveUbicacion">
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="comunidad_id">
                  Comunidad *
                </label>
                <select
                  id="comunidad_id"
                  v-model="formData.comunidad_id"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.comunidad_id}"
                  required
                >
                  <option value="">Seleccione una comunidad</option>
                  <option v-for="comunidad in comunidades" :key="comunidad.id" :value="comunidad.id">
                    {{ comunidad.nombre }} - {{ getParroquiaNombre(comunidad.parroquia_id) }} - {{ getMunicipioNombre(comunidad.parroquia_id) }} - {{ getEstadoNombre(comunidad.parroquia_id) }}
                  </option>
                </select>
                <p v-if="formErrors.comunidad_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.comunidad_id[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="calle">
                  Calle
                </label>
                <input
                  id="calle"
                  v-model="formData.calle"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.calle}"
                />
                <p v-if="formErrors.calle" class="text-red-500 text-xs italic mt-1">{{ formErrors.calle[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="avenida">
                  Avenida
                </label>
                <input
                  id="avenida"
                  v-model="formData.avenida"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.avenida}"
                />
                <p v-if="formErrors.avenida" class="text-red-500 text-xs italic mt-1">{{ formErrors.avenida[0] }}</p>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="referencia">
                Referencia
              </label>
              <textarea
                id="referencia"
                v-model="formData.referencia"
                rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.referencia}"
                placeholder="Puntos de referencia adicionales..."
              ></textarea>
              <p v-if="formErrors.referencia" class="text-red-500 text-xs italic mt-1">{{ formErrors.referencia[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar la ubicación "{{ ubicacionToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteUbicacion"
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
  name: 'Ubicaciones',
  setup() {
    const ubicaciones = ref([]);
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
    const ubicacionToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      calle: '',
      avenida: '',
      referencia: '',
      comunidad_id: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar ubicaciones en el frontend
    const ubicacionesFiltradas = computed(() => {
      if (!searchQuery.value) {
        return ubicaciones.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return ubicaciones.value.filter(ubicacion => {
        const comunidadNombre = getComunidadNombre(ubicacion.comunidad_id).toLowerCase();
        const parroquiaNombre = getParroquiaNombre(ubicacion.comunidad_id).toLowerCase();
        const municipioNombre = getMunicipioNombre(ubicacion.comunidad_id).toLowerCase();
        const estadoNombre = getEstadoNombre(ubicacion.comunidad_id).toLowerCase();
        
        return (
          ubicacion.nombre.toLowerCase().includes(query) ||
          (ubicacion.calle?.toLowerCase().includes(query) || '') ||
          (ubicacion.avenida?.toLowerCase().includes(query) || '') ||
          (ubicacion.referencia?.toLowerCase().includes(query) || '') ||
          comunidadNombre.includes(query) ||
          parroquiaNombre.includes(query) ||
          municipioNombre.includes(query) ||
          estadoNombre.includes(query)
        );
      });
    });

    // Método para obtener nombre de la comunidad
    const getComunidadNombre = (comunidadId) => {
      const comunidad = comunidades.value.find(c => c.id === comunidadId);
      return comunidad?.nombre || 'N/A';
    };

    // Método para obtener nombre de la parroquia a través de la comunidad
    const getParroquiaNombre = (comunidadId) => {
      const comunidad = comunidades.value.find(c => c.id === comunidadId);
      if (!comunidad) return 'N/A';
      
      const parroquia = parroquias.value.find(p => p.id === comunidad.parroquia_id);
      return parroquia?.nombre || 'N/A';
    };

    // Método para obtener nombre del municipio a través de la comunidad y parroquia
    const getMunicipioNombre = (comunidadId) => {
      const comunidad = comunidades.value.find(c => c.id === comunidadId);
      if (!comunidad) return 'N/A';
      
      const parroquia = parroquias.value.find(p => p.id === comunidad.parroquia_id);
      if (!parroquia) return 'N/A';
      
      const municipio = municipios.value.find(m => m.id === parroquia.municipio_id);
      return municipio?.nombre || 'N/A';
    };

    // Método para obtener nombre del estado a través de la comunidad, parroquia y municipio
    const getEstadoNombre = (comunidadId) => {
      const comunidad = comunidades.value.find(c => c.id === comunidadId);
      if (!comunidad) return 'N/A';
      
      const parroquia = parroquias.value.find(p => p.id === comunidad.parroquia_id);
      if (!parroquia) return 'N/A';
      
      const municipio = municipios.value.find(m => m.id === parroquia.municipio_id);
      if (!municipio) return 'N/A';
      
      const estado = estados.value.find(e => e.id === municipio.estado_id);
      return estado?.nombre || 'N/A';
    };

    const fetchUbicaciones = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        console.log('🔍 Fetching ubicaciones from API...');
        
        const response = await axios.get('/ubicaciones');
        
        console.log('📦 Respuesta completa de la API:', response);
        console.log('📊 Datos recibidos:', response.data);
        
        // Diferentes formas en que la API podría devolver los datos
        if (Array.isArray(response.data)) {
          ubicaciones.value = response.data;
          console.log('✅ Ubicaciones cargadas (array directo):', ubicaciones.value.length);
        } else if (response.data.data && Array.isArray(response.data.data)) {
          ubicaciones.value = response.data.data;
          console.log('✅ Ubicaciones cargadas (con paginación):', ubicaciones.value.length);
        } else if (response.data.ubicaciones && Array.isArray(response.data.ubicaciones)) {
          ubicaciones.value = response.data.ubicaciones;
          console.log('✅ Ubicaciones cargadas (con clave ubicaciones):', ubicaciones.value.length);
        } else {
          console.warn('❌ Estructura de respuesta desconocida:', response.data);
          ubicaciones.value = [];
        }
        
        if (ubicaciones.value.length === 0) {
          console.warn('⚠️  El array de ubicaciones está vacío');
        } else {
          console.log('🎉 Ubicaciones cargadas exitosamente. Primera ubicación:', ubicaciones.value[0]);
        }
        
      } catch (err) {
        console.error('💥 Error al cargar las ubicaciones:', err);
        console.error('📞 Detalles del error:', err.response);
        
        error.value = `Error al cargar las ubicaciones: ${err.response?.status || 'Sin conexión'}. ${err.response?.data?.message || ''}`;
        
        // Datos de prueba para debugging
        ubicaciones.value = [
          {
            id: 1,
            nombre: 'Ubicación de Prueba 1',
            calle: 'Calle Principal',
            avenida: 'Avenida Central',
            referencia: 'Frente a la plaza',
            comunidad_id: 1,
            created_at: '2025-01-01T00:00:00.000Z'
          },
          {
            id: 2,
            nombre: 'Ubicación de Prueba 2', 
            calle: 'Calle Secundaria',
            avenida: 'Avenida Norte',
            referencia: 'Al lado del mercado',
            comunidad_id: 2,
            created_at: '2025-01-01T00:00:00.000Z'
          }
        ];
        
        console.log('🔄 Usando datos de prueba:', ubicaciones.value);
      } finally {
        loading.value = false;
      }
    };

    const fetchComunidades = async () => {
      try {
        console.log('🔍 Fetching comunidades from API...');
        
        const response = await axios.get('/comunidades');
        
        console.log('📦 Respuesta de comunidades:', response);
        
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
              nombre: 'Comunidad Ejemplo 1',
              parroquia_id: 1
            },
            {
              id: 2,
              nombre: 'Comunidad Ejemplo 2',
              parroquia_id: 2
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
            nombre: 'Comunidad Ejemplo 1',
            parroquia_id: 1
          },
          {
            id: 2, 
            nombre: 'Comunidad Ejemplo 2',
            parroquia_id: 2
          }
        ];
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

    const openModal = (ubicacion = null) => {
      if (ubicacion) {
        isEditing.value = true;
        formData.value = {
          id: ubicacion.id,
          nombre: ubicacion.nombre,
          calle: ubicacion.calle || '',
          avenida: ubicacion.avenida || '',
          referencia: ubicacion.referencia || '',
          comunidad_id: ubicacion.comunidad_id
        };
      } else {
        isEditing.value = false;
        formData.value = {
          id: null,
          nombre: '',
          calle: '',
          avenida: '',
          referencia: '',
          comunidad_id: ''
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
        calle: '',
        avenida: '',
        referencia: '',
        comunidad_id: ''
      };
      formErrors.value = {};
    };

    const saveUbicacion = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          await axios.put(`/ubicaciones/${formData.value.id}`, {
            nombre: formData.value.nombre,
            calle: formData.value.calle,
            avenida: formData.value.avenida,
            referencia: formData.value.referencia,
            comunidad_id: formData.value.comunidad_id
          });
        } else {
          await axios.post('/ubicaciones', {
            nombre: formData.value.nombre,
            calle: formData.value.calle,
            avenida: formData.value.avenida,
            referencia: formData.value.referencia,
            comunidad_id: formData.value.comunidad_id
          });
        }
        
        closeModal();
        await fetchUbicaciones();
        
        console.log('✅ Ubicación guardada exitosamente');
      } catch (err) {
        console.error('💥 Error al guardar ubicación:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar la ubicación. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (ubicacion) => {
      ubicacionToDelete.value = ubicacion;
      showDeleteModal.value = true;
    };

    const deleteUbicacion = async () => {
      if (!ubicacionToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/ubicaciones/${ubicacionToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchUbicaciones();
        
        console.log('✅ Ubicación eliminada exitosamente');
      } catch (err) {
        console.error('💥 Error al eliminar ubicación:', err);
        error.value = err.response?.data?.message || 'Error al eliminar la ubicación. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        ubicacionToDelete.value = null;
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
      console.log('🚀 Componente Ubicaciones montado');
      fetchUbicaciones();
      fetchComunidades();
      fetchParroquias();
      fetchMunicipios();
      fetchEstados();
    });

    return {
      ubicaciones,
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
      ubicacionToDelete,
      formData,
      formErrors,
      ubicacionesFiltradas,
      fetchUbicaciones,
      openModal,
      closeModal,
      saveUbicacion,
      confirmDelete,
      deleteUbicacion,
      formatDate,
      getComunidadNombre,
      getParroquiaNombre,
      getMunicipioNombre,
      getEstadoNombre
    };
  }
};
</script>