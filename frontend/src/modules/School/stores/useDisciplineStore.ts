import { defineStore } from 'pinia';
import { ref } from 'vue';
import { CounselingService } from '../services/counselingService';
import { StudentService } from '../services/studentService';
import type { CounselingCase, PointCategoryData, ViolationData } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useDisciplineStore = defineStore('schoolDiscipline', () => {
    const cases = ref<CounselingCase[]>([]);
    const selectedCase = ref<CounselingCase | null>(null);
    const pointCategories = ref<PointCategoryData[]>([]);
    const violations = ref<ViolationData[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchCases(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await CounselingService.getCases(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                cases.value = ensureArray<CounselingCase>((raw as { data: CounselingCase[] }).data);
                totalCount.value = (raw as { total?: number }).total || 0;
                currentPage.value = (raw as { current_page?: number }).current_page || 1;
            } else {
                cases.value = ensureArray<CounselingCase>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat kasus konseling';
            error.value = msg;
            logger.error('[School:Discipline] Failed to load counseling cases', err);
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
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat kasus';
            error.value = msg;
            logger.error('[School:Discipline] Failed to fetch counseling case', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchPointCategories(params: Record<string, unknown> = {}) {
        try {
            const res = await StudentService.getPointCategories(params);
            pointCategories.value = ensureArray<PointCategoryData>(res.data?.data);
        } catch (err: unknown) {
            logger.error('[School:Discipline] Failed to fetch point categories', err);
        }
    }

    function setSelectedCase(item: CounselingCase | null) {
        selectedCase.value = item;
    }

    return {
        cases,
        selectedCase,
        pointCategories,
        violations,
        isLoading,
        error,
        totalCount,
        currentPage,
        fetchCases,
        fetchCaseById,
        fetchPointCategories,
        setSelectedCase,
    };
});

export const useCounselingStore = useDisciplineStore;
export default useDisciplineStore;
