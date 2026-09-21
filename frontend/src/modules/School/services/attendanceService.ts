import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, AttendanceRecord, StaffAttendanceData } from '../types';

export class AttendanceService {
    public static async getStudentAttendance(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<AttendanceRecord[]>>> {
        return api.get(schoolPaths.attendance.students, { params });
    }

    public static async saveBulkStudentAttendance(data: {
        date: string;
        study_group_id: number;
        records: Array<{
            student_id: number;
            status: 'present' | 'sick' | 'permission' | 'absent' | 'late' | 'H' | 'I' | 'S' | 'A';
            notes?: string;
        }>;
    }): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.post(`${schoolPaths.attendance.students}/bulk`, data);
    }

    public static async staffCheckIn(data: {
        staff_id: number;
        type?: 'in' | 'out';
        notes?: string;
        latitude?: number;
        longitude?: number;
        device_info?: string;
    }): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.post(`${schoolPaths.attendance.staff}/check-in`, data);
    }

    public static async staffCheckOut(data: {
        staff_id: number;
    }): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.post(`${schoolPaths.attendance.staff}/check-out`, data);
    }

    public static async getStaffAttendance(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<StaffAttendanceData[]>>> {
        return api.get(schoolPaths.attendance.staff, { params });
    }
}

export default AttendanceService;
