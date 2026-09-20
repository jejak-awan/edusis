import { defineStore } from 'pinia';
import { ref } from 'vue';
import { AcademicService } from '../services/academicService';
import type { AcademicYear, Department, StudyGroup, Subject } from '../services/types';

export const useAcademicStore = defineStore('schoolAcademic', () => {
    const academicYears = ref<AcademicYear[]>([]);
    const activeAcademicYear = ref<AcademicYear | null>(null);
    const departments = ref<Department[]>([]);
    const studyGroups = ref<StudyGroup[]>([]);
    const subjects = ref<Subject[]>([]);
    const isLoading = ref(false);

    async function fetchAcademicYears() {
        try {
            const res = await AcademicService.getAcademicYears();
            if (res.data?.success) {
                academicYears.value = res.data.data;
                activeAcademicYear.value = academicYears.value.find((y) => y.is_active) || null;
            }
        } catch (err) {
            console.error('Failed to load academic years', err);
        }
    }

    async function fetchDepartments() {
        try {
            const res = await AcademicService.getDepartments();
            if (res.data?.success) {
                departments.value = res.data.data;
            }
        } catch (err) {
            console.error('Failed to load departments', err);
        }
    }

    async function fetchStudyGroups(params: Record<string, any> = {}) {
        try {
            const res = await AcademicService.getStudyGroups(params);
            if (res.data?.success) {
                studyGroups.value = res.data.data;
            }
        } catch (err) {
            console.error('Failed to load study groups', err);
        }
    }

    async function fetchSubjects(params: Record<string, any> = {}) {
        try {
            const res = await AcademicService.getSubjects(params);
            if (res.data?.success) {
                subjects.value = res.data.data;
            }
        } catch (err) {
            console.error('Failed to load subjects', err);
        }
    }

    async function initializeAcademicContext() {
        isLoading.value = true;
        try {
            await Promise.all([
                fetchAcademicYears(),
                fetchDepartments(),
                fetchStudyGroups(),
                fetchSubjects(),
            ]);
        } finally {
            isLoading.value = false;
        }
    }

    return {
        academicYears,
        activeAcademicYear,
        departments,
        studyGroups,
        subjects,
        isLoading,
        fetchAcademicYears,
        fetchDepartments,
        fetchStudyGroups,
        fetchSubjects,
        initializeAcademicContext,
    };
});
