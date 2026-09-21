/**
 * School Module — Graduation & Alumni Domain Types
 * Kelulusan, Surat Keterangan Lulus (SKL), dan Portal Alumni
 */

import type { StudentData } from './student';
import type { DepartmentData } from './academic';

export interface GraduationData {
    id: number;
    school_id: number;
    student_id: number;
    graduation_year: number;
    certificate_number: string | null;
    diploma_number: string | null;
    skl_number: string | null;
    is_graduated: boolean;
    graduated_at: string | null;
    student?: StudentData;
}

export interface AlumniData {
    id: number;
    student_id: number;
    graduation_year: number;
    department_id?: number;
    current_status: 'kuliah' | 'kerja' | 'wirausaha' | 'mencari_kerja' | 'lainnya';
    current_activity_name?: string | null;
    current_position?: string | null;
    phone?: string | null;
    email?: string | null;
    address?: string | null;
    student?: StudentData;
    department?: DepartmentData;
}

export interface GraduationYearGroup {
    year: number;
    total_graduates: number;
    employed_count: number;
    college_count: number;
    entrepreneur_count: number;
}
