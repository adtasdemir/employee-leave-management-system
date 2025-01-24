import apiClient from './axios';

export default {
  getAllNfts(params) {
    return apiClient.get('/nfts', { params });
  },
  getNftById(id) {
    return apiClient.get(`/nfts/${id}`);
  },
};
