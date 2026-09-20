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

export interface LetterData {
    id: number;
    school_id: number;
    type: 'incoming' | 'outgoing';
    letter_number: string;
    reference_number: string | null;
    category: 'invitation' | 'assignment' | 'decree' | 'certificate' | 'circular' | 'general';
    sender: string;
    recipient: string;
    subject: string;
    letter_date: string;
    received_or_sent_date: string;
    status: 'draft' | 'registered' | 'dispatched' | 'archived';
    disposition_to: string | null;
    disposition_instruction: string | null;
    disposition_status: 'pending' | 'processed' | 'completed';
    attachment_path: string | null;
    notes: string | null;
}

export interface PpdbData {
    id: number;
    school_id: number;
    academic_year_id: number;
    department_id: number;
    second_choice_department_id: number | null;
    registration_number: string;
    track: 'zonasi' | 'afirmasi' | 'prestasi_akademik' | 'prestasi_non_akademik' | 'perpindahan_ortu';
    full_name: string;
    nisn: string | null;
    nik: string | null;
    gender: 'L' | 'P';
    place_of_birth: string | null;
    date_of_birth: string | null;
    religion: string | null;
    phone: string | null;
    email: string | null;
    address: string | null;
    parent_name: string | null;
    parent_phone: string | null;
    parent_job: string | null;
    parent_income: string | null;
    previous_school: string | null;
    average_report_score: number;
    status: 'submitted' | 'verified' | 'accepted' | 'rejected';
    document_paths: Record<string, string> | null;
    verified_by: number | null;
    verified_at: string | null;
    notes: string | null;
    department?: DepartmentData;
    second_choice_department?: DepartmentData;
    academic_year?: AcademicYearData;
    verifier?: StaffData;
}

export interface GuestBookData {
    id: number;
    school_id: number;
    visitor_name: string;
    institution: string | null;
    phone: string | null;
    identity_type: 'KTP' | 'SIM' | 'Passport' | 'Lainnya';
    identity_number: string | null;
    purpose: string;
    target_person: string;
    check_in_at: string;
    check_out_at: string | null;
    badge_number: string | null;
    status: 'checked_in' | 'checked_out';
    notes: string | null;
}

export interface OsisCandidateData {
    id: number;
    election_id: number;
    candidate_number: number;
    chairman_student_id: number;
    vice_chairman_student_id: number;
    vision: string;
    mission: string;
    photo_url: string | null;
    vote_count: number;
    chairman_name?: string;
    vice_chairman_name?: string;
    chairman?: StudentData;
    vice_chairman?: StudentData;
    percentage?: number;
}

export interface OsisElectionData {
    id: number;
    school_id: number;
    academic_year_id: number;
    title: string;
    period: string;
    start_date: string;
    end_date: string;
    status: 'draft' | 'active' | 'closed';
    candidates?: OsisCandidateData[];
    total_votes?: number;
}

export interface ExtracurricularData {
    id: number;
    school_id: number;
    name: string;
    category: 'wajib' | 'pilihan';
    advisor_staff_id: number | null;
    schedule_day: string | null;
    location: string | null;
    description: string | null;
    is_active: boolean;
    advisor?: StaffData;
}

export interface RoomData {
    id: number;
    school_id: number;
    code: string;
    name: string;
    type: 'classroom' | 'laboratory' | 'workshop' | 'library' | 'hall' | 'office' | 'other';
    capacity: number;
    floor: number;
    condition: 'baik' | 'rusak_ringan' | 'rusak_berat';
    description: string | null;
    assets_count?: number;
    assets?: AssetData[];
}

export interface AssetData {
    id: number;
    school_id: number;
    room_id: number | null;
    code: string;
    name: string;
    category: 'elektronik' | 'mebeler' | 'alat_praktik' | 'kendaraan' | 'buku' | 'lainnya';
    brand_model: string | null;
    acquisition_year: number | null;
    quantity: number;
    condition: 'baik' | 'rusak_ringan' | 'rusak_berat';
    source_of_fund: string;
    notes: string | null;
    room?: RoomData;
    maintenance_tickets?: MaintenanceTicketData[];
}

export interface MaintenanceTicketData {
    id: number;
    school_id: number;
    room_id: number;
    asset_id: number | null;
    reporter_staff_id: number;
    ticket_number: string;
    issue_description: string;
    priority: 'rendah' | 'sedang' | 'tinggi' | 'darurat';
    status: 'open' | 'in_progress' | 'resolved' | 'cancelled';
    cost: number;
    resolution_notes: string | null;
    resolved_at: string | null;
    created_at: string;
    room?: RoomData;
    asset?: AssetData;
    reporter?: { id: number; full_name: string; nip?: string };
}

export interface KatampiConfigData {
    id: number;
    school_id: number;
    cbt_url: string;
    api_key: string | null;
    is_active: boolean;
    last_sync_at: string | null;
    sync_stats: Record<string, unknown> | null;
}

export interface WhatsAppLogData {
    id: number;
    school_id: number;
    student_id: number | null;
    recipient_phone: string;
    recipient_name: string | null;
    message_type: 'attendance_alert' | 'discipline_sp' | 'achievement' | 'announcement' | 'general';
    content: string;
    status: 'queued' | 'sent' | 'delivered' | 'failed';
    sent_at: string | null;
    error_message: string | null;
    created_at: string;
    student?: { id: number; full_name: string; nisn?: string };
}

