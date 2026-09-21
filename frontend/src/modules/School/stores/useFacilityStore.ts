import { defineStore } from 'pinia';
import { ref } from 'vue';
import { FacilityService } from '../services/facilityService';
import type { Room, Asset, MaintenanceTicketData } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useFacilityStore = defineStore('schoolFacility', () => {
    const rooms = ref<Room[]>([]);
    const assets = ref<Asset[]>([]);
    const maintenanceTickets = ref<MaintenanceTicketData[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    async function fetchRooms(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await FacilityService.getRooms(params);
            const raw = res.data?.data;
            rooms.value = ensureArray<Room>(raw);
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data ruangan';
            error.value = msg;
            logger.error('[School:Facility] Failed to load rooms', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAssets(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await FacilityService.getAssets(params);
            const raw = res.data?.data;
            if (raw && typeof raw === 'object' && 'data' in raw) {
                assets.value = ensureArray<Asset>((raw as { data: Asset[] }).data);
            } else {
                assets.value = ensureArray<Asset>(raw);
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat data aset';
            error.value = msg;
            logger.error('[School:Facility] Failed to load assets', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchTickets(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await FacilityService.getMaintenanceTickets(params);
            const raw = res.data?.data;
            maintenanceTickets.value = ensureArray<MaintenanceTicketData>(raw);
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Gagal memuat tiket pemeliharaan';
            error.value = msg;
            logger.error('[School:Facility] Failed to load tickets', err);
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

export default useFacilityStore;
