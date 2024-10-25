import { createStore } from 'vuex';
import event from './event';
import participant from './participant';
import presence from './presence';

export default createStore({
  modules: {
    event,        
    participant,   
    presence      
  }
});
