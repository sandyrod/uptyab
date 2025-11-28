<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Eventos</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Evento
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
          @keyup.enter="fetchEventos"
        />
        <button
          @click="fetchEventos"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando eventos...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="eventos.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron eventos.</p>
      </div>
    </div>

    <!-- Eventos Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centro de Votación</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="evento in eventosFiltrados" :key="evento.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ evento.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ evento.nombre }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ evento.votacion_centro?.nombre || 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  evento.iscomunal 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ evento.iscomunal ? 'Elección Comunal' : 'Normal' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(evento.created_at) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <button
                @click="openModal(evento)"
                class="text-indigo-600 hover:text-indigo-900 mr-4"
              >
                Editar
              </button>
              <button
                @click="confirmDelete(evento)"
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
            {{ isEditing ? 'Editar Evento' : 'Nuevo Evento' }}
          </h2>
          
          <form @submit.prevent="saveEvento">
            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
                Nombre del Evento
              </label>
              <input
                id="nombre"
                v-model="formData.nombre"
                type="text"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.nombre}"
                placeholder="Ingrese el nombre del evento"
                required
              />
              <p v-if="formErrors.nombre" class="text-red-500 text-xs italic mt-1">{{ formErrors.nombre[0] }}</p>
            </div>

            <div class="mb-4">
              <label class="block text-gray-700 text-sm font-bold mb-2" for="votacioncentro_id">
                Centro de Votación
              </label>
              <select
                id="votacioncentro_id"
                v-model="formData.votacioncentro_id"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                :class="{'border-red-500': formErrors.votacioncentro_id}"
                required
              >
                <option value="">Seleccione un centro</option>
                <option v-for="centro in votacionCentros" :key="centro.id" :value="centro.id">
                  {{ centro.nombre }}
                </option>
              </select>
              <p v-if="formErrors.votacioncentro_id" class="text-red-500 text-xs italic mt-1">{{ formErrors.votacioncentro_id[0] }}</p>
            </div>

            <!-- Campo iscomunal -->
            <div class="mb-4">
              <label class="flex items-center space-x-2">
                <input
                  id="iscomunal"
                  v-model="formData.iscomunal"
                  type="checkbox"
                  class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                />
                <span class="text-gray-700 text-sm font-bold">¿Es una elección comunal?</span>
              </label>
              <p class="text-gray-500 text-xs mt-1">Si está marcado, se guardará como elección comunal</p>
              <p v-if="formErrors.iscomunal" class="text-red-500 text-xs italic mt-1">{{ formErrors.iscomunal[0] }}</p>
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
                <span v-else>{{ isEditing ? 'Actualizar' : 'Crear' }}</span>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el evento "{{ eventoToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteEvento"
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
  name: 'Eventos',
  setup() {
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
    const eventoToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      votacioncentro_id: '',
      iscomunal: false // Valor por defecto false
    });
    
    const formErrors = ref({});

    // Computed para filtrar eventos en el frontend
    const eventosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return eventos.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return eventos.value.filter(evento => 
        evento.nombre.toLowerCase().includes(query) ||
        (evento.votacion_centro?.nombre?.toLowerCase() || '').includes(query)
      );
    });

    const fetchEventos = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        const [eventosRes, centrosRes] = await Promise.all([
          axios.get('/eventos?with=votacion_centro'),
          axios.get('/votacion-centros')
        ]);
        
        eventos.value = eventosRes.data;
        votacionCentros.value = centrosRes.data;
        
      } catch (err) {
        console.error('Error fetching eventos:', err);
        error.value = err.response?.data?.message || 'Error al cargar los eventos. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const fetchVotacionCentros = async () => {
      try {
        const response = await axios.get('/votacion-centros');
        votacionCentros.value = response.data;
      } catch (err) {
        console.error('Error fetching centros de votación:', err);
        error.value = err.response?.data?.message || 'Error al cargar los centros de votación.';
      }
    };

    const openModal = (evento = null) => {
      if (evento) {
        isEditing.value = true;
        formData.value = { 
          id: evento.id, 
          nombre: evento.nombre,
          votacioncentro_id: evento.votacioncentro_id,
          iscomunal: evento.iscomunal || false // Cargar el valor existente, por defecto false
        };
      } else {
        isEditing.value = false;
        formData.value = { 
          id: null, 
          nombre: '',
          votacioncentro_id: '',
          iscomunal: false // Por defecto false al crear nuevo
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
        votacioncentro_id: '',
        iscomunal: false // Resetear a false al cerrar
      };
      formErrors.value = {};
    };

    const saveEvento = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        if (isEditing.value) {
          await axios.put(`/eventos/${formData.value.id}`, {
            nombre: formData.value.nombre,
            votacioncentro_id: formData.value.votacioncentro_id,
            iscomunal: formData.value.iscomunal // Incluir el campo iscomunal
          });
        } else {
          await axios.post('/eventos', {
            nombre: formData.value.nombre,
            votacioncentro_id: formData.value.votacioncentro_id,
            iscomunal: formData.value.iscomunal // Incluir el campo iscomunal
          });
        }
        
        closeModal();
        await fetchEventos();
        
      } catch (err) {
        console.error('Error saving evento:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el evento. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (evento) => {
      eventoToDelete.value = evento;
      showDeleteModal.value = true;
    };

    const deleteEvento = async () => {
      if (!eventoToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/eventos/${eventoToDelete.value.id}`);
        
        showDeleteModal.value = false;
        await fetchEventos();
        
      } catch (err) {
        console.error('Error deleting evento:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el evento. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        eventoToDelete.value = null;
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
      fetchEventos();
    });

    return {
      eventos,
      eventosFiltrados,
      votacionCentros,
      loading,
      saving,
      deleting,
      error,
      showModal,
      showDeleteModal,
      isEditing,
      searchQuery,
      eventoToDelete,
      formData,
      formErrors,
      fetchEventos,
      fetchVotacionCentros,
      openModal,
      closeModal,
      saveEvento,
      confirmDelete,
      deleteEvento,
      formatDate
    };
  }
};
</script>