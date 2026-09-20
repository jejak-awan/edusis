import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, CounselingCase, PaginatedData } from './types';

export class CounselingService {
    public static async getCases(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<CounselingCase>>>> {
        return api.get('/school/counseling/cases', { params });
    }

    public static async getCase(id: number | string): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.get(`/school/counseling/cases/${id}`);
    }

    public static async createCase(data: Partial<CounselingCase>): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.post('/school/counseling/cases', data);
    }

    public static async updateCase(id: number | string, data: Partial<CounselingCase>): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.put(`/school/counseling/cases/${id}`, data);
    }

    public static async resolveCase(id: number | string, data: { resolution: string; handling_notes?: string }): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.post(`/school/counseling/cases/${id}/resolve`, data);
    }
}

export default CounselingService;
