import axios from 'axios';

const BASE_URL = 'http://localhost:8080/api/v1/user';

const userService = {
  getUserList() {
    return axios.get(`${BASE_URL}/list`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
  },

  getUserById(userId) {
    return axios.get(`${BASE_URL}/${userId}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    });
  },

  updateUser(userId, userData) {
    return axios.put(
      `${BASE_URL}/${userId}`,
      userData,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );
  },

  createUser(userData) {
    return axios.post(
      BASE_URL,
      userData,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );
  },
};

export default userService;
