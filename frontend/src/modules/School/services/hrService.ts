/**
 * HR Service — Pendidik & Tenaga Kependidikan (GTK)
 * Domain service untuk manajemen data SDM sekolah.
 *
 * @see ADR-031 — Domain Services Layer
 */

import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, PaginatedData, Staff } from './types';

export class HrService {
    /** Ambil daftar staf/PTK dengan filter opsional. */
    public static async getStaff(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<Staff>>>> {
        return api.get('/school/staff', { params });
    }

    /** Ambil detail satu staf berdasarkan ID. */
    public static async getStaffById(id: number | string): Promise<AxiosResponse<ApiResponse<Staff>>> {
        return api.get(`/school/staff/${id}`);
    }

    /** Buat data staf baru. */
    public static async createStaff(data: Partial<Staff> | FormData): Promise<AxiosResponse<ApiResponse<Staff>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        return api.post('/school/staff', data, { headers });
    }

    /** Perbarui data staf. */
    public static async updateStaff(id: number | string, data: Partial<Staff> | FormData): Promise<AxiosResponse<ApiResponse<Staff>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        if (data instanceof FormData) {
            data.append('_method', 'PUT');
            return api.post(`/school/staff/${id}`, data, { headers });
        }
        return api.put(`/school/staff/${id}`, data);
    }

    /** Hapus data staf. */
    public static async deleteStaff(id: number | string): Promise<AxiosResponse<ApiResponse<void>>> {
        return api.delete(`/school/staff/${id}`);
    }

    /** Ambil daftar shift kerja staf. */
    public static async getStaffShifts(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any[]>>> {
        return api.get('/school/staff/shifts/list', { params });
    }

    /** Buat shift kerja baru. */
    public static async createStaffShift(data: {
        name: string;
        start_time: string;
        end_time: string;
        grace_period_minutes?: number;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/staff/shifts', data);
    }

    /** Rekam presensi masuk staf. */
    public static async staffCheckIn(data: {
        staff_id: number;
        shift_id?: number;
        notes?: string;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/attendance/staff/check-in', data);
    }

    /** Rekam presensi pulang staf. */
    public static async staffCheckOut(data: {
        staff_id: number;
        notes?: string;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/attendance/staff/check-out', data);
    }

    /** Ambil rekap presensi staf. */
    public static async getStaffAttendance(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any[]>>> {
        return api.get('/school/attendance/staff', { params });
    }
}

export default HrService;
