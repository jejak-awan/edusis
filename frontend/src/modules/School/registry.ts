/**
 * School Module — Sub-Module & Feature Registry (App Store Aligned)
 * Handles sub-module declarations, dependency resolution, license tiering, and active state checks.
 * Aligned with ADR-033
 */

export interface SchoolSubFeature {
    slug: string;
    name: string;
    description: string;
    category: 'foundation' | 'engine' | 'operational' | 'integration';
    dependencies: string[];
    licenseTier: 'basic' | 'standard' | 'pro' | 'enterprise';
    defaultActive: boolean;
}

export const SCHOOL_SUB_FEATURES: readonly SchoolSubFeature[] = [
    {
        slug: 'school.core',
        name: 'School Core & Institution',
        description: 'Profil sekolah, tahun ajaran, level, jurusan, dan rombel',
        category: 'foundation',
        dependencies: [],
        licenseTier: 'basic',
        defaultActive: true,
    },
    {
        slug: 'school.students',
        name: 'Student Lifecycle & Dapodik',
        description: 'Manajemen data siswa Dapodik dan orang tua/wali',
        category: 'operational',
        dependencies: ['school.core'],
        licenseTier: 'basic',
        defaultActive: true,
    },
    {
        slug: 'school.staff',
        name: 'Staff & GTK',
        description: 'Tenaga pendidik dan kependidikan, jadwal shift, dan absensi pegawai',
        category: 'operational',
        dependencies: ['school.core'],
        licenseTier: 'basic',
        defaultActive: true,
    },
    {
        slug: 'school.academic',
        name: 'Academic & Curriculum',
        description: 'Mata pelajaran, jadwal KBM, jurnal mengajar, penilaian, raport Kurikulum Merdeka',
        category: 'operational',
        dependencies: ['school.core', 'school.students'],
        licenseTier: 'standard',
        defaultActive: true,
    },
    {
        slug: 'school.attendance',
        name: 'Attendance Engine',
        description: 'Mesin pencatat presensi polimorfik harian, mapel, dan rekap rombel/siswa',
        category: 'engine',
        dependencies: ['school.students'],
        licenseTier: 'standard',
        defaultActive: true,
    },
    {
        slug: 'school.discipline',
        name: 'Discipline & Counseling',
        description: 'Kredit poin tata tertib, SP1-SP3, dan bimbingan konseling privat',
        category: 'operational',
        dependencies: ['school.students'],
        licenseTier: 'standard',
        defaultActive: true,
    },
    {
        slug: 'school.operations',
        name: 'Operations & Document Engine',
        description: 'Penomoran surat dinas otomatis, template dokumen, dan buku tamu',
        category: 'engine',
        dependencies: ['school.core'],
        licenseTier: 'standard',
        defaultActive: true,
    },
    {
        slug: 'school.osis',
        name: 'OSIS & Voting Engine',
        description: 'Kepengurusan OSIS, bilik suara kiosk, token voter, dan rekapitulasi suara',
        category: 'engine',
        dependencies: ['school.students', 'school.operations'],
        licenseTier: 'pro',
        defaultActive: true,
    },
    {
        slug: 'school.extracurricular',
        name: 'Extracurricular',
        description: 'Manajemen ekstrakurikuler, keanggotaan, dan penilaian karakter P3',
        category: 'operational',
        dependencies: ['school.students', 'school.attendance'],
        licenseTier: 'standard',
        defaultActive: true,
    },
    {
        slug: 'school.graduation',
        name: 'Graduation & Alumni',
        description: 'Kelulusan, SKL resmi, penelusuran tamatan, dan portal alumni',
        category: 'operational',
        dependencies: ['school.students', 'school.operations'],
        licenseTier: 'pro',
        defaultActive: true,
    },
    {
        slug: 'school.career',
        name: 'Career & Job Vacancies (BKK)',
        description: 'Bursa Kerja Khusus (BKK), portal lowongan kerja alumni, dan pelacakan lamaran mitra industri',
        category: 'operational',
        dependencies: ['school.core', 'school.graduation'],
        licenseTier: 'pro',
        defaultActive: true,
    },
    {
        slug: 'school.admission',
        name: 'PPDB Online',
        description: 'Penerimaan peserta didik baru, seleksi jalur, dan verifikasi berkas',
        category: 'operational',
        dependencies: ['school.core'],
        licenseTier: 'pro',
        defaultActive: true,
    },
    {
        slug: 'school.integrations',
        name: 'Integrations Gateway',
        description: 'Sinkronisasi CBT KataMPI dan WhatsApp Gateway notifikasi ortu',
        category: 'integration',
        dependencies: ['school.core'],
        licenseTier: 'enterprise',
        defaultActive: true,
    },
] as const;

export class SchoolFeatureRegistry {
    private activeFeatures: Set<string>;

    constructor(initialActiveSlugs?: string[]) {
        if (initialActiveSlugs && initialActiveSlugs.length > 0) {
            this.activeFeatures = new Set(initialActiveSlugs);
        } else {
            // Default active set
            this.activeFeatures = new Set(
                SCHOOL_SUB_FEATURES.filter((f) => f.defaultActive).map((f) => f.slug)
            );
        }
    }

    /**
     * Check if a specific sub-feature is active
     */
    isActive(slug: string): boolean {
        if (!this.activeFeatures.has(slug)) {
            return false;
        }

        // Verify dependencies are satisfied
        const feature = this.getFeature(slug);
        if (!feature) {
            return false;
        }

        return feature.dependencies.every((dep) => this.activeFeatures.has(dep));
    }

    /**
     * Get feature definition by slug
     */
    getFeature(slug: string): SchoolSubFeature | undefined {
        return SCHOOL_SUB_FEATURES.find((f) => f.slug === slug);
    }

    /**
     * List all features
     */
    getAllFeatures(): readonly SchoolSubFeature[] {
        return SCHOOL_SUB_FEATURES;
    }

    /**
     * List only active features
     */
    getActiveFeatures(): SchoolSubFeature[] {
        return SCHOOL_SUB_FEATURES.filter((f) => this.isActive(f.slug));
    }

    /**
     * Enable a feature (validating dependencies)
     */
    enableFeature(slug: string): { success: boolean; missingDependencies?: string[] } {
        const feature = this.getFeature(slug);
        if (!feature) {
            return { success: false };
        }

        const missing = feature.dependencies.filter((dep) => !this.activeFeatures.has(dep));
        if (missing.length > 0) {
            return { success: false, missingDependencies: missing };
        }

        this.activeFeatures.add(slug);
        return { success: true };
    }

    /**
     * Disable a feature
     */
    disableFeature(slug: string): void {
        this.activeFeatures.delete(slug);
    }

    /**
     * Sync active features from remote config or tenant settings
     */
    setActiveFeatures(slugs: string[]): void {
        this.activeFeatures = new Set(slugs);
    }
}

export const schoolFeatureRegistry = new SchoolFeatureRegistry();
export default schoolFeatureRegistry;
