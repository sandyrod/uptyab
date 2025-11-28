<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Proyectos</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Proyecto
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre de proyecto..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchProyectos"
        />
        <button
          @click="fetchProyectos"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando proyectos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="proyectos.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron proyectos.</p>
      </div>
    </div>

    <!-- Proyectos Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comuna</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="proyecto in proyectosFiltrados" :key="proyecto.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ proyecto.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ proyecto.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ proyecto.comuna?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(proyecto.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(proyecto)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(proyecto)"
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
            {{ isEditing ? 'Editar Proyecto' : 'Nuevo Proyecto' }}
          </h2>
          
          <form @submit.prevent="saveProyecto">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="comuna_id">
                Comuna *
              </label>
              <select
                id="comuna_id"
                v-model="formData.comuna_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.comuna_id}"
                required
              >
                <option value="">Seleccione una comuna</option>
                <option v-for="comuna in comunas" :key="comuna.id" :value="comuna.id">
                  {{ comuna.nombre }}
                </option>
              </select>
              <p v-if="formErrors.comuna_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.comuna_id[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
                Nombre del Proyecto *
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
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors"
                :disabled="saving"
              >
                <span v-if="saving">Guardando...</span>
                <span v-else>{{ isEditing ? 'Actualizar' : 'Guardar' }}</span>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el proyecto "{{ proyectoToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteProyecto"
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors"
              :disabled="deleting"
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
  name: 'Proyectos',
  setup() {
    const proyectos = ref([]);
    const comunas = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const proyectoToDelete = ref(null);
    
    const formData = ref({
      id: null,
      comuna_id: '',
      nombre: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar proyectos en el frontend
    const proyectosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return proyectos.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return proyectos.value.filter(proyecto => 
        (proyecto.nombre?.toLowerCase() || '').includes(query) ||
        (proyecto.comuna?.nombre?.toLowerCase() || '').includes(query)
      );
    });

    const fetchProyectos = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        let url = '/proyectos';
        if (searchQuery.value) {
          url += `?search=${encodeURIComponent(searchQuery.value)}`;
        }
        
        const response = await axios.get(url);
        proyectos.value = response.data.data || response.data;
        
      } catch (err) {
        console.error('Error al cargar los proyectos:', err);
        error.value = err.response?.data?.message || 'Error al cargar los proyectos. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const fetchComunas = async () => {
      try {
        const response = await axios.get('/comunas');
        comunas.value = response.data.data || response.data;
      } catch (err) {
        console.error('Error al cargar las comunas:', err);
      }
    };

    const openModal = (proyecto = null) => {
      if (proyecto) {
        isEditing.value = true;
        formData.value = { 
          id: proyecto.id,
          comuna_id: proyecto.comuna_id,
          nombre: proyecto.nombre
        };
      } else {
        isEditing.value = false;
        formData.value = { 
          id: null,
          comuna_id: '',
          nombre: ''
        };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formData.value = { 
        id: null,
        comuna_id: '',
        nombre: ''
      };
      formErrors.value = {};
    };

    const saveProyecto = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        const dataToSend = {
          comuna_id: formData.value.comuna_id,
          nombre: formData.value.nombre
        };

        if (isEditing.value) {
          await axios.put(`/proyectos/${formData.value.id}`, dataToSend);
        } else {
          await axios.post('/proyectos', dataToSend);
        }
        
        closeModal();
        await fetchProyectos();
        
      } catch (err) {
        console.error('Error al guardar el proyecto:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el proyecto. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (proyecto) => {
      proyectoToDelete.value = proyecto;
      showDeleteModal.value = true;
    };

    const deleteProyecto = async () => {
      if (!proyectoToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/proyectos/${proyectoToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchProyectos();
        
      } catch (err) {
        console.error('Error al eliminar el proyecto:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el proyecto. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        proyectoToDelete.value = null;
      }
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      try {
        const options = { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric',
          timeZone: 'UTC'
        };
        return new Date(dateString).toLocaleDateString('es-ES', options);
      } catch (error) {
        return dateString;
      }
    };

    onMounted(() => {
      fetchProyectos();
      fetchComunas();
    });

    return {
      proyectos,
      comunas,
      proyectosFiltrados,
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
      fetchProyectos,
      openModal,
      closeModal,
      saveProyecto,
      confirmDelete,
      deleteProyecto,
      formatDate
    };
  }
};
</script>