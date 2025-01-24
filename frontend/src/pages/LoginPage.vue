
<template>
  <v-container
    fluid
    class="d-flex justify-center align-center"
    style="min-height: 100vh"
  >
    <v-row justify="center" align="center" class="text-center">
      <v-col cols="12" md="4">
        <!-- Title / Description -->
        <v-card class="pa-4" elevation="2">
          <v-row class="d-flex justify-center align-center">
            <v-col cols="auto">
              <v-img
                src="@/assets/logo.png"
                alt="Logo"
                class="mb-4"
                contain
                width="200"
                height="200"
              />
            </v-col>
          </v-row>
          <v-card-title>
            <span class="text-h5">User Leave Request System</span>
          </v-card-title>
          <v-card-subtitle class="mb-4">
            <span> Manage and track your leave requests seamlessly. </span>
          </v-card-subtitle>

          <!-- Login Form -->
          <v-form>
            <v-text-field
              v-model="email"
              label="Email"
              type="email"
              required
            ></v-text-field>
            <v-text-field
              v-model="password"
              label="Password"
              type="password"
              required
            ></v-text-field>

            <v-btn @click="login" color="primary" block> Log In </v-btn>
          </v-form>
        </v-card>
      </v-col>
    </v-row>

    <error-dialog v-model="showErrorModal" :errorMessage="responeError" />
  </v-container>
  
</template>

<script>
import ErrorDialog from "@/components/ErrorDialog.vue";
import authService from '@/services/authService';

export default {
  components: {
    ErrorDialog,
  },
  data() {
    return {
      email: '',
      password: '',
      loading: false,
      responeError: '',
      showErrorModal: false,
    };
  },
  methods: {
    async login() {
      this.loading = true;
      try {
        const response = await authService.login({
          email: this.email,
          password: this.password,
        });
        localStorage.setItem('token', response.data.data.token);
        localStorage.setItem('is_admin', response.data.data.username === 'admin');
        localStorage.setItem('role', response.data.data.role_title);
        this.$router.push({ name: 'Home' });
      } catch (error) {
        this.openErrorModal(error.response ? error.response.data.message : error.message);
      }
    },
    closeErrorModal() {
      this.showErrorModal= false;
      this.responeError = '';
    },
    openErrorModal(error) {
      this.responeError = error;
      this.showErrorModal = true;
    },
  },
};
</script>

<style scoped>
.v-container {
  background-color: #f5f5f5;
}
</style>
