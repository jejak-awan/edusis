/**
 * School Module — Operations & Document Engine Types
 * Persuratan Dinas, Penomoran Otomatis, Disposisi, dan Buku Tamu
 */

export interface LetterData {
    id: number;
    school_id: number;
    type: 'incoming' | 'outgoing';
    letter_number: string;
    reference_number: string | null;
    category: 'invitation' | 'assignment' | 'decree' | 'certificate' | 'circular' | 'general';
    sender: string;
    recipient: string;
    subject: string;
    letter_date: string;
    received_or_sent_date: string;
    status: 'draft' | 'registered' | 'dispatched' | 'archived';
    disposition_to: string | null;
    disposition_instruction: string | null;
    disposition_status: 'pending' | 'processed' | 'completed';
    attachment_path: string | null;
    notes: string | null;
}

export type Letter = LetterData;

export interface GuestBookData {
    id: number;
    school_id: number;
    visitor_name: string;
    institution: string | null;
    phone: string | null;
    identity_type: 'KTP' | 'SIM' | 'Passport' | 'Lainnya';
    identity_number: string | null;
    purpose: string;
    target_person: string;
    check_in_at: string;
    check_out_at: string | null;
    badge_number: string | null;
    status: 'checked_in' | 'checked_out';
    notes: string | null;
}

export type GuestEntry = GuestBookData;
