<template>
  <Head title="Detalhes Evento" />

  <AuthenticatedLayout>
      <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="p-6 text-gray-900">
              <div class="text-center mb-6">
                <div class="flex justify-center mb-5 space-x-4">
                  <button
                    @click="showEventDetails"
                    :class="['px-4 py-2 rounded-md', activeTab === 'details' ? 'bg-blue-500 text-white' : 'bg-gray-200']">
                    Dados Evento
                  </button>
                  <button
                    @click="showParticipants"
                    :class="['px-4 py-2 rounded-md', activeTab === 'participants' ? 'bg-blue-500 text-white' : 'bg-gray-200']"
                    :disabled="!canShowParticipants">
                    Participantes
                  </button>
                </div>
              </div>
              <p v-if="successMessage" class="text-center text-green-500 mt-4">{{ successMessage }}</p>
              <div v-if="activeTab === 'details'">
                <form class="space-y-4" @submit.prevent="saveEvent">
                  <div>
                    <label class="block font-bold" for="name">Nome: *</label>
                    <input
                      v-model="event.name"
                      class="w-full p-2 border border-gray-300 rounded-md"
                      type="text"
                      id="name"
                      required/>
                  </div>

                  <div>
                    <label class="block font-bold" for="start_date">Data Inicial: *</label>
                    <input
                      v-model="event.start_date"
                      class="w-full p-2 border border-gray-300 rounded-md"
                      type="date"
                      id="start_date"
                      required/>
                  </div>

                  <div>
                    <label class="block font-bold" for="end_date">Data Final: *</label>
                    <input
                      v-model="event.end_date"
                      class="w-full p-2 border border-gray-300 rounded-md"
                      type="date"
                      id="end_date"
                      required/>
                  </div>

                  <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600" type="submit">Salvar</button>
                </form>
              </div>

              <div v-if="activeTab === 'participants' && eventId">
                <ParticipantForm 
                  :eventId="event.id" 
                  :participants="event.participants" 
                  :eventStartDate="event.start_date" 
                  :eventEndDate="event.end_date"
                  @add-participant="addParticipant" />
              </div>
            </div>
          </div>
        </div>
      </div>
  </AuthenticatedLayout>
</template>

<script>
import { mapActions, mapState } from 'vuex';
import ParticipantForm from '@/Pages/Participants/ParticipantForm.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

export default {

  components: {
    ParticipantForm,
    AuthenticatedLayout,
    Head,
  },
  
  props: {
    eventId: {
      type: String,
      default: null,
    },
  },

  data() {
    return {
      activeTab: 'details', 
      successMessage: '',
    };
  },

  computed: {
    ...mapState('event', ['event']),
    canShowParticipants() {
      return this.event.name && this.event.start_date && this.event.end_date;
    },
  },

  created() {
    if (this.eventId) {
        this.fetchEvent(this.eventId);
        this.fetchParticipants(this.eventId);
    } else {
        this.$store.commit('event/SET_EVENT', { name: '', start_date: '', end_date: '', participants: [] });
    }
  },

  watch: {
    '$store.state.participant.participants': {
        immediate: true,
        handler(participants) {
            if (participants && participants.length > 0) {
                this.event.participants = participants;
            }
        }
    }

  },
  methods: {

    async saveEvent() {
      if (this.eventId) {
        await this.$store.dispatch('event/updateEvent', this.event);
        this.successMessage = 'Evento atualizado com sucesso!';
        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      } else {
        const newEvent = await this.$store.dispatch('event/createEvent', this.event); 
        this.successMessage = 'Evento criado com sucesso!';
        setTimeout(() => {
          this.successMessage = '';
          this.$inertia.visit(`/events/details/${newEvent.id}`);
        }, 2000);

      }
    },

    addParticipant(participant) {
      this.event.participants.push(participant);
    },

    deleteParticipant(participantId) {
      this.event.participants = this.event.participants.filter(p => p.id !== participantId);
    },

    ...mapActions('event', ['fetchEvent', 'createEvent']),
    ...mapActions('participant', ['fetchParticipants', 'addParticipant']),
    showEventDetails() {
      this.activeTab = 'details';
    },

    async fetchParticipants(eventId) {
      const participants = await this.$store.dispatch('participant/fetchParticipants', eventId);
    },

    showParticipants() {
      if (this.eventId) {
        this.activeTab = 'participants';
      }
    },

  },
};
</script>