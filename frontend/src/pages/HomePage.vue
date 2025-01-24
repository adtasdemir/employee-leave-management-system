<template>
  <v-app>
    <v-app-bar app>
      <v-btn v-if="isAdmin" text @click="setAdminView">Users</v-btn>
      <v-btn text @click="setLeaveRequestsView">Leave Requests</v-btn>
      <v-spacer></v-spacer>
      <v-btn @click="logout">
        <v-icon>mdi-logout</v-icon>
      </v-btn>
    </v-app-bar>

    <v-main>
      <component
        :userID="userID"
        @selectUserLeaveRequest="openLeaveRequests"
        :is="currentView"
      />
    </v-main>

    <v-footer app>
      <span>&copy; 2025 Leave Request System</span>
    </v-footer>

    <error-dialog v-model="showErrorModal" :errorMessage="responeError" />
  </v-app>
</template>

<script>

import UserList from '@/components/UserList.vue';
import LeaveRequests from '@/components/LeaveRequests.vue';
import ErrorDialog from "@/components/ErrorDialog.vue";
import authService from '@/services/authService';


export default {
  components: {
    ErrorDialog,
  },
  data() {
    return {
      isAdmin: (localStorage.getItem('is_admin') == 'true') ? true : false,
      currentView:(localStorage.getItem('is_admin') == 'true') ? UserList : LeaveRequests,
      userID:'',
      responeError: '',
      showErrorModal: false,
    };
  },
  methods: {
    setAdminView() {
      this.currentView = UserList;
      this.userID = "";
    },
    setLeaveRequestsView() {
      this.currentView = LeaveRequests;
    },
    async logout() {
      try {
        await authService.logout();
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        this.$router.push('/login');
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
    openLeaveRequests(userId) {
      if (userId) {
        this.userID = userId;
        this.currentView = LeaveRequests;
      }
    },
  },
};
</script>

<style scoped>
.v-simple-table th,
.v-simple-table td {
  padding: 16px 24px;
  border-bottom: 1px solid #ddd;
}

.text-green {
  color: green;
}

.text-red {
  color: red;
}

.v-simple-table tr:hover {
  background-color: #f0f0f0;
}

.v-btn {
  margin-right: 10px;
}

.v-app-bar {
  background-color: #f5f5f5;
  color: white;
}

.v-footer {
  background-color: #2c3e50;
  color: white;
  text-align: center;
}

.v-row {
  margin-top: 20px;
}

.v-app-bar {
  color: black;
}

.small-btn {
  padding: 4px !important;
  min-width: 32px !important;
  height: 32px !important;
}
</style>
