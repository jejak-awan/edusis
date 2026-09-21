/**
 * School Module — Student / Kesiswaan Domain Types
 * Biodata Dapodik, Relasi Orang Tua/Wali, dan Profil Siswa
 */

import type { DepartmentData, StudyGroupData } from './academic';

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
    study_group?: StudyGroupData;
    photo?: string | null;
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

export type Student = StudentData;
