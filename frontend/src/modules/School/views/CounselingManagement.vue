<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.counseling.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.counseling.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openAddModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <UserPlus class="size-4" />
          {{ t('school.counseling.addCase') }}
        </button>
      </div>
    </div>

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Total Kasus Konseling</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ cases.length }} Kasus</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <HeartHandshake class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Dalam Pendampingan</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">
            {{ cases.filter(c => c.status === 'in_progress' || c.status === 'open').length }} Siswa
          </div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Clock class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Selesai / Terbina</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">
            {{ cases.filter(c => c.status === 'resolved').length }} Kasus
          </div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Bimbingan Karir & PKL</div>
          <div class="text-2xl font-bold text-indigo-600 mt-1">
            {{ cases.filter(c => c.category === 'karir').length }} Sesi
          </div>
        </div>
        <div class="size-10 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <Compass class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
      <div class="relative w-full sm:max-w-xs">
        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari siswa atau nomor kasus..."
          class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          @input="onSearch"
        />
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <select
          v-model="filterCategory"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadCases"
        >
          <option value="">Semua Kategori</option>
          <option value="belajar">{{ t('school.counseling.learning') }}</option>
          <option value="pribadi">{{ t('school.counseling.personal') }}</option>
          <option value="sosial">{{ t('school.counseling.social') }}</option>
          <option value="karir">{{ t('school.counseling.career') }}</option>
        </select>

        <select
          v-model="filterStatus"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadCases"
        >
          <option value="">Semua Status</option>
          <option value="open">{{ t('school.counseling.open') }}</option>
          <option value="in_progress">{{ t('school.counseling.inProgress') }}</option>
          <option value="resolved">{{ t('school.counseling.resolved') }}</option>
          <option value="referred">{{ t('school.counseling.referred') }}</option>
        </select>

        <select
          v-model="filterConfidentiality"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadCases"
        >
          <option value="">Semua Tingkat Kerahasiaan</option>
          <option value="biasa">{{ t('school.counseling.public') }}</option>
          <option value="internal">{{ t('school.counseling.internal') }}</option>
          <option value="rahasia">{{ t('school.counseling.secret') }}</option>
          <option value="sangat_rahasia">{{ t('school.counseling.strictlySecret') }}</option>
        </select>
      </div>
    </div>

    <!-- Counseling Cases Feed -->
    <div class="space-y-4">
      <div v-if="loading" class="rounded-xl border border-border/60 bg-card p-8 text-center text-sm text-muted-foreground">
        <div class="flex items-center justify-center gap-2">
          <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
          Memuat data layanan BK...
        </div>
      </div>

      <div v-else-if="cases.length === 0" class="rounded-xl border border-border/60 bg-card p-8 text-center text-sm text-muted-foreground">
        <ShieldQuestion class="size-8 mx-auto text-muted-foreground/50 mb-2" />
        {{ t('school.counseling.empty') }}
      </div>

      <div
        v-for="c in cases"
        :key="c.id"
        class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3 hover:border-primary/40 transition-colors"
      >
        <!-- Card Top Bar -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/40 pb-3">
          <div class="flex items-center gap-3">
            <span class="font-mono text-xs font-bold text-primary bg-primary/10 px-2 py-0.5 rounded">
              {{ c.case_number }}
            </span>
            <span :class="['rounded-full px-2 py-0.5 text-[11px] font-semibold uppercase', getCategoryBadgeClass(c.category)]">
              {{ formatCategory(c.category) }}
            </span>
            <span :class="['rounded-full px-2 py-0.5 text-[10px] font-medium border', getConfidentialityBadgeClass(c.confidentiality)]">
              {{ formatConfidentiality(c.confidentiality) }}
            </span>
          </div>

          <div class="flex items-center gap-2">
            <span
              :class="[
                'rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase',
                c.status === 'resolved' ? 'bg-emerald-500/10 text-emerald-600' :
                c.status === 'in_progress' ? 'bg-amber-500/10 text-amber-600' : 'bg-blue-500/10 text-blue-600'
              ]"
            >
              {{ formatStatus(c.status) }}
            </span>

            <button
              v-if="c.status !== 'resolved'"
              @click="resolveCase(c.id)"
              class="inline-flex items-center gap-1 rounded bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 px-2.5 py-1 text-xs font-semibold hover:bg-emerald-500/20 transition"
              title="Tandai Selesai"
            >
              <CheckCircle2 class="size-3.5" />
              Selesai
            </button>

            <button
              @click="deleteCase(c.id)"
              class="rounded p-1 text-muted-foreground hover:bg-red-500/10 hover:text-red-600 transition"
              title="Hapus Kasus"
            >
              <Trash2 class="size-4" />
            </button>
          </div>
        </div>

        <!-- Student & Counselor Info -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-muted-foreground gap-2">
          <div>
            Klien Siswa: <strong class="text-foreground">{{ c.student?.full_name || 'Siswa' }}</strong>
            <span v-if="c.student?.study_groups?.[0]"> ({{ c.student.study_groups[0].name }})</span>
            • NISN: <span class="font-mono">{{ c.student?.nisn || '-' }}</span>
          </div>
          <div>
            Konselor: <strong class="text-foreground">{{ c.counselor?.full_name || 'Guru BK' }}</strong> • Tanggal: {{ c.date }}
          </div>
        </div>

        <!-- Problem Description -->
        <div class="space-y-1 rounded-lg bg-muted/30 p-3 text-xs">
          <div class="font-semibold text-foreground">Deskripsi Kasus & Permasalahan:</div>
          <p class="text-muted-foreground leading-relaxed">{{ c.problem_description }}</p>
        </div>

        <!-- Solution & Follow-up Notes -->
        <div v-if="c.solution_plan || c.follow_up_notes" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div v-if="c.solution_plan" class="rounded-lg border border-border/60 p-2.5 space-y-0.5">
            <span class="font-semibold text-foreground">Rencana Kesepakatan / Solusi:</span>
            <p class="text-muted-foreground">{{ c.solution_plan }}</p>
          </div>

          <div v-if="c.follow_up_notes" class="rounded-lg border border-border/60 p-2.5 space-y-0.5">
            <span class="font-semibold text-foreground">Catatan Tindak Lanjut:</span>
            <p class="text-muted-foreground">{{ c.follow_up_notes }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form Catat Kasus Konseling -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-2xl rounded-2xl border border-border bg-card p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-base font-bold text-foreground">{{ t('school.counseling.addCase') }}</h3>
          <button @click="showAddModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <form @submit.prevent="submitCaseForm" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.student') }} *</label>
              <select
                v-model="caseForm.student_id"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="">-- Pilih Siswa --</option>
                <option v-for="st in students" :key="st.id" :value="st.id">
                  {{ st.full_name }} ({{ st.nisn || '-' }})
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.counselor') }} *</label>
              <select
                v-model="caseForm.counselor_id"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="">-- Pilih Guru BK / Konselor --</option>
                <option v-for="stf in counselors" :key="stf.id" :value="stf.id">
                  {{ stf.full_name }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">Tanggal Konseling *</label>
              <input
                v-model="caseForm.date"
                type="date"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.category') }} *</label>
              <select
                v-model="caseForm.category"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="belajar">{{ t('school.counseling.learning') }}</option>
                <option value="pribadi">{{ t('school.counseling.personal') }}</option>
                <option value="sosial">{{ t('school.counseling.social') }}</option>
                <option value="karir">{{ t('school.counseling.career') }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.confidentiality') }} *</label>
              <select
                v-model="caseForm.confidentiality"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="biasa">{{ t('school.counseling.public') }}</option>
                <option value="internal">{{ t('school.counseling.internal') }}</option>
                <option value="rahasia">{{ t('school.counseling.secret') }}</option>
                <option value="sangat_rahasia">{{ t('school.counseling.strictlySecret') }}</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.problem') }} *</label>
            <textarea
              v-model="caseForm.problem_description"
              rows="3"
              required
              placeholder="Uraikan latar belakang masalah, keluhan siswa, atau rujukan kejadian..."
              class="w-full rounded-lg border border-border bg-background p-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.counselingSteps') }}</label>
            <textarea
              v-model="caseForm.counseling_steps"
              rows="2"
              placeholder="Teknik bimbingan, wawancara, observasi atau mediasi yang dilakukan..."
              class="w-full rounded-lg border border-border bg-background p-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">{{ t('school.counseling.solutionPlan') }}</label>
            <textarea
              v-model="caseForm.solution_plan"
              rows="2"
              placeholder="Kesepakatan komitmen penyelesaian siswa atau program pendampingan..."
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
              Simpan Kasus BK
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  HeartHandshake,
  Clock,
  CheckCircle2,
  Compass,
  UserPlus,
  Search,
  Trash2,
  X,
  ShieldQuestion,
} from 'lucide-vue-next';
import {
  SchoolService,
  type CounselingCaseData,
  type StudentData,
  type StaffData,
} from '../services/schoolService';

const { t } = useI18n();

const cases = ref<CounselingCaseData[]>([]);
const students = ref<StudentData[]>([]);
const counselors = ref<StaffData[]>([]);
const loading = ref(false);

const searchQuery = ref('');
const filterCategory = ref('');
const filterStatus = ref('');
const filterConfidentiality = ref('');

const showAddModal = ref(false);
const caseForm = ref({
  student_id: '',
  counselor_id: '',
  date: new Date().toISOString().split('T')[0] || '',
  category: 'belajar',
  confidentiality: 'internal',
  problem_description: '',
  counseling_steps: '',
  solution_plan: '',
});

async function loadInitialData() {
  try {
    const [stdRes, staffRes] = await Promise.all([
      SchoolService.getStudents(),
      SchoolService.getStaff(),
    ]);

    const rawStd = stdRes.data?.data ?? stdRes.data;
    students.value = Array.isArray(rawStd) ? rawStd : (rawStd?.data ?? []);

    const rawStaff = staffRes.data?.data ?? staffRes.data;
    counselors.value = Array.isArray(rawStaff) ? rawStaff : (rawStaff?.data ?? []);

    await loadCases();
  } catch (err) {
    console.error('Failed to load initial data for counseling', err);
  }
}

async function loadCases() {
  loading.value = true;
  try {
    const params: Record<string, unknown> = {};
    if (filterCategory.value) params.category = filterCategory.value;
    if (filterStatus.value) params.status = filterStatus.value;
    if (filterConfidentiality.value) params.confidentiality = filterConfidentiality.value;

    const res = await SchoolService.getCounselingCases(params);
    const raw = res.data?.data ?? res.data;
    cases.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
  } catch (err) {
    console.error('Failed to load counseling cases', err);
  } finally {
    loading.value = false;
  }
}

let searchTimer: any = null;
function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    loadCases();
  }, 300);
}

