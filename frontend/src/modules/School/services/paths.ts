/**
 * School Module — Canonical API Paths Registry
 * Single Source of Truth for all School endpoint URIs (relative to axios baseURL `/api/v1`).
 * Aligned with ADR-031 and ADR-033
 */

export const schoolPaths = {
    dashboard: '/school/dashboard',

    academic: {
        years: '/school/academic/years',
        year: (id: number | string) => `/school/academic/years/${id}`,
        departments: '/school/academic/departments',
        department: (id: number | string) => `/school/academic/departments/${id}`,
        studyGroups: '/school/academic/study-groups',
        studyGroup: (id: number | string) => `/school/academic/study-groups/${id}`,
        subjects: '/school/academic/subjects',
        subject: (id: number | string) => `/school/academic/subjects/${id}`,
        levels: '/school/academic/levels',
        schedules: '/school/schedules',
        schedule: (id: number | string) => `/school/schedules/${id}`,
        journals: '/school/journals',
        journal: (id: number | string) => `/school/journals/${id}`,
        assessments: '/school/assessments',
        assessment: (id: number | string) => `/school/assessments/${id}`,
        grades: (assessmentId: number | string) => `/school/assessments/${assessmentId}/grades`,
        ledger: '/school/reports/ledger',
        studentReport: (studentId: number | string) => `/school/reports/student/${studentId}`,
        p5Projects: '/school/p5/projects',
        p5Project: (id: number | string) => `/school/p5/projects/${id}`,
    },

    students: {
        index: '/school/students',
        show: (id: number | string) => `/school/students/${id}`,
        create: '/school/students',
        update: (id: number | string) => `/school/students/${id}`,
        delete: (id: number | string) => `/school/students/${id}`,
        import: '/school/students/import',
        disciplineSummary: (studentId: number | string) => `/school/students/${studentId}/discipline-summary`,
    },

    staff: {
        index: '/school/staff',
        show: (id: number | string) => `/school/staff/${id}`,
        create: '/school/staff',
        update: (id: number | string) => `/school/staff/${id}`,
        delete: (id: number | string) => `/school/staff/${id}`,
        shifts: '/school/staff/shifts/list',
        salaryStructure: (id: number | string) => `/school/staff/${id}/salary-structure`,
    },

    attendance: {
        students: '/school/attendance/students',
        saveStudents: '/school/attendance/students',
        staff: '/school/attendance/staff',
        saveStaff: '/school/attendance/staff',
        recapClass: '/school/attendance/recap/class',
        recapStudent: (studentId: number | string) => `/school/attendance/recap/student/${studentId}`,
    },

    discipline: {
        categories: '/school/discipline/categories',
        violations: '/school/discipline/violations',
        violation: (id: number | string) => `/school/discipline/violations/${id}`,
        achievements: '/school/discipline/achievements',
        achievement: (id: number | string) => `/school/discipline/achievements/${id}`,
        counselingCases: '/school/counseling/cases',
        counselingCase: (id: number | string) => `/school/counseling/cases/${id}`,
    },

    operations: {
        letters: '/school/letters',
        letter: (id: number | string) => `/school/letters/${id}`,
        generateNumber: '/school/letters/generate-number',
        ppdb: '/school/ppdb',
        ppdbItem: (id: number | string) => `/school/ppdb/${id}`,
        guestBook: '/school/guest-book',
        guestBookItem: (id: number | string) => `/school/guest-book/${id}`,
    },

    osis: {
        elections: '/school/osis/elections',
        election: (id: number | string) => `/school/osis/elections/${id}`,
        quickCount: (id: number | string) => `/school/osis/elections/${id}/quick-count`,
        votingEvents: '/school/voting/events',
        votingEvent: (id: number | string) => `/school/voting/events/${id}`,
        voters: (eventId: number | string) => `/school/voting/events/${eventId}/voters`,
        castVote: '/school/voting/cast',
        generateTokens: (eventId: number | string) => `/school/voting/events/${eventId}/generate-tokens`,
    },

    extracurricular: {
        index: '/school/extracurriculars',
        show: (id: number | string) => `/school/extracurriculars/${id}`,
        members: (id: number | string) => `/school/extracurriculars/${id}/members`,
    },

    graduation: {
        index: '/school/graduation',
        show: (id: number | string) => `/school/graduation/${id}`,
        studentResult: (studentId: number | string) => `/school/graduation/student/${studentId}`,
        downloadCertificate: (studentId: number | string) => `/school/graduation/student/${studentId}/certificate`,
        alumni: '/school/alumni',
    },

    grades: {
        studentGrades: (studentId: number | string) => `/school/grades/student/${studentId}`,
        saveBulk: '/school/grades/bulk',
    },

    facilities: {
        rooms: '/school/facilities/rooms',
        room: (id: number | string) => `/school/facilities/rooms/${id}`,
        assets: '/school/facilities/assets',
        asset: (id: number | string) => `/school/facilities/assets/${id}`,
        tickets: '/school/facilities/tickets',
        ticket: (id: number | string) => `/school/facilities/tickets/${id}`,
    },

    integrations: {
        katampiConfig: '/school/integrations/katampi/config',
        katampiTest: '/school/integrations/katampi/test',
        katampiSync: '/school/integrations/katampi/sync',
        katampiRoster: '/school/integrations/katampi/roster',
        whatsAppLogs: '/school/communications/whatsapp/logs',
        whatsAppStats: '/school/communications/whatsapp/stats',
        whatsAppSend: '/school/communications/whatsapp/send',
    },

    public: {
        votingKiosk: (eventId: number | string) => `/public/school/voting/${eventId}/kiosk`,
        verifyToken: '/public/school/voting/verify-token',
        castVote: '/public/school/voting/cast',
        ppdbRegister: '/public/school/ppdb/register',
        verifyDocument: (hash: string) => `/public/school/verify/${hash}`,
        alumniDirectory: '/public/school/alumni',
    },
} as const;

export default schoolPaths;
