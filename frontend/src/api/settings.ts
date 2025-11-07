import axios from 'axios';

const API_BASE_URL = '/api';

export interface Setting {
  key: string;
  value: any;
  type: string;
  description?: string;
}

export const settingsApi = {
  // Get all settings
  getAll: async (): Promise<Setting[]> => {
    const response = await axios.get(`${API_BASE_URL}/settings`);
    return response.data.data;
  },

  // Get a single setting by key
  get: async (key: string): Promise<any> => {
    const response = await axios.get(`${API_BASE_URL}/settings/${key}`);
    return response.data.data.value;
  },

  // Update a setting
  update: async (key: string, value: any): Promise<void> => {
    await axios.put(`${API_BASE_URL}/settings/${key}`, { value });
  },
};
