<template>
  <div class="space-y-6">
    <!-- Header Greeting -->
    <div class="rounded-2xl border border-primary/20 bg-gradient-to-r from-primary/10 via-background to-background p-6 shadow-sm">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <span class="inline-flex items-center rounded-full bg-primary/20 px-3 py-1 text-xs font-semibold text-primary">
            Portal Siswa Mandiri
          </span>
          <h1 class="mt-2 text-2xl font-bold tracking-tight text-foreground">Halo, Peserta Didik!</h1>
          <p class="text-sm text-muted-foreground">Pantau catatan akademik, kehadiran harian, dan mutasi poin disiplin secara realtime.</p>
        </div>

        <div class="flex items-center gap-3">
          <router-link
            :to="{ name: 'school.student.reports' }"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          >
            <FileText class="size-4" />
            Buku Raport Digital
          </router-link>
        </div>
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Poin Disiplin Siswa</span>
          <ShieldCheck class="size-4 text-emerald-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">100 / 100</p>
        <p class="text-xs text-emerald-500 mt-1 font-medium">Kategori: Sangat Baik (Tertib)</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Persentase Kehadiran</span>
          <CalendarCheck class="size-4 text-primary" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">98.2%</p>
        <p class="text-xs text-muted-foreground mt-1">Semester Ganjil 2026/2027</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Rata-rata Nilai TP</span>
          <GraduationCap class="size-4 text-amber-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">87.5</p>
        <p class="text-xs text-muted-foreground mt-1">Kurikulum Merdeka</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Status Pemilu OSIS</span>
          <Vote class="size-4 text-indigo-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">DPT Terdaftar</p>
        <p class="text-xs text-indigo-500 mt-1 font-medium">Bilik Suara Siap</p>
      </div>
    </div>

    <!-- Quick Navigation Hub -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-lg bg-primary/10 text-primary">
            <FileText class="size-5" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-foreground">Buku Raport Merdeka</h3>
            <p class="text-xs text-muted-foreground">Capaian kompetensi & ekstrakurikuler</p>
          </div>
        </div>
        <p class="text-xs text-muted-foreground leading-relaxed">
          Akses lembar penilaian capaian pembelajaran dan unduh dokumen raport resmi berformat PDF.
        </p>
        <router-link
          :to="{ name: 'school.student.reports' }"
          class="inline-block text-xs font-semibold text-primary hover:underline"
        >
          Buka Raport Digital &rarr;
        </router-link>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-500">
            <CalendarCheck class="size-5" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-foreground">Rekap Presensi Harian</h3>
            <p class="text-xs text-muted-foreground">Log kehadiran fingerprint & RFID</p>
          </div>
        </div>
        <p class="text-xs text-muted-foreground leading-relaxed">
          Pantau riwayat absensi harian dan status keterangan sakit, izin, atau alpa secara transparan.
        </p>
        <router-link
          :to="{ name: 'school.student.attendance' }"
          class="inline-block text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline"
        >
          Lihat Kalender Presensi &rarr;
        </router-link>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-4">
        <div class="flex items-center gap-3">
          <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-500">
            <Vote class="size-5" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-foreground">Pemilu & E-Voting OSIS</h3>
            <p class="text-xs text-muted-foreground">Bilik suara digital kesiswaan</p>
          </div>
        </div>
        <p class="text-xs text-muted-foreground leading-relaxed">
          Gunakan token pemilihan unik Anda untuk memberikan suara secara langsung di bilik suara digital.
        </p>
        <router-link
          :to="{ name: 'school.kiosk.voting' }"
          class="inline-block text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
        >
          Masuk Kiosk E-Voting &rarr;
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import {
  FileText,
  ShieldCheck,
  CalendarCheck,
  GraduationCap,
  Vote,
} from 'lucide-vue-next';
</script>
