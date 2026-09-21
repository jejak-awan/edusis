/**
 * School Service Facade — Backward-Compatibility Re-export Layer
 *
 * File ini dipertahankan sebagai FACADE untuk backward-compatibility dengan
 * seluruh view yang masih menggunakan `SchoolService.*` secara langsung.
 *
 * JANGAN menambahkan metode baru di sini.
 * Gunakan domain service yang sesuai:
 *  - Academic     → services/academicService.ts
 *  - Student      → services/studentService.ts
 *  - Attendance   → services/attendanceService.ts
 *  - Counseling   → services/counselingService.ts
 *  - Facility     → services/facilityService.ts
 *  - Operations   → services/operationsService.ts
 *  - Voting       → services/votingService.ts
 *  - HR/Staff     → services/hrService.ts
 *  - Integration  → services/integrationService.ts
 *
 * @see ADR-031 — Bab 2.B.2 (Domain Services & Facade Strategy)
 */

import api from '@/engine/api/client';
import type { AxiosResponse } from 'axios';

// ─── Re-export Domain Types ──────────────────────────────────────────────────
// Semua interface dipindahkan ke services/types.ts (SoT tunggal).
// Re-export di sini hanya untuk backward-compatibility.
export type {
    SchoolInfo,
    Department,
    AcademicYear,
    Semester,
    StudyGroup,
    Subject,
    Student,
    Staff,
    Schedule,
    TeachingJournal,
    AttendanceRecord,
    CounselingCase,
    Letter,
    PpdbApplicant,
    GuestEntry,
    Room,
    Asset,
    VotingEvent,
    VotingCandidate,
    VotingVoter,
    ApiResponse,
    PaginatedData,
} from './types';

// ─── Re-export Domain-Specific Types (dari schoolService lama) ───────────────
// Interface yang lebih spesifik dan belum ada di types.ts
export type {
    AcademicYearData,
    SemesterData,
    DepartmentData,
    SubjectData,
    StudyGroupData,
    StudentParentData,
    StudentData,
    StaffData,
    StaffShiftData,
    StaffAttendanceData,
    ScheduleData,
    TeachingJournalData,
    StudentAttendanceItem,
    PointCategoryData,
    ViolationData,
    AchievementData,
    AssessmentData,
    GradeData,
    ReportCardData,
    P5ProjectData,
    CounselingCaseData,
    LetterData,
    PpdbData,
    GuestBookData,
    OsisCandidateData,
    OsisElectionData,
    VotingEventType,
    VotingMode,
    TargetAudience,
    VoteCandidateData,
    VoteVoterData,
    VoteEventData,
    VotingQuickCountData,
    ExtracurricularData,
    RoomData,
    AssetData,
    MaintenanceTicketData,
    KatampiConfigData,
    WhatsAppLogData,
    WhatsAppStatsData,
} from './schoolServiceTypes';

// ─── Re-export Domain Service Classes ────────────────────────────────────────
export { AcademicService } from './academicService';
export { StudentService } from './studentService';
export { AttendanceService } from './attendanceService';
export { CounselingService } from './counselingService';
export { FacilityService } from './facilityService';
export { OperationsService } from './operationsService';
export { VotingService } from './votingService';
export { HrService } from './hrService';
export { IntegrationService } from './integrationService';

// ─── SchoolService Facade Object ─────────────────────────────────────────────
// Method-method di bawah ini mendelegasikan ke domain service yang sesuai.
// Dipertahankan untuk backward-compatibility dengan view yang belum dimigrasikan.

import { AcademicService } from './academicService';
import { StudentService } from './studentService';
import { AttendanceService } from './attendanceService';
import { CounselingService } from './counselingService';
import { FacilityService } from './facilityService';
import { OperationsService } from './operationsService';
import { VotingService } from './votingService';
import { HrService } from './hrService';
import { IntegrationService } from './integrationService';
import { schoolPaths } from './paths';

