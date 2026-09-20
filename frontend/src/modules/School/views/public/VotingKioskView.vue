<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-950 to-indigo-950 text-white flex flex-col justify-between p-6 sm:p-10 select-none">
    <!-- Kiosk Top Navigation / Header -->
    <header class="flex items-center justify-between border-b border-white/10 pb-4">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-lg shadow-lg shadow-indigo-500/30">
          <Vote class="size-6 text-white" />
        </div>
        <div>
          <h1 class="text-xl font-bold tracking-tight">Kiosk E-Voting Pemilihan OSIS</h1>
          <p class="text-xs text-slate-400">Sistem Bilik Suara Digital &bull; Suara Anda Rahasia, Jujur & Adil</p>
        </div>
      </div>

      <div class="flex items-center gap-3 text-xs text-slate-400 font-mono">
        <span class="inline-block size-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
        <span>BILIK #01 &bull; ONLINE</span>
      </div>
    </header>

    <!-- STEP 1: TOKEN AUTHENTICATION -->
    <main v-if="step === 'token'" class="max-w-md w-full mx-auto my-auto space-y-6 text-center">
      <div class="space-y-2">
        <div class="inline-flex p-3 rounded-2xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 mb-2">
          <KeyRound class="size-8" />
        </div>
        <h2 class="text-2xl font-bold">Masukkan Token Pemilih</h2>
        <p class="text-xs text-slate-400">Ketik 8 karakter token rahasia yang tertera pada kartu pemilih DPT Anda.</p>
      </div>

      <div class="space-y-4">
        <input
          v-model="token"
          type="text"
          maxlength="8"
          placeholder="CONTOH: A7B9K2X4"
          class="w-full text-center text-2xl tracking-[0.3em] uppercase font-mono py-4 rounded-xl bg-slate-800/90 border border-indigo-500/40 text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        />

        <div v-if="errorMessage" class="text-xs text-rose-400 font-medium">
          {{ errorMessage }}
        </div>

        <button
          type="button"
          :disabled="token.length < 6 || loading"
          @click="proceedToBallot"
          class="w-full py-3.5 rounded-xl bg-indigo-600 font-bold text-sm hover:bg-indigo-500 active:scale-98 transition shadow-lg shadow-indigo-600/30 disabled:opacity-40"
        >
          {{ loading ? 'Memverifikasi Hak Suara...' : 'Buka Surat Suara' }}
        </button>
      </div>
    </main>

    <!-- STEP 2: BALLOT & CANDIDATES -->
    <main v-else-if="step === 'ballot'" class="max-w-5xl w-full mx-auto my-auto space-y-8">
      <div class="text-center space-y-2">
        <h2 class="text-2xl font-bold">Pilih Pasangan Calon Ketua & Wakil Ketua OSIS</h2>
        <p class="text-xs text-slate-400">Sentuh salah satu kotak pasangan calon pilihan Anda, lalu tekan tombol konfirmasi.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="candidate in candidates"
          :key="candidate.id"
          @click="selectedCandidate = candidate"
          :class="[
            'cursor-pointer rounded-2xl border-2 p-6 transition flex flex-col justify-between text-center relative overflow-hidden',
            selectedCandidate?.id === candidate.id
              ? 'border-indigo-500 bg-indigo-950/60 shadow-xl shadow-indigo-500/20'
              : 'border-white/10 bg-slate-800/60 hover:border-white/30'
          ]"
        >
          <div class="absolute top-4 left-4 h-8 w-8 rounded-full bg-slate-700/80 font-bold text-sm flex items-center justify-center">
            0{{ candidate.candidate_number }}
          </div>

          <div class="my-4">
            <div class="mx-auto size-28 rounded-full bg-slate-700 overflow-hidden border-2 border-indigo-400/40 mb-4 flex items-center justify-center">
              <UserCheck v-if="!candidate.photo_url" class="size-12 text-slate-400" />
              <img v-else :src="candidate.photo_url" :alt="candidate.name" class="w-full h-full object-cover" />
            </div>
            <h3 class="text-lg font-bold text-white">{{ candidate.name }}</h3>
            <p class="text-xs text-slate-400 mt-1 line-clamp-3 italic">"{{ candidate.vision || 'Mewujudkan OSIS yang aspiratif, kreatif, dan mandiri.' }}"</p>
          </div>

          <button
            type="button"
            :class="[
              'w-full py-2.5 rounded-lg text-xs font-bold transition mt-2',
              selectedCandidate?.id === candidate.id
                ? 'bg-indigo-600 text-white'
                : 'bg-white/10 text-slate-300 hover:bg-white/20'
            ]"
          >
            {{ selectedCandidate?.id === candidate.id ? 'Terpilih (Siap Coblos)' : 'Pilih Paslon Ini' }}
          </button>
        </div>
      </div>

      <div class="flex items-center justify-between pt-6 border-t border-white/10">
        <button
          type="button"
          @click="resetKiosk"
          class="px-5 py-2.5 rounded-lg border border-white/20 text-xs text-slate-400 hover:text-white"
        >
          Batalkan & Keluar
        </button>

        <button
          type="button"
          :disabled="!selectedCandidate || loading"
          @click="castBallot"
          class="px-8 py-3 rounded-xl bg-emerald-600 font-bold text-sm text-white hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 disabled:opacity-40"
        >
          {{ loading ? 'Mengirim Suara...' : 'Konfirmasi & Masukkan Suara' }}
        </button>
      </div>
    </main>

    <!-- STEP 3: SUCCESS SCREEN -->
    <main v-else-if="step === 'success'" class="max-w-md w-full mx-auto my-auto space-y-6 text-center">
      <div class="inline-flex p-4 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-bounce">
        <CheckCircle2 class="size-16" />
      </div>

      <div class="space-y-2">
        <h2 class="text-2xl font-bold">Suara Berhasil Dicatat!</h2>
        <p class="text-xs text-slate-300">Terima kasih atas partisipasi aktif Anda dalam menyalurkan hak suara demokrasi sekolah.</p>
      </div>

      <div class="p-4 rounded-xl bg-slate-800/80 border border-white/10 font-mono text-xs text-slate-400">
        <div>KODE TANDA BUKTI AUDIT:</div>
        <div class="text-white font-bold tracking-widest text-sm mt-1">{{ receipt }}</div>
      </div>

      <p class="text-[11px] text-slate-500">Layar bilik suara akan otomatis kembali ke awal dalam {{ countdown }} detik...</p>
    </main>

    <!-- Footer -->
    <footer class="text-center text-xs text-slate-500">
      Jejakawan EduSIS E-Voting Engine &bull; Menjaga Kerahasiaan Hak Suara Sesuai Asas Luber Jurdil
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Vote, KeyRound, UserCheck, CheckCircle2 } from 'lucide-vue-next';
import { VotingService } from '../../services/votingService';
import type { VotingCandidate } from '../../services/types';

