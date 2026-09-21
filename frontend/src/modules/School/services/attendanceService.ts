import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, AttendanceRecord } from './types';

export class AttendanceService {
    public static async getStudentAttendance(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<AttendanceRecord[]>>> {
        return api.get('/school/attendance/students', { params });
    }

    public static async saveBulkStudentAttendance(data: {
        date: string;
        study_group_id: number;
        records: Array<{
            student_id: number;
            status: 'present' | 'sick' | 'permission' | 'absent' | 'late' | 'H' | 'I' | 'S' | 'A';
            notes?: string;
        }>;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/attendance/students/bulk', data);
    }

    public static async staffCheckIn(data: {
        staff_id: number;
        type?: 'in' | 'out';
        notes?: string;
        latitude?: number;
        longitude?: number;
        device_info?: string;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/attendance/staff/check-in', data);
    }

    public static async staffCheckOut(data: {
        staff_id: number;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/attendance/staff/check-out', data);
    }

    public static async getStaffAttendance(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<any[]>>> {
        return api.get('/school/attendance/staff', { params });
    }
}

export default AttendanceService;
