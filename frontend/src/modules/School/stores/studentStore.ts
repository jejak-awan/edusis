import { defineStore } from 'pinia';
import { ref } from 'vue';
import { StudentService } from '../services/studentService';
import type { Student } from '../services/types';

export const useStudentStore = defineStore('schoolStudent', () => {
    const students = ref<Student[]>([]);
    const selectedStudent = ref<Student | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);

    async function fetchStudents(params: Record<string, any> = {}) {
        isLoading.value = true;
        error.value = null;
        try {
            const res = await StudentService.getStudents(params);
            if (res.data?.success) {
                students.value = res.data.data.data;
                totalCount.value = res.data.data.total;
                currentPage.value = res.data.data.current_page;
            }
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Failed to fetch students';
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
        } catch (err: any) {
            error.value = err.response?.data?.message || err.message || 'Failed to fetch student';
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
