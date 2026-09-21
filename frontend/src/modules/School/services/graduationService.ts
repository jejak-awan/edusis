import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, AlumniData, GraduationData, GraduationYearGroup, PaginatedData } from '../types';

export class GraduationService {
    public static async getGraduates(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<GraduationData>>>> {
        return api.get('/school/graduation/graduates', { params });
    }

    public static async getAlumni(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<AlumniData>>>> {
        return api.get('/school/graduation/alumni', { params });
    }

    public static async getAlumniStats(): Promise<AxiosResponse<ApiResponse<GraduationYearGroup[]>>> {
        return api.get('/school/graduation/alumni/stats');
    }

    public static async updateAlumni(id: number | string, data: Partial<AlumniData>): Promise<AxiosResponse<ApiResponse<AlumniData>>> {
        return api.put(`/school/graduation/alumni/${id}`, data);
    }

    public static async generateSkl(studentId: number | string): Promise<AxiosResponse<ApiResponse<{ skl_number: string; pdf_url: string }>>> {
        return api.post(`/school/graduation/skl/${studentId}/generate`);
    }

    public static async getPublicAlumniDirectory(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<AlumniData>>>> {
        return api.get(schoolPaths.public.alumniDirectory, { params });
    }
}

export default GraduationService;
