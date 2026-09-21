import { defineStore } from 'pinia';
import { ref } from 'vue';
import { HrService } from '../services/hrService';
import type { Staff, StaffShiftData } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useStaffStore = defineStore('schoolStaff', () => {
    const staffList = ref<Staff[]>([]);
    const selectedStaff = ref<Staff | null>(null);
    const shifts = ref<StaffShiftData[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchStaff(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await HrService.getStaff(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                staffList.value = ensureArray<Staff>((raw as { data: Staff[] }).data);
                totalCount.value = (raw as { total?: number }).total || 0;
                currentPage.value = (raw as { current_page?: number }).current_page || 1;
            } else {
                staffList.value = ensureArray<Staff>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data PTK';
            error.value = msg;
            logger.error('[School:Staff] Failed to load staff', err);
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
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data PTK';
            error.value = msg;
            logger.error('[School:Staff] Failed to fetch staff by id', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchShifts() {
        try {
            const res = await HrService.getStaffShifts();
            shifts.value = ensureArray<StaffShiftData>(res.data?.data);
        } catch (err: unknown) {
            shifts.value = [];
            logger.error('[School:Staff] Failed to load shifts', err);
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

export const useHrStore = useStaffStore;
export default useStaffStore;
