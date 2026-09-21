import { defineStore } from 'pinia';
import { ref } from 'vue';
import { GraduationService } from '../services/graduationService';
import type { AlumniData, GraduationData, GraduationYearGroup } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useGraduationStore = defineStore('schoolGraduation', () => {
    const graduates = ref<GraduationData[]>([]);
    const alumniList = ref<AlumniData[]>([]);
    const alumniStats = ref<GraduationYearGroup[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchGraduates(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await GraduationService.getGraduates(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                graduates.value = ensureArray<GraduationData>((raw as { data: GraduationData[] }).data);
            } else {
                graduates.value = ensureArray<GraduationData>(raw);
            }
        } catch (err: unknown) {
            logger.error('[School:Graduation] Failed to load graduates', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAlumni(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await GraduationService.getAlumni(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                alumniList.value = ensureArray<AlumniData>((raw as { data: AlumniData[] }).data);
            } else {
                alumniList.value = ensureArray<AlumniData>(raw);
            }
        } catch (err: unknown) {
            logger.error('[School:Graduation] Failed to load alumni', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAlumniStats() {
        try {
            const res = await GraduationService.getAlumniStats();
            alumniStats.value = ensureArray<GraduationYearGroup>(res.data?.data);
        } catch (err: unknown) {
            logger.error('[School:Graduation] Failed to load alumni stats', err);
        }
    }

    return {
        graduates,
        alumniList,
        alumniStats,
        isLoading,
        error,
        fetchGraduates,
        fetchAlumni,
        fetchAlumniStats,
    };
});

export default useGraduationStore;
