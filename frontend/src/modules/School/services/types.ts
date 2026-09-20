/**
 * School Module TypeScript Definitions & Domain Models
 * Aligned with Jejakawan Core Engine & EduSIS standards.
 */

export interface SchoolInfo {
    id: number;
    name: string;
    npsn?: string;
    code?: string;
    level?: string;
    status?: string;
    address?: string;
    phone?: string;
    email?: string;
    website?: string;
    principal_name?: string;
    principal_nip?: string;
    logo_url?: string;
}

export interface Department {
    id: number;
    school_id: number;
    code: string;
    name: string;
    description?: string;
    is_active: boolean;
    head_of_department_id?: number;
}

export interface AcademicYear {
    id: number;
    school_id: number;
    name: string;
    start_date: string;
    end_date: string;
    is_active: boolean;
    active_semester?: Semester;
    semesters?: Semester[];
}

export interface Semester {
    id: number;
    academic_year_id: number;
    semester: number | string;
    name: string;
    is_active: boolean;
    start_date?: string;
    end_date?: string;
}

export interface StudyGroup {
    id: number;
    school_id: number;
    department_id?: number;
    academic_year_id?: number;
    homeroom_teacher_id?: number;
    name: string;
    grade_level: number;
    capacity?: number;
    current_students_count?: number;
    department?: Department;
    homeroom_teacher?: Staff;
}

export interface Subject {
    id: number;
    school_id: number;
    department_id?: number;
    code: string;
    name: string;
    type?: string;
    credit_hours?: number;
    passing_grade?: number;
}

export interface Student {
    id: number;
    school_id: number;
    department_id?: number;
    study_group_id?: number;
    user_id?: string;
    nisn?: string;
    nis?: string;
    nik?: string;
    full_name: string;
    gender: 'L' | 'P';
    place_of_birth?: string;
    date_of_birth?: string;
    religion?: string;
    phone?: string;
    email?: string;
    address?: string;
    rt?: string;
    rw?: string;
    dusun?: string;
    desa_kelurahan?: string;
    kecamatan?: string;
    postal_code?: string;
    father_name?: string;
    father_nik?: string;
    father_occupation?: string;
    father_phone?: string;
    mother_name?: string;
    mother_nik?: string;
    mother_occupation?: string;
    mother_phone?: string;
    guardian_name?: string;
    guardian_phone?: string;
    entry_year?: number;
    previous_school?: string;
    is_kip_recipient?: boolean;
    kip_number?: string;
    status: 'active' | 'graduated' | 'transferred' | 'dropped_out';
    total_credit_score?: number;
    avatar_url?: string;
    department?: Department;
    study_groups?: StudyGroup[];
}

export interface Staff {
    id: number;
    school_id: number;
    user_id?: string;
    nip?: string;
    nik?: string;
    full_name: string;
    gender: 'L' | 'P';
    type: 'guru' | 'tendik' | 'staff';
    status: string;
    phone?: string;
    email?: string;
    position?: string;
}

export interface Schedule {
    id: number;
    school_id: number;
    academic_year_id: number;
    semester_id: number;
    study_group_id: number;
    subject_id: number;
    teacher_id: number;
    room_id?: number;
    day_of_week: number;
    start_time: string;
    end_time: string;
    study_group?: StudyGroup;
    subject?: Subject;
    teacher?: Staff;
    room?: Room;
}

export interface TeachingJournal {
    id: number;
    school_id: number;
    schedule_id?: number;
    teacher_id: number;
    study_group_id: number;
    subject_id: number;
    date: string;
    period_start?: number;
    period_end?: number;
    topic: string;
    subtopic?: string;
    activity_description?: string;
    notes?: string;
    attendance_present?: number;
    attendance_sick?: number;
    attendance_permission?: number;
    attendance_absent?: number;
    teacher?: Staff;
    study_group?: StudyGroup;
    subject?: Subject;
}

export interface AttendanceRecord {
    id: number;
    school_id: number;
    student_id: number;
    date: string;
    status: 'present' | 'sick' | 'permission' | 'absent' | 'late';
    check_in_time?: string;
    check_out_time?: string;
    notes?: string;
    recorder_id?: string;
    student?: Student;
}

export interface CounselingCase {
    id: number;
    school_id: number;
    student_id: number;
    counselor_id?: string | number;
    case_number: string;
    title: string;
    category: string;
    description: string;
    handling_notes?: string;
    resolution?: string;
    status: 'open' | 'in_progress' | 'resolved' | 'closed';
    student?: Student;
    created_at: string;
    updated_at: string;
}

export interface Letter {
    id: number;
    school_id: number;
    letter_type: 'incoming' | 'outgoing';
    letter_number: string;
    agenda_number?: string;
    sender_or_recipient: string;
    subject: string;
    date_of_letter: string;
    date_received_or_sent: string;
    description?: string;
    status: string;
    file_path?: string;
}

export interface PpdbApplicant {
    id: number;
    school_id: number;
    department_id: number;
    second_choice_department_id?: number;
    registration_number: string;
    track: 'zonasi' | 'afirmasi' | 'prestasi_akademik' | 'prestasi_non_akademik' | 'perpindahan_ortu';
    full_name: string;
    nisn?: string;
    nik?: string;
    gender: 'L' | 'P';
    phone?: string;
    status: 'pending' | 'verified' | 'accepted' | 'rejected' | 'enrolled';
    score?: number;
    created_at: string;
}

export interface GuestEntry {
    id: number;
    school_id: number;
    guest_name: string;
    institution?: string;
    phone?: string;
    purpose: string;
    person_to_meet: string;
    check_in_at: string;
    check_out_at?: string;
    notes?: string;
}

export interface Room {
    id: number;
    school_id: number;
    code: string;
    name: string;
    type: string;
    capacity: number;
    is_active: boolean;
}

export interface Asset {
    id: number;
    school_id: number;
    room_id?: number;
    code: string;
    name: string;
    category: string;
    condition: 'good' | 'minor_damage' | 'heavy_damage' | 'broken';
    quantity: number;
    unit: string;
    room?: Room;
}

export interface VotingEvent {
    id: number;
    school_id: number;
    title: string;
    description?: string;
    start_time: string;
    end_time: string;
    status: 'draft' | 'ongoing' | 'paused' | 'completed' | 'closed';
    candidates_count?: number;
    voters_count?: number;
    ballots_count?: number;
    candidates?: VotingCandidate[];
}

export interface VotingCandidate {
    id: number;
    event_id: number;
    candidate_number: number;
    name: string;
    vision?: string;
    mission?: string;
    photo_url?: string;
    votes_count?: number;
}

export interface VotingVoter {
    id: number;
    event_id: number;
    voter_type: 'student' | 'staff';
    voter_name: string;
    voter_identifier?: string;
    token: string;
    has_voted: boolean;
    voted_at?: string;
}

export interface ApiResponse<T> {
    success: boolean;
    data: T;
    message?: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}
