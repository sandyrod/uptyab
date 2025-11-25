<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Partidos</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Partido
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
          @keyup.enter="fetchPartidos"
        />
        <button
          @click="fetchPartidos"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando partidos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="partidos.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron partidos.</p>
      </div>
    </div>

    <!-- Partidos Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Votos</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="partido in partidosFiltrados" :key="partido.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ partido.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ partido.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatNumber(partido.votos) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(partido.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(partido)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(partido)"
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
            {{ isEditing ? 'Editar Partido' : 'Nuevo Partido' }}
          </h2>
          
          <form @submit.prevent="savePartido">
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
                placeholder="Ingrese el nombre del partido"
                required
              />
              <p v-if="formErrors.nombre" class="text-red-500 text-xs italic mt-1">{{ formErrors.nombre[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="votos">
                Votos
              </label>
              <input
                id="votos"
                v-model="formData.votos"
                type="number"
                min="0"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.votos}"
                placeholder="Ingrese la cantidad de votos"
                required
              />
              <p v-if="formErrors.votos" class="text-red-500 text-xs italic mt-1">{{ formErrors.votos[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el partido "{{ partidoToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deletePartido"
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
  name: 'Partidos',
  setup() {
    const partidos = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const partidoToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      votos: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar partidos en el frontend
    const partidosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return partidos.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return partidos.value.filter(partido => 
        partido.nombre.toLowerCase().includes(query) ||
        partido.votos.toString().includes(query)
      );
    });

    const fetchPartidos = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        let url = '/partidos';
        if (searchQuery.value) {
          url += `?search=${encodeURIComponent(searchQuery.value)}`;
        }
        
        const response = await axios.get(url);
        partidos.value = response.data;
        
      } catch (err) {
        console.error('Error fetching partidos:', err);
        error.value = err.response?.data?.message || 'Error al cargar los partidos. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const openModal = (partido = null) => {
      if (partido) {
        isEditing.value = true;
        formData.value = { 
          id: partido.id, 
          nombre: partido.nombre,
          votos: partido.votos
        };
      } else {
        isEditing.value = false;
        formData.value = { 
          id: null, 
          nombre: '',
          votos: ''
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
        votos: ''
      };
      formErrors.value = {};
    };

    const savePartido = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          await axios.put(`/partidos/${formData.value.id}`, {
            nombre: formData.value.nombre,
            votos: formData.value.votos
          });
        } else {
          await axios.post('/partidos', {
            nombre: formData.value.nombre,
            votos: formData.value.votos
          });
        }
        
        closeModal();
        await fetchPartidos();
        
      } catch (err) {
        console.error('Error saving partido:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el partido. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (partido) => {
      partidoToDelete.value = partido;
      showDeleteModal.value = true;
    };

    const deletePartido = async () => {
      if (!partidoToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/partidos/${partidoToDelete.value.id}`);
        
        showDeleteModal.value = false;
        await fetchPartidos();
        
      } catch (err) {
        console.error('Error deleting partido:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el partido. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        partidoToDelete.value = null;
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

    const formatNumber = (number) => {
      if (!number && number !== 0) return '';
      return new Intl.NumberFormat('es-ES').format(number);
    };

    onMounted(() => {
      fetchPartidos();
    });

    return {
      partidos,
      partidosFiltrados,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      partidoToDelete,
      formData,
      formErrors,
      fetchPartidos,
      openModal,
      closeModal,
      savePartido,
      confirmDelete,
      deletePartido,
      formatDate,
      formatNumber
    };
  }
};
</script>