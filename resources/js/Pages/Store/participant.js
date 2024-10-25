import axios from 'axios';

export default {
  namespaced: true,
  state: {
    participants: [],
  },

  mutations: {
    SET_PARTICIPANTS(state, participants) {
      state.participants = participants;
    },
  },

  actions: {
    async fetchParticipants({ commit }, eventId) {
      const { data } = await axios.get(`/api/events/${eventId}/participants`);
      commit('SET_PARTICIPANTS', data);
    },

    async addParticipant({ dispatch }, { eventId, participant }) {
      try {
        const response = await axios.post(`/api/events/${eventId}/participants`, participant);
        await dispatch('fetchParticipants', eventId); 
        return response.data;
      } catch (error) {
        console.error("Erro ao adicionar participante:", error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
        throw error;
      }
    },    

    async updateParticipant({ dispatch }, { eventId, participant }) {
      try {
        const response = await axios.put(`/api/events/${eventId}/participants/${participant.id}`, participant);
        await dispatch('fetchParticipants', eventId); 
        return response.data;
      } catch (error) {
        console.error("Erro ao atualizar participante:", error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
        throw error;
      }
    },

    async deleteParticipant({ dispatch }, { eventId, participantId }) {
      try {
        const response = await axios.delete(`/api/events/${eventId}/participants/${participantId}`);
        await dispatch('fetchParticipants', eventId);
        return response.data;
      } catch (error) {
        console.error("Erro ao excluir participante:", error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
        throw error;
      }
    }
  },

};
