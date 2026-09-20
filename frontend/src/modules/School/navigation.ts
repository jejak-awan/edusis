import type { NavItem } from '@/shared/utils/navigation';

export const schoolNavigation: NavItem[] = [
    {
        label: 'Sistem Sekolah',
        labelKey: 'school.navigation.menu.school',
        icon: 'graduation-cap',
        group: 'school',
        priority: 96,
        children: [
            {
                name: 'school.dashboard',
                label: 'Dashboard Sekolah',
                labelKey: 'school.navigation.menu.dashboard',
                icon: 'layout-dashboard',
                priority: 100,
            },
            {
                name: 'school.academic',
                label: 'Data Akademik',
                labelKey: 'school.navigation.menu.academic',
                icon: 'book-open',
                priority: 90,
            },
            {
                name: 'school.students',
                label: 'Data Siswa (Dapodik)',
                labelKey: 'school.navigation.menu.students',
                icon: 'users',
                priority: 80,
            },
            {
                name: 'school.discipline',
                label: 'Disiplin & Poin Kredit',
                labelKey: 'school.navigation.menu.discipline',
                icon: 'shield-alert',
                priority: 70,
            },
            {
                name: 'school.staff',
                label: 'Pendidik & GTK',
                labelKey: 'school.navigation.menu.staff',
                icon: 'user-check',
                priority: 65,
            },
            {
                name: 'school.attendance',
                label: 'Presensi Harian',
                labelKey: 'school.navigation.menu.attendance',
                icon: 'calendar-check',
                priority: 60,
            },
            {
                name: 'school.journals',
                label: 'Jurnal Pembelajaran KBM',
                labelKey: 'school.navigation.menu.journals',
                icon: 'clipboard-list',
                priority: 55,
            },
            {
                name: 'school.grades',
                label: 'Nilai & Asesmen',
                labelKey: 'school.navigation.menu.grades',
                icon: 'file-spreadsheet',
                priority: 50,
            },
            {
                name: 'school.reports',
                label: 'Leger & Raport Siswa',
                labelKey: 'school.navigation.menu.reports',
                icon: 'award',
                priority: 45,
            },
            {
                name: 'school.counseling',
                label: 'Bimbingan Konseling (BK)',
                labelKey: 'school.navigation.menu.counseling',
                icon: 'heart-handshake',
                priority: 40,
            },
        ],
    },
];

export default schoolNavigation;
