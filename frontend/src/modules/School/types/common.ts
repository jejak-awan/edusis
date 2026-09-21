/**
 * School Module — Common Types & Base Interfaces
 * Single Source of Truth (SoT) per ADR-031 / ADR-033
 */

export interface SchoolInfo {
    id: number;
    name: string;
    npsn?: string;
    code?: string;
    level?: string;
    status?: string;
    address?: string;
    phone?: string;
    email?: string;
    website?: string;
    principal_name?: string;
    principal_nip?: string;
    logo_url?: string;
}

export interface ApiResponse<T = unknown> {
    success: boolean;
    data: T;
    message?: string;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

export interface QueryFilters {
    page?: number;
    per_page?: number;
    search?: string;
    sort_by?: string;
    sort_direction?: 'asc' | 'desc';
    status?: string;
    [key: string]: unknown;
}
