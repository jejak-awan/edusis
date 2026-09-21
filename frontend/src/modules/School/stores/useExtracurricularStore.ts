import { defineStore } from 'pinia';
import { ref } from 'vue';
import { ExtracurricularService } from '../services/extracurricularService';
import type { ExtracurricularData, ExtracurricularMemberData } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useExtracurricularStore = defineStore('schoolExtracurricular', () => {
    const extracurriculars = ref<ExtracurricularData[]>([]);
    const selectedExtracurricular = ref<ExtracurricularData | null>(null);
    const members = ref<ExtracurricularMemberData[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchExtracurriculars(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await ExtracurricularService.getExtracurriculars(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                extracurriculars.value = ensureArray<ExtracurricularData>((raw as { data: ExtracurricularData[] }).data);
            } else {
                extracurriculars.value = ensureArray<ExtracurricularData>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data ekstrakurikuler';
            error.value = msg;
            logger.error('[School:Extracurricular] Failed to load extracurriculars', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchExtracurricularById(id: number | string) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await ExtracurricularService.getExtracurricular(id);
            if (res.data?.success) {
                selectedExtracurricular.value = res.data.data;
            }
        } catch (err: unknown) {
            logger.error('[School:Extracurricular] Failed to fetch detail', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchMembers(id: number | string) {
        try {
            const res = await ExtracurricularService.getMembers(id);
            members.value = ensureArray<ExtracurricularMemberData>(res.data?.data);
        } catch (err: unknown) {
            logger.error('[School:Extracurricular] Failed to fetch members', err);
        }
    }

    return {
        extracurriculars,
        selectedExtracurricular,
        members,
        isLoading,
        error,
        fetchExtracurriculars,
        fetchExtracurricularById,
        fetchMembers,
    };
});

export default useExtracurricularStore;
