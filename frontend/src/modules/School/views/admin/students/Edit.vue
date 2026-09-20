<template>
  <div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link to="/school/students" class="hover:text-foreground">Kesiswaan</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Ubah Data Siswa</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Perbarui Data Siswa</h1>
        <p class="text-sm text-muted-foreground">
          Perubahan pada biodata dan rombel akan tersimpan di buku induk siswa.
        </p>
      </div>

      <router-link
        to="/school/students"
        class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
      >
        <ArrowLeft class="size-4" />
        Kembali ke Daftar
      </router-link>
    </div>

    <!-- Alert / Banner Error -->
    <div v-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-xs text-destructive">
      {{ errorMessage }}
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="rounded-xl border border-border/60 bg-card p-12 text-center text-sm text-muted-foreground">
      Memuat data siswa...
    </div>

    <!-- Student Form -->
    <StudentForm
      v-else-if="student"
      :initial-data="student"
      :departments="departments"
      :study-groups="studyGroups"
      :is-edit="true"
      :loading="isSubmitting"
      @submit="handleUpdateStudent"
      @cancel="$router.push('/school/students')"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowLeft } from 'lucide-vue-next';
import StudentForm from './components/StudentForm.vue';
import { StudentService } from '../../../services/studentService';
import { AcademicService } from '../../../services/academicService';
import type { Department, StudyGroup, Student } from '../../../services/types';

const route = useRoute();
const router = useRouter();

const student = ref<Student | null>(null);
const departments = ref<Department[]>([]);
const studyGroups = ref<StudyGroup[]>([]);
const isLoading = ref(true);
const isSubmitting = ref(false);
const errorMessage = ref<string | null>(null);

async function loadData() {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    const studentId = route.params.id as string;
    const [studentRes, deptRes, groupRes] = await Promise.all([
      StudentService.getStudent(studentId),
      AcademicService.getDepartments(),
      AcademicService.getStudyGroups(),
    ]);

    if (studentRes.data?.success) {
      student.value = studentRes.data.data;
    }
    if (deptRes.data?.success) {
      departments.value = deptRes.data.data;
    }
    if (groupRes.data?.success) {
      studyGroups.value = groupRes.data.data;
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat profil data siswa.';
  } finally {
    isLoading.value = false;
  }
}

async function handleUpdateStudent(payload: Partial<Student>) {
  if (!student.value) return;

  isSubmitting.value = true;
  errorMessage.value = null;

  try {
    const res = await StudentService.updateStudent(student.value.id, payload);
    if (res.data?.success) {
      router.push('/school/students');
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal memperbarui data siswa.';
  } finally {
    isSubmitting.value = false;
  }
}

onMounted(() => {
  loadData();
});
</script>
