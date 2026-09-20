/**
 * School Module TypeScript Definitions & Domain Models
 * Aligned with Jejakawan Core Engine & EduSIS standards.
 * Re-exports canonical domain models from schoolServiceTypes.ts to ensure Single Source of Truth (SoT).
 */

import type {
    AcademicYearData,
    SemesterData,
    DepartmentData,
    SubjectData,
    StudyGroupData,
    ScheduleData,
    TeachingJournalData,
    StudentAttendanceItem,
    StudentData,
    CounselingCaseData,
    StaffData,
    LetterData,
    PpdbData,
    GuestBookData,
    VoteCandidateData,
    VoteVoterData,
    VoteEventData,
    RoomData,
    AssetData,
} from './schoolServiceTypes';

export * from './schoolServiceTypes';

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

export type Department = DepartmentData;
export type AcademicYear = AcademicYearData;
export type Semester = SemesterData;
export type StudyGroup = StudyGroupData;
export type Subject = SubjectData;
export type Student = StudentData;
export type Staff = StaffData;
export type Schedule = ScheduleData;
export type TeachingJournal = TeachingJournalData;
export type AttendanceRecord = StudentAttendanceItem;
export type CounselingCase = CounselingCaseData;
export type Letter = LetterData;
export type PpdbApplicant = PpdbData;
export type GuestEntry = GuestBookData;
export type Room = RoomData;
export type Asset = AssetData;
export type VotingEvent = VoteEventData;
export type VotingCandidate = VoteCandidateData;
export type VotingVoter = VoteVoterData;

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
