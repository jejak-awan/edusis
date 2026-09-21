/**
 * School Module — Attendance Engine Types
 * Presensi Siswa, Rekapitulasi Rombel, Presensi GTK, dan Log Kehadiran Polimorfik
 */

export interface StudentAttendanceItem {
    student_id: number;
    nisn: string | null;
    full_name: string;
    gender: string;
    status: 'H' | 'I' | 'S' | 'A';
    notes: string | null;
}

export type AttendanceRecord = StudentAttendanceItem;

export interface AttendanceSummary {
    total: number;
    present: number;
    sick: number;
    permit: number;
    absent: number;
    percentage: number;
}

export interface AttendanceSession {
    id: number;
    school_id: number;
    study_group_id?: number;
    subject_id?: number;
    staff_id?: number;
    date: string;
    session_type: 'daily' | 'subject' | 'extracurricular' | 'event';
    status: 'draft' | 'submitted' | 'locked';
}
