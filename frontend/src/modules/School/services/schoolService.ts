import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';

export interface AcademicYearData {
    id: number;
    school_id: number;
    name: string;
    start_date: string | null;
    end_date: string | null;
    is_active: boolean;
    semesters?: SemesterData[];
    active_semester?: SemesterData;
}

export interface SemesterData {
    id: number;
    academic_year_id: number;
    name: string;
    semester_number: number;
    is_active: boolean;
}

export interface DepartmentData {
    id: number;
    school_id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    students_count?: number;
}

export interface SubjectData {
    id: number;
    school_id: number;
    code: string | null;
    name: string;
    group: string;
    kkm: number;
    is_active: boolean;
}

export interface StudyGroupData {
    id: number;
    school_id: number;
    school_level_id: number | null;
    department_id: number | null;
    academic_year_id: number;
    name: string;
    capacity: number;
    is_active: boolean;
    level?: { id: number; name: string };
    department?: DepartmentData;
    academic_year?: AcademicYearData;
    students_count?: number;
    students?: StudentData[];
}

export interface StudentParentData {
    id?: number;
    student_id?: number;
    father_name?: string | null;
    father_nik?: string | null;
    father_occupation?: string | null;
    father_income?: string | null;
    father_phone?: string | null;
    mother_name?: string | null;
    mother_nik?: string | null;
    mother_occupation?: string | null;
    mother_income?: string | null;
    mother_phone?: string | null;
    guardian_name?: string | null;
    guardian_relation?: string | null;
    guardian_phone?: string | null;
    economic_status?: string | null;
    is_pkh?: boolean;
    is_kip?: boolean;
    kip_number?: string | null;
    emergency_phone?: string | null;
}

export interface StudentData {
    id: number;
    school_id: number;
    school_level_id: number | null;
    department_id: number | null;
    nisn: string | null;
    nis: string | null;
    nik: string | null;
    full_name: string;
    gender: 'L' | 'P';
    place_of_birth: string | null;
    date_of_birth: string | null;
    religion: string | null;
    address: string | null;
    phone: string | null;
    email: string | null;
    entry_year: string | null;
    status: 'active' | 'graduated' | 'transferred' | 'dropped_out';
    notes: string | null;
    level?: { id: number; name: string };
    department?: DepartmentData;
    study_groups?: StudyGroupData[];
    parent?: StudentParentData;
    total_credit_score?: number;
}

export interface PointCategoryData {
    id: number;
    name: string;
    type: 'pelanggaran' | 'prestasi';
    default_points: number;
    description: string | null;
    is_active: boolean;
}

export interface ViolationData {
    id: number;
    student_id: number;
    violation_date: string;
    sanction: string | null;
    sp_level: 'none' | 'sp1' | 'sp2' | 'sp3';
    sp_letter_number: string | null;
    is_resolved: boolean;
    student?: StudentData;
    credit_point?: {
        id: number;
        points: number;
        description: string;
        category?: PointCategoryData;
    };
}

export interface AchievementData {
    id: number;
    student_id: number;
    title: string;
    level: string;
    rank: string | null;
    type: 'akademik' | 'non_akademik';
    event_name: string;
    event_date: string;
    student?: StudentData;
}

export const SchoolService = {
    // Dashboard
    getDashboard(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/dashboard');
    },

    // Academic Years
    getYears(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/academic/years');
    },
    storeYear(payload: Partial<AcademicYearData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/academic/years', payload);
    },
    activateYear(id: number): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/academic/years/${id}/activate`);
    },
    deleteYear(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/academic/years/${id}`);
    },
    activateSemester(id: number): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/academic/semesters/${id}/activate`);
    },

    // Levels
    getLevels(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/academic/levels');
    },

    // Departments
    getDepartments(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/academic/departments');
    },
    storeDepartment(payload: Partial<DepartmentData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/academic/departments', payload);
    },
    updateDepartment(id: number, payload: Partial<DepartmentData>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/academic/departments/${id}`, payload);
    },
    deleteDepartment(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/academic/departments/${id}`);
    },

    // Subjects
    getSubjects(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/academic/subjects');
    },
    storeSubject(payload: Partial<SubjectData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/academic/subjects', payload);
    },
    updateSubject(id: number, payload: Partial<SubjectData>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/academic/subjects/${id}`, payload);
    },
    deleteSubject(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/academic/subjects/${id}`);
    },

    // Study Groups
    getStudyGroups(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/academic/study-groups', { params });
    },
    storeStudyGroup(payload: Partial<StudyGroupData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/academic/study-groups', payload);
    },
    showStudyGroup(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/academic/study-groups/${id}`);
    },
    updateStudyGroup(id: number, payload: Partial<StudyGroupData>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/academic/study-groups/${id}`, payload);
    },
    deleteStudyGroup(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/academic/study-groups/${id}`);
    },

    // Students
    getStudents(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/students', { params });
    },
    showStudent(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/students/${id}`);
    },
    storeStudent(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/students', payload);
    },
    updateStudent(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/students/${id}`, payload);
    },
    deleteStudent(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/students/${id}`);
    },

    // Discipline & SISKA Points
    getDisciplineCategories(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/discipline/categories', { params });
    },
    storeDisciplineCategory(payload: Partial<PointCategoryData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/discipline/categories', payload);
    },
    recordPoint(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/discipline/record', payload);
    },
    getViolations(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/discipline/violations', { params });
    },
    resolveViolation(id: number, notes?: string): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/discipline/violations/${id}/resolve`, { follow_up_notes: notes });
    },
    getAchievements(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/discipline/achievements', { params });
    },
};
