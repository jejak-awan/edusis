import { defineStore } from 'pinia';
import { ref } from 'vue';
import { VotingService } from '../services/votingService';
import type { VotingEvent, VotingQuickCountData } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useOsisStore = defineStore('schoolOsis', () => {
    const events = ref<VotingEvent[]>([]);
    const currentEvent = ref<VotingEvent | null>(null);
    const quickCount = ref<VotingQuickCountData | null>(null);
    const isLoading = ref(false);
    const isVoted = ref(false);
    const lastReceipt = ref<string | null>(null);

    async function fetchEvents(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        try {
            const res = await VotingService.getEvents(params);
            if (res.data?.success) {
                events.value = ensureArray<VotingEvent>(res.data.data);
            }
        } catch (err: unknown) {
            logger.error('[School:Osis] Failed to load voting events', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchEventDetail(id: number | string) {
        isLoading.value = true;
        try {
            const res = await VotingService.getEvent(id);
            if (res.data?.success) {
                currentEvent.value = res.data.data;
            }
        } catch (err: unknown) {
            logger.error('[School:Osis] Failed to load event detail', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchQuickCount(id: number | string) {
        try {
            const res = await VotingService.getQuickCount(id);
            if (res.data?.success) {
                quickCount.value = res.data.data;
            }
        } catch (err: unknown) {
            logger.error('[School:Osis] Failed to load quick count', err);
        }
    }

    async function castVote(token: string, candidateId: number) {
        try {
            const res = await VotingService.castVote(token, candidateId);
            if (res.data?.success) {
                isVoted.value = true;
                lastReceipt.value = res.data.data.receipt;
            }
            return res;
        } catch (err: unknown) {
            logger.error('[School:Osis] Failed to cast vote', err);
            throw err;
        }
    }

    function resetVoteState() {
        isVoted.value = false;
        lastReceipt.value = null;
    }

    return {
        events,
        currentEvent,
        quickCount,
        isLoading,
        isVoted,
        lastReceipt,
        fetchEvents,
        fetchEventDetail,
        fetchQuickCount,
        castVote,
        resetVoteState,
    };
});

export const useVotingStore = useOsisStore;
export default useOsisStore;
