import type { RouteRecordRaw } from 'vue-router';

export const schoolRoutes: RouteRecordRaw[] = [
    {
        path: 'school/dashboard',
        name: 'school.dashboard',
        component: () => import('@/modules/School/views/SchoolDashboard.vue'),
        meta: { title: 'school.dashboard.title', breadcrumb: 'school.navigation.menu.dashboard' },
    },
    {
        path: 'school/academic',
        name: 'school.academic',
        component: () => import('@/modules/School/views/AcademicManagement.vue'),
        meta: { title: 'school.academic.title', breadcrumb: 'school.navigation.menu.academic' },
    },
    {
        path: 'school/students',
        name: 'school.students',
        component: () => import('@/modules/School/views/StudentList.vue'),
        meta: { title: 'school.students.title', breadcrumb: 'school.navigation.menu.students' },
    },
    {
        path: 'school/discipline',
        name: 'school.discipline',
        component: () => import('@/modules/School/views/DisciplineManagement.vue'),
        meta: { title: 'school.discipline.title', breadcrumb: 'school.navigation.menu.discipline' },
    },
    {
        path: 'school/staff',
        name: 'school.staff',
        component: () => import('@/modules/School/views/StaffManagement.vue'),
        meta: { title: 'school.staff.title', breadcrumb: 'school.navigation.menu.staff' },
    },
    {
        path: 'school/attendance',
        name: 'school.attendance',
        component: () => import('@/modules/School/views/DailyAttendance.vue'),
        meta: { title: 'school.attendance.title', breadcrumb: 'school.navigation.menu.attendance' },
    },
    {
        path: 'school/journals',
        name: 'school.journals',
        component: () => import('@/modules/School/views/TeachingJournals.vue'),
        meta: { title: 'school.journals.title', breadcrumb: 'school.navigation.menu.journals' },
    },
];

export default schoolRoutes;
