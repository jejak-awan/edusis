/**
 * School Module — Facility & Asset Domain Types
 * Sarana & Prasarana, Ruangan, Aset, dan Tiket Pemeliharaan
 */

export interface RoomData {
    id: number;
    school_id: number;
    code: string;
    name: string;
    type: 'classroom' | 'laboratory' | 'workshop' | 'library' | 'hall' | 'office' | 'other';
    capacity: number;
    floor: number;
    condition: 'baik' | 'rusak_ringan' | 'rusak_berat';
    description: string | null;
    assets_count?: number;
    assets?: AssetData[];
}

export type Room = RoomData;

export interface AssetData {
    id: number;
    school_id: number;
    room_id: number | null;
    code: string;
    name: string;
    category: 'elektronik' | 'mebeler' | 'alat_praktik' | 'kendaraan' | 'buku' | 'lainnya';
    brand_model: string | null;
    acquisition_year: number | null;
    quantity: number;
    condition: 'baik' | 'rusak_ringan' | 'rusak_berat';
    source_of_fund: string;
    notes: string | null;
    room?: RoomData;
    maintenance_tickets?: MaintenanceTicketData[];
}

export type Asset = AssetData;

export interface MaintenanceTicketData {
    id: number;
    school_id: number;
    room_id: number;
    asset_id: number | null;
    reporter_staff_id: number;
    ticket_number: string;
    issue_description: string;
    priority: 'rendah' | 'sedang' | 'tinggi' | 'darurat';
    status: 'open' | 'in_progress' | 'resolved' | 'cancelled';
    cost: number;
    resolution_notes: string | null;
    resolved_at: string | null;
    created_at: string;
    room?: RoomData;
    asset?: AssetData;
    reporter?: { id: number; full_name: string; nip?: string };
}
