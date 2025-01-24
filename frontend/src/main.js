import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import Toast from 'vue-toastification'; 
import 'vue-toastification/dist/index.css'; 

createApp(App)
  .use(router)
  .use(vuetify)
  .use(Toast)  
  .mount('#app');
