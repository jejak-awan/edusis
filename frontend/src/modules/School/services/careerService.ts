import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, JobVacancyData, JobApplicationData, CareerStats, PaginatedData } from '../types';

export class CareerService {
    public static async getVacancies(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<JobVacancyData>>>> {
        return api.get(schoolPaths.career.vacancies, { params });
    }

    public static async getVacancy(id: number | string): Promise<AxiosResponse<ApiResponse<JobVacancyData>>> {
        return api.get(schoolPaths.career.vacancy(id));
    }

    public static async createVacancy(data: Partial<JobVacancyData>): Promise<AxiosResponse<ApiResponse<JobVacancyData>>> {
        return api.post(schoolPaths.career.vacancies, data);
    }

    public static async updateVacancy(id: number | string, data: Partial<JobVacancyData>): Promise<AxiosResponse<ApiResponse<JobVacancyData>>> {
        return api.put(schoolPaths.career.vacancy(id), data);
    }

    public static async deleteVacancy(id: number | string): Promise<AxiosResponse<ApiResponse<void>>> {
        return api.delete(schoolPaths.career.vacancy(id));
    }

    public static async applyVacancy(vacancyId: number | string, data: Partial<JobApplicationData>): Promise<AxiosResponse<ApiResponse<JobApplicationData>>> {
        return api.post(schoolPaths.career.apply(vacancyId), data);
    }

    public static async getApplications(vacancyId: number | string): Promise<AxiosResponse<ApiResponse<JobApplicationData[]>>> {
        return api.get(schoolPaths.career.applications(vacancyId));
    }

    public static async updateApplicationStatus(
        applicationId: number | string,
        status: string,
        notes?: string
    ): Promise<AxiosResponse<ApiResponse<JobApplicationData>>> {
        return api.put(schoolPaths.career.updateApplicationStatus(applicationId), { status, notes });
    }

    public static async getStats(): Promise<AxiosResponse<ApiResponse<CareerStats>>> {
        return api.get(schoolPaths.career.stats);
    }
}

export default CareerService;
