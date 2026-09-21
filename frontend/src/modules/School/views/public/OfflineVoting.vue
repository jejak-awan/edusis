<template>
  <div class="min-h-screen bg-slate-950 text-white flex flex-col justify-between p-6 select-none">
    <!-- Kiosk Top Header -->
    <header class="flex items-center justify-between border-b border-white/10 pb-4">
      <div class="flex items-center gap-3">
        <div class="size-10 rounded-xl bg-primary flex items-center justify-center font-bold shadow-lg shadow-primary/30">
          <Vote class="size-6 text-white" />
        </div>
        <div>
          <h1 class="text-lg font-bold tracking-tight">Bilik Suara Mandiri (E-Voting Kiosk)</h1>
          <p class="text-xs text-slate-400">Pemilihan Ketua & Wakil Ketua OSIS &bull; Mode Terenkripsi</p>
        </div>
      </div>

      <!-- Online / Offline & Sync Indicator -->
      <div class="flex items-center gap-3">
        <div
          :class="[
            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold font-mono',
            isOnline ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30'
          ]"
        >
          <span :class="['size-2 rounded-full', isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500']"></span>
          <span>{{ isOnline ? 'ONLINE' : 'MODE OFFLINE' }}</span>
        </div>

        <div v-if="unsyncedVotes.length > 0" class="flex items-center gap-1.5 text-xs text-amber-300 font-mono">
          <Clock class="size-3.5" />
          <span>{{ unsyncedVotes.length }} antrean lokal</span>
          <button
            v-if="isOnline"
            type="button"
            class="ml-1 text-[11px] underline hover:text-white"
            @click="syncOfflineVotes"
          >
            Sync
          </button>
        </div>
      </div>
    </header>

    <!-- STEP 1: SCAN QR / ENTER TOKEN -->
    <main v-if="step === 'auth'" class="max-w-md w-full mx-auto my-auto space-y-6 text-center">
      <div class="space-y-2">
        <div class="inline-flex p-3 rounded-2xl bg-primary/10 text-primary border border-primary/20 mb-2">
          <KeyRound class="size-8" />
        </div>
        <h2 class="text-2xl font-black tracking-tight">Otentikasi Hak Suara</h2>
        <p class="text-xs text-slate-400">
          Gunakan kamera untuk memindai kartu pemilih atau ketik 8 karakter token DPT Anda.
        </p>
      </div>

      <div class="space-y-4">
        <!-- Manual Input -->
        <input
          v-model="voterToken"
          type="text"
          maxlength="8"
          placeholder="A7B9K2X4"
          class="w-full text-center text-2xl tracking-[0.25em] uppercase font-mono py-4 rounded-xl bg-slate-900 border-2 border-primary/40 text-white placeholder-slate-700 focus:outline-none focus:border-primary"
          @keyup.enter="handleAuthenticate"
        />

        <div v-if="authError" class="text-xs text-rose-400 font-medium">
          {{ authError }}
        </div>

        <button
          type="button"
          :disabled="voterToken.trim().length < 6 || isValidating"
          class="w-full py-3.5 rounded-xl bg-primary font-bold text-sm text-white hover:bg-primary/90 transition shadow-lg shadow-primary/30 disabled:opacity-40"
          @click="handleAuthenticate"
        >
          {{ isValidating ? 'Memverifikasi Hak Suara...' : 'Buka Bilik Suara' }}
        </button>

        <button
          type="button"
          class="text-xs text-slate-400 hover:text-white underline transition"
          @click="showScannerModal = true"
        >
          Atau Pindai dengan Kamera QR Scanner
        </button>
      </div>
    </main>

    <!-- STEP 2: BALLOT / CANDIDATES SELECTION -->
    <main v-else-if="step === 'ballot'" class="max-w-5xl w-full mx-auto my-auto space-y-8">
      <div class="text-center space-y-2">
        <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold border border-primary/20">
          SURAT SUARA ELEKTRONIK RESMI
        </span>
        <h2 class="text-2xl font-black tracking-tight mt-2">
          Pilih Pasangan Calon Pilihan Anda
        </h2>
        <p class="text-xs text-slate-400">
          Sentuh pada kotak calon pilihan Anda, lalu tekan tombol Konfirmasi Pilihan.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto">
        <CandidateCard
          v-for="candidate in candidates"
          :key="candidate.id"
          :candidate="candidate"
          :selected="selectedCandidate?.id === candidate.id"
          @select="selectedCandidate = $event"
        />
      </div>

      <div class="flex items-center justify-center gap-4 pt-4">
        <button
          type="button"
          class="rounded-xl border border-white/20 px-6 py-3 text-xs font-semibold text-slate-300 hover:bg-white/10 transition"
          @click="resetKiosk"
        >
          Batal & Keluar
        </button>
        <button
          type="button"
          :disabled="!selectedCandidate"
          class="rounded-xl bg-primary px-8 py-3 text-xs font-bold text-white shadow-xl shadow-primary/30 hover:bg-primary/90 transition disabled:opacity-40"
          @click="showConfirmModal = true"
        >
          Konfirmasi Pilihan Suara
        </button>
      </div>
    </main>

    <!-- STEP 3: SUCCESS CONFIRMATION -->
    <main v-else-if="step === 'success'" class="max-w-md w-full mx-auto my-auto text-center space-y-6">
      <div class="size-20 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center mx-auto shadow-2xl shadow-emerald-500/20">
        <CheckCircle2 class="size-10" />
      </div>

      <div class="space-y-2">
        <h2 class="text-2xl font-black tracking-tight text-white">Suara Anda Berhasil Dicatat!</h2>
        <p class="text-xs text-slate-400">
          Terima kasih telah berpartisipasi menyalurkan hak suara Anda. Pilihan Anda dirahasiakan dan disimpan dengan aman.
        </p>
      </div>

      <div class="rounded-xl border border-white/10 bg-slate-900 p-4 text-xs font-mono text-slate-400">
        Bilik akan di-reset otomatis dalam <span class="text-primary font-bold">{{ countdown }} detik</span>.
      </div>

      <button
        type="button"
        class="w-full py-3 rounded-xl border border-white/20 text-xs font-semibold hover:bg-white/10 transition"
        @click="resetKiosk"
      >
        Selesai Sekarang
      </button>
    </main>

    <!-- Kiosk Footer Disclaimer -->
    <footer class="text-center text-[10px] text-slate-500 border-t border-white/10 pt-4">
      &bull; Sistem Bilik Suara E-Voting Terpadu &bull; Hak Cipta Dilindungi &bull;
    </footer>

    <!-- Confirm Modal -->
    <div
      v-if="showConfirmModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
      @click.self="showConfirmModal = false"
    >
      <div class="w-full max-w-sm rounded-2xl border border-border/60 bg-card p-6 shadow-2xl text-center space-y-4">
        <div class="size-14 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto">
          <Vote class="size-7" />
        </div>
        <h3 class="text-lg font-bold text-foreground">Yakin dengan Pilihan Anda?</h3>
        <p class="text-xs text-muted-foreground">
          Anda memilih pasangan calon <span class="font-bold text-foreground">#0{{ selectedCandidate?.candidate_number }} &bull; {{ selectedCandidate?.name }}</span>.
          Pilihan tidak dapat diubah setelah dicoblos.
        </p>

        <div class="flex items-center justify-end gap-2.5 pt-2">
          <button
            type="button"
            class="flex-1 rounded-xl border border-border py-2.5 text-xs font-semibold text-muted-foreground hover:bg-muted"
            @click="showConfirmModal = false"
          >
            Ubah Pilihan
          </button>
          <button
            type="button"
            :disabled="isSubmittingVote"
            class="flex-1 rounded-xl bg-primary py-2.5 text-xs font-bold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
            @click="submitVote"
          >
            {{ isSubmittingVote ? 'Mencatat...' : 'Ya, Masukkan Suara' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Scanner Modal -->
    <div
      v-if="showScannerModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm"
      @click.self="showScannerModal = false"
    >
      <div class="w-full max-w-md space-y-3">
        <QrScanner
          @scan="handleScannedToken"
          @error="authError = $event"
        />
        <div class="text-center">
          <button
            type="button"
            class="text-xs text-slate-400 hover:text-white underline"
            @click="showScannerModal = false"
          >
            Tutup Scanner
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Vote, KeyRound, CheckCircle2, Clock } from 'lucide-vue-next';
import CandidateCard from '../../components/osis/CandidateCard.vue';
import QrScanner from '../../components/shared/QrScanner.vue';
import api from '@/engine/api/client';
import { schoolPaths } from '../../services/paths';
import type { CandidateData } from '../../types/osis';

const step = ref<'auth' | 'ballot' | 'success'>('auth');
const voterToken = ref('');
const isValidating = ref(false);
const authError = ref<string | null>(null);
const isOnline = ref(navigator.onLine);
const showScannerModal = ref(false);
const showConfirmModal = ref(false);
const isSubmittingVote = ref(false);
const countdown = ref(5);
let countdownTimer: ReturnType<typeof setInterval> | null = null;

const selectedCandidate = ref<CandidateData | null>(null);
const unsyncedVotes = ref<Array<{ token: string; candidate_id: number; timestamp: string }>>([]);

const candidates = ref<CandidateData[]>([
  {
    id: 1,
    event_id: 1,
    candidate_number: 1,
    name: 'Muhammad Raditya',
    vice_name: 'Syifa Azzahra',
    vision: 'Mewujudkan OSIS yang aspiratif, progresif, dan menjadi wadah prestasi bakat siswa.',
    mission: '1. Mengoptimalkan aspirasi siswa.\n2. Kolaborasi aktif ekskul.\n3. Digitalisasi mading sekolah.',
  },
  {
    id: 2,
    event_id: 1,
    candidate_number: 2,
    name: 'Fikri Haikal',
    vice_name: 'Zahra Amalia',
    vision: 'Menciptakan kepengurusan siswa yang berakhlak mulia, adaptif, dan berwawasan global.',
    mission: '1. Penguatan keimanan dan ketakwaan.\n2. Forum diskusi terbuka.\n3. Gerakan kepedulian lingkungan.',
  },
]);

function handleScannedToken(token: string) {
  voterToken.value = token;
  showScannerModal.value = false;
  handleAuthenticate();
}

async function handleAuthenticate() {
  if (voterToken.value.trim().length < 6) return;

  isValidating.value = true;
  authError.value = null;

  try {
    if (isOnline.value) {
      await api.post(schoolPaths.public.verifyToken, { token: voterToken.value.trim() });
    }
    step.value = 'ballot';
  } catch (err: any) {
    // If offline, allow voting and queue vote
    if (!isOnline.value) {
      step.value = 'ballot';
    } else {
      authError.value = err.response?.data?.message || 'Token tidak valid atau sudah pernah digunakan.';
    }
  } finally {
    isValidating.value = false;
  }
}

async function submitVote() {
  if (!selectedCandidate.value) return;

  isSubmittingVote.value = true;

  const votePayload = {
    token: voterToken.value,
    candidate_id: selectedCandidate.value.id,
    timestamp: new Date().toISOString(),
  };

  try {
    if (isOnline.value) {
      await api.post(schoolPaths.public.castVote, votePayload);
    } else {
      unsyncedVotes.value.push(votePayload);
      localStorage.setItem('evoting_unsynced_votes', JSON.stringify(unsyncedVotes.value));
    }

    showConfirmModal.value = false;
    step.value = 'success';
    startAutoResetTimer();
  } catch {
    // Queue offline if API errors
    unsyncedVotes.value.push(votePayload);
    localStorage.setItem('evoting_unsynced_votes', JSON.stringify(unsyncedVotes.value));
    showConfirmModal.value = false;
    step.value = 'success';
    startAutoResetTimer();
  } finally {
    isSubmittingVote.value = false;
  }
}

function startAutoResetTimer() {
  countdown.value = 5;
  countdownTimer = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) {
      resetKiosk();
    }
  }, 1000);
}

