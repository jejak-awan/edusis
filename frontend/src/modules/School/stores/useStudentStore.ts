import { defineStore } from 'pinia';
import { ref } from 'vue';
import { StudentService } from '../services/studentService';
import type { Student } from '../types';
import { logger } from '@/shared/utils/logger';
import { ensureArray } from '@/shared/utils/responseParser';

export const useStudentStore = defineStore('schoolStudent', () => {
    const students = ref<Student[]>([]);
    const selectedStudent = ref<Student | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchStudents(params: Record<string, unknown> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await StudentService.getStudents(params);
            if (res.data?.success) {
                students.value = ensureArray<Student>(res.data.data?.data);
                totalCount.value = res.data.data?.total || 0;
                currentPage.value = res.data.data?.current_page || 1;
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Failed to fetch students';
            error.value = msg;
            logger.error('[School:Student] Failed to fetch students', err);
        } finally {
            isLoading.value = false;
        }
    }

    async function fetchStudentById(id: number | string) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await StudentService.getStudent(id);
            if (res.data?.success) {
                selectedStudent.value = res.data.data;
            }
        } catch (err: unknown) {
            const msg = (err as { response?: { data?: { message?: string } } })?.response?.data?.message || 'Failed to fetch student';
            error.value = msg;
            logger.error('[School:Student] Failed to fetch student by id', err);
        } finally {
            isLoading.value = false;
        }
    }

    function setSelectedStudent(student: Student | null) {
        selectedStudent.value = student;
    }

    return {
        students,
        selectedStudent,
        isLoading,
        error,
        totalCount,
        currentPage,
        fetchStudents,
        fetchStudentById,
        setSelectedStudent,
    };
});

export default useStudentStore;
