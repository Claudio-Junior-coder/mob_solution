<template>
  <div class="m-5">
    <button
      class="mb-4 px-4 py-2 bg-black text-white rounded cursor-pointer"
      @click="openParticipantModal(null)"
    >
      Adicionar participante
    </button>
    <p v-if="successMessage" class="text-center text-green-500 py-3">{{ successMessage }}</p>
    <table class="w-full border-collapse">
      <thead>
        <tr>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">Nome</th>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">CPF</th>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">E-mail</th>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">% participação</th>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">Editar</th>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">Excluir</th>
          <th class="p-2 border border-gray-300 bg-gray-200 font-bold">Adc. presença</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="participant in participants" :key="participant.id" class="hover:bg-gray-100">
          <td class="p-2 border border-gray-300">{{ participant.name }}</td>
          <td class="p-2 border border-gray-300">{{ participant.cpf }}</td>
          <td class="p-2 border border-gray-300">{{ participant.email }}</td>
          <td class="p-2 border border-gray-300">{{ calculateParticipation(participant) }}%</td>
          <td class="p-2 border border-gray-300">
            <button @click="openParticipantModal(participant)" class="cursor-pointer">📝</button>
          </td>
          <td class="p-2 border border-gray-300"> 
            <button @click="confirmDelete(participant.id)" class="cursor-pointer">🗑️</button>
          </td>
          <td class="p-2 border border-gray-300">
            <button @click="openAttendanceModal(participant.id)" class="cursor-pointer">✔️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal de Cadastro de Participante -->
    <div v-if="showParticipantModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-md w-80 text-center">
        <h3 class="mb-5 text-lg font-bold">{{ isEdit ? 'Editar Participante' : 'Cadastro Participante' }}</h3>
        <form @submit.prevent="saveParticipant">
          <label for="name" class="block text-left">Nome:</label>
          <input type="text" v-model="newParticipant.name" required class="w-full mb-3 p-2 border rounded" />

          <label for="cpf" class="block text-left">CPF:</label>
          <input type="text" v-model="newParticipant.cpf" required class="w-full mb-3 p-2 border rounded" :disabled="isEdit" />

          <label for="email" class="block text-left">E-mail:</label>
          <input type="email" v-model="newParticipant.email" required class="w-full mb-3 p-2 border rounded" />

          <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">Salvar</button>
          <button type="button" @click="closeParticipantModal" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
        </form>
      </div>
    </div>

    <!-- Modal de Adicionar Presença -->
    <div v-if="showAttendanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-md w-80 text-center">
        <h3 class="mb-5 text-lg font-bold">Adicionar Presença</h3>
        <form @submit.prevent="addPresence">
          <p>{{ selectedParticipant.name }}</p> <br>
          <p class="mb-4"> (CPF: {{ selectedParticipant.cpf }})</p>
          <label for="date" class="block text-left">Data:</label>
          <input type="date" v-model="attendanceDate" name="date" required class="w-full mb-3 p-2 border rounded" />

          <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">Salvar</button>
          <button type="button" @click="closeAttendanceModal" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
        </form>
      </div>
    </div>


    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-md w-80 text-center">
        <h3 class="mb-5 text-lg font-bold">Confirmar Exclusão</h3>
        <p class="mb-4">Realmente deseja remover este registro?</p>
        <button @click="deleteParticipant" class="px-4 py-2 bg-red-500 text-white rounded mr-2">Sim</button>
        <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-300 rounded">Não</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {

  props: {
    participants: {
      type: Array,
      required: true,
      default: () => []
    },
    eventId: {
      type: Number,
      required: true
    },
    eventStartDate: {
      type: String, 
      required: true
    },
    eventEndDate: {
      type: String, 
      required: true
    }
  },

  data() {
    return {
      showParticipantModal: false,
      showAttendanceModal: false,
      showDeleteModal: false,
      selectedParticipant: {},
      isEdit: false,
      successMessage: '',
      newParticipant: {
        id: null,
        name: '',
        cpf: '',
        email: '',
      },
      attendanceDate: '', 
    };
  },

  methods: {
    calculateParticipation(participant) {
      const totalDays = this.getTotalEventDays();
      const participantPresences = participant.presences ? participant.presences.length : 0;

      if (totalDays === 0) {
        return '--';
      }

      const participationPercentage = (participantPresences / totalDays) * 100;
      return participationPercentage.toFixed(2);
    },

    getTotalEventDays() {
      const startDate = new Date(this.eventStartDate);
      const endDate = new Date(this.eventEndDate);
      const diffTime = Math.abs(endDate - startDate);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

      return diffDays;
    },

    openParticipantModal(participant) {
      if (participant) {
        this.isEdit = true;
        this.newParticipant = { ...participant };
      } else {
        this.isEdit = false;
        this.newParticipant = { id: null, name: '', cpf: '', email: '' };
      }
      this.showParticipantModal = true;
    },

    closeParticipantModal() {
      this.showParticipantModal = false;
    },

    async saveParticipant() {
      if (this.isEdit) {
        await this.$store.dispatch('participant/updateParticipant', {
          eventId: this.eventId,
          participant: this.newParticipant
        });
        this.successMessage = 'Participante atualizado com sucesso!';
      } else {
        const existingParticipant = this.participants.find(participant => participant.cpf === this.newParticipant.cpf);
        if (existingParticipant) {
          alert("Já existe um participante com esse CPF.");
          return;
        }
        
        await this.$store.dispatch('participant/addParticipant', {
          eventId: this.eventId,
          participant: this.newParticipant
        });
        this.successMessage = 'Participante adicionado com sucesso!';
      }

      this.closeParticipantModal();
      setTimeout(() => {
        this.successMessage = '';
      }, 3000);
    },

    confirmDelete(id) {
      this.selectedParticipant = this.participants.find(participant => participant.id === id);
      this.showDeleteModal = true;
    },

    async deleteParticipant() {
      await this.$store.dispatch('participant/deleteParticipant', {
        eventId: this.eventId,
        participantId: this.selectedParticipant.id
      });

      const index = this.participants.findIndex(p => p.id === this.selectedParticipant.id);
      if (index !== -1) {
        this.participants.splice(index, 1);  
      }

      this.successMessage = 'Participante excluído com sucesso!';
      
      this.closeDeleteModal();
        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
    },    

    closeDeleteModal() {
      this.showDeleteModal = false;
    },

    async addPresence() {
      if (!this.attendanceDate) {
        alert('Por favor, selecione uma data.');
        return;
      }

      const response = await this.$store.dispatch('presence/addAttendance', {
        eventId: this.eventId,
        participantId: this.selectedParticipant.id,
        date: this.attendanceDate
      });

      const updatedParticipant = response.participant; 

      const index = this.participants.findIndex(participant => participant.id === updatedParticipant.id);
      if (index !== -1) {
        this.participants.splice(index, 1, updatedParticipant);  
      }

      this.closeAttendanceModal();
      setTimeout(() => {
        this.successMessage = '';
      }, 3000);
    },

    openAttendanceModal(id) {
      this.selectedParticipant = this.participants.find(participant => participant.id === id);
      this.showAttendanceModal = true;
    },

    closeAttendanceModal() {
      this.showAttendanceModal = false;
      this.attendanceDate = '';  
    }
  }
  
};
</script>
