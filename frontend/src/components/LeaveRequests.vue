<template>
  <v-container>
    <v-btn class="float-right" text @click="openAddLeaveRequestModal"
      >Add Leave Request</v-btn
    >
    <br /><br />
    <v-table>
      <thead>
        <tr>
          <th class="text-center pa-2">Username</th>
          <th class="text-center pa-2">Start Date</th>
          <th class="text-center pa-2">End Date</th>
          <th class="text-center pa-2">Status</th>
          <th class="text-center pa-2">Created At</th>
          <th class="text-center pa-2">Updated At</th>
          <th class="text-center pa-2">Rejection Reason</th>
          <th v-if="isAdmin" class="text-center pa-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="request in leaveRequests"
          :key="request.id"
          class="hover:bg-gray-100"
        >
          <td class="pa-2">{{ request.username }}</td>
          <td class="pa-2">{{ request.start_date }}</td>
          <td class="pa-2">{{ request.end_date }}</td>
          <td class="pa-2">
            <span
              :class="{'text-green': request.status === 'approved', 'text-red': request.status === 'rejected'}"
            >
              {{ request.status }}
            </span>
          </td>
          <td class="pa-2">{{ request.created_at }}</td>
          <td class="pa-2">{{ request.updated_at }}</td>
          <td class="pa-2">{{ request.rejection_reason }}</td>
          <td class="text-center">
            <v-btn
              class="small-btn"
              dense
              v-if="isAdmin && request.status === 'pending'"
              @click="approveRequest(request.id)"
              color="green"
              small
            >
              Approve
            </v-btn>
            <v-btn
              class="small-btn"
              dense
              v-if="isAdmin && request.status === 'pending'"
              @click="rejectRequest(request.id)"
              color="red"
              small
            >
              Reject
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>

    <div class="d-flex justify-center mt-4">
      <v-btn
        v-if="pagination.prev_page_url"
        @click="fetchLeaveRequests(pagination.prev_page_url)"
        color="primary"
        outlined
      >
        Previous
      </v-btn>
      <v-btn
        v-if="pagination.next_page_url"
        @click="fetchLeaveRequests(pagination.next_page_url)"
        color="primary"
        outlined
      >
        Next
      </v-btn>
    </div>

    <v-dialog v-model="showLeaveRequestModal" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="headline">{{ 'Add Leave Request' }}</span>
        </v-card-title>
        <v-card-text>
          <v-form ref="leaveRequestForm" v-model="formValid">
            <v-text-field
              v-model="leaveRequestForm.start_date"
              label="Start Date"
              required
              type="date"
              :min="today"
            ></v-text-field>

            <v-text-field
              v-model="leaveRequestForm.end_date"
              label="End Date"
              required
              type="date"
              :rules="[endDateAfterStartDate]"
            ></v-text-field>

            <div v-if="startDate && endDate && daysDifference >= 0">
              <p>{{ daysDifference }} day(s) leave</p>
            </div>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn text color="red" @click="closeLeaveRequestModal">Cancel</v-btn>
          <v-btn
            text
            color="green"
            :disabled="!formValid"
            @click="saveLeaveRequest"
            >{{ 'Create' }}</v-btn
          >
        </v-card-actions>
      </v-card>
    </v-dialog>

    <error-dialog v-model="showErrorModal" :errorMessage="responeError" />
  </v-container>
</template>

<script>
import ErrorDialog from "@/components/ErrorDialog.vue";
import leaveRequestService from '@/services/leaveRequestService';

export default {
  components: {
    ErrorDialog,
  },
  props: {
    userID: Number,
  },
  data() {
    return {
      leaveRequests: [],
      isAdmin:  (localStorage.getItem('is_admin') == 'true') ? true : false,
      pagination: {
        next_page_url: null,
        prev_page_url: null,
        total: 0,
      },
      showLeaveRequestModal: false,
      leaveRequestForm: {
        start_date: '',
        end_date: '',
      },
      formValid: false,
      responeError : "",
      showErrorModal : false
    };

  },
  mounted() {
    this.fetchLeaveRequests();
  },
  methods: {
      openErrorModal(error) {
        this.responeError = error;
        this.showErrorModal = true;
      },
      async fetchLeaveRequests() {
        try {
          const response = await leaveRequestService.getLeaveRequests(this.userID);
          this.leaveRequests = response.data.data; 
        } catch (error) {
          this.openErrorModal(error.response ? error.response.data.message : error.message);
        }
      },
    async approveRequest(requestId) {
      try {
          await leaveRequestService.approveLeaveRequest(requestId);
          this.fetchLeaveRequests();
        } catch (error) {
          this.openErrorModal(error.response ? error.response.data.message : error.message);
        }
      },
    async rejectRequest(requestId) {
      try {
        await leaveRequestService.rejectLeaveRequest(requestId);
        this.fetchLeaveRequests();
      } catch (error) {
        this.openErrorModal(error.response ? error.response.data.message : error.message);
      }
    },
    openAddLeaveRequestModal() {
      this.resetLeaveRequestForm();
      this.showLeaveRequestModal = true;
    },
    closeLeaveRequestModal() {
      this.showLeaveRequestModal = false;
    },
    resetLeaveRequestForm() {
      this.leaveRequestForm = { start_date: '',end_date: ''};
    },
    async saveLeaveRequest() {
      try {
        await leaveRequestService.createLeaveRequest(this.leaveRequestForm);
        this.fetchLeaveRequests();
        this.closeLeaveRequestModal();
      } catch (error) {
        this.openErrorModal(error.response ? error.response.data.message : error.message);
      }
    },
  },
  computed: {
    daysDifference() {
      const start = new Date(this.leaveRequestForm.start_date);
      const end = new Date(this.leaveRequestForm.end_date);
      if (start && end) {
        const timeDiff = end - start;
        return Math.floor(timeDiff / (1000 * 3600 * 24));
      }
      return null;
    },
    endDateAfterStartDate() {
      return value => {
        const startDate = new Date(this.leaveRequestForm.start_date);
        const endDate = new Date(value);
        if (!startDate || !endDate) {
          return 'Start Date and End Date are required.';
        }
        if (endDate < startDate) {
          return 'End Date cannot be earlier than Start Date.';
        }
        return true;
      };
    },
    today() {
      const now = new Date();
      return now.toISOString().split('T')[0];
    },
    startDate() {
      return this.leaveRequestForm.start_date;
    },

    endDate() {
      return this.leaveRequestForm.end_date;
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

.v-table {
  margin-top: 20px;
}

.small-btn {
  margin-right: 10px;
  padding: 4px !important;
  min-width: 32px !important;
  height: 32px !important;
}
</style>
