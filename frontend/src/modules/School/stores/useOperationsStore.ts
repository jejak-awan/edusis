import { defineStore } from 'pinia';
import { ref } from 'vue';
import { OperationsService } from '../services/operationsService';
import type { Letter, PpdbApplicant, GuestEntry } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useOperationsStore = defineStore('schoolOperations', () => {
    // ── Letters ───────────────────────────────────────────────────────────
    const letters = ref<Letter[]>([]);
    const isLoadingLetters = ref(false);

    // ── PPDB ──────────────────────────────────────────────────────────────
    const ppdbApplicants = ref<PpdbApplicant[]>([]);
    const isLoadingPpdb = ref(false);

    // ── Guest Book ────────────────────────────────────────────────────────
    const guestEntries = ref<GuestEntry[]>([]);
    const isLoadingGuests = ref(false);

    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchLetters(params: Record<string, unknown> = {}) {
        isLoadingLetters.value = true;
        error.value = null;
        try {
            const res = await OperationsService.getLetters(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                letters.value = ensureArray<Letter>((raw as { data: Letter[] }).data);
            } else {
                letters.value = ensureArray<Letter>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data surat';
            error.value = msg;
            logger.error('[School:Operations] Failed to load letters', err);
        } finally {
            isLoadingLetters.value = false;
        }
    }

    async function fetchPpdbApplicants(params: Record<string, unknown> = {}) {
        isLoadingPpdb.value = true;
        error.value = null;
        try {
            const res = await OperationsService.getPpdbApplicants(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                ppdbApplicants.value = ensureArray<PpdbApplicant>((raw as { data: PpdbApplicant[] }).data);
                totalCount.value = (raw as { total?: number }).total || 0;
                currentPage.value = (raw as { current_page?: number }).current_page || 1;
            } else {
                ppdbApplicants.value = ensureArray<PpdbApplicant>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data PPDB';
            error.value = msg;
            logger.error('[School:Operations] Failed to load PPDB applicants', err);
        } finally {
            isLoadingPpdb.value = false;
        }
    }

    async function fetchGuestEntries(params: Record<string, unknown> = {}) {
        isLoadingGuests.value = true;
        error.value = null;
        try {
            const res = await OperationsService.getGuestBookEntries(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                guestEntries.value = ensureArray<GuestEntry>((raw as { data: GuestEntry[] }).data);
            } else {
                guestEntries.value = ensureArray<GuestEntry>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat buku tamu';
            error.value = msg;
            logger.error('[School:Operations] Failed to load guest book', err);
        } finally {
            isLoadingGuests.value = false;
        }
    }

    return {
        letters,
        ppdbApplicants,
        guestEntries,
        isLoadingLetters,
        isLoadingPpdb,
        isLoadingGuests,
        error,
        totalCount,
        currentPage,
        fetchLetters,
        fetchPpdbApplicants,
        fetchGuestEntries,
    };
});

export default useOperationsStore;
