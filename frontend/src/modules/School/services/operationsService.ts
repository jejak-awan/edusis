import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, GuestEntry, Letter, PaginatedData, PpdbApplicant } from './types';

export class OperationsService {
    // Surat / Letters
    public static async getLetters(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<Letter>>>> {
        return api.get('/school/letters', { params });
    }

    public static async generateLetterNumber(params: string | { type: string; category?: string }): Promise<AxiosResponse<ApiResponse<{ suggested_number: string; letter_number?: string }>>> {
        const queryParams = typeof params === 'string' ? { type: params } : params;
        return api.get('/school/letters/generate-number', { params: queryParams });
    }

    public static async createLetter(data: Partial<Letter> | FormData): Promise<AxiosResponse<ApiResponse<Letter>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        return api.post('/school/letters', data, { headers });
    }

    // PPDB
    public static async getPpdbApplicants(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<PpdbApplicant>>>> {
        return api.get('/school/ppdb/applicants', { params });
    }

    public static async submitPpdbApplication(data: Partial<PpdbApplicant>): Promise<AxiosResponse<ApiResponse<PpdbApplicant>>> {
        return api.post('/school/ppdb/register', data);
    }

    public static async verifyPpdbApplicant(id: number | string, data: { status: string; notes?: string }): Promise<AxiosResponse<ApiResponse<PpdbApplicant>>> {
        return api.post(`/school/ppdb/applicants/${id}/verify`, data);
    }

    public static async admitPpdbApplicant(id: number | string, data: { study_group_id?: number }): Promise<AxiosResponse<ApiResponse<Record<string, unknown>>>> {
        return api.post(`/school/ppdb/applicants/${id}/admit`, data);
    }

    // Buku Tamu / Guest Book
    public static async getGuestBookEntries(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<GuestEntry>>>> {
        return api.get('/school/guests', { params });
    }

    public static async checkInGuest(data: Partial<GuestEntry>): Promise<AxiosResponse<ApiResponse<GuestEntry>>> {
        return api.post('/school/guests/check-in', data);
    }

    public static async checkOutGuest(id: number | string, notes?: string): Promise<AxiosResponse<ApiResponse<GuestEntry>>> {
        return api.post(`/school/guests/${id}/check-out`, { notes });
    }
}

export default OperationsService;
