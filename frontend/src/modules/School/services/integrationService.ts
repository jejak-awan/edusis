/**
 * Integration Service — External System Integrations
 * Domain service untuk KataMPI CBT & WhatsApp Gateway.
 *
 * @see ADR-031 — Domain Services Layer
 */

import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, PaginatedData, WhatsAppLogData, WhatsAppStatsData } from './types';

export class IntegrationService {
    // ─── KataMPI CBT Integration ──────────────────────────────────────────

    /** Ambil konfigurasi integrasi KataMPI saat ini. */
    public static async getKatampiConfig(): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.get('/school/integrations/katampi/config');
    }

    /** Perbarui konfigurasi KataMPI (URL + API Key). */
    public static async updateKatampiConfig(data: {
        cbt_url: string;
        api_key?: string;
        is_active?: boolean;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.put('/school/integrations/katampi/config', data);
    }

    /** Generate SSO token untuk login ke platform KataMPI. */
    public static async generateKatampiSsoToken(data: {
        staff_id?: number;
        student_id?: number;
    } = {}): Promise<AxiosResponse<ApiResponse<{
        token: string;
        redirect_url: string;
        cbt_url?: string;
        expires_in?: number;
        user?: { name: string; role: string; nisn?: string };
    }>>> {
        return api.post('/school/integrations/katampi/sso-token', data);
    }

    /** Ambil roster peserta ujian dari KataMPI. */
    public static async getKatampiRoster(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<Record<string, unknown>>>> {
        return api.get('/school/integrations/katampi/roster', { params });
    }

    /** Sinkronkan nilai ujian dari KataMPI ke buku raport. */
    public static async syncKatampiGrades(data: {
        exam_id: number;
        study_group_id?: number;
        subject_id?: number;
    }): Promise<AxiosResponse<ApiResponse<{ synced: number; failed: number }>>> {
        return api.post('/school/integrations/katampi/sync-grades', data);
    }

    // ─── WhatsApp Gateway ─────────────────────────────────────────────────

    /** Ambil log pengiriman pesan WhatsApp. */
    public static async getWhatsAppLogs(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<WhatsAppLogData>>>> {
        return api.get('/school/communications/whatsapp/logs', { params });
    }

    /** Ambil statistik pengiriman WhatsApp (total, sent, failed, dll.). */
    public static async getWhatsAppStats(): Promise<AxiosResponse<ApiResponse<WhatsAppStatsData>>> {
        return api.get('/school/communications/whatsapp/stats');
    }

    /** Kirim pesan WhatsApp custom ke nomor tertentu. */
    public static async sendWhatsAppMessage(data: {
        recipient_phone: string;
        message: string;
        student_id?: number;
        message_type?: string;
    }): Promise<AxiosResponse<ApiResponse<Record<string, unknown>>>> {
        return api.post('/school/communications/whatsapp/send', data);
    }

    /** Kirim alert presensi otomatis ke orang tua siswa alpha/izin/sakit. */
    public static async sendWhatsAppAttendanceAlerts(data: {
        date?: string;
        statuses?: string[];
    }): Promise<AxiosResponse<ApiResponse<{
        date: string;
        total_detected: number;
        sent_count: number;
        skipped_count: number;
        dispatched?: number;
    }>>> {
        return api.post('/school/communications/whatsapp/alerts/attendance', data);
    }

    /** Kirim ulang log pesan yang gagal. */
    public static async resendWhatsAppLog(id: number | string): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post(`/school/communications/whatsapp/logs/${id}/resend`);
    }
}

export default IntegrationService;
