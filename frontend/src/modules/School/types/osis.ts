/**
 * School Module — OSIS & Voting / Election Engine Types
 * Fork ja-evoting: Bilik Suara, Manajemen DPT, Token 8-Karakter, & Rekapitulasi Real-Time
 */

import type { StudentData } from './student';
import type { StaffData } from './staff';

export type VotingEventType = 'osis' | 'mpk' | 'ekskul' | 'guru_favorit' | 'polling' | 'komite';
export type VotingMode = 'token_kiosk' | 'online_login' | 'hybrid';
export type TargetAudience = 'all_students' | 'specific_classes' | 'teachers_only' | 'parents' | 'all';

export interface OsisCandidateData {
    id: number;
    election_id: number;
    candidate_number: number;
    chairman_student_id: number;
    vice_chairman_student_id: number;
    vision: string;
    mission: string;
    photo_url: string | null;
    vote_count: number;
    chairman_name?: string;
    vice_chairman_name?: string;
    chairman?: StudentData;
    vice_chairman?: StudentData;
    percentage?: number;
}

export interface OsisElectionData {
    id: number;
    school_id: number;
    academic_year_id: number;
    title: string;
    period: string;
    start_date: string;
    end_date: string;
    status: 'draft' | 'active' | 'closed';
    candidates?: OsisCandidateData[];
    total_votes?: number;
}

export interface VoteCandidateData {
    id: number;
    event_id: number;
    candidate_number: number;
    title?: string;
    name?: string;
    leader_student_id?: number | null;
    vice_student_id?: number | null;
    staff_id?: number | null;
    vision?: string | null;
    mission?: string | null;
    photo_url?: string | null;
    vote_count?: number;
    votes_count?: number;
    percentage?: number;
    leader_name?: string;
    vice_name?: string;
    leader?: StudentData;
    vice?: StudentData;
    staff?: StaffData;
}

export interface VoteVoterData {
    id: number;
    event_id: number;
    voter_type: 'student' | 'staff' | 'parent' | 'general';
    student_id?: number | null;
    staff_id?: number | null;
    voter_name: string;
    voter_identifier?: string | null;
    token: string;
    has_voted: boolean;
    voted_at?: string | null;
    token_generated_at?: string | null;
}

export interface VoteEventData {
    id: number;
    school_id: number;
    academic_year_id?: number | null;
    title: string;
    type: VotingEventType;
    description?: string | null;
    target_audience: TargetAudience;
    voting_mode: VotingMode;
    start_date: string;
    end_date: string;
    status: 'draft' | 'active' | 'closed';
    allow_abstain: boolean;
    candidates?: VoteCandidateData[];
    candidates_count?: number;
    voters_count?: number;
    ballots_count?: number;
}

export interface VotingQuickCountData {
    event_id: number;
    event_title: string;
    event_type: VotingEventType;
    status: string;
    total_ballots: number;
    total_dpt: number;
    voted_dpt: number;
    turnout_percentage: number;
    candidates: VoteCandidateData[];
}

export type VotingCandidate = VoteCandidateData;
export type CandidateData = VoteCandidateData;
export type VotingEvent = VoteEventData;
export type ElectionData = OsisElectionData;
export type VotingVoter = VoteVoterData;
