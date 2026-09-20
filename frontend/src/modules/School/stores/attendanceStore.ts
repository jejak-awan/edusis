/**
 * Attendance Store — Pinia state management untuk presensi siswa & staf.
 *
 * @see ADR-031 — Bab 2.B.3 (Pinia Domain Stores)
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import { AttendanceService } from '../services/attendanceService';
import type { AttendanceRecord } from '../services/types';

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
            // res.data → ApiResponse<AttendanceRecord[]>, res.data.data → AttendanceRecord[]
            attendanceRecords.value = res.data?.data ?? [];
            selectedDate.value = params.date;
            selectedStudyGroupId.value = params.study_group_id;
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data presensi';
        } finally {
            isLoading.value = false;
        }
    }

    async function saveBulkAttendance(payload: {
        study_group_id: number;
        date: string;
        records: Array<{ student_id: number; status: 'present' | 'sick' | 'permission' | 'absent' | 'late'; notes?: string }>;
    }) {
        isSaving.value = true;
        error.value = null;
        try {
            await AttendanceService.saveBulkStudentAttendance(payload);
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal menyimpan presensi';
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
