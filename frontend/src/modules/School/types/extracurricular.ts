/**
 * School Module — Extracurricular Domain Types
 * Manajemen Ekstrakurikuler, Pembina, Anggota Siswa, dan Nilai Kegiatan
 */

import type { StaffData } from './staff';
import type { StudentData } from './student';

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
    members_count?: number;
}

export interface ExtracurricularMemberData {
    id: number;
    extracurricular_id: number;
    student_id: number;
    role: 'member' | 'leader' | 'secretary' | 'treasurer';
    joined_at: string;
    grade?: string | null;
    notes?: string | null;
    student?: StudentData;
}
