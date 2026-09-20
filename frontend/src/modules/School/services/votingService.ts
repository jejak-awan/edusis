import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';
import type { ApiResponse, PaginatedData, VotingCandidate, VotingEvent, VotingVoter } from './types';

export class VotingService {
    public static async getEvents(params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<VotingEvent[]>>> {
        return api.get('/school/voting/events', { params });
    }

    public static async getEvent(id: number | string): Promise<AxiosResponse<ApiResponse<VotingEvent>>> {
        return api.get(`/school/voting/events/${id}`);
    }

    public static async createEvent(data: Partial<VotingEvent>): Promise<AxiosResponse<ApiResponse<VotingEvent>>> {
        return api.post('/school/voting/events', data);
    }

    public static async addCandidate(eventId: number | string, data: Partial<VotingCandidate> | FormData): Promise<AxiosResponse<ApiResponse<VotingCandidate>>> {
        const headers = data instanceof FormData ? { 'Content-Type': 'multipart/form-data' } : undefined;
        return api.post(`/school/voting/events/${eventId}/candidates`, data, { headers });
    }

    public static async generateVoters(eventId: number | string, data: { source: string; study_group_id?: number }): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.post(`/school/voting/events/${eventId}/voters/generate`, data);
    }

    public static async getVoters(eventId: number | string, params: Record<string, any> = {}): Promise<AxiosResponse<ApiResponse<PaginatedData<VotingVoter>>>> {
        return api.get(`/school/voting/events/${eventId}/voters`, { params });
    }

    public static async castVote(token: string, candidateId: number): Promise<AxiosResponse<ApiResponse<{ receipt: string; voted_at: string }>>> {
        return api.post('/school/voting/cast', { token, candidate_id: candidateId });
    }

    public static async getQuickCount(eventId: number | string): Promise<AxiosResponse<ApiResponse<any>>> {
        return api.get(`/school/voting/events/${eventId}/quick-count`);
    }
}

export default VotingService;
