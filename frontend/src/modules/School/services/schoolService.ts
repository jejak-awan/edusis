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

export interface StaffData {
    id: number;
    school_id: number;
    nip: string | null;
    nuptk: string | null;
    nik: string | null;
    full_name: string;
    title_prefix: string | null;
    title_suffix: string | null;
    gender: 'L' | 'P';
    employment_status: string;
    role_title: string;
    staff_type?: string;
    last_education: string | null;
    major: string | null;
    certification_status: boolean;
    phone: string | null;
    email: string | null;
    is_active: boolean;
    formal_name?: string;
}

export interface StaffShiftData {
    id: number;
    name: string;
    start_time: string;
    end_time: string;
    grace_period_minutes: number;
    is_active: boolean;
}

export interface StaffAttendanceData {
    id: number;
    school_id: number;
    staff_id: number;
    shift_id: number | null;
    date: string;
    time_in: string | null;
    time_out: string | null;
    status: 'present' | 'late' | 'absent' | 'sick' | 'permit' | string;
    notes: string | null;
    staff?: StaffData;
}

export interface ScheduleData {
    id: number;
    school_id: number;
    academic_year_id: number;
    semester_id: number;
    study_group_id: number;
    subject_id: number;
    teacher_id: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
    room_name: string | null;
    day_name?: string;
    study_group?: StudyGroupData;
    subject?: SubjectData;
    teacher?: StaffData;
}

export interface TeachingJournalData {
    id: number;
    school_id: number;
    study_group_id: number;
    subject_id: number;
    teacher_id: number;
    date: string;
    teaching_hour: string;
    topic: string;
    competency_description: string | null;
    learning_activity: string | null;
    present_count: number;
    sick_count: number;
    permit_count: number;
    absent_count: number;
    notes_reflection: string | null;
    study_group?: StudyGroupData;
    subject?: SubjectData;
    teacher?: StaffData;
    status?: string;
    lesson_hour_start?: number;
    lesson_hour_end?: number;
    activities?: string;
    permission_count?: number;
}

export interface StudentAttendanceItem {
    student_id: number;
    nisn: string | null;
    full_name: string;
    gender: string;
    status: 'H' | 'I' | 'S' | 'A';
    notes: string | null;
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

export interface AssessmentData {
    id: number;
    school_id: number;
    academic_year_id: number;
    semester_id: number;
    study_group_id: number;
    subject_id: number;
    teacher_id: number;
    learning_objective_id: number | null;
    title: string;
    type: 'formative' | 'summative_material' | 'summative_final';
    weight: number;
    max_score: number;
    date: string;
    description: string | null;
    study_group?: StudyGroupData;
    subject?: SubjectData;
    teacher?: StaffData;
    grades?: GradeData[];
}

export interface GradeData {
    id?: number;
    assessment_id?: number;
    student_id: number;
    nisn?: string | null;
    nis?: string | null;
    full_name?: string;
    gender?: string;
    score: number;
    remedial_score: number | null;
    notes: string | null;
}

export interface ReportCardData {
    id: number;
    school_id: number;
    academic_year_id: number;
    semester_id: number;
    study_group_id: number;
    student_id: number;
    subject_id: number;
    final_score: number;
    letter_grade: 'A' | 'B' | 'C' | 'D' | string;
    achieved_competency: string | null;
    improved_competency: string | null;
    subject?: SubjectData;
}

export interface P5ProjectData {
    id: number;
    school_id: number;
    academic_year_id: number;
    coordinator_id: number | null;
    title: string;
    theme: string;
    phase: 'E' | 'F' | string;
    description: string | null;
    coordinator?: StaffData;
    assessments_count?: number;
}

export interface CounselingCaseData {
    id: number;
    school_id: number;
    student_id: number;
    counselor_id: number;
    academic_year_id: number;
    semester_id: number;
    violation_id: number | null;
    case_number: string;
    date: string;
    category: 'pribadi' | 'sosial' | 'belajar' | 'karir';
    confidentiality: 'biasa' | 'internal' | 'rahasia' | 'sangat_rahasia';
    problem_description: string;
    counseling_steps: string | null;
    solution_plan: string | null;
    follow_up_notes: string | null;
    status: 'open' | 'in_progress' | 'resolved' | 'referred';
    student?: StudentData;
    counselor?: StaffData;
    violation?: ViolationData;
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

