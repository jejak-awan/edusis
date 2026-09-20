/**
 * Operations Store — Pinia state management untuk Tata Usaha & Administrasi.
 * Mencakup: Persuratan, PPDB, dan Buku Tamu.
 *
 * @see ADR-031 — Bab 2.B.3 (Pinia Domain Stores)
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import { OperationsService } from '../services/operationsService';
import type { Letter, PpdbApplicant, GuestEntry } from '../services/types';

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

    async function fetchLetters(params: Record<string, any> = {}) {
        isLoadingLetters.value = true;
        error.value = null;
        try {
            const res = await OperationsService.getLetters(params);
            const raw = res.data?.data ?? res.data;
            letters.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data surat';
        } finally {
            isLoadingLetters.value = false;
        }
    }

    async function fetchPpdbApplicants(params: Record<string, any> = {}) {
        isLoadingPpdb.value = true;
        error.value = null;
        try {
            const res = await OperationsService.getPpdbApplicants(params);
            const raw = res.data?.data ?? res.data;
            ppdbApplicants.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
            if (!Array.isArray(raw) && raw?.total !== undefined) {
                totalCount.value = raw.total;
                currentPage.value = raw.current_page ?? 1;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data PPDB';
        } finally {
            isLoadingPpdb.value = false;
        }
    }

    async function fetchGuestEntries(params: Record<string, any> = {}) {
        isLoadingGuests.value = true;
        error.value = null;
        try {
            const res = await OperationsService.getGuestBookEntries(params);
            const raw = res.data?.data ?? res.data;
            guestEntries.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat buku tamu';
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
