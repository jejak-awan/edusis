/**
 * Counseling Store — Pinia state management untuk Bimbingan Konseling (BK).
 *
 * @see ADR-031 — Bab 2.B.3 (Pinia Domain Stores)
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import { CounselingService } from '../services/counselingService';
import type { CounselingCase } from '../services/types';

export const useCounselingStore = defineStore('schoolCounseling', () => {
    const cases = ref<CounselingCase[]>([]);
    const selectedCase = ref<CounselingCase | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchCases(params: Record<string, any> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await CounselingService.getCases(params);
            const raw = res.data?.data ?? res.data;
            cases.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
            if (!Array.isArray(raw) && raw?.total !== undefined) {
                totalCount.value = raw.total;
                currentPage.value = raw.current_page ?? 1;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat kasus konseling';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchCaseById(id: number | string) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await CounselingService.getCase(id);
            if (res.data?.success) {
                selectedCase.value = res.data.data;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat kasus';
        } finally {
            isLoading.value = false;
        }
    }

    function setSelectedCase(item: CounselingCase | null) {
        selectedCase.value = item;
    }

    return {
        cases,
        selectedCase,
        isLoading,
        error,
        totalCount,
        currentPage,
        fetchCases,
        fetchCaseById,
        setSelectedCase,
    };
});
