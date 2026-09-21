/**
 * School Module — Academic Domain Types
 * Kurikulum, Rombel, Jadwal, Mapel, Penilaian, dan Jurnal Mengajar
 */

import type { StaffData } from './staff';
import type { StudentData } from './student';
import type { RoomData } from './facility';

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

export type AcademicYear = AcademicYearData;

export interface SemesterData {
    id: number;
    academic_year_id: number;
    name: string;
    semester_number: number;
    is_active: boolean;
}

export type Semester = SemesterData;

export interface DepartmentData {
    id: number;
    school_id: number;
    code: string;
    name: string;
    description: string | null;
    is_active: boolean;
    students_count?: number;
}

export type Department = DepartmentData;

export interface SubjectData {
    id: number;
    school_id: number;
    code: string | null;
    name: string;
    group: string;
    kkm: number;
    is_active: boolean;
}

export type Subject = SubjectData;

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

export type StudyGroup = StudyGroupData;

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

export type Schedule = ScheduleData;

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

export type TeachingJournal = TeachingJournalData;

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
