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
        ],
    },
];

export default schoolNavigation;