    // Staff & GTK
    getStaff(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/staff', { params });
    },
    storeStaff(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/staff', payload);
    },
    showStaff(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/staff/${id}`);
    },
    updateStaff(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/staff/${id}`, payload);
    },
    deleteStaff(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/staff/${id}`);
    },
    getStaffShifts(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/staff/shifts/list');
    },
    storeStaffShift(payload: Partial<StaffShiftData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/staff/shifts', payload);
    },

    // Attendances
    getStudentAttendance(params: { study_group_id: number; date: string }): Promise<AxiosResponse> {
        return api.get('/api/v1/school/attendance/students', { params });
    },
    saveBulkStudentAttendance(payload: { study_group_id: number; date: string; records: Array<{ student_id: number; status: string; notes?: string }> }): Promise<AxiosResponse> {
        return api.post('/api/v1/school/attendance/students/bulk', payload);
    },
    getStaffAttendance(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/attendance/staff', { params });
    },
    recordStaffCheckIn(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/attendance/staff/check-in', payload);
    },
    recordStaffCheckOut(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/attendance/staff/check-out', payload);
    },

    // Schedules
    getSchedules(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/schedules', { params });
    },
    storeSchedule(payload: Partial<ScheduleData>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/schedules', payload);
    },
    updateSchedule(id: number, payload: Partial<ScheduleData>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/schedules/${id}`, payload);
    },
    deleteSchedule(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/schedules/${id}`);
    },

    // Teaching Journals
    getJournals(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/journals', { params });
    },
    storeJournal(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/journals', payload);
    },
    showJournal(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/journals/${id}`);
    },
    updateJournal(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/journals/${id}`, payload);
    },
    deleteJournal(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/journals/${id}`);
    },

    // Assessments & Grades
    getAssessments(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/assessments', { params });
    },
    storeAssessment(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/assessments', payload);
    },
    showAssessment(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/assessments/${id}`);
    },
    deleteAssessment(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/assessments/${id}`);
    },
    getAssessmentGrades(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/assessments/${id}/grades`);
    },
    storeBulkGrades(id: number, grades: Array<{ student_id: number; score: number; remedial_score?: number | null; notes?: string | null }>): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/assessments/${id}/grades/bulk`, { grades });
    },

    // Reports & Ledger
    getLedger(studyGroupId: number): Promise<AxiosResponse> {
        return api.get('/api/v1/school/reports/ledger', { params: { study_group_id: studyGroupId } });
    },
    calculateReportCards(studyGroupId: number): Promise<AxiosResponse> {
        return api.post('/api/v1/school/reports/calculate', { study_group_id: studyGroupId });
    },
    getStudentReport(studentId: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/reports/student/${studentId}`);
    },

    // P5 Projects
    getP5Projects(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/p5/projects', { params });
    },
    storeP5Project(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/p5/projects', payload);
    },
    showP5Project(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/p5/projects/${id}`);
    },
    storeBulkP5Assessments(id: number, assessments: Array<{ student_id: number; dimension: string; predicate: string; notes?: string }>): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/p5/projects/${id}/assessments/bulk`, { assessments });
    },

    // Counseling (BK)
    getCounselingCases(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/counseling/cases', { params });
    },
    storeCounselingCase(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/counseling/cases', payload);
    },
    showCounselingCase(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/counseling/cases/${id}`);
    },
    updateCounselingCase(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/counseling/cases/${id}`, payload);
    },
    resolveCounselingCase(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/counseling/cases/${id}/resolve`, payload);
    },
    deleteCounselingCase(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/counseling/cases/${id}`);
    },
};
