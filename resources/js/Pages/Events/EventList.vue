<template>
  <Head title="Eventos" />

  <AuthenticatedLayout>
    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <div class="flex justify-end items-center mb-5">
              <a
                href="/events/details"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500">
                Cadastrar Novo
              </a>
            </div>

            <div class="flex justify-between items-center mb-5 gap-3">
              <label class="min-w-[80px]">Nome:</label>
              <input
                v-model="filters.name"
                type="text"
                placeholder="Nome do evento"
                class="p-2 border rounded border-gray-300"/>

              <label class="min-w-[80px]">Data Inicial:</label>
              <input
                v-model="filters.start_date"
                type="date"
                class="p-2 border rounded border-gray-300"/>

              <label class="min-w-[80px]">Data Final:</label>
              <input
                v-model="filters.end_date"
                type="date"
                class="p-2 border rounded border-gray-300"/>

              <button
                @click="searchEvents"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500">
                Pesquisar
              </button>
            </div>
            <p v-if="successMessage" class="text-center text-green-500 mt-4">{{ successMessage }}</p>
            <table class="w-full border-collapse mt-5">
              <thead>
                <tr>
                  <th class="border p-3 bg-gray-200">Nome</th>
                  <th class="border p-3 bg-gray-200">Data Início</th>
                  <th class="border p-3 bg-gray-200">Data Fim</th>
                  <th class="border p-3 bg-gray-200">Duração</th>
                  <th class="border p-3 bg-gray-200">Editar</th>
                  <th class="border p-3 bg-gray-200">Excluir</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="event in filteredEvents"
                  :key="event.id"
                  class="hover:bg-gray-100">
                  <td class="border p-3 text-center">{{ event.name }}</td>
                  <td class="border p-3 text-center">{{ formatDate(event.start_date) }}</td>
                  <td class="border p-3 text-center">{{ formatDate(event.end_date) }}</td>
                  <td class="border p-3 text-center">
                    {{ calculateDuration(event.start_date, event.end_date) }}
                  </td>
                  <td class="border p-3 text-center">
                    <a :href="`/events/details/${event.id}`" class="p-2 bg-yellow-400 text-white rounded">✏️</a>
                  </td>
                  <td class="border p-3 text-center">
                    <button
                      @click="confirmDelete(event.id)"
                      class="p-2 bg-red-500 text-white rounded">
                      🗑️
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script>
import { mapActions, mapState } from 'vuex';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {
  components: {
    AuthenticatedLayout,
    Head,
  },
  data() {
    return {
      filters: {
        name: '',
        start_date: '',
        end_date: '',       
      },
      filteredEvents: [],
      successMessage: '',
    };
  },
  computed: {
    ...mapState('event', ['events']),
  },
  created() {
    this.fetchEvents(); 
  },
  methods: {
    ...mapActions('event', ['fetchEvents', 'deleteEvent']),

    searchEvents() {
      this.filteredEvents = this.events.filter((event) => {
        const matchesName = this.filters.name
          ? event.name.toLowerCase().includes(this.filters.name.toLowerCase())
          : true;
        const matchesStartDate = this.filters.start_date
          ? new Date(event.start_date) >= new Date(this.filters.start_date)
          : true;
        const matchesEndDate = this.filters.end_date
          ? new Date(event.end_date) <= new Date(this.filters.end_date)
          : true;

        return matchesName && matchesStartDate && matchesEndDate;
      });
    },

    async fetchEvents() {
      await this.$store.dispatch('event/fetchEvents'); 
      this.filteredEvents = this.events; 
    },

    formatDate(date) {
      const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
      return new Date(date).toLocaleDateString('pt-BR', options);
    },

    calculateDuration(startDate, endDate) {
      const start = new Date(startDate);
      const end = new Date(endDate);
      const diffTime = Math.abs(end - start);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      return `${diffDays} dias`;
    },

    confirmDelete(eventId) {
      if (confirm('Realmente deseja remover este registro? (Sim/Não)')) {
        this.deleteEvent(eventId).then(() => {
          this.filteredEvents = this.filteredEvents.filter(event => event.id !== eventId);
          
          this.successMessage = 'Evento excluído com sucesso!';
      
          setTimeout(() => {
            this.successMessage = '';
          }, 3000);
        });
      }
    },
  },
};
</script>
