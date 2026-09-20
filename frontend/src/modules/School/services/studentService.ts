import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, PaginatedData, Student } from './types';

export class StudentService {
    public static async getStudents(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<Student>>>> {
        return api.get('/school/students', { params });
    }

    public static async getStudent(id: number | string): Promise<AxiosResponse<ApiResponse<Student>>> {
        return api.get(`/school/students/${id}`);
    }

    public static async createStudent(data: Partial<Student> | FormData): Promise<AxiosResponse<ApiResponse<Student>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        return api.post('/school/students', data, { headers });
    }

    public static async updateStudent(id: number | string, data: Partial<Student> | FormData): Promise<AxiosResponse<ApiResponse<Student>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        if (data instanceof FormData) {
            data.append('_method', 'PUT');
            return api.post(`/school/students/${id}`, data, { headers });
        }
        return api.put(`/school/students/${id}`, data);
    }

    public static async deleteStudent(id: number | string): Promise<AxiosResponse<ApiResponse<void>>> {
        return api.delete(`/school/students/${id}`);
    }

    public static async getPointCategories(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any[]>>> {
        return api.get('/school/discipline/categories', { params });
    }

    public static async recordDisciplinePoint(data: {
        student_id: number;
        category_id: number | string;
        points?: number;
        description: string;
        date: string;
        sanction?: string;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/discipline/points', data);
    }

    public static async getStudentDisciplineSummary(studentId: number | string): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.get(`/school/students/${studentId}/discipline-summary`);
    }
}

export default StudentService;
