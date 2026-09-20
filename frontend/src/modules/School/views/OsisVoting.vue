<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.osis.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.osis.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          v-if="currentTab === 'evoting'"
          @click="openVoteModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Vote class="size-4" />
          {{ t('school.osis.btnCastVote') }}
        </button>
        <button
          v-if="currentTab === 'candidates'"
          @click="showAddCandidateModal = true"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <UserPlus class="size-4" />
          {{ t('school.osis.btnAddCandidate') }}
        </button>
        <button
          v-if="currentTab === 'extracurriculars'"
          @click="showAddEkskulModal = true"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.osis.btnAddEkskul') }}
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="inline-flex rounded-lg border border-border/60 bg-muted/30 p-1">
      <button
        @click="currentTab = 'evoting'"
        :class="[
          'rounded-md px-4 py-1.5 text-xs font-semibold transition',
          currentTab === 'evoting' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <BarChart3 class="inline-block mr-1.5 size-3.5" />
        {{ t('school.osis.tabEvoting') }}
      </button>
      <button
        @click="currentTab = 'candidates'"
        :class="[
          'rounded-md px-4 py-1.5 text-xs font-semibold transition',
          currentTab === 'candidates' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <Users class="inline-block mr-1.5 size-3.5" />
        {{ t('school.osis.tabCandidates') }}
      </button>
      <button
        @click="currentTab = 'extracurriculars'"
        :class="[
          'rounded-md px-4 py-1.5 text-xs font-semibold transition',
          currentTab === 'extracurriculars' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <Activity class="inline-block mr-1.5 size-3.5" />
        {{ t('school.osis.tabExtracurriculars') }}
      </button>
    </div>

    <!-- TAB 1: E-VOTING & QUICK COUNT -->
    <div v-if="currentTab === 'evoting'" class="space-y-6">
      <!-- Election Summary Banner -->
      <div v-if="activeElection" class="rounded-xl border border-primary/30 bg-primary/5 p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <div class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-semibold bg-primary/20 text-primary uppercase tracking-wider mb-2">
              {{ t('school.osis.electionActive') }} ({{ activeElection.period }})
            </div>
            <h2 class="text-lg font-bold text-foreground">{{ activeElection.title }}</h2>
            <p class="text-xs text-muted-foreground mt-0.5">
              {{ t('school.osis.voteIntegrityBadge') }}
            </p>
          </div>
          <div class="flex items-center gap-4 bg-card/80 p-3 rounded-lg border border-border/40">
            <div class="text-center">
              <div class="text-[11px] text-muted-foreground">{{ t('school.osis.totalVotesIn') }}</div>
              <div class="text-2xl font-black text-foreground font-mono mt-0.5">{{ quickCountData?.total_votes || 0 }}</div>
            </div>
            <div class="text-center border-l border-border/60 pl-4">
              <div class="text-[11px] text-muted-foreground">{{ t('school.osis.candidatesTotal') }}</div>
              <div class="text-2xl font-black text-primary font-mono mt-0.5">{{ candidates.length }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Count Visual Cards -->
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div
          v-for="cand in (quickCountData?.candidates || candidates)"
          :key="cand.id"
          class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4 hover:border-primary/40 transition"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="size-12 rounded-xl bg-primary text-primary-foreground font-black text-xl flex items-center justify-center shadow-md">
                0{{ cand.candidate_number }}
              </div>
              <div>
                <div class="font-bold text-base text-foreground">
                  {{ cand.chairman_name || cand.chairman?.full_name || 'Ketua' }}
                </div>
                <div class="text-xs text-muted-foreground">
                  & {{ cand.vice_chairman_name || cand.vice_chairman?.full_name || 'Wakil Ketua' }}
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-black text-foreground font-mono">
                {{ cand.percentage || 0 }}%
              </div>
              <div class="text-[11px] text-muted-foreground font-mono">
                {{ cand.vote_count }} Suara
              </div>
            </div>
          </div>

          <!-- Percentage Progress Bar -->
          <div class="space-y-1">
            <div class="h-3 w-full rounded-full bg-muted/40 overflow-hidden">
              <div
                class="h-full rounded-full bg-primary transition-all duration-700"
                :style="{ width: `${cand.percentage || 0}%` }"
              ></div>
            </div>
          </div>

          <!-- Vision & Mission Preview -->
          <div class="bg-muted/20 p-3 rounded-lg border border-border/40 text-xs space-y-1">
            <div>
              <span class="font-semibold text-foreground">Visi:</span>
              <span class="text-muted-foreground ml-1">{{ cand.vision }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 2: CANDIDATES LIST -->
    <div v-if="currentTab === 'candidates'" class="space-y-6">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div
          v-for="cand in candidates"
          :key="cand.id"
          class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-4"
        >
          <div class="flex items-center gap-4">
            <div class="size-14 rounded-xl bg-primary/10 text-primary font-black text-2xl flex items-center justify-center border border-primary/20">
              #{{ cand.candidate_number }}
            </div>
            <div>
              <div class="text-xs font-bold uppercase tracking-wider text-primary">Kandidat Paslon {{ cand.candidate_number }}</div>
              <div class="font-bold text-base text-foreground mt-0.5">
                {{ cand.chairman?.full_name }} & {{ cand.vice_chairman?.full_name }}
              </div>
            </div>
          </div>

          <div class="space-y-2 text-xs">
            <div>
              <div class="font-semibold text-foreground mb-0.5">{{ t('school.osis.vision') }}</div>
              <div class="text-muted-foreground bg-muted/20 p-2.5 rounded-lg border border-border/40">
                {{ cand.vision }}
              </div>
            </div>

            <div>
              <div class="font-semibold text-foreground mb-0.5">{{ t('school.osis.mission') }}</div>
              <div class="text-muted-foreground bg-muted/20 p-2.5 rounded-lg border border-border/40 whitespace-pre-line">
                {{ cand.mission }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: EXTRACURRICULARS -->
    <div v-if="currentTab === 'extracurriculars'" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="ekskul in extracurriculars"
          :key="ekskul.id"
          class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3 hover:border-primary/40 transition"
        >
          <div class="flex items-start justify-between">
            <div>
              <span
                :class="[
                  'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold mb-1',
                  ekskul.category === 'wajib' ? 'bg-amber-500/10 text-amber-600' : 'bg-blue-500/10 text-blue-600'
                ]"
              >
                {{ ekskul.category.toUpperCase() }}
              </span>
              <h3 class="font-bold text-sm text-foreground">{{ ekskul.name }}</h3>
            </div>
            <span
              :class="[
                'size-2 rounded-full mt-1',
                ekskul.is_active ? 'bg-emerald-500' : 'bg-muted-foreground'
              ]"
            ></span>
          </div>

          <p v-if="ekskul.description" class="text-xs text-muted-foreground line-clamp-2">
            {{ ekskul.description }}
          </p>

          <div class="space-y-1.5 border-t border-border/40 pt-2.5 text-xs">
            <div class="flex items-center gap-1.5 text-muted-foreground">
              <UserCheck class="size-3.5 text-primary" />
              <span>Pembina: <strong class="text-foreground">{{ ekskul.advisor?.full_name || '-' }}</strong></span>
            </div>
            <div class="flex items-center gap-1.5 text-muted-foreground">
              <Calendar class="size-3.5 text-primary" />
              <span>Jadwal: {{ ekskul.schedule_day || '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-muted-foreground">
              <MapPin class="size-3.5 text-primary" />
              <span>Lokasi: {{ ekskul.location || '-' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Bilik Suara Digital (Vote) -->
    <div
      v-if="showVoteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-lg rounded-xl border border-border/60 bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <div>
            <h2 class="text-base font-semibold text-foreground">{{ t('school.osis.modalVoteTitle') }}</h2>
            <div class="text-xs text-muted-foreground">{{ t('school.osis.modalVoteSubtitle') }}</div>
          </div>
          <button @click="showVoteModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="castVote" class="space-y-4 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.osis.selectStudentVoter') }}</label>
            <select
              v-model="voteForm.voter_student_id"
              required
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            >
              <option v-for="std in students" :key="std.id" :value="std.id">
                {{ std.full_name }} (NISN: {{ std.nisn || '-' }})
              </option>
            </select>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-2">{{ t('school.osis.selectCandidate') }}</label>
            <div class="grid grid-cols-2 gap-3">
              <div
                v-for="cand in candidates"
                :key="cand.id"
                @click="voteForm.candidate_id = cand.id"
                :class="[
                  'cursor-pointer rounded-xl border p-4 text-center transition',
                  voteForm.candidate_id === cand.id ? 'border-primary bg-primary/10 shadow-sm' : 'border-border/60 bg-card hover:border-border'
                ]"
              >
                <div class="text-2xl font-black text-primary font-mono mb-1">
                  0{{ cand.candidate_number }}
                </div>
                <div class="font-bold text-xs text-foreground">
                  {{ cand.chairman?.full_name }}
                </div>
                <div class="text-[11px] text-muted-foreground mt-0.5">
                  & {{ cand.vice_chairman?.full_name }}
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showVoteModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving || !voteForm.candidate_id || !voteForm.voter_student_id"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.osis.btnConfirmVote') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  BarChart3,
  Users,
  Activity,
  Vote,
  UserPlus,
  Plus,
  Calendar,
  MapPin,
  UserCheck,
  X,
} from 'lucide-vue-next';
import {
  SchoolService,
  type OsisElectionData,
  type OsisCandidateData,
  type ExtracurricularData,
  type StudentData,
} from '../services/schoolService';

const { t } = useI18n();

const currentTab = ref<'evoting' | 'candidates' | 'extracurriculars'>('evoting');
const elections = ref<OsisElectionData[]>([]);
const activeElection = ref<OsisElectionData | null>(null);
const candidates = ref<OsisCandidateData[]>([]);
const quickCountData = ref<{ total_votes: number; candidates: Array<OsisCandidateData & { chairman_name: string; vice_chairman_name: string; percentage: number }> } | null>(null);
const extracurriculars = ref<ExtracurricularData[]>([]);
const students = ref<StudentData[]>([]);

const showVoteModal = ref(false);
const showAddCandidateModal = ref(false);
const showAddEkskulModal = ref(false);
const saving = ref(false);

const voteForm = reactive({
  voter_student_id: 0,
  candidate_id: 0,
});

const loadElections = async () => {
  try {
    const res = await SchoolService.getOsisElections();
    elections.value = res.data.data || [];
    const firstElection = elections.value[0];
    if (firstElection) {
      activeElection.value = firstElection;
      candidates.value = firstElection.candidates || [];
      await loadQuickCount(firstElection.id);
    }
  } catch (err) {
    console.error('Failed to load OSIS elections:', err);
  }
};

const loadQuickCount = async (electionId: number) => {
  try {
    const res = await SchoolService.getOsisQuickCount(electionId);
    quickCountData.value = res.data.data;
  } catch (err) {
    console.error('Failed to load quick count:', err);
  }
};

const loadExtracurriculars = async () => {
  try {
    const res = await SchoolService.getExtracurriculars();
    extracurriculars.value = res.data.data || [];
  } catch (err) {
    console.error('Failed to load extracurriculars:', err);
  }
};

const loadStudents = async () => {
  try {
    const res = await SchoolService.getStudents({ per_page: 50 });
    students.value = res.data.data.data || res.data.data || [];
    const firstStudent = students.value[0];
    if (firstStudent) {
      voteForm.voter_student_id = firstStudent.id;
    }
  } catch (err) {
    console.error('Failed to load students:', err);
  }
};

const openVoteModal = () => {
  const firstCand = candidates.value[0];
  if (firstCand) {
    voteForm.candidate_id = firstCand.id;
  }
  showVoteModal.value = true;
};

const castVote = async () => {
  if (!activeElection.value) return;
  saving.value = true;
  try {
    await SchoolService.voteOsis(activeElection.value.id, {
      candidate_id: voteForm.candidate_id,
      voter_student_id: voteForm.voter_student_id,
    });
    alert(t('school.osis.voteSuccessMessage'));
    showVoteModal.value = false;
    await loadQuickCount(activeElection.value.id);
  } catch (err: any) {
    alert(err?.response?.data?.message || 'Gagal merekam suara');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadElections();
  loadExtracurriculars();
  loadStudents();
});
</script>
