import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, Asset, PaginatedData, Room } from './types';

export class FacilityService {
    public static async getRooms(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<Room[]>>> {
        return api.get('/school/facilities/rooms', { params });
    }

    public static async createRoom(data: Partial<Room>): Promise<AxiosResponse<ApiResponse<Room>>> {
        return api.post('/school/facilities/rooms', data);
    }

    public static async getAssets(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<Asset>>>> {
        return api.get('/school/facilities/assets', { params });
    }

    public static async createAsset(data: Partial<Asset>): Promise<AxiosResponse<ApiResponse<Asset>>> {
        return api.post('/school/facilities/assets', data);
    }

    public static async getMaintenanceTickets(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any[]>>> {
        return api.get('/school/facilities/tickets', { params });
    }

    public static async createMaintenanceTicket(data: any): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/facilities/tickets', data);
    }

    public static async resolveMaintenanceTicket(id: number | string, data: any): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post(`/school/facilities/tickets/${id}/resolve`, data);
    }
}

export default FacilityService;
