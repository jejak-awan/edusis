/**
 * School Module — Integration Domain Types
 * CBT KataMPI Gateway & WhatsApp Gateway Logs
 */

export interface KatampiConfigData {
    id: number;
    school_id: number;
    cbt_url: string;
    api_key: string | null;
    is_active: boolean;
    last_sync_at: string | null;
    sync_stats: Record<string, unknown> | null;
}

export interface WhatsAppLogData {
    id: number;
    school_id: number;
    student_id: number | null;
    recipient_phone: string;
    recipient_name: string | null;
    message_type: 'attendance_alert' | 'discipline_sp' | 'achievement' | 'announcement' | 'general';
    content: string;
    status: 'queued' | 'sent' | 'delivered' | 'failed';
    sent_at: string | null;
    error_message: string | null;
    created_at: string;
    student?: { id: number; full_name: string; nisn?: string };
}

export interface WhatsAppStatsData {
    total: number;
    sent: number;
    failed: number;
    queued?: number;
    delivered?: number;
    attendance_alerts: number;
    announcements: number;
}
