/**
 * School Module — Admission / PPDB Domain Types
 * Pendaftaran Siswa Baru, Seleksi Jalur, dan Verifikasi Berkas
 */

import type { DepartmentData, AcademicYearData } from './academic';
import type { StaffData } from './staff';

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

export type PpdbApplicant = PpdbData;
