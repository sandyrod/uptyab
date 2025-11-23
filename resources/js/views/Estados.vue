<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Estados</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Estado
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
          @keyup.enter="fetchEstados"
        />
        <button
          @click="fetchEstados"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando estados...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="estados.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron estados.</p>
      </div>
    </div>

    <!-- Estados Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="estado in estadosFiltrados" :key="estado.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ estado.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ estado.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(estado.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(estado)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(estado)"
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
            {{ isEditing ? 'Editar Estado' : 'Nuevo Estado' }}
          </h2>
          
          <form @submit.prevent="saveEstado">
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el estado "{{ estadoToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteEstado"
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
  name: 'Estados',
  setup() {
    const estados = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const estadoToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar estados en el frontend
    const estadosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return estados.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return estados.value.filter(estado => 
        estado.nombre.toLowerCase().includes(query)
      );
    });

    const fetchEstados = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const response = await axios.get('/estados');
        
        // Tu API devuelve un array directo, no datos paginados
        estados.value = response.data;
        
        console.log('Estados cargados:', response.data);
      } catch (err) {
        console.error('Error fetching estados:', err);
        error.value = err.response?.data?.message || 'Error al cargar los estados. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const openModal = (estado = null) => {
      if (estado) {
        isEditing.value = true;
        formData.value = { ...estado };
      } else {
        isEditing.value = false;
        formData.value = { id: null, nombre: '' };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formData.value = { id: null, nombre: '' };
      formErrors.value = {};
    };

    const saveEstado = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          // PUT /estados/{id}
          await axios.put(`/estados/${formData.value.id}`, {
            nombre: formData.value.nombre
          });
        } else {
          // POST /estados
          await axios.post('/estados', {
            nombre: formData.value.nombre
          });
        }
        
        closeModal();
        await fetchEstados(); // Recargar la lista
        
        // Mostrar mensaje de éxito
        error.value = null;
      } catch (err) {
        console.error('Error saving estado:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el estado. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (estado) => {
      estadoToDelete.value = estado;
      showDeleteModal.value = true;
    };

    const deleteEstado = async () => {
      if (!estadoToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        // DELETE /estados/{id}
        await axios.delete(`/estados/${estadoToDelete.value.id}`);
        
        showDeleteModal.value = false;
        await fetchEstados(); // Recargar la lista
        
        // Mostrar mensaje de éxito
        error.value = null;
      } catch (err) {
        console.error('Error deleting estado:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el estado. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        estadoToDelete.value = null;
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
      fetchEstados();
    });

    return {
      estados,
      estadosFiltrados,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      estadoToDelete,
      formData,
      formErrors,
      fetchEstados,
      openModal,
      closeModal,
      saveEstado,
      confirmDelete,
      deleteEstado,
      formatDate
    };
  }
};
</script>