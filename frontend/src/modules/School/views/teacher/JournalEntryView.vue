<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link to="/school/teacher/dashboard" class="hover:text-foreground">Portal Guru</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Jurnal Mengajar</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Pengisian Jurnal Mengajar</h1>
        <p class="text-sm text-muted-foreground">Catat materi pembelajaran, capaian kompetensi, dan rekapitulasi kehadiran siswa.</p>
      </div>

      <router-link
        to="/school/teacher/dashboard"
        class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
      >
        <ArrowLeft class="size-4" />
        Kembali ke Dashboard
      </router-link>
    </div>

    <!-- Alert Success / Error -->
    <div v-if="successMessage" class="rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-4 text-xs text-emerald-600 dark:text-emerald-400">
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-xs text-destructive">
      {{ errorMessage }}
    </div>

    <!-- Form Section -->
    <form @submit.prevent="submitJournal" class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-xs font-medium text-muted-foreground">Rombongan Belajar (Rombel) <span class="text-destructive">*</span></label>
          <select
            v-model="form.study_group_id"
            required
            class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          >
            <option :value="0">Pilih Rombel</option>
            <option v-for="g in studyGroups" :key="g.id" :value="g.id">
              Kelas {{ g.grade_level }} - {{ g.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="text-xs font-medium text-muted-foreground">Mata Pelajaran <span class="text-destructive">*</span></label>
          <select
            v-model="form.subject_id"
            required
            class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          >
            <option :value="0">Pilih Mata Pelajaran</option>
            <option v-for="s in subjects" :key="s.id" :value="s.id">
              {{ s.code }} - {{ s.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="text-xs font-medium text-muted-foreground">Tanggal Pembelajaran <span class="text-destructive">*</span></label>
          <input
            v-model="form.date"
            type="date"
            required
            class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          />
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="text-xs font-medium text-muted-foreground">Jam Ke- Mulai</label>
            <input
              v-model.number="form.period_start"
              type="number"
              min="1"
              max="12"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
          <div>
            <label class="text-xs font-medium text-muted-foreground">Jam Ke- Selesai</label>
            <input
              v-model.number="form.period_end"
              type="number"
              min="1"
              max="12"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="text-xs font-medium text-muted-foreground">Materi Pokok / Bab <span class="text-destructive">*</span></label>
          <input
            v-model="form.topic"
            type="text"
            required
            placeholder="Contoh: Pemrograman Berorientasi Objek - Konsep Inheritance"
            class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          />
        </div>

        <div class="md:col-span-2">
          <label class="text-xs font-medium text-muted-foreground">Uraian Kegiatan Pembelajaran</label>
          <textarea
            v-model="form.activity_description"
            rows="3"
            placeholder="Jelaskan tahapan pembelajaran, metode diskusi, atau praktikum yang dilaksanakan..."
            class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
        </div>
      </div>

      <!-- Presensi Rekapitulasi Ringkas -->
      <div class="border-t border-border/60 pt-5 space-y-3">
        <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Rekap Presensi Siswa Sesi Ini</h4>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div>
            <label class="text-xs text-muted-foreground">Hadir (Siswa)</label>
            <input
              v-model.number="form.attendance_present"
              type="number"
              min="0"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-1.5 text-sm text-foreground focus:outline-none"
            />
          </div>
          <div>
            <label class="text-xs text-muted-foreground">Sakit</label>
            <input
              v-model.number="form.attendance_sick"
              type="number"
              min="0"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-1.5 text-sm text-foreground focus:outline-none"
            />
          </div>
          <div>
            <label class="text-xs text-muted-foreground">Izin</label>
            <input
              v-model.number="form.attendance_permission"
              type="number"
              min="0"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-1.5 text-sm text-foreground focus:outline-none"
            />
          </div>
          <div>
            <label class="text-xs text-muted-foreground">Alpa</label>
            <input
              v-model.number="form.attendance_absent"
              type="number"
              min="0"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-1.5 text-sm text-foreground focus:outline-none"
            />
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="flex justify-end gap-3 pt-4 border-t border-border/60">
        <button
          type="button"
          @click="$router.push('/school/teacher/dashboard')"
          class="rounded-lg border border-border px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-muted"
        >
          Batal
        </button>
        <button
          type="submit"
          :disabled="isSubmitting"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-5 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90 transition disabled:opacity-50"
        >
          <Save class="size-4" />
          Simpan Jurnal
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowLeft, Save } from 'lucide-vue-next';
import { AcademicService } from '../../services/academicService';
import type { StudyGroup, Subject, TeachingJournal } from '../../services/types';

const route = useRoute();
const router = useRouter();

const studyGroups = ref<StudyGroup[]>([]);
const subjects = ref<Subject[]>([]);
const isSubmitting = ref(false);
const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const form = ref<Partial<TeachingJournal>>({
  study_group_id: 0,
  subject_id: 0,
  date: new Date().toISOString().split('T')[0],
  period_start: 1,
  period_end: 2,
  topic: '',
  subtopic: '',
  activity_description: '',
  notes: '',
  attendance_present: 32,
  attendance_sick: 0,
  attendance_permission: 0,
  attendance_absent: 0,
});

async function loadMetadata() {
  try {
    const [groupRes, subjectRes] = await Promise.all([
      AcademicService.getStudyGroups(),
      AcademicService.getSubjects(),
    ]);

    if (groupRes.data?.success) {
      studyGroups.value = groupRes.data.data;
    }
    if (subjectRes.data?.success) {
      subjects.value = subjectRes.data.data;
    }

    if (route.query.schedule_id) {
      form.value.schedule_id = Number(route.query.schedule_id);
    }
  } catch (err) {
    console.error('Failed to load journal metadata', err);
  }
}

async function submitJournal() {
  isSubmitting.value = true;
  successMessage.value = null;
  errorMessage.value = null;

  try {
    const res = await AcademicService.createTeachingJournal(form.value);
    if (res.data?.success) {
      successMessage.value = 'Jurnal mengajar berhasil disimpan ke buku kerja pendidik.';
      setTimeout(() => {
        router.push('/school/teacher/dashboard');
      }, 1500);
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal menyimpan jurnal mengajar.';
  } finally {
    isSubmitting.value = false;
  }
}

onMounted(() => {
  loadMetadata();
});
</script>
