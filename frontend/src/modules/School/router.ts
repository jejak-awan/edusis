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
];

export default schoolRoutes;
