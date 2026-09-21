import api from '@/engine/api/client';
import { schoolPaths } from './paths';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, PaginatedData, VotingCandidate, VotingEvent, VotingVoter, VotingQuickCountData } from '../types';

export class VotingService {
    public static async getEvents(params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<VotingEvent[]>>> {
        return api.get(schoolPaths.osis.votingEvents, { params });
    }

    public static async getEvent(id: number | string): Promise<AxiosResponse<ApiResponse<VotingEvent>>> {
        return api.get(schoolPaths.osis.votingEvent(id));
    }

    public static async createEvent(data: Partial<VotingEvent>): Promise<AxiosResponse<ApiResponse<VotingEvent>>> {
        return api.post(schoolPaths.osis.votingEvents, data);
    }

    public static async addCandidate(eventId: number | string, data: Partial<VotingCandidate> | FormData): Promise<AxiosResponse<ApiResponse<VotingCandidate>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        return api.post(`${schoolPaths.osis.votingEvent(eventId)}/candidates`, data, { headers });
    }

    public static async generateVoters(eventId: number | string, data: { source: string; study_group_id?: number }): Promise<AxiosResponse<ApiResponse<unknown>>> {
        return api.post(schoolPaths.osis.generateTokens(eventId), data);
    }

    public static async getVoters(eventId: number | string, params: Record<string, unknown> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<VotingVoter>>>> {
        return api.get(schoolPaths.osis.voters(eventId), { params });
    }

    public static async castVote(token: string, candidateId: number): Promise<AxiosResponse<ApiResponse<{ receipt: string; voted_at: string }>>> {
        return api.post(schoolPaths.osis.castVote, { token, candidate_id: candidateId });
    }

    public static async getQuickCount(eventId: number | string): Promise<AxiosResponse<ApiResponse<VotingQuickCountData>>> {
        return api.get(schoolPaths.osis.quickCount(eventId));
    }
}

export default VotingService;
