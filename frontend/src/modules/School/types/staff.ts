/**
 * School Module — Staff / GTK Domain Types
 * Kepegawaian, Tenaga Pendidik & Kependidikan, Shift, dan Presensi Pegawai
 */

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

export type Staff = StaffData;

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
