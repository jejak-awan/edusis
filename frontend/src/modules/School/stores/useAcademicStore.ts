import { defineStore } from 'pinia';
import { ref } from 'vue';
import { AcademicService } from '../services/academicService';
import type { AcademicYear, Department, StudyGroup, Subject } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

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
            const data = ensureArray<AcademicYear>(res.data?.data);
            academicYears.value = data;
            activeAcademicYear.value = data.find((y) => y.is_active) || null;
        } catch (err) {
            logger.error('[School:Academic] Failed to load academic years', err);
        }
    }

    async function fetchDepartments() {
        try {
            const res = await AcademicService.getDepartments();
            departments.value = ensureArray<Department>(res.data?.data);
        } catch (err) {
            logger.error('[School:Academic] Failed to load departments', err);
        }
    }

    async function fetchStudyGroups(params: Record<string, unknown> = {}) {
        try {
            const res = await AcademicService.getStudyGroups(params);
            studyGroups.value = ensureArray<StudyGroup>(res.data?.data);
        } catch (err) {
            logger.error('[School:Academic] Failed to load study groups', err);
        }
    }

    async function fetchSubjects(params: Record<string, unknown> = {}) {
        try {
            const res = await AcademicService.getSubjects(params);
            subjects.value = ensureArray<Subject>(res.data?.data);
        } catch (err) {
            logger.error('[School:Academic] Failed to load subjects', err);
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

export default useAcademicStore;
