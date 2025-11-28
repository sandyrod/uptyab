<template>
  <div class="container mx-auto p-6">
    <!-- Header and Add Button -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Gestión de Consejos Comunales</h1>
      <button
        @click="openModal()"
        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors"
      >
        Agregar Consejo Comunal
      </button>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6">
      <div class="flex">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Buscar por nombre, RIF, nombres o apellidos..."
          class="flex-1 px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
          @keyup.enter="fetchConsejos"
        />
        <button
          @click="fetchConsejos"
          class="bg-indigo-600 text-white px-4 py-2 rounded-r-md hover:bg-indigo-700 transition-colors"
        >
          Buscar
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-2 text-gray-600">Cargando consejos comunales...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error</p>
      <p>{{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="consejos.length === 0" class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-8 text-center">
        <p class="text-gray-600">No se encontraron consejos comunales.</p>
      </div>
    </div>

    <!-- Consejos Comunales Table -->
    <div v-else class="bg-white shadow overflow-hidden sm:rounded-lg">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RIF</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Representante</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Elección</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Electores</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="consejo in consejosFiltrados" :key="consejo.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ consejo.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ consejo.nombre }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ consejo.rif }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ consejo.nombres }} {{ consejo.apellidos }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(consejo.fechaelccion) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatNumber(consejo.cantidadelectores) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button
                  @click="openModal(consejo)"
                  class="text-indigo-600 hover:text-indigo-900 mr-4"
                >
                  Editar
                </button>
                <button
                  @click="confirmDelete(consejo)"
                  class="text-red-600 hover:text-red-900"
                >
                  Eliminar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-4">
            {{ isEditing ? 'Editar Consejo Comunal' : 'Nuevo Consejo Comunal' }}
          </h2>
          
          <form @submit.prevent="saveConsejo">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">
                  Nombre del Consejo Comunal *
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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="rif">
                  RIF *
                </label>
                <input
                  id="rif"
                  v-model="formData.rif"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.rif}"
                  required
                />
                <p v-if="formErrors.rif" class="text-red-500 text-xs italic mt-1">{{ formErrors.rif[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombres">
                  Nombres del Representante *
                </label>
                <input
                  id="nombres"
                  v-model="formData.nombres"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.nombres}"
                  required
                />
                <p v-if="formErrors.nombres" class="text-red-500 text-xs italic mt-1">{{ formErrors.nombres[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="apellidos">
                  Apellidos del Representante *
                </label>
                <input
                  id="apellidos"
                  v-model="formData.apellidos"
                  type="text"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.apellidos}"
                  required
                />
                <p v-if="formErrors.apellidos" class="text-red-500 text-xs italic mt-1">{{ formErrors.apellidos[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fechaelccion">
                  Fecha de Elección *
                </label>
                <input
                  id="fechaelccion"
                  v-model="formData.fechaelccion"
                  type="date"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.fechaelccion}"
                  required
                />
                <p v-if="formErrors.fechaelccion" class="text-red-500 text-xs italic mt-1">{{ formErrors.fechaelccion[0] }}</p>
              </div>

              <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="cantidadelectores">
                  Cantidad de Electores *
                </label>
                <input
                  id="cantidadelectores"
                  v-model.number="formData.cantidadelectores"
                  type="number"
                  min="0"
                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :class="{'border-red-500': formErrors.cantidadelectores}"
                  required
                />
                <p v-if="formErrors.cantidadelectores" class="text-red-500 text-xs italic mt-1">{{ formErrors.cantidadelectores[0] }}</p>
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
          <p class="mb-6">¿Estás seguro de que deseas eliminar el consejo comunal "{{ consejoToDelete?.nombre }}"?</p>
          
          <div class="flex justify-end space-x-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Cancelar
            </button>
            <button
              @click="deleteConsejo"
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
  name: 'Consejos',
  setup() {
    const consejos = ref([]);
    const loading = ref(false);
    const saving = ref(false);
    const deleting = ref(false);
    const error = ref(null);
    const showModal = ref(false);
    const showDeleteModal = ref(false);
    const isEditing = ref(false);
    const searchQuery = ref('');
    const consejoToDelete = ref(null);
    
    const formData = ref({
      id: null,
      nombre: '',
      rif: '',
      nombres: '',
      apellidos: '',
      fechaelccion: '',
      cantidadelectores: ''
    });
    
    const formErrors = ref({});

    // Computed para filtrar consejos en el frontend
    const consejosFiltrados = computed(() => {
      if (!searchQuery.value) {
        return consejos.value;
      }
      
      const query = searchQuery.value.toLowerCase();
      return consejos.value.filter(consejo => 
        (consejo.nombre?.toLowerCase() || '').includes(query) ||
        (consejo.rif?.toLowerCase() || '').includes(query) ||
        (consejo.nombres?.toLowerCase() || '').includes(query) ||
        (consejo.apellidos?.toLowerCase() || '').includes(query)
      );
    });

    const fetchConsejos = async () => {
      loading.value = true;
      error.value = null;
      
      try {
        let url = '/consejos';
        if (searchQuery.value) {
          url += `?search=${encodeURIComponent(searchQuery.value)}`;
        }
        
        const response = await axios.get(url);
        consejos.value = response.data;
        
      } catch (err) {
        console.error('Error al cargar los consejos comunales:', err);
        error.value = err.response?.data?.message || 'Error al cargar los consejos comunales. Por favor, intente nuevamente.';
      } finally {
        loading.value = false;
      }
    };

    const openModal = (consejo = null) => {
      if (consejo) {
        isEditing.value = true;
        formData.value = { 
          id: consejo.id,
          nombre: consejo.nombre,
          rif: consejo.rif,
          nombres: consejo.nombres,
          apellidos: consejo.apellidos,
          fechaelccion: formatDateForInput(consejo.fechaelccion),
          cantidadelectores: consejo.cantidadelectores
        };
      } else {
        isEditing.value = false;
        formData.value = { 
          id: null,
          nombre: '',
          rif: '',
          nombres: '',
          apellidos: '',
          fechaelccion: '',
          cantidadelectores: ''
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
        rif: '',
        nombres: '',
        apellidos: '',
        fechaelccion: '',
        cantidadelectores: ''
      };
      formErrors.value = {};
    };

    const saveConsejo = async () => {
      saving.value = true;
      formErrors.value = {};
      error.value = null;
      
      try {
        const dataToSend = {
          nombre: formData.value.nombre,
          rif: formData.value.rif,
          nombres: formData.value.nombres,
          apellidos: formData.value.apellidos,
          fechaelccion: formData.value.fechaelccion,
          cantidadelectores: formData.value.cantidadelectores
        };

        if (isEditing.value) {
          await axios.put(`/consejos/${formData.value.id}`, dataToSend);
        } else {
          await axios.post('/consejos', dataToSend);
        }
        
        closeModal();
        await fetchConsejos();
        
      } catch (err) {
        console.error('Error al guardar el consejo comunal:', err);
        if (err.response?.status === 422) {
          formErrors.value = err.response.data.errors || {};
        } else {
          error.value = err.response?.data?.message || 'Error al guardar el consejo comunal. Por favor, intente nuevamente.';
        }
      } finally {
        saving.value = false;
      }
    };

    const confirmDelete = (consejo) => {
      consejoToDelete.value = consejo;
      showDeleteModal.value = true;
    };

    const deleteConsejo = async () => {
      if (!consejoToDelete.value) return;
      
      deleting.value = true;
      error.value = null;
      
      try {
        await axios.delete(`/consejos/${consejoToDelete.value.id}`);
        showDeleteModal.value = false;
        await fetchConsejos();
        
      } catch (err) {
        console.error('Error al eliminar el consejo comunal:', err);
        error.value = err.response?.data?.message || 'Error al eliminar el consejo comunal. Por favor, intente nuevamente.';
      } finally {
        deleting.value = false;
        consejoToDelete.value = null;
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

    const formatDateForInput = (dateString) => {
      if (!dateString) return '';
      try {
        const date = new Date(dateString);
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
      } catch (error) {
        return '';
      }
    };

    const formatNumber = (number) => {
      if (number === null || number === undefined) return '0';
      return new Intl.NumberFormat('es-VE').format(number);
    };

    onMounted(() => {
      fetchConsejos();
    });

    return {
      consejos,
      consejosFiltrados,
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
      fetchConsejos,
      openModal,
      closeModal,
      saveConsejo,
      confirmDelete,
      deleteConsejo,
      formatDate,
      formatNumber
    };
  }
};
</script>