function openAddModal() {
  caseForm.value = {
    student_id: students.value[0]?.id ? String(students.value[0].id) : '',
    counselor_id: counselors.value[0]?.id ? String(counselors.value[0].id) : '',
    date: new Date().toISOString().split('T')[0] || '',
    category: 'belajar',
    confidentiality: 'internal',
    problem_description: '',
    counseling_steps: '',
    solution_plan: '',
  };
  showAddModal.value = true;
}

async function submitCaseForm() {
  try {
    await SchoolService.storeCounselingCase({
      student_id: Number(caseForm.value.student_id),
      counselor_id: Number(caseForm.value.counselor_id),
      date: caseForm.value.date,
      category: caseForm.value.category,
      confidentiality: caseForm.value.confidentiality,
      problem_description: caseForm.value.problem_description,
      counseling_steps: caseForm.value.counseling_steps || undefined,
      solution_plan: caseForm.value.solution_plan || undefined,
    });
    showAddModal.value = false;
    await loadCases();
  } catch (err) {
    console.error('Failed to store counseling case', err);
  }
}

async function resolveCase(id: number) {
  try {
    await SchoolService.resolveCounselingCase(id, {
      follow_up_notes: 'Masalah telah ditangani dan siswa menunjukkan komitmen yang baik.',
    });
    await loadCases();
  } catch (err) {
    console.error('Failed to resolve counseling case', err);
  }
}

