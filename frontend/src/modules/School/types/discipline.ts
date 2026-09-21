/**
 * School Module — Discipline & Counseling (BK) Domain Types
 * Kredit Poin Pelanggaran & Prestasi, Ambang Batas SP, dan Kasus Konseling Privat
 */

import type { StudentData } from './student';
import type { StaffData } from './staff';

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

export type CounselingCase = CounselingCaseData;
