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
    {
        path: 'school/grades',
        name: 'school.grades',
        component: () => import('@/modules/School/views/GradeManagement.vue'),
        meta: { title: 'school.grades.title', breadcrumb: 'school.navigation.menu.grades' },
    },
    {
        path: 'school/reports',
        name: 'school.reports',
        component: () => import('@/modules/School/views/ReportCardGenerator.vue'),
        meta: { title: 'school.reports.title', breadcrumb: 'school.navigation.menu.reports' },
    },
    {
        path: 'school/counseling',
        name: 'school.counseling',
        component: () => import('@/modules/School/views/CounselingManagement.vue'),
        meta: { title: 'school.counseling.title', breadcrumb: 'school.navigation.menu.counseling' },
    },
    {
        path: 'school/letters',
        name: 'school.letters',
        component: () => import('@/modules/School/views/LetterManagement.vue'),
        meta: { title: 'school.letters.title', breadcrumb: 'school.navigation.menu.letters' },
    },
    {
        path: 'school/ppdb',
        name: 'school.ppdb',
        component: () => import('@/modules/School/views/PpdbManagement.vue'),
        meta: { title: 'school.ppdb.title', breadcrumb: 'school.navigation.menu.ppdb' },
    },
    {
        path: 'school/osis',
        name: 'school.osis',
        component: () => import('@/modules/School/views/OsisVoting.vue'),
        meta: { title: 'school.osis.title', breadcrumb: 'school.navigation.menu.osis' },
    },
    {
        path: 'school/guests',
        name: 'school.guests',
        component: () => import('@/modules/School/views/GuestBook.vue'),
        meta: { title: 'school.guests.title', breadcrumb: 'school.navigation.menu.guests' },
    },
    {
        path: 'school/facilities',
        name: 'school.facilities',
        component: () => import('@/modules/School/views/FacilityManagement.vue'),
        meta: { title: 'school.facilities.title', breadcrumb: 'school.navigation.menu.facilities' },
    },
    {
        path: 'school/katampi',
        name: 'school.katampi',
        component: () => import('@/modules/School/views/KatampiIntegration.vue'),
        meta: { title: 'school.katampi.title', breadcrumb: 'school.navigation.menu.katampi' },
    },
    {
        path: 'school/whatsapp',
        name: 'school.whatsapp',
        component: () => import('@/modules/School/views/WhatsAppGateway.vue'),
        meta: { title: 'school.whatsapp.title', breadcrumb: 'school.navigation.menu.whatsapp' },
    },
];

export default schoolRoutes;