async function deleteCase(id: number) {
  if (!confirm('Apakah Anda yakin ingin menghapus catatan kasus BK ini?')) return;
  try {
    await SchoolService.deleteCounselingCase(id);
    await loadCases();
  } catch (err) {
    console.error('Failed to delete counseling case', err);
  }
}

function formatCategory(cat: string): string {
  switch (cat) {
    case 'belajar': return 'Belajar / Akademik';
    case 'pribadi': return 'Pribadi';
    case 'sosial': return 'Sosial';
    case 'karir': return 'Karir & PKL';
    default: return cat;
  }
}

function getCategoryBadgeClass(cat: string): string {
  switch (cat) {
    case 'belajar': return 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20';
    case 'pribadi': return 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20';
    case 'sosial': return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20';
    case 'karir': return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20';
    default: return 'bg-muted text-muted-foreground border border-border';
  }
}

function formatConfidentiality(c: string): string {
  switch (c) {
    case 'biasa': return 'Biasa';
    case 'internal': return 'Internal';
    case 'rahasia': return 'Rahasia';
    case 'sangat_rahasia': return 'Sangat Rahasia';
    default: return c;
  }
}

function getConfidentialityBadgeClass(c: string): string {
  switch (c) {
    case 'rahasia':
    case 'sangat_rahasia':
      return 'border-red-500/30 text-red-600 bg-red-500/5';
    case 'internal':
      return 'border-amber-500/30 text-amber-600 bg-amber-500/5';
    default:
      return 'border-border text-muted-foreground';
  }
}

function formatStatus(status: string): string {
  switch (status) {
    case 'open': return 'Terbuka';
    case 'in_progress': return 'Proses';
    case 'resolved': return 'Selesai';
    case 'referred': return 'Rujukan';
    default: return status;
  }
}

onMounted(() => {
  loadInitialData();
});
</script>
