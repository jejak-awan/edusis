import { defineStore } from 'pinia';
import { ref } from 'vue';
import { AttendanceService } from '../services/attendanceService';
import type { AttendanceRecord } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useAttendanceStore = defineStore('schoolAttendance', () => {
    const attendanceRecords = ref<AttendanceRecord[]>([]);
    const selectedDate = ref<string>(new Date().toISOString().slice(0, 10));
    const selectedStudyGroupId = ref<number | null>(null);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const error = ref<string | null>(null);

    async function fetchStudentAttendance(params: { study_group_id: number; date: string }) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await AttendanceService.getStudentAttendance(params);
            const rawData = res.data?.data;
            if (Array.isArray(rawData)) {
                attendanceRecords.value = rawData;
            } else if (rawData && typeof rawData === 'object' && 'attendances' in (rawData as Record<string, unknown>)) {
                attendanceRecords.value = ensureArray<AttendanceRecord>((rawData as Record<string, unknown>).attendances);
            } else {
                attendanceRecords.value = [];
            }
            selectedDate.value = params.date;
            selectedStudyGroupId.value = params.study_group_id;
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data presensi';
            error.value = msg;
            logger.error('[School:Attendance] Failed to load attendance', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function saveBulkAttendance(payload: {
        study_group_id: number;
        date: string;
        records: Array<{ student_id: number; status: 'present' | 'sick' | 'permission' | 'absent' | 'late' | 'H' | 'I' | 'S' | 'A'; notes?: string }>;
    }) {
        isSaving.value = true;
        error.value = null;
        try {
            await AttendanceService.saveBulkStudentAttendance(payload);
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal menyimpan presensi';
            error.value = msg;
            logger.error('[School:Attendance] Failed to save bulk attendance', err);
            throw err;
        } finally {
            isSaving.value = false;
        }
    }

    function reset() {
        attendanceRecords.value = [];
        error.value = null;
    }

    return {
        attendanceRecords,
        selectedDate,
        selectedStudyGroupId,
        isLoading,
        isSaving,
        error,
        fetchStudentAttendance,
        saveBulkAttendance,
        reset,
    };
});

export default useAttendanceStore;
