<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Afluencias</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Afluencia
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por evento o centro..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchAfluencias"
        />
        <button
          @click="fetchAfluencias"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando afluencias...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="afluencias.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron afluencias.</p>
      </div>
    </div>

    <!-- Afluencias Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centro de Votación</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad de Votantes</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="afluencia in afluenciasFiltrados" :key="afluencia.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ afluencia.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ afluencia.evento?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ afluencia.votacion_centro?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ afluencia.cantidadvotantes }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ afluencia.hora }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(afluencia.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(afluencia)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(afluencia)"
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
            {{ isEditing ? 'Editar Afluencia' : 'Nueva Afluencia' }}
          </h2>
          
          <form @submit.prevent="saveAfluencia">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="evento_id">
                Evento
              </label>
              <select
                id="evento_id"
                v-model="formData.evento_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.evento_id}"
                required
              >
                <option value="">Seleccione un evento</option>
                <option v-for="evento in eventos" :key="evento.id" :value="evento.id">
                  {{ evento.nombre }}
                </option>
              </select>
              <p v-if="formErrors.evento_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.evento_id[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="votacion_centro_id">
                Centro de Votación
              </label>
              <select
                id="votacion_centro_id"
                v-model="formData.votacion_centro_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.votacion_centro_id}"
                required
              >
                <option value="">Seleccione un centro de votación</option>
                <option v-for="centro in votacionCentros" :key="centro.id" :value="centro.id">
                  {{ centro.nombre }} ({{ centro.codigo }})
                </option>
              </select>
              <p v-if="formErrors.votacion_centro_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.votacion_centro_id[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="cantidadvotantes">
                Cantidad de Votantes
              </label>
              <input
                id="cantidadvotantes"
                v-model="formData.cantidadvotantes"
                type="number"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.cantidadvotantes}"
                required
              />
              <p v-if="formErrors.cantidadvotantes" class="text-red-500 text-xs italic mt-1">{{ formErrors.cantidadvotantes[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="hora">
                Hora
              </label>
              <input
                id="hora"
                v-model="formData.hora"
                type="time"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.hora}"
                required
              />
              <p v-if="formErrors.hora" class="text-red-500 text-xs italic mt-1">{{ formErrors.hora[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar la afluencia del evento "{{ afluenciaToDelete?.evento?.nombre }}" en el centro "{{ afluenciaToDelete?.votacion_centro?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteAfluencia"
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
  name: 'Afluencias',
  setup() {
    const afluencias = ref([]);
    const eventos = ref([]);
    const votacionCentros = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const afluenciaToDelete = ref(null);
    
    const formData = ref({
      id: null,
      evento_id: '',
      votacion_centro_id: '',
      cantidadvotantes: '',
      hora: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar afluencias en el frontend
    const afluenciasFiltrados = computed(() => {
      if (!searchQuery.value) {
        return afluencias.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return afluencias.value.filter(afluencia => 
        (afluencia.evento?.nombre?.toLowerCase() || '').includes(query) ||
        (afluencia.votacion_centro?.nombre?.toLowerCase() || '').includes(query) ||
        afluencia.cantidadvotantes.toString().includes(query) ||
        afluencia.hora.toLowerCase().includes(query)
      );
    });

    const fetchAfluencias = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const [afluenciasRes, eventosRes, centrosRes] = await Promise.all([
          axios.get('/afluencias'),
          axios.get('/eventos'),
          axios.get('/votacion-centros') // Asumiendo que tienes esta ruta
        ]);
        
        afluencias.value = afluenciasRes.data;
        eventos.value = eventosRes.data;
        votacionCentros.value = centrosRes.data;
        
      } catch (err) {
        console.error('Error fetching afluencias:', err);
        error.value = err.response?.data?.message || 'Error al cargar las afluencias. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const openModal = (afluencia = null) => {
      if (afluencia) {
        isEditing.value = true;
        formData.value = { 
          id: afluencia.id, 
          evento_id: afluencia.evento_id,
          votacion_centro_id: afluencia.votacion_centro_id,
          cantidadvotantes: afluencia.cantidadvotantes,
          hora: afluencia.hora
        };
      } else {
        isEditing.value = false;
        formData.value = { 
          id: null, 
          evento_id: '',
          votacion_centro_id: '',
          cantidadvotantes: '',
          hora: ''
        };
      }
      formErrors.value = {};
      showModal.value = true;
    };

    const closeModal = () => {
      showModal.value = false;
      formData.value = { 
        id: null, 
        evento_id: '',
        votacion_centro_id: '',
        cantidadvotantes: '',
        hora: ''
      };
      formErrors.value = {};
    };

    const saveAfluencia = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          await axios.put(`/afluencias/${formData.value.id}`, {
            evento_id: formData.value.evento_id,
            votacion_centro_id: formData.value.votacion_centro_id,
            cantidadvotantes: formData.value.cantidadvotantes,
            hora: formData.value.hora
          });
        } else {
          await axios.post('/afluencias', {
            evento_id: formData.value.evento_id,
            votacion_centro_id: formData.value.votacion_centro_id,
            cantidadvotantes: formData.value.cantidadvotantes,
            hora: formData.value.hora
          });
        }
        
        closeModal();
        await fetchAfluencias();
        
      } catch (err) {
        console.error('Error saving afluencia:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar la afluencia. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (afluencia) => {
      afluenciaToDelete.value = afluencia;
      showDeleteModal.value = true;
    };

    const deleteAfluencia = async () => {
      if (!afluenciaToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/afluencias/${afluenciaToDelete.value.id}`);
        
        showDeleteModal.value = false;
        await fetchAfluencias();
        
      } catch (err) {
        console.error('Error deleting afluencia:', err);
        error.value = err.response?.data?.message || 'Error al eliminar la afluencia. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        afluenciaToDelete.value = null;
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
      fetchAfluencias();
    });

    return {
      afluencias,
      afluenciasFiltrados,
      eventos,
      votacionCentros,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      afluenciaToDelete,
      formData,
      formErrors,
      fetchAfluencias,
      openModal,
      closeModal,
      saveAfluencia,
      confirmDelete,
      deleteAfluencia,
      formatDate
    };
  }
};
</script>