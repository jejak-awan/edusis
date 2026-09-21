import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, ExtracurricularData, ExtracurricularMemberData, PaginatedData } from '../types';

export class ExtracurricularService {
    public static async getExtracurriculars(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<ExtracurricularData[] | PaginatedData<ExtracurricularData>>>> {
        return api.get(schoolPaths.extracurricular.index, { params });
    }

    public static async getExtracurricular(id: number | string): Promise<AxiosResponse<ApiResponse<ExtracurricularData>>> {
        return api.get(schoolPaths.extracurricular.show(id));
    }

    public static async createExtracurricular(data: Partial<ExtracurricularData>): Promise<AxiosResponse<ApiResponse<ExtracurricularData>>> {
        return api.post(schoolPaths.extracurricular.index, data);
    }

    public static async updateExtracurricular(id: number | string, data: Partial<ExtracurricularData>): Promise<AxiosResponse<ApiResponse<ExtracurricularData>>> {
        return api.put(schoolPaths.extracurricular.show(id), data);
    }

    public static async deleteExtracurricular(id: number | string): Promise<AxiosResponse<ApiResponse<void>>> {
        return api.delete(schoolPaths.extracurricular.show(id));
    }

    public static async getMembers(id: number | string): Promise<AxiosResponse<ApiResponse<ExtracurricularMemberData[]>>> {
        return api.get(schoolPaths.extracurricular.members(id));
    }

    public static async addMember(id: number | string, data: { student_id: number; role?: string }): Promise<AxiosResponse<ApiResponse<ExtracurricularMemberData>>> {
        return api.post(schoolPaths.extracurricular.members(id), data);
    }

    public static async removeMember(id: number | string, memberId: number | string): Promise<AxiosResponse<ApiResponse<void>>> {
        return api.delete(`${schoolPaths.extracurricular.members(id)}/${memberId}`);
    }
}

export default ExtracurricularService;
