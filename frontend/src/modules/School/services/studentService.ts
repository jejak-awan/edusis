import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, PaginatedData, Student, PointCategoryData } from '../types';

export class StudentService {
    public static async getStudents(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<Student>>>> {
        return api.get(schoolPaths.students.index, { params });
    }

    public static async getStudent(id: number | string): Promise<AxiosResponse<ApiResponse<Student>>> {
        return api.get(schoolPaths.students.show(id));
    }

    public static async createStudent(data: Partial<Student> | FormData): Promise<AxiosResponse<ApiResponse<Student>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        return api.post(schoolPaths.students.create, data, { headers });
    }

    public static async updateStudent(id: number | string, data: Partial<Student> | FormData): Promise<AxiosResponse<ApiResponse<Student>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        if (data instanceof FormData) {
            data.append('_method', 'PUT');
            return api.post(schoolPaths.students.update(id), data, { headers });
        }
        return api.put(schoolPaths.students.update(id), data);
    }

    public static async deleteStudent(id: number | string): Promise<AxiosResponse<ApiResponse<void>>> {
        return api.delete(schoolPaths.students.delete(id));
    }

    public static async getPointCategories(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PointCategoryData[]>>> {
        return api.get(schoolPaths.discipline.categories, { params });
    }

    public static async recordDisciplinePoint(data: {
        student_id: number;
        category_id: number | string;
        points?: number;
        description: string;
        date: string;
        sanction?: string;
    }): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.post('/school/discipline/points', data);
    }

    public static async getStudentDisciplineSummary(studentId: number | string): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.get(schoolPaths.students.disciplineSummary(studentId));
    }
}

export default StudentService;