function resetKiosk() {
  if (countdownTimer) {
    clearInterval(countdownTimer);
    countdownTimer = null;
  }
  step.value = 'auth';
  voterToken.value = '';
  selectedCandidate.value = null;
  authError.value = null;
  showConfirmModal.value = false;
}

async function syncOfflineVotes() {
  if (!isOnline.value || unsyncedVotes.value.length === 0) return;

  const queue = [...unsyncedVotes.value];
  for (const item of queue) {
    try {
      await api.post(schoolPaths.public.castVote, item);
      unsyncedVotes.value = unsyncedVotes.value.filter(v => v !== item);
    } catch {
      // Continue next
    }
  }
  localStorage.setItem('evoting_unsynced_votes', JSON.stringify(unsyncedVotes.value));
}

function updateOnlineStatus() {
  isOnline.value = navigator.onLine;
  if (isOnline.value) {
    syncOfflineVotes();
  }
}

onMounted(() => {
  window.addEventListener('online', updateOnlineStatus);
  window.addEventListener('offline', updateOnlineStatus);

  const saved = localStorage.getItem('evoting_unsynced_votes');
  if (saved) {
    try {
      unsyncedVotes.value = JSON.parse(saved);
    } catch {
      // Non-blocking
    }
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('online', updateOnlineStatus);
  window.removeEventListener('offline', updateOnlineStatus);
  if (countdownTimer) {
    clearInterval(countdownTimer);
  }
});
</script>
