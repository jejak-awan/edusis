import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, AttendanceRecord } from './types';

export class AttendanceService {
    public static async getStudentAttendance(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<AttendanceRecord[]>>> {
        return api.get('/school/academic/attendance/students', { params });
    }

    public static async saveBulkStudentAttendance(data: {
        date: string;
        study_group_id: number;
        records: Array<{
            student_id: number;
            status: 'present' | 'sick' | 'permission' | 'absent' | 'late';
            notes?: string;
        }>;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/academic/attendance/students/bulk', data);
    }

    public static async staffCheckIn(data: {
        staff_id: number;
        type: 'in' | 'out';
        notes?: string;
    }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post('/school/hr/staff/attendance/check-in', data);
    }
}

export default AttendanceService;
