<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.student.dashboard' }" class="hover:text-foreground">Portal Siswa</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Kelulusan</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Informasi Kelulusan & SKL</h1>
        <p class="text-sm text-muted-foreground">
          Pengumuman resmi status kelulusan dan pengunduhan Surat Keterangan Lulus (SKL) digital.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          v-if="graduationData?.status === 'graduated'"
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          @click="downloadCertificate"
        >
          <Download class="size-4" />
          Unduh SKL Resmi (PDF)
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="p-12 text-center text-sm text-muted-foreground bg-card rounded-2xl border border-border/60">
      <div class="inline-block size-6 animate-spin rounded-full border-2 border-primary border-t-transparent mb-2"></div>
      <div>Memeriksa status kelulusan...</div>
    </div>

    <!-- Main Content Grid -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Status Card -->
      <div class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm flex flex-col items-center text-center space-y-4">
        <div
          :class="[
            'size-20 rounded-full flex items-center justify-center shadow-inner',
            graduationData.status === 'graduated'
              ? 'bg-emerald-500/10 text-emerald-600'
              : 'bg-amber-500/10 text-amber-600'
          ]"
        >
          <GraduationCap class="size-10" />
        </div>

        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground">
            Status Kelulusan
          </span>
          <h2
            :class="[
              'text-2xl font-black tracking-tight mt-1',
              graduationData.status === 'graduated'
                ? 'text-emerald-600 dark:text-emerald-400'
                : 'text-amber-600 dark:text-amber-400'
            ]"
          >
            {{ graduationData.status === 'graduated' ? 'DINYATAKAN LULUS' : 'PROSES VERIFIKASI' }}
          </h2>
        </div>

        <div class="w-full border-t border-border/50 pt-4 text-xs space-y-2">
          <div class="flex items-center justify-between text-muted-foreground">
            <span>Tahun Kelulusan:</span>
            <span class="font-bold text-foreground">{{ graduationData.graduation_year }}</span>
          </div>
          <div class="flex items-center justify-between text-muted-foreground">
            <span>Nomor Induk Siswa (NISN):</span>
            <span class="font-mono font-medium text-foreground">{{ graduationData.nisn }}</span>
          </div>
        </div>

        <button
          v-if="graduationData.status === 'graduated'"
          type="button"
          class="w-full mt-2 inline-flex items-center justify-center gap-2 rounded-xl bg-primary py-2.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          @click="downloadCertificate"
        >
          <Download class="size-4" />
          Unduh SKL & Transkrip
        </button>
      </div>

      <!-- Certificate & Breakdown Card -->
      <div class="lg:col-span-2 rounded-2xl border border-border/60 bg-card p-6 shadow-sm space-y-6">
        <!-- Verification Banner -->
        <div class="rounded-xl border border-primary/20 bg-primary/5 p-4 flex items-start gap-3">
          <ShieldCheck class="size-5 text-primary shrink-0 mt-0.5" />
          <div class="space-y-0.5 text-xs">
            <span class="font-bold text-foreground">Nomor Registrasi Surat Keterangan Lulus (SKL)</span>
            <p class="font-mono text-primary font-semibold">{{ graduationData.certificate_number }}</p>
            <p class="text-[11px] text-muted-foreground">
              Dokumen ini dilengkapi segel digital QR Code yang terverifikasi secara terpadu.
            </p>
          </div>
        </div>

        <!-- Subject Scores Summary -->
        <div class="space-y-3">
          <div class="flex items-center gap-2">
            <ListChecks class="size-4 text-primary" />
            <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">
              Rincian Nilai Ijazah / SKL
            </h3>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div
              v-for="(score, subject) in graduationData.grades"
              :key="subject"
              class="flex items-center justify-between rounded-lg border border-border/60 bg-muted/20 px-3.5 py-2.5"
            >
              <span class="font-medium text-foreground truncate pr-2">{{ subject }}</span>
              <span class="font-mono font-bold text-primary">{{ score }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { GraduationCap, Download, ShieldCheck, ListChecks } from 'lucide-vue-next';

interface GraduationDetail {
  status: 'graduated' | 'pending';
  graduation_year: string;
  nisn: string;
  certificate_number: string;
  grades: Record<string, number>;
}

const loading = ref(false);
const graduationData = ref<GraduationDetail>({
  status: 'graduated',
  graduation_year: '2025/2026',
  nisn: '0058291042',
  certificate_number: '421.5/084/SKL-SMK/2026',
  grades: {
    'Pendidikan Agama dan Budi Pekerti': 89,
    'Pendidikan Pancasila': 90,
    'Bahasa Indonesia': 86,
    'Matematika': 92,
    'Bahasa Inggris': 88,
    'Informatika & Kejuruan': 95,
    'Praktik Kerja Lapangan (PKL)': 94,
  },
});

function downloadCertificate() {
  window.print();
}
</script>
