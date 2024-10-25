import axios from 'axios';

const token = localStorage.getItem('authToken');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

axios.defaults.withCredentials = true;

export default {
  namespaced: true,
  state: {
    events: [],
    event: {},
  },
  
  mutations: {
    SET_EVENTS(state, events) {
      state.events = events;
    },
    SET_EVENT(state, event) {
      state.event = event;
    },
    REMOVE_EVENT(state, eventId) {
      state.events = state.events.filter(event => event.id !== eventId);
    },
  },

  actions: {
    async fetchEvents({ commit }) {
      try {
        const { data } = await axios.get('/api/events');
        commit('SET_EVENTS', data);  
      } catch (error) {
        console.error('Erro ao buscar eventos:', error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
      }
    },

    async fetchEvent({ commit }, eventId) {
      try {
        const { data } = await axios.get(`/api/events/${eventId}`);
        commit('SET_EVENT', data);  
      } catch (error) {
        console.error(`Erro ao buscar o evento com ID ${eventId}:`, error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
      }
    },

    async createEvent({ commit }, event) {
      try {
        const response = await axios.post('/api/events', event);
        commit('SET_EVENT', response.data); 
        return response.data; 
      } catch (error) {
        console.error('Erro ao criar evento:', error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
      }
    },
  
    async updateEvent({ commit }, event) {
      try {
        const response = await axios.put(`/api/events/${event.id}`, event);
        commit('SET_EVENT', response.data);  
      } catch (error) {
        console.error('Erro ao atualizar evento:', error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
      }
    },

    async deleteEvent({ commit }, eventId) {
      try {
        const response = await axios.delete(`/api/events/${eventId}`);
        commit('REMOVE_EVENT', eventId);
        return response.data;
      } catch (error) {
        console.error('Erro ao deletar evento:', error.response ? error.response.data : error.message);
        alert('Erro: ' + error.response.data.message);
      }
    }
    
  },
};
