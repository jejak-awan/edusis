/**
 * School Service — Extended Domain Types
 *
 * Interface TypeScript yang lebih spesifik dan detail untuk setiap subdomain School.
 * File ini merupakan pelengkap dari `types.ts` (yang berisi interface umum/bersama).
 *
 * Dipecah dari schoolService.ts monolith untuk menegakkan SoC & SoT (ADR-031 Bab 2.B.1).
 */

// ─── Academic ────────────────────────────────────────────────────────────────

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
    grade_level?: number;
    homeroom_teacher?: StaffData;
    homeroom_teacher_id?: number;
    current_students_count?: number;
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
    room?: RoomData;
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
    schedule_id?: number;
    period_start?: number;
    period_end?: number;
    subtopic?: string;
    activity_description?: string;
    attendance_present?: number;
    attendance_sick?: number;
    attendance_permission?: number;
    attendance_absent?: number;
    notes?: string | null;
}

export interface StudentAttendanceItem {
    student_id: number;
    nisn: string | null;
    full_name: string;
    gender: string;
    status: 'H' | 'I' | 'S' | 'A';
    notes: string | null;
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

// ─── Student ─────────────────────────────────────────────────────────────────

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
    entry_year: string | number | null;
    status: 'active' | 'graduated' | 'transferred' | 'dropped_out';
    notes: string | null;
    level?: { id: number; name: string };
    department?: DepartmentData;
    study_groups?: StudyGroupData[];
    parent?: StudentParentData;
    total_credit_score?: number;
    father_name?: string | null;
    father_nik?: string | null;
    father_occupation?: string | null;
    father_phone?: string | null;
    mother_name?: string | null;
    mother_nik?: string | null;
    mother_occupation?: string | null;
    mother_phone?: string | null;
    guardian_name?: string | null;
    guardian_phone?: string | null;
    study_group_id?: number | null;
    previous_school?: string | null;
    is_kip_recipient?: boolean | null;
    kip_number?: string | null;
    rt?: string | null;
    rw?: string | null;
    dusun?: string | null;
    desa_kelurahan?: string | null;
    kecamatan?: string | null;
    postal_code?: string | null;
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

// ─── HR / Staff ──────────────────────────────────────────────────────────────

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

// ─── Operations (Letters, PPDB, Guest Book) ───────────────────────────────────

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

// ─── Student Affairs / OSIS & Voting ─────────────────────────────────────────

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

export type VotingEventType = 'osis' | 'mpk' | 'ekskul' | 'guru_favorit' | 'polling' | 'komite';
export type VotingMode = 'token_kiosk' | 'online_login' | 'hybrid';
export type TargetAudience = 'all_students' | 'specific_classes' | 'teachers_only' | 'parents' | 'all';

export interface VoteCandidateData {
    id: number;
    event_id: number;
    candidate_number: number;
    title?: string;
    name?: string;
    leader_student_id?: number | null;
    vice_student_id?: number | null;
    staff_id?: number | null;
    vision?: string | null;
    mission?: string | null;
    photo_url?: string | null;
    vote_count?: number;
    votes_count?: number;
    percentage?: number;
    leader_name?: string;
    vice_name?: string;
    leader?: StudentData;
    vice?: StudentData;
    staff?: StaffData;
}

export interface VoteVoterData {
    id: number;
    event_id: number;
    voter_type: 'student' | 'staff' | 'parent' | 'general';
    student_id?: number | null;
    staff_id?: number | null;
    voter_name: string;
    voter_identifier?: string | null;
    token: string;
    has_voted: boolean;
    voted_at?: string | null;
    token_generated_at?: string | null;
}

export interface VoteEventData {
    id: number;
    school_id: number;
    academic_year_id?: number | null;
    title: string;
    type: VotingEventType;
    description?: string | null;
    target_audience: TargetAudience;
    voting_mode: VotingMode;
    start_date: string;
    end_date: string;
    status: 'draft' | 'active' | 'closed';
    allow_abstain: boolean;
    candidates?: VoteCandidateData[];
    candidates_count?: number;
    voters_count?: number;
    ballots_count?: number;
}

export interface VotingQuickCountData {
    event_id: number;
    event_title: string;
    event_type: VotingEventType;
    status: string;
    total_ballots: number;
    total_dpt: number;
    voted_dpt: number;
    turnout_percentage: number;
    candidates: VoteCandidateData[];
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

// ─── Facilities ───────────────────────────────────────────────────────────────

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

// ─── Integration ──────────────────────────────────────────────────────────────

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