export const SchoolService = {
    // ── Dashboard ──────────────────────────────────────────────────────────
    getDashboard(): Promise<AxiosResponse> {
        return api.get(schoolPaths.dashboard);
    },

    // ── Academic Years ─────────────────────────────────────────────────────
    getYears: (params = {}) => AcademicService.getAcademicYears(params),
    storeYear: (payload: object) => api.post('/school/academic/years', payload),
    activateYear: (id: number) => api.post(`/school/academic/years/${id}/activate`),
    deleteYear: (id: number) => api.delete(`/school/academic/years/${id}`),
    activateSemester: (id: number) => api.post(`/school/academic/semesters/${id}/activate`),

    // ── Levels ─────────────────────────────────────────────────────────────
    getLevels: () => api.get('/school/academic/levels'),

    // ── Departments ────────────────────────────────────────────────────────
    getDepartments: (params = {}) => AcademicService.getDepartments(params),
    storeDepartment: (payload: object) => AcademicService.createDepartment(payload as any),
    updateDepartment: (id: number, payload: object) => api.put(`/school/academic/departments/${id}`, payload),
    deleteDepartment: (id: number) => api.delete(`/school/academic/departments/${id}`),

    // ── Subjects ───────────────────────────────────────────────────────────
    getSubjects: (params = {}) => AcademicService.getSubjects(params),
    storeSubject: (payload: object) => AcademicService.createSubject(payload as any),
    updateSubject: (id: number, payload: object) => api.put(`/school/academic/subjects/${id}`, payload),
    deleteSubject: (id: number) => api.delete(`/school/academic/subjects/${id}`),

    // ── Study Groups ───────────────────────────────────────────────────────
    getStudyGroups: (params = {}) => AcademicService.getStudyGroups(params),
    storeStudyGroup: (payload: object) => AcademicService.createStudyGroup(payload as any),
    showStudyGroup: (id: number) => api.get(`/school/academic/study-groups/${id}`),
    updateStudyGroup: (id: number, payload: object) => api.put(`/school/academic/study-groups/${id}`, payload),
    deleteStudyGroup: (id: number) => api.delete(`/school/academic/study-groups/${id}`),

    // ── Students ───────────────────────────────────────────────────────────
    getStudents: (params = {}) => StudentService.getStudents(params),
    showStudent: (id: number) => StudentService.getStudent(id),
    storeStudent: (payload: object) => StudentService.createStudent(payload as any),
    updateStudent: (id: number, payload: object) => StudentService.updateStudent(id, payload as any),
    deleteStudent: (id: number) => StudentService.deleteStudent(id),

    // ── Discipline & Credit Points ─────────────────────────────────────────
    getDisciplineCategories: (params = {}) => api.get('/school/discipline/categories', { params }),
    storeDisciplineCategory: (payload: object) => api.post('/school/discipline/categories', payload),
    recordPoint: (payload: object) => api.post('/school/discipline/record', payload),
    getViolations: (params = {}) => api.get('/school/discipline/violations', { params }),
    resolveViolation: (id: number, notes?: string) => api.post(`/school/discipline/violations/${id}/resolve`, { follow_up_notes: notes }),
    getAchievements: (params = {}) => api.get('/school/discipline/achievements', { params }),

    // ── Staff & GTK ────────────────────────────────────────────────────────
    getStaff: (params = {}) => HrService.getStaff(params),
    storeStaff: (payload: object) => HrService.createStaff(payload as any),
    showStaff: (id: number) => HrService.getStaffById(id),
    updateStaff: (id: number, payload: object) => HrService.updateStaff(id, payload as any),
    deleteStaff: (id: number) => HrService.deleteStaff(id),
    getStaffShifts: () => api.get('/school/staff/shifts/list'),
    storeStaffShift: (payload: object) => api.post('/school/staff/shifts', payload),

    // ── Attendance ─────────────────────────────────────────────────────────
    getStudentAttendance: (params: { study_group_id: number; date: string }) =>
        AttendanceService.getStudentAttendance(params),
    saveBulkStudentAttendance: (payload: { study_group_id: number; date: string; records: any[] }) =>
        AttendanceService.saveBulkStudentAttendance(payload),
    getStaffAttendance: (params = {}) => api.get('/school/attendance/staff', { params }),
    recordStaffCheckIn: (payload: object) => api.post('/school/attendance/staff/check-in', payload),
    recordStaffCheckOut: (payload: object) => api.post('/school/attendance/staff/check-out', payload),

    // ── Schedules ──────────────────────────────────────────────────────────
    getSchedules: (params = {}) => AcademicService.getSchedules(params),
    storeSchedule: (payload: object) => AcademicService.createSchedule(payload as any),
    updateSchedule: (id: number, payload: object) => api.put(`/school/schedules/${id}`, payload),
    deleteSchedule: (id: number) => api.delete(`/school/schedules/${id}`),

    // ── Teaching Journals ──────────────────────────────────────────────────
    getJournals: (params = {}) => AcademicService.getTeachingJournals(params),
    storeJournal: (payload: object) => AcademicService.createTeachingJournal(payload as any),
    showJournal: (id: number) => api.get(`/school/journals/${id}`),
    updateJournal: (id: number, payload: object) => api.put(`/school/journals/${id}`, payload),
    deleteJournal: (id: number) => api.delete(`/school/journals/${id}`),

    // ── Assessments & Grades ───────────────────────────────────────────────
    getAssessments: (params = {}) => AcademicService.getAssessments(params),
    storeAssessment: (payload: object) => AcademicService.createAssessment(payload),
    showAssessment: (id: number) => api.get(`/school/assessments/${id}`),
    deleteAssessment: (id: number) => api.delete(`/school/assessments/${id}`),
    getAssessmentGrades: (id: number) => api.get(`/school/assessments/${id}/grades`),
    storeBulkGrades: (id: number, grades: any[]) => AcademicService.saveBulkGrades(id, grades),

    // ── Reports & Ledger ───────────────────────────────────────────────────
    getLedger: (studyGroupId: number) => AcademicService.getGradeLedger({ study_group_id: studyGroupId }),
    calculateReportCards: (studyGroupId: number) => api.post('/school/reports/calculate', { study_group_id: studyGroupId }),
    getStudentReport: (studentId: number) => AcademicService.getStudentReportCard(studentId),

    // ── P5 Projects ────────────────────────────────────────────────────────
    getP5Projects: (params = {}) => api.get('/school/p5/projects', { params }),
    storeP5Project: (payload: object) => api.post('/school/p5/projects', payload),
    showP5Project: (id: number) => api.get(`/school/p5/projects/${id}`),
    storeBulkP5Assessments: (id: number, assessments: any[]) =>
        api.post(`/school/p5/projects/${id}/assessments/bulk`, { assessments }),

    // ── Counseling (BK) ────────────────────────────────────────────────────
    getCounselingCases: (params = {}) => CounselingService.getCases(params),
    storeCounselingCase: (payload: object) => CounselingService.createCase(payload as any),
    showCounselingCase: (id: number) => CounselingService.getCase(id),
    updateCounselingCase: (id: number, payload: object) => CounselingService.updateCase(id, payload as any),
    resolveCounselingCase: (id: number, payload: object) => CounselingService.resolveCase(id, payload as any),
    deleteCounselingCase: (id: number) => api.delete(`/school/counseling/cases/${id}`),

    // ── Letters & Correspondence ───────────────────────────────────────────
    getLetters: (params = {}) => OperationsService.getLetters(params),
    generateLetterNumber: (params: { type: string; category: string }) =>
        OperationsService.generateLetterNumber(params.type),
    storeLetter: (payload: object) => OperationsService.createLetter(payload as any),
    showLetter: (id: number) => api.get(`/school/letters/${id}`),
    updateLetter: (id: number, payload: object) => api.put(`/school/letters/${id}`, payload),
    updateLetterDisposition: (id: number, payload: object) => api.post(`/school/letters/${id}/disposition`, payload),
    deleteLetter: (id: number) => api.delete(`/school/letters/${id}`),

    // ── PPDB Online ────────────────────────────────────────────────────────
    getPpdbList: (params = {}) => OperationsService.getPpdbApplicants(params),
    storePpdb: (payload: object) => OperationsService.submitPpdbApplication(payload as any),
    showPpdb: (id: number) => api.get(`/school/ppdb/${id}`),
    updatePpdb: (id: number, payload: object) => api.put(`/school/ppdb/${id}`, payload),
    verifyPpdb: (id: number, payload: object) => OperationsService.verifyPpdbApplicant(id, payload as any),
    admitPpdb: (id: number) => OperationsService.admitPpdbApplicant(id, {}),
    deletePpdb: (id: number) => api.delete(`/school/ppdb/${id}`),

    // ── Guest Book ─────────────────────────────────────────────────────────
    getGuestBook: (params = {}) => OperationsService.getGuestBookEntries(params),
    storeGuest: (payload: object) => OperationsService.checkInGuest(payload as any),
    showGuest: (id: number) => api.get(`/school/guest-book/${id}`),
    updateGuest: (id: number, payload: object) => api.put(`/school/guest-book/${id}`, payload),
    checkoutGuest: (id: number, notes?: string) => OperationsService.checkOutGuest(id, notes),
    deleteGuest: (id: number) => api.delete(`/school/guest-book/${id}`),

    // ── OSIS & E-Voting (OSIS Elections) ───────────────────────────────────
    getOsisElections: (params = {}) => api.get('/school/osis/elections', { params }),
    storeOsisElection: (payload: object) => api.post('/school/osis/elections', payload),
    showOsisElection: (id: number) => api.get(`/school/osis/elections/${id}`),
    updateOsisElection: (id: number, payload: object) => api.put(`/school/osis/elections/${id}`, payload),
    deleteOsisElection: (id: number) => api.delete(`/school/osis/elections/${id}`),
    addOsisCandidate: (electionId: number, payload: object) =>
        api.post(`/school/osis/elections/${electionId}/candidates`, payload),
    voteOsis: (electionId: number, payload: object) =>
        api.post(`/school/osis/elections/${electionId}/vote`, payload),
    getOsisQuickCount: (electionId: number) =>
        api.get(`/school/osis/elections/${electionId}/quick-count`),

    // ── Generic E-Voting Engine ────────────────────────────────────────────
    getVotingEvents: (params = {}) => VotingService.getEvents(params),
    storeVotingEvent: (payload: object) => VotingService.createEvent(payload as any),
    showVotingEvent: (id: number) => VotingService.getEvent(id),
    updateVotingEvent: (id: number, payload: object) => api.put(`/school/voting/events/${id}`, payload),
    deleteVotingEvent: (id: number) => api.delete(`/school/voting/events/${id}`),
    addVotingCandidate: (eventId: number, payload: object) => VotingService.addCandidate(eventId, payload as any),
    generateVotingVoters: (eventId: number, payload: { source: string; study_group_id?: number }) =>
        VotingService.generateVoters(eventId, payload),
    getVotingVoters: (eventId: number, params = {}) => VotingService.getVoters(eventId, params),
    castVoteEngine: (eventId: number, payload: { candidate_id: number; token?: string; mode?: string }) =>
        api.post(`/school/voting/events/${eventId}/vote`, payload),
    getVotingQuickCount: (eventId: number) => VotingService.getQuickCount(eventId),

    // ── Extracurriculars ───────────────────────────────────────────────────
    getExtracurriculars: (params = {}) => api.get('/school/extracurriculars', { params }),
    storeExtracurricular: (payload: object) => api.post('/school/extracurriculars', payload),
    showExtracurricular: (id: number) => api.get(`/school/extracurriculars/${id}`),
    updateExtracurricular: (id: number, payload: object) => api.put(`/school/extracurriculars/${id}`, payload),
    deleteExtracurricular: (id: number) => api.delete(`/school/extracurriculars/${id}`),

    // ── Facilities — Rooms ─────────────────────────────────────────────────
    getRooms: (params = {}) => FacilityService.getRooms(params),
    storeRoom: (payload: object) => FacilityService.createRoom(payload as any),
    updateRoom: (id: number, payload: object) => api.put(`/school/facilities/rooms/${id}`, payload),
    deleteRoom: (id: number) => api.delete(`/school/facilities/rooms/${id}`),

    // ── Facilities — Assets ────────────────────────────────────────────────
    getAssets: (params = {}) => FacilityService.getAssets(params),
    storeAsset: (payload: object) => FacilityService.createAsset(payload as any),
    updateAsset: (id: number, payload: object) => api.put(`/school/facilities/assets/${id}`, payload),
    deleteAsset: (id: number) => api.delete(`/school/facilities/assets/${id}`),

    // ── Facilities — Maintenance Tickets ───────────────────────────────────
    getTickets: (params = {}) => FacilityService.getMaintenanceTickets(params),
    storeTicket: (payload: object) => FacilityService.createMaintenanceTicket(payload),
    updateTicket: (id: number, payload: object) => api.put(`/school/facilities/tickets/${id}`, payload),
    resolveTicket: (id: number, payload: object) => FacilityService.resolveMaintenanceTicket(id, payload as Record<string, unknown>),

    // ── KataMPI CBT Integration ────────────────────────────────────────────
    getKatampiConfig: () => IntegrationService.getKatampiConfig(),
    updateKatampiConfig: (payload: object) => IntegrationService.updateKatampiConfig(payload as any),
    generateKatampiSsoToken: (payload: object = {}) => IntegrationService.generateKatampiSsoToken(payload as any),
    getKatampiRoster: (params = {}) => IntegrationService.getKatampiRoster(params),
    syncKatampiGrades: (payload: object) => IntegrationService.syncKatampiGrades(payload as any),

    // ── WhatsApp Gateway ───────────────────────────────────────────────────
    getWhatsAppLogs: (params = {}) => IntegrationService.getWhatsAppLogs(params),
    getWhatsAppStats: () => IntegrationService.getWhatsAppStats(),
    sendWhatsAppMessage: (payload: object) => IntegrationService.sendWhatsAppMessage(payload as any),
    sendWhatsAppAttendanceAlerts: (payload: object = {}) =>
        IntegrationService.sendWhatsAppAttendanceAlerts(payload as any),
    resendWhatsAppLog: (id: number) => IntegrationService.resendWhatsAppLog(id),
};
