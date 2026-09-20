/**
 * HR Store — Pinia state management untuk Data PTK (Pendidik & Tenaga Kependidikan).
 *
 * @see ADR-031 — Bab 2.B.3 (Pinia Domain Stores)
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import { HrService } from '../services/hrService';
import type { Staff } from '../services/types';

export const useHrStore = defineStore('schoolHr', () => {
    const staffList = ref<Staff[]>([]);
    const selectedStaff = ref<Staff | null>(null);
    const shifts = ref<any[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchStaff(params: Record<string, any> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await HrService.getStaff(params);
            const raw = res.data?.data ?? res.data;
            staffList.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
            if (!Array.isArray(raw) && raw?.total !== undefined) {
                totalCount.value = raw.total;
                currentPage.value = raw.current_page ?? 1;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data PTK';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchStaffById(id: number | string) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await HrService.getStaffById(id);
            if (res.data?.success) {
                selectedStaff.value = res.data.data;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data PTK';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchShifts() {
        try {
            const res = await HrService.getStaffShifts();
            const raw = res.data?.data ?? res.data;
            shifts.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
        } catch {
            shifts.value = [];
        }
    }

    function setSelectedStaff(staff: Staff | null) {
        selectedStaff.value = staff;
    }

    return {
        staffList,
        selectedStaff,
        shifts,
        isLoading,
        error,
        totalCount,
        currentPage,
        fetchStaff,
        fetchStaffById,
        fetchShifts,
        setSelectedStaff,
    };
});
