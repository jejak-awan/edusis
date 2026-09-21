import { defineStore } from 'pinia';
import { ref } from 'vue';
import { SchoolService } from '../services/schoolService';
import type {
    AcademicYearData,
    DepartmentData,
    StudyGroupData,
    StudentData,
    PointCategoryData,
    ViolationData,
    AchievementData,
} from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useSchoolStore = defineStore('school', () => {
    // State
    const dashboardData = ref<Record<string, unknown> | null>(null);
    const academicYears = ref<AcademicYearData[]>([]);
    const activeYear = ref<AcademicYearData | null>(null);
    const departments = ref<DepartmentData[]>([]);
    const studyGroups = ref<StudyGroupData[]>([]);
    const students = ref<StudentData[]>([]);
    const totalStudentsCount = ref<number>(0);
    const pointCategories = ref<PointCategoryData[]>([]);
    const violations = ref<ViolationData[]>([]);
    const achievements = ref<AchievementData[]>([]);
    const isLoading = ref<boolean>(false);
    const error = ref<string | null>(null);

    // Actions
    async function fetchDashboard() {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await SchoolService.getDashboard();
            if (res.data?.success) {
                dashboardData.value = res.data.data;
                activeYear.value = res.data.data.active_academic_year ?? null;
            }
        } catch (e: unknown) {
            const msg = e instanceof Error ? e.message : 'Failed to fetch dashboard data';
            error.value = msg;
            logger.error('[School:Store] Failed to fetch dashboard data', e);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAcademicYears() {
        isLoading.value = true;
        try {
            const res = await SchoolService.getYears();
            if (res.data?.success) {
                const list = ensureArray<AcademicYearData>(res.data.data);
                academicYears.value = list;
                activeYear.value = list.find((y) => y.is_active) ?? null;
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch academic years', e);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchDepartments() {
        try {
            const res = await SchoolService.getDepartments();
            if (res.data?.success) {
                departments.value = ensureArray<DepartmentData>(res.data.data);
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch departments', e);
        }
    }

    async function fetchStudyGroups(academicYearId?: number) {
        try {
            const params: Record<string, unknown> = {};
            if (academicYearId) params.academic_year_id = academicYearId;
            const res = await SchoolService.getStudyGroups(params);
            if (res.data?.success) {
                studyGroups.value = ensureArray<StudyGroupData>(res.data.data);
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch study groups', e);
        }
    }

    async function fetchStudents(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        try {
            const res = await SchoolService.getStudents(params);
            if (res.data?.success) {
                students.value = ensureArray<StudentData>(res.data.data?.data);
                totalStudentsCount.value = res.data.data?.total ?? 0;
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch students', e);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchDisciplineCategories() {
        try {
            const res = await SchoolService.getDisciplineCategories();
            if (res.data?.success) {
                pointCategories.value = ensureArray<PointCategoryData>(res.data.data);
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch categories', e);
        }
    }

    async function fetchViolations() {
        try {
            const res = await SchoolService.getViolations();
            if (res.data?.success) {
                violations.value = ensureArray<ViolationData>(res.data.data?.data);
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch violations', e);
        }
    }

    async function fetchAchievements() {
        try {
            const res = await SchoolService.getAchievements();
            if (res.data?.success) {
                achievements.value = ensureArray<AchievementData>(res.data.data?.data);
            }
        } catch (e: unknown) {
            logger.error('[School:Store] Failed to fetch achievements', e);
        }
    }

    return {
        dashboardData,
        academicYears,
        activeYear,
        departments,
        studyGroups,
        students,
        totalStudentsCount,
        pointCategories,
        violations,
        achievements,
        isLoading,
        error,
        fetchDashboard,
        fetchAcademicYears,
        fetchDepartments,
        fetchStudyGroups,
        fetchStudents,
        fetchDisciplineCategories,
        fetchViolations,
        fetchAchievements,
    };
});

export default useSchoolStore;