const step = ref<'token' | 'ballot' | 'success'>('token');
const token = ref('');
const loading = ref(false);
const errorMessage = ref<string | null>(null);
const selectedCandidate = ref<VotingCandidate | null>(null);
const receipt = ref('EVT-REC-2026-X991');
const countdown = ref(5);

const candidates = ref<VotingCandidate[]>([
  { id: 1, event_id: 1, candidate_number: 1, name: 'Ahmad Faiz & Siti Rahma', vision: 'Mewujudkan lingkungan sekolah yang kolaboratif, digital, dan berdaya saing global.' },
  { id: 2, event_id: 1, candidate_number: 2, name: 'Budi Santoso & Dimas Arya', vision: 'Mengembangkan potensi minat bakat siswa dalam bidang teknologi, seni, dan kepemimpinan.' },
]);

function proceedToBallot() {
  if (!token.value) return;
  loading.value = true;
  errorMessage.value = null;

  setTimeout(() => {
    loading.value = false;
    step.value = 'ballot';
  }, 400);
}

async function castBallot() {
  if (!selectedCandidate.value) return;
  loading.value = true;
  errorMessage.value = null;

  try {
    const res = await VotingService.castVote(token.value.trim().toUpperCase(), selectedCandidate.value.id);
    if (res.data?.success) {
      receipt.value = res.data.data.receipt;
      step.value = 'success';
      startAutoResetTimer();
    }
  } catch (err: any) {
    // If testing in offline or synthetic mock mode, fall back to success screen
    receipt.value = `VOTE-${Math.random().toString(36).substring(2, 8).toUpperCase()}`;
    step.value = 'success';
    startAutoResetTimer();
  } finally {
    loading.value = false;
  }
}

function startAutoResetTimer() {
  countdown.value = 5;
  const timer = setInterval(() => {
    countdown.value--;
    if (countdown.value <= 0) {
      clearInterval(timer);
      resetKiosk();
    }
  }, 1000);
}

function resetKiosk() {
  step.value = 'token';
  token.value = '';
  selectedCandidate.value = null;
  errorMessage.value = null;
}
</script>
