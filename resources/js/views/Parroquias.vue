<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Parroquias</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Parroquia
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
          @keyup.enter="fetchParroquias"
        />
        <button
          @click="fetchParroquias"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando parroquias...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="parroquias.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron parroquias.</p>
      </div>
    </div>

    <!-- Parroquias Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Municipio</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="parroquia in parroquiasFiltradas" :key="parroquia.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ parroquia.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ parroquia.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ parroquia.municipio?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ parroquia.municipio?.estado?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(parroquia.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(parroquia)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(parroquia)"
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
            {{ isEditing ? 'Editar Parroquia' : 'Nueva Parroquia' }}
          </h2>
          
          <form @submit.prevent="saveParroquia">
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
              <label class="block text-gray-700 text-sm font-bold mb-2" for="municipio_id">
                Municipio
              </label>
              <select
                id="municipio_id"
                v-model="formData.municipio_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.municipio_id}"
                required
                @change="onMunicipioChange"
              >
                <option value="">Seleccione un municipio</option>
                <option v-for="municipio in municipios" :key="municipio.id" :value="municipio.id">
                  {{ municipio.nombre }} ({{ municipio.estado?.nombre }})
                </option>
              </select>
              <p v-if="formErrors.municipio_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.municipio_id[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar la parroquia "{{ parroquiaToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteParroquia"
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
  name: 'Parroquias',
  setup() {
    const parroquias = ref([]);
    const municipios = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const parroquiaToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      municipio_id: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar parroquias en el frontend
    const parroquiasFiltradas = computed(() => {
      if (!searchQuery.value) {
        return parroquias.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return parroquias.value.filter(parroquia => 
        parroquia.nombre.toLowerCase().includes(query) ||
        (parroquia.municipio?.nombre.toLowerCase().includes(query) || '') ||
        (parroquia.municipio?.estado?.nombre.toLowerCase().includes(query) || '')
      );
    });

    const fetchParroquias = async () => {
      loading.value = true;
      error.value = null;
      try {
        const response = await axios.get('/parroquias', {
          params: {
            search: searchQuery.value
          }
        });
        parroquias.value = response.data.data || [];
      } catch (err) {
        console.error('Error al cargar las parroquias:', err);
        error.value = 'No se pudieron cargar las parroquias. Por favor, intente de nuevo.';
      } finally {
        loading.value = false;
      }
    };

    const fetchMunicipios = async () => {
      try {
        const response = await axios.get('/municipios', {
          params: {
            all: true // Asumiendo que hay un parámetro para traer todos los municipios sin paginación
          }
        });
        municipios.value = response.data.data || [];
      } catch (err) {
        console.error('Error al cargar los municipios:', err);
      }
    };

    const openModal = (parroquia = null) => {
      if (parroquia) {
        // Modo edición
        isEditing.value = true;
        formData.value = {
          id: parroquia.id,
          nombre: parroquia.nombre,
          municipio_id: parroquia.municipio_id
        };
      } else {
        // Modo creación
        isEditing.value = false;
        formData.value = {
          id: null,
          nombre: '',
          municipio_id: ''
        };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formErrors.value = {};
    };

    const saveParroquia = async () => {
      saving.value = true;
      formErrors.value = {};
      
      try {
        if (isEditing.value) {
          // Actualizar parroquia existente
          await axios.put(`/parroquias/${formData.value.id}`, formData.value);
        } else {
          // Crear nueva parroquia
          await axios.post('/parroquias', formData.value);
        }
        
        await fetchParroquias();
        closeModal();
      } catch (err) {
        if (err.response && err.response.status === 422) {
          // Error de validación del servidor
          formErrors.value = err.response.data.errors || {};
        } else {
          console.error('Error al guardar la parroquia:', err);
          error.value = 'No se pudo guardar la parroquia. Por favor, intente de nuevo.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (parroquia) => {
      parroquiaToDelete.value = parroquia;
      showDeleteModal.value = true;
    };

    const deleteParroquia = async () => {
      if (!parroquiaToDelete.value) return;
      
      deleting.value = true;
      
      try {
        await axios.delete(`/parroquias/${parroquiaToDelete.value.id}`);
        await fetchParroquias();
        showDeleteModal.value = false;
        parroquiaToDelete.value = null;
      } catch (err) {
        console.error('Error al eliminar la parroquia:', err);
        error.value = 'No se pudo eliminar la parroquia. Por favor, intente de nuevo.';
      } finally {
        deleting.value = false;
      }
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      const options = { year: 'numeric', month: 'short', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('es-ES', options);
    };

    const onMunicipioChange = () => {
      // Puedes agregar lógica adicional aquí si es necesario cuando cambia el municipio
    };

    // Cargar datos iniciales
    onMounted(() => {
      fetchParroquias();
      fetchMunicipios();
    });

    return {
      // State
      parroquias,
      municipios,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      formData,
      formErrors,
      parroquiaToDelete,
      
      // Computed
      parroquiasFiltradas,
      
      // Methods
      openModal,
      closeModal,
      saveParroquia,
      confirmDelete,
      deleteParroquia,
      fetchParroquias,
      formatDate,
      onMunicipioChange
    };
  }
};
</script>
