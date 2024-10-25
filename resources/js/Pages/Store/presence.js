import axios from 'axios';

export default {
  namespaced: true,
  state: {
    presences: [],
  },

  mutations: {
    SET_PRESENCES(state, presences) {
      state.presences = presences;
    },
  },

  actions: {
    async addAttendance({ dispatch }, { eventId, participantId, date }) {
      try {
        const response = await axios.post(`/api/events/${eventId}/attendance`, {
          participant_id: participantId,
          date: date
        });
    
        return response.data; 
      } catch (error) {
        console.error("Erro ao adicionar presença:", error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
        throw error;
      }
    }
  }

};
