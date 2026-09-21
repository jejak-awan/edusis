<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.student.dashboard' }" class="hover:text-foreground">Portal Siswa</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Nilai Belajar</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">E-Rapor & Capaian Belajar</h1>
        <p class="text-sm text-muted-foreground">
          Pantau hasil evaluasi akademik, nilai per mata pelajaran, dan predikat capaian kompetensi.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
          @click="handlePrint"
        >
          <Printer class="size-4" />
          Cetak Transkrip
        </button>
      </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Nilai Rata-Rata</span>
          <div class="text-2xl font-bold font-mono text-primary mt-1">{{ averageGrade }}</div>
        </div>
        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <Award class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Mata Pelajaran Tuntas</span>
          <div class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">
            {{ gradesList.filter(g => (g.final_grade || 0) >= 75).length }} / {{ gradesList.length }}
          </div>
        </div>
        <div class="size-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <GraduationCap class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Predikat Umum</span>
          <div class="text-2xl font-bold text-foreground mt-1">Sangat Baik (A)</div>
        </div>
        <div class="size-10 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center">
          <BookOpen class="size-5" />
        </div>
      </div>
    </div>

    <!-- Grades Table -->
    <div class="rounded-2xl border border-border/60 bg-card shadow-sm overflow-hidden">
      <div v-if="loading" class="p-12 text-center text-sm text-muted-foreground">
        <div class="inline-block size-6 animate-spin rounded-full border-2 border-primary border-t-transparent mb-2"></div>
        <div>Memuat nilai raport...</div>
      </div>

      <div v-else-if="gradesList.length === 0" class="p-12 text-center text-sm text-muted-foreground">
        <BookOpen class="size-10 mx-auto mb-2 opacity-30 text-primary" />
        <p class="font-medium text-foreground">Belum ada nilai raport yang dipublikasikan.</p>
        <p class="text-xs text-muted-foreground mt-1">Nilai Anda akan muncul di sini setelah divalidasi oleh dewan guru.</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-muted/40 border-b border-border/60 text-[11px] font-semibold text-muted-foreground">
            <tr>
              <th class="py-3 px-4 w-12 text-center">No</th>
              <th class="py-3 px-4 min-w-[200px]">Mata Pelajaran</th>
              <th class="py-3 px-4 w-28 text-center">Formatif</th>
              <th class="py-3 px-4 w-28 text-center">STS / UTS</th>
              <th class="py-3 px-4 w-28 text-center">SAS / UAS</th>
              <th class="py-3 px-4 w-24 text-center">Nilai Akhir</th>
              <th class="py-3 px-4 w-24 text-center">Predikat</th>
              <th class="py-3 px-4 min-w-[240px]">Capaian Kompetensi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr
              v-for="(grade, idx) in gradesList"
              :key="grade.id"
              class="hover:bg-muted/20 transition"
            >
              <td class="py-3.5 px-4 text-center font-mono text-muted-foreground">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-semibold text-foreground">{{ grade.subject_name }}</td>
              <td class="py-3.5 px-4 text-center font-mono text-foreground">{{ grade.formative_score }}</td>
              <td class="py-3.5 px-4 text-center font-mono text-foreground">{{ grade.mid_score }}</td>
              <td class="py-3.5 px-4 text-center font-mono text-foreground">{{ grade.final_score }}</td>
              <td
                class="py-3.5 px-4 text-center font-mono font-bold"
                :class="grade.final_grade >= 80 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600'"
              >
                {{ grade.final_grade }}
              </td>
              <td class="py-3.5 px-4 text-center">
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase',
                    grade.predicate === 'A'
                      ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                      : 'bg-sky-500/10 text-sky-600 border border-sky-500/20'
                  ]"
                >
                  {{ grade.predicate }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-muted-foreground truncate max-w-xs">
                {{ grade.notes || 'Tercapai dengan amat baik dalam seluruh tujuan pembelajaran.' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Award, GraduationCap, BookOpen, Printer } from 'lucide-vue-next';

interface StudentGradeItem {
  id: number;
  subject_name: string;
  formative_score: number;
  mid_score: number;
  final_score: number;
  final_grade: number;
  predicate: string;
  notes?: string;
}

const loading = ref(false);
const gradesList = ref<StudentGradeItem[]>([
  {
    id: 1,
    subject_name: 'Pendidikan Pancasila & Kewarganegaraan',
    formative_score: 88,
    mid_score: 86,
    final_score: 90,
    final_grade: 88,
    predicate: 'A',
    notes: 'Sangat menguasai nilai-nilai luhur Pancasila dalam kehidupan berbangsa.',
  },
  {
    id: 2,
    subject_name: 'Bahasa Indonesia',
    formative_score: 85,
    mid_score: 82,
    final_score: 86,
    final_grade: 84,
    predicate: 'B',
    notes: 'Mampu menyusun teks laporan observasi dan esai argumentatif dengan runtut.',
  },
  {
    id: 3,
    subject_name: 'Matematika Umum',
    formative_score: 92,
    mid_score: 88,
    final_score: 94,
    final_grade: 91,
    predicate: 'A',
    notes: 'Sangat terampil menyelesaikan permodelan fungsi dan kalkulus terapan.',
  },
  {
    id: 4,
    subject_name: 'Bahasa Inggris',
    formative_score: 87,
    mid_score: 85,
    final_score: 89,
    final_grade: 87,
    predicate: 'B',
    notes: 'Memiliki pemahaman mendalam dalam komunikasi profesional lisan dan tertulis.',
  },
  {
    id: 5,
    subject_name: 'Informatika & Rekayasa Perangkat Lunak',
    formative_score: 95,
    mid_score: 94,
    final_score: 96,
    final_grade: 95,
    predicate: 'A',
    notes: 'Unggul dalam perancangan algoritma, basis data, dan pengembangan web modern.',
  },
]);

const averageGrade = computed(() => {
  if (gradesList.value.length === 0) return 0;
  const sum = gradesList.value.reduce((acc, curr) => acc + curr.final_grade, 0);
  return (sum / gradesList.value.length).toFixed(1);
});

function handlePrint() {
  window.print();
}

onMounted(() => {
  // Can optionally fetch from backend if active student session exists
});
</script>
