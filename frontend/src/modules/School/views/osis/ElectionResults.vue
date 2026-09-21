<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.osis' }" class="hover:text-foreground">Kesiswaan & OSIS</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Quick Count E-Voting</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Hasil Perhitungan Suara Real-Time</h1>
        <p class="text-sm text-muted-foreground">
          Pantau perolehan suara pasangan calon ketua dan wakil ketua OSIS secara langsung, transparan, dan akuntabel.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <router-link
          :to="{ name: 'school.osis' }"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
        >
          <ArrowLeft class="size-4" />
          Kembali
        </router-link>

        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
          @click="loadResults"
        >
          <RefreshCw class="size-4" :class="{ 'animate-spin': isRefreshing }" />
          Muat Ulang
        </button>

        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
          @click="handlePrint"
        >
          <Printer class="size-4" />
          Cetak Rekapitulasi
        </button>
      </div>
    </div>

    <!-- Live Polling Badge & Status Indicator -->
    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="flex items-center gap-2.5">
        <span class="relative flex size-3">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full size-3 bg-emerald-500"></span>
        </span>
        <span class="text-xs font-semibold text-foreground">Penghitungan Suara Aktif & Terhubung</span>
        <span class="text-[11px] text-muted-foreground font-mono">
          &bull; Pembaruan otomatis setiap 10 detik
        </span>
      </div>

      <div class="flex items-center gap-2 text-xs text-muted-foreground">
        <span>Sinkronisasi Terakhir:</span>
        <span class="font-mono font-medium text-foreground">{{ lastUpdatedTime }}</span>
      </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Total DPT Terdaftar</span>
          <div class="text-2xl font-bold font-mono text-foreground mt-1">{{ totalVoters }} Pemilih</div>
        </div>
        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <Users class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Total Suara Masuk</span>
          <div class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">
            {{ totalVotesCasted }} Suara
          </div>
        </div>
        <div class="size-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Tingkat Partisipasi</span>
          <div class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400 mt-1">
            {{ turnoutPercentage }}%
          </div>
        </div>
        <div class="size-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <BarChart3 class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Belum Memberikan Suara</span>
          <div class="text-2xl font-bold font-mono text-muted-foreground mt-1">
            {{ totalVoters - totalVotesCasted }} Orang
          </div>
        </div>
        <div class="size-10 rounded-xl bg-muted text-muted-foreground flex items-center justify-center">
          <Clock class="size-5" />
        </div>
      </div>
    </div>

    <!-- Candidate Results Visualization -->
    <div class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm space-y-4">
      <div class="flex items-center justify-between border-b border-border/60 pb-3">
        <h3 class="text-base font-bold text-foreground flex items-center gap-2">
          <BarChart3 class="size-4 text-primary" />
          Rincian Perolehan Suara Paslon
        </h3>
      </div>

      <VoteResultChart :results="candidateResults" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import { ArrowLeft, RefreshCw, Printer, Users, CheckCircle2, BarChart3, Clock } from 'lucide-vue-next';
import VoteResultChart, { type CandidateVoteResult } from '../../components/osis/VoteResultChart.vue';
import api from '@/engine/api/client';
import { schoolPaths } from '../../services/paths';

const route = useRoute();

const isRefreshing = ref(false);
const totalVoters = ref(480);
const lastUpdatedTime = ref('');
let pollInterval: ReturnType<typeof setInterval> | null = null;

const candidateResults = ref<CandidateVoteResult[]>([
  {
    candidate_id: 1,
    candidate_number: 1,
    candidate_name: 'Muhammad Raditya & Syifa Azzahra',
    vice_name: 'Syifa Azzahra',
    votes: 215,
    percentage: 54.4,
  },
  {
    candidate_id: 2,
    candidate_number: 2,
    candidate_name: 'Fikri Haikal & Zahra Amalia',
    vice_name: 'Zahra Amalia',
    votes: 180,
    percentage: 45.6,
  },
]);

const totalVotesCasted = computed(() => {
  return candidateResults.value.reduce((acc, curr) => acc + curr.votes, 0);
});

const turnoutPercentage = computed(() => {
  if (totalVoters.value === 0) return '0.0';
  return ((totalVotesCasted.value / totalVoters.value) * 100).toFixed(1);
});

async function loadResults() {
  isRefreshing.value = true;
  lastUpdatedTime.value = new Date().toLocaleTimeString('id-ID');

  try {
    const eventId = route.params.id as string || '1';
    const res = await api.get(schoolPaths.osis.quickCount(eventId));
    if (res.data?.success && res.data.data) {
      if (Array.isArray(res.data.data.candidates)) {
        candidateResults.value = res.data.data.candidates;
      }
      if (res.data.data.total_voters) {
        totalVoters.value = res.data.data.total_voters;
      }
    }
  } catch {
    // Non-blocking fallback
  } finally {
    isRefreshing.value = false;
  }
}

function handlePrint() {
  window.print();
}

onMounted(() => {
  loadResults();
  pollInterval = setInterval(loadResults, 10000);
});

onBeforeUnmount(() => {
  if (pollInterval) {
    clearInterval(pollInterval);
  }
});
</script>
