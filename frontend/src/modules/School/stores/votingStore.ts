import { defineStore } from 'pinia';
import { ref } from 'vue';
import { VotingService } from '../services/votingService';
import type { VotingEvent } from '../services/types';

export const useVotingStore = defineStore('schoolVoting', () => {
    const events = ref<VotingEvent[]>([]);
    const currentEvent = ref<VotingEvent | null>(null);
    const isLoading = ref(false);
    const isVoted = ref(false);
    const lastReceipt = ref<string | null>(null);

    async function fetchEvents() {
        isLoading.value = true;
        try {
            const res = await VotingService.getEvents();
            if (res.data?.success) {
                events.value = res.data.data;
            }
        } catch (err) {
            console.error('Failed to load voting events', err);
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
        } catch (err) {
            console.error('Failed to load event detail', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function castVote(token: string, candidateId: number) {
        const res = await VotingService.castVote(token, candidateId);
        if (res.data?.success) {
            isVoted.value = true;
            lastReceipt.value = res.data.data.receipt;
        }
        return res;
    }

    function resetVoteState() {
        isVoted.value = false;
        lastReceipt.value = null;
    }

    return {
        events,
        currentEvent,
        isLoading,
        isVoted,
        lastReceipt,
        fetchEvents,
        fetchEventDetail,
        castVote,
        resetVoteState,
    };
});
