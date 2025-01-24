<template>
  <v-container>
    <v-btn class="float-right" text @click="openAddUserModal">Add User</v-btn>
    <br /><br />
    <v-table>
      <thead>
        <tr>
          <th class="text-center pa-2">Name</th>
          <th class="text-center pa-2">Username</th>
          <th class="text-center pa-2">Email</th>
          <th class="text-center pa-2">Status</th>
          <th class="text-center pa-2">Annual Leave</th>
          <th class="text-center pa-2">Role</th>
          <th class="text-center pa-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-100">
          <td class="pa-2">{{ user.name }} {{ user.surname }}</td>
          <td class="pa-2">{{ user.username }}</td>
          <td class="pa-2">{{ user.email }}</td>
          <td class="pa-2">
            <span
              :class="{'text-green': user.status === 'active', 'text-red': user.status !== 'active'}"
            >
              {{ user.status }}
            </span>
          </td>
          <td class="pa-2">
            {{ user.remaining_annual_leave_days }} /
            {{ user.annual_leave_days }}
          </td>
          <td class="pa-2">{{ user.role_title }}</td>
          <td class="text-center">
            <v-btn
              class="small-btn"
              dense
              @click="openLeaveRequests(user.id)"
              color="grey"
            >
              <v-icon small>mdi-format-list-bulleted</v-icon>
            </v-btn>
            <v-btn
              class="small-btn"
              dense
              @click="openEditUserModal(user)"
              color="primary"
            >
              <v-icon small>mdi-pencil</v-icon>
            </v-btn>
            <v-btn
              class="small-btn"
              dense
              @click="deleteUser(user.id)"
              color="red"
            >
              <v-icon small>mdi-delete</v-icon>
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>

    <v-dialog v-model="showUserModal" max-width="500px">
      <v-card>
        <v-card-title>
          <span
            class="headline"
            >{{ isEditMode ? 'Edit User' : 'Add User' }}</span
          >
        </v-card-title>
        <v-card-text>
          <v-form ref="userForm" v-model="formValid">
            <v-text-field
              v-model="userForm.name"
              label="Name"
              required
            ></v-text-field>
            <v-text-field
              v-model="userForm.surname"
              label="Surname"
              required
            ></v-text-field>
            <v-text-field
              v-model="userForm.email"
              label="Email"
              required
            ></v-text-field>
            <v-text-field
              v-model="userForm.username"
              label="Username"
              required
            ></v-text-field>
            <v-select
              v-if="isEditMode"
              v-model="userForm.status"
              :items="['active', 'inactive']"
              label="Status"
              required
            ></v-select>
            <v-text-field
              v-if="!isEditMode"
              v-model="userForm.password"
              type="password"
              label="Password"
              required
            ></v-text-field>
            <v-text-field
              v-if="!isEditMode"
              v-model="userForm.password_confirmation"
              type="password"
              label="Confirm Password"
              required
            ></v-text-field>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text color="red" @click="closeUserModal">Cancel</v-btn>
          <v-btn
            text
            color="green"
            :disabled="!formValid"
            @click="saveUser"
            >{{ isEditMode ? 'Update' : 'Create' }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <error-dialog v-model="showErrorModal" :errorMessage="responeError" />
  </v-container>
</template>

<script>
import ErrorDialog from "@/components/ErrorDialog.vue";
import UserService from '@/services/userService';

export default {
    components: {
      ErrorDialog,
    },
    data() {
    return {
      users: [],
      showUserModal: false,
      isEditMode: false,
      userForm: {
        id: null,
        email: '',
        password: '',
        name: '',
        surname: '',
        password_confirmation: '',
        username: '',
      },
      formValid: false,
      responeError: '',
      showErrorModal: false,
    };
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async openEditUserModal(user) {
      try {
        this.isEditMode = true;
        const response = await UserService.getUserById(user.id);
        this.userForm = { ...response.data.data, password: '', password_confirmation: '' };
        this.showUserModal = true;
      } catch (error) {
        this.openErrorModal(error.response ? error.response.data.message : error.message);
      }
    },
    async fetchUsers() {
      try {
        const response = await UserService.getUserList();
        this.users = response.data.data; 
      } catch (error) {
        this.openErrorModal(error.response ? error.response.data.message : error.message);
      }
    },
    async saveUser() {
      try {
        if (this.isEditMode) {
          await UserService.updateUser(this.userForm.id,this.userForm);
        } else {
          await UserService.createUser(this.userForm);
        }
        this.fetchUsers();
        this.closeUserModal();
      } catch (error) {
        this.openErrorModal(error.response ? error.response.data.message : error.message);
      }
    },
    openAddUserModal() {
      this.isEditMode = false;
      this.resetUserForm();
      this.showUserModal = true;
    },
    openLeaveRequests(userId) {
      this.$emit('selectUserLeaveRequest', userId);
    },
    closeErrorModal() {
        this.showErrorModal= false;
        this.responeError = '';
    },
    openErrorModal(error) {
      this.responeError = error;
      this.showErrorModal = true;
    },
    closeUserModal() {
      this.showUserModal = false;
    },
    resetUserForm() {
      this.userForm = {
        id: null,
        email: '',
        password: '',
        name: '',
        surname: '',
        password_confirmation: '',
        username: '',
      };
    },
  },
};
</script>

<style scoped>
.text-green {
  color: green;
}

.text-red {
  color: red;
}

.v-table tr:hover {
  background-color: #f0f0f0;
}

.small-btn {
  margin-right: 2px;
  padding: 4px !important;
  min-width: 32px !important;
  height: 32px !important;
}

.float-right {
  float: right;
}
</style>
