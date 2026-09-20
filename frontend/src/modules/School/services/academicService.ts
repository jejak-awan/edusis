import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type {
    ApiResponse,
    AcademicYear,
    Department,
    StudyGroup,
    Subject,
    Schedule,
    TeachingJournal,
} from './types';

export class AcademicService {
    public static async getAcademicYears(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<AcademicYear[]>>> {
        return api.get('/school/academic/years', { params });
    }

    public static async createAcademicYear(data: Partial<AcademicYear>): Promise<AxiosResponse<ApiResponse<AcademicYear>>> {
        return api.post('/school/academic/years', data);
    }

    public static async getDepartments(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<Department[]>>> {
        return api.get('/school/academic/departments', { params });
    }

    public static async createDepartment(data: Partial<Department>): Promise<AxiosResponse<ApiResponse<Department>>> {
        return api.post('/school/academic/departments', data);
    }

    public static async getStudyGroups(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<StudyGroup[]>>> {
        return api.get('/school/academic/study-groups', { params });
    }

    public static async createStudyGroup(data: Partial<StudyGroup>): Promise<AxiosResponse<ApiResponse<StudyGroup>>> {
        return api.post('/school/academic/study-groups', data);
    }

    public static async getSubjects(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<Subject[]>>> {
        return api.get('/school/academic/subjects', { params });
    }

    public static async createSubject(data: Partial<Subject>): Promise<AxiosResponse<ApiResponse<Subject>>> {
        return api.post('/school/academic/subjects', data);
    }

    public static async getSchedules(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<Schedule[]>>> {
        return api.get('/school/schedules', { params });
    }

    public static async createSchedule(data: Partial<Schedule>): Promise<AxiosResponse<ApiResponse<Schedule>>> {
        return api.post('/school/schedules', data);
    }

    public static async getTeachingJournals(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<TeachingJournal[]>>> {
        return api.get('/school/journals', { params });
    }

    public static async createTeachingJournal(data: Partial<TeachingJournal>): Promise<AxiosResponse<ApiResponse<TeachingJournal>>> {
        return api.post('/school/journals', data);
    }

    public static async getAssessments(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any[]>>> {
        return api.get('/school/assessments', { params });
    }

    public static async createAssessment(data: any): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/assessments', data);
    }

    public static async saveBulkGrades(assessmentId: number, grades: any[]): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post(`/school/assessments/${assessmentId}/grades/bulk`, { grades });
    }

    public static async getGradeLedger(params: Record<string, any>): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.get('/school/reports/ledger', { params });
    }

    public static async getStudentReportCard(studentId: number | string, params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.get(`/school/reports/student/${studentId}`, { params });
    }
}

export default AcademicService;
