import api from './index'
import type { ApiResponse, StarOperationRequest, StarRecord } from '@/types'

export const starsApi = {
  // Add stars to a child
  add: async (childId: number, data: StarOperationRequest): Promise<{ star_count: number }> => {
    const response = await api.post<ApiResponse<{ star_count: number }>>(
      `/children/${childId}/stars/add`,
      data
    )
    if (!response.data.data) {
      throw new Error('Failed to add stars')
    }
    return response.data.data
  },

  // Subtract stars from a child
  subtract: async (childId: number, data: StarOperationRequest): Promise<{ star_count: number }> => {
    const response = await api.post<ApiResponse<{ star_count: number }>>(
      `/children/${childId}/stars/subtract`,
      data
    )
    if (!response.data.data) {
      throw new Error('Failed to subtract stars')
    }
    return response.data.data
  },

  // Get recent star records
  getRecent: async (limit: number = 10): Promise<StarRecord[]> => {
    const response = await api.get<ApiResponse<StarRecord[]>>(
      `/stars/recent?limit=${limit}`
    )
    if (!response.data.data) {
      throw new Error('Failed to get recent star records')
    }
    return response.data.data
  },
}
