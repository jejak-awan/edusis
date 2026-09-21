import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, Asset, PaginatedData, Room, MaintenanceTicketData } from '../types';

export class FacilityService {
    public static async getRooms(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<Room[]>>> {
        return api.get(schoolPaths.facilities.rooms, { params });
    }

    public static async createRoom(data: Partial<Room>): Promise<AxiosResponse<ApiResponse<Room>>> {
        return api.post(schoolPaths.facilities.rooms, data);
    }

    public static async getAssets(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<Asset>>>> {
        return api.get(schoolPaths.facilities.assets, { params });
    }

    public static async createAsset(data: Partial<Asset>): Promise<AxiosResponse<ApiResponse<Asset>>> {
        return api.post(schoolPaths.facilities.assets, data);
    }

    public static async getMaintenanceTickets(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<MaintenanceTicketData[]>>> {
        return api.get(schoolPaths.facilities.tickets, { params });
    }

    public static async createMaintenanceTicket(data: Partial<MaintenanceTicketData>): Promise<AxiosResponse<ApiResponse<MaintenanceTicketData>>> {
        return api.post(schoolPaths.facilities.tickets, data);
    }

    public static async resolveMaintenanceTicket(id: number | string, data: Record<string, unknown>): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.post(`${schoolPaths.facilities.ticket(id)}/resolve`, data);
    }
}

export default FacilityService;
