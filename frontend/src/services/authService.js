import axios from 'axios';

const BASE_URL = 'http://localhost:8080/api';

const authService = {
  login(data) {
    return axios.post(`${BASE_URL}/login`, data);
  },
  logout() {
    return axios.post(
      `${BASE_URL}/logout`,
      {},
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
        },
      }
    );
  },
};

export default authService;
