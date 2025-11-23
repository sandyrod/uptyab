<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Municipios</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Municipio
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
          @keyup.enter="fetchMunicipios"
        />
        <button
          @click="fetchMunicipios"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando municipios...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="municipios.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron municipios.</p>
      </div>
    </div>

    <!-- Municipios Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="municipio in municipiosFiltrados" :key="municipio.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ municipio.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ municipio.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ municipio.estado?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(municipio.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(municipio)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(municipio)"
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
            {{ isEditing ? 'Editar Municipio' : 'Nuevo Municipio' }}
          </h2>
          
          <form @submit.prevent="saveMunicipio">
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
              <label class="block text-gray-700 text-sm font-bold mb-2" for="estado_id">
                Estado
              </label>
              <select
                id="estado_id"
                v-model="formData.estado_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.estado_id}"
                required
              >
                <option value="">Seleccione un estado</option>
                <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                  {{ estado.nombre }}
                </option>
              </select>
              <p v-if="formErrors.estado_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.estado_id[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el municipio "{{ municipioToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteMunicipio"
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
  name: 'Municipios',
  setup() {
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
    const municipioToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      estado_id: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar municipios en el frontend
    const municipiosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return municipios.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return municipios.value.filter(municipio => 
        municipio.nombre.toLowerCase().includes(query) ||
        (municipio.estado?.nombre?.toLowerCase() || '').includes(query)
      );
    });

    const fetchMunicipios = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        // Cargar municipios con la relación de estado
        const [municipiosRes, estadosRes] = await Promise.all([
          axios.get('/municipios?with=estado'),
          axios.get('/estados')
        ]);
        
        municipios.value = municipiosRes.data;
        estados.value = estadosRes.data;
        
        console.log('Municipios cargados:', municipiosRes.data);
      } catch (err) {
        console.error('Error fetching municipios:', err);
        error.value = err.response?.data?.message || 'Error al cargar los municipios. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const openModal = (municipio = null) => {
      if (municipio) {
        isEditing.value = true;
        formData.value = { 
          id: municipio.id, 
          nombre: municipio.nombre,
          estado_id: municipio.estado_id
        };
      } else {
        isEditing.value = false;
        formData.value = { 
          id: null, 
          nombre: '',
          estado_id: ''
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
        estado_id: ''
      };
      formErrors.value = {};
    };

    const saveMunicipio = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          // PUT /municipios/{id}
          await axios.put(`/municipios/${formData.value.id}`, {
            nombre: formData.value.nombre,
            estado_id: formData.value.estado_id
          });
        } else {
          // POST /municipios
          await axios.post('/municipios', {
            nombre: formData.value.nombre,
            estado_id: formData.value.estado_id
          });
        }
        
        closeModal();
        await fetchMunicipios(); // Recargar la lista
        
        // Mostrar mensaje de éxito
        error.value = null;
      } catch (err) {
        console.error('Error saving municipio:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el municipio. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (municipio) => {
      municipioToDelete.value = municipio;
      showDeleteModal.value = true;
    };

    const deleteMunicipio = async () => {
      if (!municipioToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        // DELETE /municipios/{id}
        await axios.delete(`/municipios/${municipioToDelete.value.id}`);
        
        showDeleteModal.value = false;
        await fetchMunicipios(); // Recargar la lista
        
        // Mostrar mensaje de éxito
        error.value = null;
      } catch (err) {
        console.error('Error deleting municipio:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el municipio. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        municipioToDelete.value = null;
      }
    };

    const formatDate = (dateString) => {
      if (!dateString) return '';
      try {
        const options = { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('es-ES', options);
      } catch (error) {
        return dateString;
      }
    };

    onMounted(() => {
      fetchMunicipios();
    });

    return {
      municipios,
      municipiosFiltrados,
      estados,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      municipioToDelete,
      formData,
      formErrors,
      fetchMunicipios,
      openModal,
      closeModal,
      saveMunicipio,
      confirmDelete,
      deleteMunicipio,
      formatDate
    };
  }
};
</script>