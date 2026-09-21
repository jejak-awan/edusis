import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type {
    ApiResponse,
    AcademicYear,
    Department,
    StudyGroup,
    Subject,
    Schedule,
    TeachingJournal,
    AssessmentData,
    GradeData,
    ReportCardData,
} from '../types';

export class AcademicService {
    public static async getAcademicYears(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<AcademicYear[]>>> {
        return api.get(schoolPaths.academic.years, { params });
    }

    public static async createAcademicYear(data: Partial<AcademicYear>): Promise<AxiosResponse<ApiResponse<AcademicYear>>> {
        return api.post(schoolPaths.academic.years, data);
    }

    public static async getDepartments(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<Department[]>>> {
        return api.get(schoolPaths.academic.departments, { params });
    }

    public static async createDepartment(data: Partial<Department>): Promise<AxiosResponse<ApiResponse<Department>>> {
        return api.post(schoolPaths.academic.departments, data);
    }

    public static async getStudyGroups(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<StudyGroup[]>>> {
        return api.get(schoolPaths.academic.studyGroups, { params });
    }

    public static async createStudyGroup(data: Partial<StudyGroup>): Promise<AxiosResponse<ApiResponse<StudyGroup>>> {
        return api.post(schoolPaths.academic.studyGroups, data);
    }

    public static async getSubjects(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<Subject[]>>> {
        return api.get(schoolPaths.academic.subjects, { params });
    }

    public static async createSubject(data: Partial<Subject>): Promise<AxiosResponse<ApiResponse<Subject>>> {
        return api.post(schoolPaths.academic.subjects, data);
    }

    public static async getSchedules(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<Schedule[]>>> {
        return api.get(schoolPaths.academic.schedules, { params });
    }

    public static async createSchedule(data: Partial<Schedule>): Promise<AxiosResponse<ApiResponse<Schedule>>> {
        return api.post(schoolPaths.academic.schedules, data);
    }

    public static async getTeachingJournals(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<TeachingJournal[]>>> {
        return api.get(schoolPaths.academic.journals, { params });
    }

    public static async createTeachingJournal(data: Partial<TeachingJournal>): Promise<AxiosResponse<ApiResponse<TeachingJournal>>> {
        return api.post(schoolPaths.academic.journals, data);
    }

    public static async getAssessments(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<AssessmentData[]>>> {
        return api.get(schoolPaths.academic.assessments, { params });
    }

    public static async createAssessment(data: Partial<AssessmentData>): Promise<AxiosResponse<ApiResponse<AssessmentData>>> {
        return api.post(schoolPaths.academic.assessments, data);
    }

    public static async saveBulkGrades(assessmentId: number | string, grades: GradeData[]): Promise<AxiosResponse<ApiResponse<GradeData[]>>> {
        return api.post(`${schoolPaths.academic.assessment(assessmentId)}/grades/bulk`, { grades });
    }

    public static async getGradeLedger(params: Record<string, unknown>): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.get(schoolPaths.academic.ledger, { params });
    }

    public static async getStudentReportCard(studentId: number | string, params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<ReportCardData>>> {
        return api.get(schoolPaths.academic.studentReport(studentId), { params });
    }
}

export default AcademicService;
