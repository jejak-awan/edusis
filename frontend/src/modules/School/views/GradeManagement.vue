<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.grades.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.grades.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openAddAssessmentModal"
          :disabled="!selectedGroupId || !selectedSubjectId"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 disabled:opacity-50"
        >
          <Plus class="size-4" />
          {{ t('school.grades.addAssessment') }}
        </button>
      </div>
    </div>

    <!-- Selectors Toolbar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
      <div class="flex flex-wrap items-center gap-3">
        <div>
          <label class="block text-[11px] font-semibold text-muted-foreground uppercase mb-1">
            {{ t('school.grades.selectGroup') }}:
          </label>
          <select
            v-model="selectedGroupId"
            class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            @change="loadAssessments"
          >
            <option value="">-- Pilih Rombel --</option>
            <option v-for="g in studyGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>
        </div>

        <div>
          <label class="block text-[11px] font-semibold text-muted-foreground uppercase mb-1">
            {{ t('school.grades.selectSubject') }}:
          </label>
          <select
            v-model="selectedSubjectId"
            class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            @change="loadAssessments"
          >
            <option value="">-- Pilih Mata Pelajaran --</option>
            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
          </select>
        </div>
      </div>

      <!-- Type Filter Tabs -->
      <div class="flex items-center gap-1 rounded-lg bg-muted p-1 text-xs">
        <button
          type="button"
          @click="selectedTypeFilter = ''; loadAssessments()"
          :class="['px-2.5 py-1 rounded-md font-medium transition', selectedTypeFilter === '' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']"
        >
          Semua
        </button>
        <button
          type="button"
          @click="selectedTypeFilter = 'formative'; loadAssessments()"
          :class="['px-2.5 py-1 rounded-md font-medium transition', selectedTypeFilter === 'formative' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']"
        >
          Formatif
        </button>
        <button
          type="button"
          @click="selectedTypeFilter = 'summative_material'; loadAssessments()"
          :class="['px-2.5 py-1 rounded-md font-medium transition', selectedTypeFilter === 'summative_material' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']"
        >
          Sumatif Bab
        </button>
        <button
          type="button"
          @click="selectedTypeFilter = 'summative_final'; loadAssessments()"
          :class="['px-2.5 py-1 rounded-md font-medium transition', selectedTypeFilter === 'summative_final' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground']"
        >
          SAS
        </button>
      </div>
    </div>

    <!-- Assessment List -->
    <div class="space-y-4">
      <div v-if="loading" class="rounded-xl border border-border/60 bg-card p-8 text-center text-sm text-muted-foreground">
        <div class="flex items-center justify-center gap-2">
          <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
          Memuat daftar asesmen...
        </div>
      </div>

      <div v-else-if="assessments.length === 0" class="rounded-xl border border-border/60 bg-card p-8 text-center text-sm text-muted-foreground">
        <FileSpreadsheet class="size-8 mx-auto text-muted-foreground/50 mb-2" />
        {{ t('school.grades.empty') }}
      </div>

      <div
        v-for="asm in assessments"
        :key="asm.id"
        class="rounded-xl border border-border/60 bg-card p-4 shadow-sm hover:border-primary/40 transition-colors flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
      >
        <div class="space-y-1">
          <div class="flex items-center gap-2">
            <span :class="['rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase', getTypeBadgeClass(asm.type)]">
              {{ formatType(asm.type) }}
            </span>
            <span class="text-xs text-muted-foreground">Bobot: {{ asm.weight }}x • Skor Maks: {{ asm.max_score }}</span>
            <span class="text-xs text-muted-foreground">• Tanggal: {{ asm.date }}</span>
          </div>

          <h3 class="text-base font-bold text-foreground">{{ asm.title }}</h3>
          <p v-if="asm.description" class="text-xs text-muted-foreground">{{ asm.description }}</p>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="openInputGradesModal(asm)"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary/10 border border-primary/20 px-3.5 py-1.5 text-xs font-semibold text-primary hover:bg-primary/20 transition"
          >
            <PenSquare class="size-3.5" />
            {{ t('school.grades.inputGrades') }}
          </button>

          <button
            @click="deleteAssessment(asm.id)"
            class="rounded-lg p-1.5 text-muted-foreground hover:bg-red-500/10 hover:text-red-600 transition"
            title="Hapus Asesmen"
          >
            <Trash2 class="size-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Form Tambah Asesmen -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-lg rounded-2xl border border-border bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-base font-bold text-foreground">{{ t('school.grades.addAssessment') }}</h3>
          <button @click="showAddModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <form @submit.prevent="submitAssessmentForm" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">Judul Asesmen *</label>
            <input
              v-model="assessmentForm.title"
              type="text"
              required
              placeholder="Contoh: Formatif 1: Logika Percabangan & Fungsi"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.grades.assessmentType') }} *</label>
              <select
                v-model="assessmentForm.type"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="formative">{{ t('school.grades.formative') }}</option>
                <option value="summative_material">{{ t('school.grades.summativeMaterial') }}</option>
                <option value="summative_final">{{ t('school.grades.summativeFinal') }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">Tanggal *</label>
              <input
                v-model="assessmentForm.date"
                type="date"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.grades.weight') }} (1 - 10) *</label>
              <input
                v-model.number="assessmentForm.weight"
                type="number"
                min="1"
                max="10"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.grades.maxScore') }} *</label>
              <input
                v-model.number="assessmentForm.max_score"
                type="number"
                min="10"
                max="100"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">Keterangan / Tujuan Asesmen</label>
            <textarea
              v-model="assessmentForm.description"
              rows="2"
              placeholder="Uraian kisi-kisi atau cakupan kompetensi..."
              class="w-full rounded-lg border border-border bg-background p-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-4">
            <button
              type="button"
              @click="showAddModal = false"
              class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
            >
              Batal
            </button>
            <button
              type="submit"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90"
            >
              Simpan Asesmen
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Input / Edit Nilai Siswa (Spreadsheet Grid) -->
    <div
      v-if="showGradesModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-4xl rounded-2xl border border-border bg-card p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <div>
            <h3 class="text-base font-bold text-foreground">
              Input Nilai: {{ activeAssessment?.title }}
            </h3>
            <p class="text-xs text-muted-foreground">
              {{ activeAssessment?.study_group?.name || 'Rombel' }} • {{ activeAssessment?.subject?.name || 'Mapel' }} • Bobot: {{ activeAssessment?.weight }}x
            </p>
          </div>
          <button @click="showGradesModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <!-- Realtime KPI Metrics -->
        <div class="grid grid-cols-3 gap-3">
          <div class="rounded-xl border border-border/60 bg-muted/20 p-3 text-center">
            <div class="text-[11px] text-muted-foreground uppercase font-medium">Rata-rata Nilai</div>
            <div class="text-lg font-bold text-primary mt-0.5">{{ gradesAverage }}</div>
          </div>
          <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-3 text-center">
            <div class="text-[11px] text-emerald-600 font-medium">Nilai Tertinggi</div>
            <div class="text-lg font-bold text-emerald-600 mt-0.5">{{ gradesHighest }}</div>
          </div>
          <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-3 text-center">
            <div class="text-[11px] text-amber-600 font-medium">Nilai Terendah</div>
            <div class="text-lg font-bold text-amber-600 mt-0.5">{{ gradesLowest }}</div>
          </div>
        </div>

        <!-- Success Banner -->
        <div v-if="saveSuccess" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-700 dark:text-emerald-300 flex items-center justify-between">
          <div class="flex items-center gap-2">
            <CheckCircle2 class="size-4 text-emerald-600" />
            {{ t('school.grades.savedSuccess') }}
          </div>
          <button @click="saveSuccess = false" class="text-emerald-700 hover:text-emerald-900">
            <X class="size-3.5" />
          </button>
        </div>

        <!-- Grades Table -->
        <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
          <table class="w-full text-left text-sm">
            <thead class="border-b border-border/60 bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              <tr>
                <th class="w-12 px-4 py-3 text-center">No</th>
                <th class="px-4 py-3">Nama Siswa</th>
                <th class="px-4 py-3">NISN</th>
                <th class="w-28 px-4 py-3 text-center">{{ t('school.grades.score') }} (0-100)</th>
                <th class="w-28 px-4 py-3 text-center">{{ t('school.grades.remedialScore') }}</th>
                <th class="px-4 py-3">Catatan Pembinaan Guru</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
              <tr v-for="(g, index) in studentGrades" :key="g.student_id" class="hover:bg-muted/30">
                <td class="px-4 py-2.5 text-center font-mono text-xs text-muted-foreground">{{ index + 1 }}</td>
                <td class="px-4 py-2.5 font-semibold text-foreground">{{ g.full_name }}</td>
                <td class="px-4 py-2.5 font-mono text-xs text-muted-foreground">{{ g.nisn || '-' }}</td>
                <td class="px-4 py-2.5 text-center">
                  <input
                    v-model.number="g.score"
                    type="number"
                    min="0"
                    max="100"
                    step="0.5"
                    class="h-8 w-20 rounded-lg border border-border bg-background px-2 text-center text-xs font-bold text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </td>
                <td class="px-4 py-2.5 text-center">
                  <input
                    v-model.number="g.remedial_score"
                    type="number"
                    min="0"
                    max="100"
                    step="0.5"
                    placeholder="-"
                    class="h-8 w-20 rounded-lg border border-border bg-background px-2 text-center text-xs font-bold text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </td>
                <td class="px-4 py-2.5">
                  <input
                    v-model="g.notes"
                    type="text"
                    placeholder="Catatan kompetensi..."
                    class="h-8 w-full rounded-lg border border-border bg-background px-2 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-4">
          <button
            type="button"
            @click="showGradesModal = false"
            class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
          >
            Tutup
          </button>
          <button
            type="button"
            @click="saveGrades"
            :disabled="savingGrades"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 disabled:opacity-50"
          >
            <Save class="size-4" />
            {{ savingGrades ? 'Menyimpan...' : t('school.grades.saveGrades') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Plus,
  Trash2,
  X,
  Save,
  PenSquare,
  FileSpreadsheet,
  CheckCircle2,
} from 'lucide-vue-next';
import {
  SchoolService,
  type StudyGroupData,
  type SubjectData,
  type AssessmentData,
  type GradeData,
} from '../services/schoolService';

const { t } = useI18n();

const studyGroups = ref<StudyGroupData[]>([]);
const subjects = ref<SubjectData[]>([]);
const assessments = ref<AssessmentData[]>([]);

const selectedGroupId = ref<number | ''>('');
const selectedSubjectId = ref<number | ''>('');
const selectedTypeFilter = ref('');
const loading = ref(false);

const showAddModal = ref(false);
const assessmentForm = ref({
  title: '',
  type: 'formative',
  weight: 1,
  max_score: 100,
  date: new Date().toISOString().split('T')[0] || '',
  description: '',
});

// Grades modal
const showGradesModal = ref(false);
const activeAssessment = ref<AssessmentData | null>(null);
const studentGrades = ref<GradeData[]>([]);
const savingGrades = ref(false);
const saveSuccess = ref(false);

const gradesAverage = computed(() => {
  if (studentGrades.value.length === 0) return 0;
  const sum = studentGrades.value.reduce((acc, g) => acc + (g.remedial_score ?? g.score ?? 0), 0);
  return Math.round((sum / studentGrades.value.length) * 10) / 10;
});

const gradesHighest = computed(() => {
  if (studentGrades.value.length === 0) return 0;
  return Math.max(...studentGrades.value.map(g => g.remedial_score ?? g.score ?? 0));
});

const gradesLowest = computed(() => {
  if (studentGrades.value.length === 0) return 0;
  return Math.min(...studentGrades.value.map(g => g.remedial_score ?? g.score ?? 0));
});

async function loadInitialData() {
  try {
    const [groupsRes, subjRes] = await Promise.all([
      SchoolService.getStudyGroups(),
      SchoolService.getSubjects(),
    ]);

    if (groupsRes.data?.success) {
      studyGroups.value = groupsRes.data.data || [];
      if (studyGroups.value[0]) {
        selectedGroupId.value = studyGroups.value[0].id;
      }
    }

    if (subjRes.data?.success) {
      subjects.value = subjRes.data.data || [];
      if (subjects.value[0]) {
        selectedSubjectId.value = subjects.value[0].id;
      }
    }

    await loadAssessments();
  } catch (err) {
    console.error('Failed to load initial grade management data', err);
  }
}

async function loadAssessments() {
  if (!selectedGroupId.value || !selectedSubjectId.value) return;
  loading.value = true;
  try {
    const params: Record<string, unknown> = {
      study_group_id: selectedGroupId.value,
      subject_id: selectedSubjectId.value,
    };
    if (selectedTypeFilter.value) {
      params.type = selectedTypeFilter.value;
    }

    const res = await SchoolService.getAssessments(params);
    if (res.data?.success) {
      assessments.value = res.data.data.data || [];
    }
  } catch (err) {
    console.error('Failed to load assessments', err);
  } finally {
    loading.value = false;
  }
}

function openAddAssessmentModal() {
  assessmentForm.value = {
    title: '',
    type: 'formative',
    weight: 1,
    max_score: 100,
    date: new Date().toISOString().split('T')[0] || '',
    description: '',
  };
  showAddModal.value = true;
}

async function submitAssessmentForm() {
  if (!selectedGroupId.value || !selectedSubjectId.value) return;
  try {
    await SchoolService.storeAssessment({
      study_group_id: selectedGroupId.value,
      subject_id: selectedSubjectId.value,
      teacher_id: 1, // Fallback teacher ID
      ...assessmentForm.value,
    });
    showAddModal.value = false;
    await loadAssessments();
  } catch (err) {
    console.error('Failed to store assessment', err);
  }
}

async function openInputGradesModal(asm: AssessmentData) {
  activeAssessment.value = asm;
  saveSuccess.value = false;
  try {
    const res = await SchoolService.getAssessmentGrades(asm.id);
    if (res.data?.success) {
      studentGrades.value = res.data.data || [];
      showGradesModal.value = true;
    }
  } catch (err) {
    console.error('Failed to load assessment grades', err);
  }
}

async function saveGrades() {
  if (!activeAssessment.value) return;
  savingGrades.value = true;
  saveSuccess.value = false;
  try {
    const payload = studentGrades.value.map(g => ({
      student_id: g.student_id,
      score: g.score,
      remedial_score: g.remedial_score,
      notes: g.notes,
    }));
    await SchoolService.storeBulkGrades(activeAssessment.value.id, payload);
    saveSuccess.value = true;
  } catch (err) {
    console.error('Failed to save grades', err);
  } finally {
    savingGrades.value = false;
  }
}

async function deleteAssessment(id: number) {
  if (!confirm('Apakah Anda yakin ingin menghapus asesmen ini?')) return;
  try {
    await SchoolService.deleteAssessment(id);
    await loadAssessments();
  } catch (err) {
    console.error('Failed to delete assessment', err);
  }
}

function formatType(type: string): string {
  switch (type) {
    case 'formative': return 'Formatif';
    case 'summative_material': return 'Sumatif Bab';
    case 'summative_final': return 'Sumatif Akhir';
    default: return type;
  }
}

function getTypeBadgeClass(type: string): string {
  switch (type) {
    case 'formative':
      return 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20';
    case 'summative_material':
      return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20';
    case 'summative_final':
      return 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20';
    default:
      return 'bg-muted text-muted-foreground border border-border';
  }
}

onMounted(() => {
  loadInitialData();
});
</script>
