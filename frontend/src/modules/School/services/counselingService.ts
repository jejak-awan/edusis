import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, CounselingCase, PaginatedData } from '../types';

export class CounselingService {
    public static async getCases(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<CounselingCase>>>> {
        return api.get(schoolPaths.discipline.counselingCases, { params });
    }

    public static async getCase(id: number | string): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.get(schoolPaths.discipline.counselingCase(id));
    }

    public static async createCase(data: Partial<CounselingCase>): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.post(schoolPaths.discipline.counselingCases, data);
    }

    public static async updateCase(id: number | string, data: Partial<CounselingCase>): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.put(schoolPaths.discipline.counselingCase(id), data);
    }

    public static async resolveCase(id: number | string, data: { resolution: string; handling_notes?: string }): Promise<AxiosResponse<ApiResponse<CounselingCase>>> {
        return api.post(`${schoolPaths.discipline.counselingCase(id)}/resolve`, data);
    }
}

export default CounselingService;