export interface WhatsAppStatsData {
    total: number;
    sent: number;
    failed: number;
    queued?: number;
    delivered?: number;
    attendance_alerts: number;
    announcements: number;
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

    // Letters & Correspondence
    getLetters(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/letters', { params });
    },
    generateLetterNumber(params: { type: string; category: string }): Promise<AxiosResponse> {
        return api.get('/api/v1/school/letters/generate-number', { params });
    },
    storeLetter(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/letters', payload);
    },
    showLetter(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/letters/${id}`);
    },
    updateLetter(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/letters/${id}`, payload);
    },
    updateLetterDisposition(id: number, payload: { disposition_to: string; disposition_instruction: string; disposition_status: string }): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/letters/${id}/disposition`, payload);
    },
    deleteLetter(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/letters/${id}`);
    },

    // PPDB Online
    getPpdbList(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/ppdb', { params });
    },
    storePpdb(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/ppdb', payload);
    },
    showPpdb(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/ppdb/${id}`);
    },
    updatePpdb(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/ppdb/${id}`, payload);
    },
    verifyPpdb(id: number, payload: { status: string; notes?: string; verified_by?: number }): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/ppdb/${id}/verify`, payload);
    },
    admitPpdb(id: number): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/ppdb/${id}/admit`);
    },
    deletePpdb(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/ppdb/${id}`);
    },

    // Guest Book
    getGuestBook(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/guest-book', { params });
    },
    storeGuest(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/guest-book', payload);
    },
    showGuest(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/guest-book/${id}`);
    },
    updateGuest(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/guest-book/${id}`, payload);
    },
    checkoutGuest(id: number, notes?: string): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/guest-book/${id}/checkout`, { notes });
    },
    deleteGuest(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/guest-book/${id}`);
    },

    // OSIS & E-Voting
    getOsisElections(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/osis/elections', { params });
    },
    storeOsisElection(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/osis/elections', payload);
    },
    showOsisElection(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/osis/elections/${id}`);
    },
    updateOsisElection(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/osis/elections/${id}`, payload);
    },
    deleteOsisElection(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/osis/elections/${id}`);
    },
    addOsisCandidate(electionId: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/osis/elections/${electionId}/candidates`, payload);
    },
    voteOsis(electionId: number, payload: { candidate_id: number; voter_student_id: number; token?: string }): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/osis/elections/${electionId}/vote`, payload);
    },
    getOsisQuickCount(electionId: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/osis/elections/${electionId}/quick-count`);
    },

    // Extracurriculars
    getExtracurriculars(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/extracurriculars', { params });
    },
    storeExtracurricular(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/extracurriculars', payload);
    },
    showExtracurricular(id: number): Promise<AxiosResponse> {
        return api.get(`/api/v1/school/extracurriculars/${id}`);
    },
    updateExtracurricular(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/extracurriculars/${id}`, payload);
    },
    deleteExtracurricular(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/extracurriculars/${id}`);
    },

    // Facilities - Rooms
    getRooms(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/facilities/rooms', { params });
    },
    storeRoom(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/facilities/rooms', payload);
    },
    updateRoom(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/facilities/rooms/${id}`, payload);
    },
    deleteRoom(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/facilities/rooms/${id}`);
    },

    // Facilities - Assets
    getAssets(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/facilities/assets', { params });
    },
    storeAsset(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/facilities/assets', payload);
    },
    updateAsset(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/facilities/assets/${id}`, payload);
    },
    deleteAsset(id: number): Promise<AxiosResponse> {
        return api.delete(`/api/v1/school/facilities/assets/${id}`);
    },

    // Facilities - Maintenance Tickets
    getTickets(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/facilities/tickets', { params });
    },
    storeTicket(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/facilities/tickets', payload);
    },
    updateTicket(id: number, payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put(`/api/v1/school/facilities/tickets/${id}`, payload);
    },
    resolveTicket(id: number, payload: { resolution_notes: string; cost?: number; status?: string }): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/facilities/tickets/${id}/resolve`, payload);
    },

    // KataMPI CBT Integration
    getKatampiConfig(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/integrations/katampi/config');
    },
    updateKatampiConfig(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.put('/api/v1/school/integrations/katampi/config', payload);
    },
    generateKatampiSsoToken(payload: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.post('/api/v1/school/integrations/katampi/sso-token', payload);
    },
    getKatampiRoster(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/integrations/katampi/roster', { params });
    },
    syncKatampiGrades(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/integrations/katampi/sync-grades', payload);
    },

    // WhatsApp Gateway
    getWhatsAppLogs(params: Record<string, unknown> = {}): Promise<AxiosResponse> {
        return api.get('/api/v1/school/communications/whatsapp/logs', { params });
    },
    getWhatsAppStats(): Promise<AxiosResponse> {
        return api.get('/api/v1/school/communications/whatsapp/stats');
    },
    sendWhatsAppMessage(payload: Record<string, unknown>): Promise<AxiosResponse> {
        return api.post('/api/v1/school/communications/whatsapp/send', payload);
    },
    sendWhatsAppAttendanceAlerts(payload: { date?: string; statuses?: string[] } = {}): Promise<AxiosResponse> {
        return api.post('/api/v1/school/communications/whatsapp/alerts/attendance', payload);
    },
    resendWhatsAppLog(id: number): Promise<AxiosResponse> {
        return api.post(`/api/v1/school/communications/whatsapp/logs/${id}/resend`);
    },
};

