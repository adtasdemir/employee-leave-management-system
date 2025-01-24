import axios from 'axios';

const BASE_URL = 'http://localhost:8080/api/v1/leave-request';

const leaveRequestService = {
  getLeaveRequests(userID) {
    let apiUrl;
    if (localStorage.getItem('is_admin') == 'true') {
      apiUrl = userID
        ? `${BASE_URL}/list?user_id=${userID}`
        : `${BASE_URL}/list`;
    } else {
      apiUrl = `${BASE_URL}`;
    }
  
    return axios.get(apiUrl, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
  },

  approveLeaveRequest(requestId) {
    return axios.put(
      `${BASE_URL}/approve-request/${requestId}`,
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );
  },

  rejectLeaveRequest(requestId) {
    return axios.put(
      `${BASE_URL}/reject-request/${requestId}`,
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );
  },

  createLeaveRequest(data) {
    return axios.post(
      BASE_URL,
      data,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );
  },
};

export default leaveRequestService;
