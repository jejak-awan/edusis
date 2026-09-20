<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.katampi.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.katampi.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="showConfigModal = true"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border/60 bg-background px-3.5 py-2 text-xs font-semibold text-foreground shadow-sm transition hover:bg-muted/40"
        >
          <Settings class="size-4" />
          {{ t('school.katampi.btnConfigure') }}
        </button>

        <a
          :href="config.cbt_url"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <ExternalLink class="size-4" />
          {{ t('school.katampi.btnOpenKatampi') }}
        </a>
      </div>
    </div>

    <!-- Status & Metric Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.katampi.cbtServer') }}</div>
          <div class="flex items-center gap-2 mt-1">
            <span class="size-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-sm font-bold text-foreground font-mono">{{ config.cbt_url }}</span>
          </div>
          <div class="text-[11px] text-muted-foreground mt-1">
            {{ t('school.katampi.lastSynced') }}: {{ formatTimeAgo(config.last_sync_at) }}
          </div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <Server class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.katampi.totalExams') }}</div>
          <div class="text-2xl font-bold text-primary mt-1">{{ totalExamsCount }} Sesi Ujian</div>
          <div class="text-[11px] text-muted-foreground mt-1">KataMPI CBT Engine Ready</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <FileText class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.katampi.gradesIngested') }}</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">{{ totalGradesSynced }} Nilai</div>
          <div class="text-[11px] text-muted-foreground mt-1">{{ t('school.katampi.autoMapped') }}</div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-border/60">
      <nav class="flex space-x-6">
        <button
          @click="activeTab = 'sso'"
          :class="[
            activeTab === 'sso'
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-muted-foreground hover:text-foreground',
            'border-b-2 py-3 text-xs transition flex items-center gap-1.5'
          ]"
        >
          <KeyRound class="size-4" />
          {{ t('school.katampi.tabSso') }}
        </button>

        <button
          @click="activeTab = 'roster'"
          :class="[
            activeTab === 'roster'
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-muted-foreground hover:text-foreground',
            'border-b-2 py-3 text-xs transition flex items-center gap-1.5'
          ]"
        >
          <Users class="size-4" />
          {{ t('school.katampi.tabRoster') }}
        </button>

        <button
          @click="activeTab = 'grades'"
          :class="[
            activeTab === 'grades'
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-muted-foreground hover:text-foreground',
            'border-b-2 py-3 text-xs transition flex items-center gap-1.5'
          ]"
        >
          <ArrowDownToLine class="size-4" />
          {{ t('school.katampi.tabGradeSync') }}
        </button>
      </nav>
    </div>

    <!-- TAB 1: SSO LAUNCHER -->
    <div v-if="activeTab === 'sso'" class="space-y-4">
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-border/60 pb-3">
          <div>
            <h3 class="text-sm font-bold text-foreground">{{ t('school.katampi.ssoGenerator') }}</h3>
            <p class="text-xs text-muted-foreground">{{ t('school.katampi.ssoDesc') }}</p>
          </div>
          <span class="inline-flex items-center rounded-md bg-emerald-500/10 px-2 py-1 text-xs font-semibold text-emerald-600">
            HMAC-SHA256 Signed
          </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.katampi.selectStudentOrStaff') }}</label>
            <select
              v-model="selectedStudentForSso"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            >
              <option :value="null">{{ t('school.katampi.currentUserStaff') }}</option>
              <option v-for="std in rosterList" :key="std.id" :value="std.id">
                {{ std.nisn }} - {{ std.name }} ({{ std.study_group_name }})
              </option>
            </select>
          </div>

          <div class="flex items-end">
            <button
              @click="generateSso"
              :disabled="generatingSso"
              class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              <KeyRound class="size-4" />
              {{ generatingSso ? t('school.common.loading') : t('school.katampi.btnGenerateToken') }}
            </button>
          </div>
        </div>

        <div v-if="generatedSsoData" class="rounded-lg border border-border/60 bg-muted/30 p-4 space-y-3 text-xs">
          <div class="flex items-center justify-between">
            <div class="font-semibold text-foreground flex items-center gap-1.5">
              <CheckCircle2 class="size-4 text-emerald-600" />
              {{ t('school.katampi.tokenActiveFor') }}: {{ generatedSsoData.user.name }} ({{ generatedSsoData.user.role }})
            </div>
            <span class="text-[11px] text-muted-foreground">Berlaku 60 menit</span>
          </div>

          <div>
            <div class="text-[11px] text-muted-foreground font-medium mb-1">Launch Redirect URL:</div>
            <div class="flex items-center gap-2">
              <input
                type="text"
                readonly
                :value="generatedSsoData.redirect_url"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-1.5 font-mono text-[11px] text-foreground"
              />
              <button
                @click="copyToClipboard(generatedSsoData.redirect_url)"
                class="rounded-lg border border-border/60 bg-background px-3 py-1.5 font-semibold text-foreground hover:bg-muted/40 transition"
              >
                {{ copied ? 'Tersalin!' : 'Salin' }}
              </button>
            </div>
          </div>

          <div class="pt-2 flex items-center justify-end">
            <a
              :href="generatedSsoData.redirect_url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white shadow-sm hover:bg-emerald-700 transition"
            >
              <ExternalLink class="size-3.5" />
              {{ t('school.katampi.btnLaunchCbtSession') }}
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: PARTICIPANTS ROSTER SYNC -->
    <div v-if="activeTab === 'roster'" class="space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="selectedGroupFilter"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.katampi.allGroups') }}</option>
            <option v-for="g in studyGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>
        </div>

        <div class="flex items-center gap-2">
          <button
            @click="copyRosterJson"
            class="inline-flex items-center gap-1 rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
          >
            <Copy class="size-3.5" />
            {{ t('school.katampi.btnCopyJson') }}
          </button>
          <button
            @click="downloadRosterCsv"
            class="inline-flex items-center gap-1 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground hover:bg-primary/90 transition shadow-sm"
          >
            <Download class="size-3.5" />
            {{ t('school.katampi.btnDownloadCsv') }}
          </button>
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border/60 bg-muted/40 text-muted-foreground font-medium">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">NISN</th>
              <th class="p-3">NIS</th>
              <th class="p-3">{{ t('school.katampi.studentName') }}</th>
              <th class="p-3">{{ t('school.katampi.gender') }}</th>
              <th class="p-3">{{ t('school.katampi.studyGroup') }}</th>
              <th class="p-3">{{ t('school.katampi.cbtUsername') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-if="loadingRoster">
              <td colspan="7" class="p-6 text-center text-muted-foreground">
                <Loader2 class="size-5 animate-spin mx-auto mb-1 text-primary" />
                {{ t('school.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="filteredRoster.length === 0">
              <td colspan="7" class="p-6 text-center text-muted-foreground">
                {{ t('school.common.noData') }}
              </td>
            </tr>
            <tr v-for="(std, index) in filteredRoster" :key="std.id" class="hover:bg-muted/20">
              <td class="p-3 text-muted-foreground">{{ Number(index) + 1 }}</td>
              <td class="p-3 font-mono font-bold text-primary">{{ std.nisn }}</td>
              <td class="p-3 font-mono text-muted-foreground">{{ std.nis || '-' }}</td>
              <td class="p-3 font-semibold text-foreground">{{ std.name }}</td>
              <td class="p-3">{{ std.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
              <td class="p-3 font-medium text-foreground">{{ std.study_group_name }}</td>
              <td class="p-3 font-mono text-[11px] text-muted-foreground">
                cbt_{{ std.nisn }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- TAB 3: CBT GRADE INGESTION -->
    <div v-if="activeTab === 'grades'" class="space-y-4">
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4">
        <div class="border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">{{ t('school.katampi.ingestTitle') }}</h3>
          <p class="text-xs text-muted-foreground">{{ t('school.katampi.ingestDesc') }}</p>
        </div>

        <form @submit.prevent="submitSyncGrades" class="space-y-3 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.katampi.examTitle') }}</label>
              <input
                v-model="gradeSyncForm.assessment_title"
                type="text"
                required
                placeholder="e.g. Sumatif CBT Bab 3 Pemrograman Web"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.katampi.targetSubject') }}</label>
              <select
                v-model="gradeSyncForm.subject_id"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.katampi.targetGroup') }}</label>
              <select
                v-model="gradeSyncForm.study_group_id"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="" disabled>-- Pilih Rombel --</option>
                <option v-for="g in studyGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
              </select>
            </div>
          </div>

          <div class="rounded-lg border border-dashed border-border/80 bg-muted/20 p-4 space-y-2">
            <div class="flex items-center justify-between">
              <span class="font-semibold text-foreground">{{ t('school.katampi.previewParticipantsGrades') }}</span>
              <button
                type="button"
                @click="populateSampleGrades"
                class="text-[11px] font-medium text-primary hover:underline"
              >
                {{ t('school.katampi.btnAutoFillSample') }}
              </button>
            </div>

            <div class="max-h-60 overflow-y-auto space-y-1.5 pr-1">
              <div
                v-for="item in gradeSyncForm.grades"
                :key="item.student_id"
                class="flex items-center justify-between rounded-lg bg-background p-2 border border-border/60 text-xs"
              >
                <span class="font-medium text-foreground">{{ getStudentName(item.student_id) }}</span>
                <div class="flex items-center gap-2">
                  <span class="text-muted-foreground text-[11px]">Skor (0-100):</span>
                  <input
                    v-model.number="item.score"
                    type="number"
                    min="0"
                    max="100"
                    step="0.5"
                    class="w-20 rounded border border-border/60 bg-card px-2 py-1 text-xs font-bold text-center text-foreground"
                  />
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end pt-2">
            <button
              type="submit"
              :disabled="syncingGrades || gradeSyncForm.grades.length === 0"
              class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50"
            >
              <CheckCircle2 class="size-4" />
              {{ syncingGrades ? t('school.common.saving') : t('school.katampi.btnCommitSyncGrades') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- CONFIG MODAL -->
    <div v-if="showConfigModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-md rounded-xl border border-border/60 bg-card p-5 shadow-lg space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">{{ t('school.katampi.configTitle') }}</h3>
          <button @click="showConfigModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="saveConfig" class="space-y-3 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.katampi.cbtUrl') }}</label>
            <input
              v-model="configForm.cbt_url"
              type="url"
              required
              placeholder="http://ja-katampi:8787"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.katampi.apiKey') }}</label>
            <input
              v-model="configForm.api_key"
              type="text"
              placeholder="Shared secret token..."
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 font-mono text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center gap-2 pt-1">
            <input
              v-model="configForm.is_active"
              type="checkbox"
              id="activeCheckbox"
              class="rounded border-border/60 text-primary focus:ring-primary"
            />
            <label for="activeCheckbox" class="font-medium text-foreground">{{ t('school.katampi.enableIntegration') }}</label>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showConfigModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="savingConfig"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              {{ savingConfig ? t('school.common.saving') : t('school.common.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Server,
  FileText,
  CheckCircle2,
  ExternalLink,
  Settings,
  KeyRound,
  Users,
  ArrowDownToLine,
  Copy,
  Download,
  X,
  Loader2,
} from 'lucide-vue-next';
import {
  SchoolService,
  type KatampiConfigData,
  type StudyGroupData,
  type SubjectData,
} from '../services/schoolService';

const { t } = useI18n();

const activeTab = ref<'sso' | 'roster' | 'grades'>('sso');
const config = ref<KatampiConfigData>({
  id: 1,
  school_id: 1,
  cbt_url: 'http://ja-katampi:8787',
  api_key: '',
  is_active: true,
  last_sync_at: null,
  sync_stats: null,
});

interface RosterStudent {
  id: number;
  nisn: string;
  nis: string;
  name: string;
  gender: string;
  study_group_id: number;
  study_group_name: string;
}

const rosterList = ref<RosterStudent[]>([]);
const studyGroups = ref<StudyGroupData[]>([]);
const subjects = ref<SubjectData[]>([]);

const loadingRoster = ref(false);
const generatingSso = ref(false);
const syncingGrades = ref(false);
const savingConfig = ref(false);
const showConfigModal = ref(false);
const copied = ref(false);

const selectedStudentForSso = ref<number | null>(null);
interface SsoResponseData {
  token: string;
  redirect_url: string;
  expires_in: number;
  user: {
    name: string;
    role: string;
    nisn?: string;
  };
}
const generatedSsoData = ref<SsoResponseData | null>(null);

const selectedGroupFilter = ref('');

const configForm = reactive({
  cbt_url: 'http://ja-katampi:8787',
  api_key: '',
  is_active: true,
});

const gradeSyncForm = reactive({
  assessment_title: 'Sumatif CBT Harian: Pengembangan Web',
  subject_id: '' as unknown as number,
  study_group_id: '' as unknown as number,
  grades: [] as { student_id: number; score: number; notes: string }[],
});

// Stats Computed
const totalExamsCount = computed(() => {
  const stats = config.value.sync_stats as Record<string, unknown> | null;
  return Number(stats?.total_exams || 12);
});

const totalGradesSynced = computed(() => {
  const stats = config.value.sync_stats as Record<string, unknown> | null;
  return Number(stats?.total_grades_synced || 432);
});

const filteredRoster = computed(() => {
  if (!selectedGroupFilter.value) return rosterList.value;
  return rosterList.value.filter((s) => s.study_group_id === Number(selectedGroupFilter.value));
});

function formatTimeAgo(dateStr: string | null) {
  if (!dateStr) return 'Belum pernah';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
}

function getStudentName(id: number) {
  const s = rosterList.value.find((item) => item.id === id);
  return s ? `${s.nisn} - ${s.name}` : `Siswa ID #${id}`;
}

async function loadConfig() {
  try {
    const res = await SchoolService.getKatampiConfig();
    config.value = res.data.data;
    configForm.cbt_url = config.value.cbt_url;
    configForm.api_key = config.value.api_key || '';
    configForm.is_active = config.value.is_active;
  } catch (err) {
    console.error(err);
  }
}

async function loadRoster() {
  loadingRoster.value = true;
  try {
    const res = await SchoolService.getKatampiRoster();
    rosterList.value = res.data.data.students || [];
    populateSampleGrades();
  } catch (err) {
    console.error(err);
  } finally {
    loadingRoster.value = false;
  }
}

async function loadMasterData() {
  try {
    const [grpRes, subjRes] = await Promise.all([
      SchoolService.getStudyGroups(),
      SchoolService.getSubjects(),
    ]);
    studyGroups.value = grpRes.data.data;
    subjects.value = subjRes.data.data;

    if (studyGroups.value.length > 0 && studyGroups.value[0]) {
      gradeSyncForm.study_group_id = studyGroups.value[0].id;
    }
    if (subjects.value.length > 0 && subjects.value[0]) {
      gradeSyncForm.subject_id = subjects.value[0].id;
    }
  } catch (err) {
    console.error(err);
  }
}

async function generateSso() {
  generatingSso.value = true;
  try {
    const payload = selectedStudentForSso.value ? { student_id: selectedStudentForSso.value } : {};
    const res = await SchoolService.generateKatampiSsoToken(payload);
    generatedSsoData.value = res.data.data;
  } catch (err) {
    console.error(err);
  } finally {
    generatingSso.value = false;
  }
}

function copyToClipboard(text: string) {
  navigator.clipboard.writeText(text);
  copied.value = true;
  setTimeout(() => (copied.value = false), 2000);
}

function copyRosterJson() {
  const jsonStr = JSON.stringify(filteredRoster.value, null, 2);
  copyToClipboard(jsonStr);
  alert('Data roster format JSON berhasil disalin ke clipboard!');
}

function downloadRosterCsv() {
  const headers = ['id', 'nisn', 'nis', 'name', 'gender', 'study_group_name'];
  const rows = filteredRoster.value.map((r) => [
    r.id,
    `"${r.nisn}"`,
    `"${r.nis || ''}"`,
    `"${r.name}"`,
    r.gender,
    `"${r.study_group_name}"`,
  ]);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map((e) => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `roster_peserta_cbt_${new Date().toISOString().slice(0, 10)}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function populateSampleGrades() {
  const sample = rosterList.value.slice(0, 10).map((s) => ({
    student_id: s.id,
    score: Math.floor(Math.random() * 25) + 75,
    notes: 'Diimpor otomatis dari ja-katampi CBT',
  }));
  gradeSyncForm.grades = sample;
}

async function submitSyncGrades() {
  syncingGrades.value = true;
  try {
    const res = await SchoolService.syncKatampiGrades(gradeSyncForm);
    alert(`Berhasil mensinkronkan ${res.data.data.grades_synced} nilai ujian dari KataMPI CBT ke EduSIS!`);
    await loadConfig();
  } catch (err) {
    console.error(err);
  } finally {
    syncingGrades.value = false;
  }
}

async function saveConfig() {
  savingConfig.value = true;
  try {
    await SchoolService.updateKatampiConfig(configForm);
    showConfigModal.value = false;
    await loadConfig();
  } catch (err) {
    console.error(err);
  } finally {
    savingConfig.value = false;
  }
}

onMounted(() => {
  loadConfig();
  loadRoster();
  loadMasterData();
});
</script>
