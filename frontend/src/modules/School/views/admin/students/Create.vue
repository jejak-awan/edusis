<template>
  <div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.students' }" class="hover:text-foreground">Kesiswaan</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Tambah Siswa Baru</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Pendaftaran Siswa Baru</h1>
        <p class="text-sm text-muted-foreground">Lengkapi data pokok siswa secara komprehensif untuk buku induk sekolah.</p>
      </div>

      <router-link
        :to="{ name: 'school.students' }"
        class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
      >
        <ArrowLeft class="size-4" />
        Kembali ke Daftar
      </router-link>
    </div>

    <!-- Alert / Banner Info -->
    <div v-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-xs text-destructive">
      {{ errorMessage }}
    </div>

    <!-- Student Form -->
    <StudentForm
      :departments="departments"
      :study-groups="studyGroups"
      :loading="isSubmitting"
      @submit="handleCreateStudent"
      @cancel="$router.push({ name: 'school.students' })"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowLeft } from 'lucide-vue-next';
import StudentForm from './components/StudentForm.vue';
import { StudentService } from '../../../services/studentService';
import { AcademicService } from '../../../services/academicService';
import type { Department, StudyGroup, Student } from '../../../services/types';

const router = useRouter();

const departments = ref<Department[]>([]);
const studyGroups = ref<StudyGroup[]>([]);
const isSubmitting = ref(false);
const errorMessage = ref<string | null>(null);

async function loadMetadata() {
  try {
    const [deptRes, groupRes] = await Promise.all([
      AcademicService.getDepartments(),
      AcademicService.getStudyGroups(),
    ]);

    if (deptRes.data?.success) {
      departments.value = deptRes.data.data;
    }
    if (groupRes.data?.success) {
      studyGroups.value = groupRes.data.data;
    }
  } catch (err: any) {
    console.error('Failed to load academic metadata', err);
  }
}

async function handleCreateStudent(payload: Partial<Student>) {
  isSubmitting.value = true;
  errorMessage.value = null;

  try {
    const res = await StudentService.createStudent(payload);
    if (res.data?.success) {
      router.push({ name: 'school.students' });
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan data siswa baru. Periksa kembali kelengkapan formulir.';
  } finally {
    isSubmitting.value = false;
  }
}

onMounted(() => {
  loadMetadata();
});
</script>
