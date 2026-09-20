/**
 * Facility Store — Pinia state management untuk Sarana & Prasarana.
 *
 * @see ADR-031 — Bab 2.B.3 (Pinia Domain Stores)
 */

import { defineStore } from 'pinia';
import { ref } from 'vue';
import { FacilityService } from '../services/facilityService';
import type { Room, Asset } from '../services/types';

export const useFacilityStore = defineStore('schoolFacility', () => {
    const rooms = ref<Room[]>([]);
    const assets = ref<Asset[]>([]);
    const maintenanceTickets = ref<any[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchRooms(params: Record<string, any> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await FacilityService.getRooms(params);
            const raw = res.data?.data ?? res.data;
            rooms.value = Array.isArray(raw) ? raw : ((raw as any)?.data ?? []);
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data ruangan';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAssets(params: Record<string, any> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await FacilityService.getAssets(params);
            const raw = res.data?.data ?? res.data;
            assets.value = Array.isArray(raw) ? raw : ((raw as any)?.data ?? []);
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat data aset';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchTickets(params: Record<string, any> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await FacilityService.getMaintenanceTickets(params);
            const raw = res.data?.data ?? res.data;
            maintenanceTickets.value = Array.isArray(raw) ? raw : ((raw as any)?.data ?? []);
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Gagal memuat tiket pemeliharaan';
        } finally {
            isLoading.value = false;
        }
    }

    return {
        rooms,
        assets,
        maintenanceTickets,
        isLoading,
        error,
        fetchRooms,
        fetchAssets,
        fetchTickets,
    };
});
