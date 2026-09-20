import { defineStore } from 'pinia';
import { ref } from 'vue';
import {
    SchoolService,
    type AcademicYearData,
    type DepartmentData,
    type StudyGroupData,
    type StudentData,
    type PointCategoryData,
    type ViolationData,
    type AchievementData,
} from '../services/schoolService';

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
            error.value = e instanceof Error ? e.message : 'Failed to fetch dashboard data';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchAcademicYears() {
        isLoading.value = true;
        try {
            const res = await SchoolService.getYears();
            if (res.data?.success) {
                academicYears.value = res.data.data;
                activeYear.value = academicYears.value.find((y) => y.is_active) ?? null;
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch academic years';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchDepartments() {
        try {
            const res = await SchoolService.getDepartments();
            if (res.data?.success) {
                departments.value = res.data.data;
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch departments';
        }
    }

    async function fetchStudyGroups(academicYearId?: number) {
        try {
            const params: Record<string, unknown> = {};
            if (academicYearId) params.academic_year_id = academicYearId;
            const res = await SchoolService.getStudyGroups(params);
            if (res.data?.success) {
                studyGroups.value = res.data.data;
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch study groups';
        }
    }

    async function fetchStudents(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        try {
            const res = await SchoolService.getStudents(params);
            if (res.data?.success) {
                students.value = res.data.data.data ?? [];
                totalStudentsCount.value = res.data.data.total ?? 0;
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch students';
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchDisciplineCategories() {
        try {
            const res = await SchoolService.getDisciplineCategories();
            if (res.data?.success) {
                pointCategories.value = res.data.data;
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch categories';
        }
    }

    async function fetchViolations() {
        try {
            const res = await SchoolService.getViolations();
            if (res.data?.success) {
                violations.value = res.data.data.data ?? [];
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch violations';
        }
    }

    async function fetchAchievements() {
        try {
            const res = await SchoolService.getAchievements();
            if (res.data?.success) {
                achievements.value = res.data.data.data ?? [];
            }
        } catch (e: unknown) {
            error.value = e instanceof Error ? e.message : 'Failed to fetch achievements';
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
