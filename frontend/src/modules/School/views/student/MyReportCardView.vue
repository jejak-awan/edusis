<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link to="/school/student/dashboard" class="hover:text-foreground">Portal Siswa</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Buku Raport</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Buku Raport Digital</h1>
        <p class="text-sm text-muted-foreground">Lembar hasil belajar peserta didik berstandar Kurikulum Merdeka.</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="printRaport"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
        >
          <Printer class="size-4" />
          Cetak Lembar Raport
        </button>
      </div>
    </div>

    <!-- Student Info Card -->
    <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
      <div>
        <span class="text-muted-foreground">Nama Peserta Didik:</span>
        <p class="font-bold text-foreground mt-0.5 text-sm">{{ reportData.student_name }}</p>
      </div>
      <div>
        <span class="text-muted-foreground">NISN / NIS:</span>
        <p class="font-bold text-foreground mt-0.5 text-sm">{{ reportData.nisn }} / {{ reportData.nis }}</p>
      </div>
      <div>
        <span class="text-muted-foreground">Kelas / Rombel:</span>
        <p class="font-bold text-foreground mt-0.5 text-sm">{{ reportData.study_group }}</p>
      </div>
      <div>
        <span class="text-muted-foreground">Semester / Tahun:</span>
        <p class="font-bold text-foreground mt-0.5 text-sm">Ganjil / 2026-2027</p>
      </div>
    </div>

    <!-- Subjects Grades Table -->
    <div class="rounded-xl border border-border/60 bg-card shadow-sm overflow-hidden">
      <div class="border-b border-border/60 px-5 py-3 bg-muted/20">
        <h3 class="text-sm font-semibold text-foreground">A. Nilai Capaian Kompetensi Mata Pelajaran</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border bg-muted/40 text-muted-foreground uppercase tracking-wider text-[11px]">
            <tr>
              <th class="px-4 py-3 w-12 text-center">No</th>
              <th class="px-4 py-3">Mata Pelajaran</th>
              <th class="px-4 py-3 w-24 text-center">Nilai Akhir</th>
              <th class="px-4 py-3 w-28 text-center">Predikat</th>
              <th class="px-4 py-3">Capaian Kompetensi Tertinggi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-for="(sub, idx) in reportData.subjects" :key="idx" class="hover:bg-muted/10 transition">
              <td class="px-4 py-3 text-center font-medium text-muted-foreground">{{ idx + 1 }}</td>
              <td class="px-4 py-3 font-semibold text-foreground">{{ sub.name }}</td>
              <td class="px-4 py-3 text-center font-bold text-primary">{{ sub.score }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="['px-2 py-0.5 rounded text-[10px] font-bold', sub.score >= 85 ? 'bg-emerald-500/10 text-emerald-500' : 'bg-primary/10 text-primary']">
                  {{ sub.score >= 85 ? 'Sangat Baik' : 'Baik' }}
                </span>
              </td>
              <td class="px-4 py-3 text-muted-foreground text-xs leading-relaxed">{{ sub.description }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Attendance Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3">
        <h3 class="text-sm font-semibold text-foreground">B. Rekapitulasi Kehadiran</h3>
        <div class="divide-y divide-border/40 text-xs">
          <div class="flex justify-between py-2">
            <span class="text-muted-foreground">Sakit (S)</span>
            <span class="font-bold text-foreground">{{ reportData.attendance.sick }} Hari</span>
          </div>
          <div class="flex justify-between py-2">
            <span class="text-muted-foreground">Izin (I)</span>
            <span class="font-bold text-foreground">{{ reportData.attendance.permission }} Hari</span>
          </div>
          <div class="flex justify-between py-2">
            <span class="text-muted-foreground">Tanpa Keterangan (A)</span>
            <span class="font-bold text-foreground">{{ reportData.attendance.absent }} Hari</span>
          </div>
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3">
        <h3 class="text-sm font-semibold text-foreground">C. Catatan Wali Kelas</h3>
        <p class="text-xs text-muted-foreground leading-relaxed italic bg-muted/20 p-3 rounded-lg border border-border/40">
          "Pertahankan prestasi yang sangat baik ini. Tingkatkan keaktifan dalam kegiatan praktikum kolaboratif dan penguatan karakter Profil Pelajar Pancasila."
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { Printer } from 'lucide-vue-next';

const reportData = ref({
  student_name: 'Muhammad Fauzan',
  nisn: '0061234567',
  nis: '23241001',
  study_group: 'XII Rekayasa Perangkat Lunak 1',
  subjects: [
    { name: 'Pendidikan Agama dan Budi Pekerti', score: 88, description: 'Menunjukkan penguasaan sangat baik dalam memahami toleransi antarumat beragama.' },
    { name: 'Pendidikan Pancasila', score: 86, description: 'Menunjukkan kemampuan tinggi dalam menerapkan norma hukum dan konstitusi.' },
    { name: 'Bahasa Indonesia', score: 90, description: 'Sangat mahir dalam menyusun teks eksposisi dan artikel ilmiah populer.' },
    { name: 'Matematika Terapan', score: 84, description: 'Menguasai konsep aljabar matriks dan transformasi geometri.' },
    { name: 'Konsentrasi Keahlian RPL (Backend & DB)', score: 94, description: 'Sangat unggul dalam arsitektur mikroservis, perancangan basis data relasional, dan API enterprise.' },
  ],
  attendance: {
    sick: 1,
    permission: 0,
    absent: 0,
  },
});

function printRaport() {
  window.print();
}
</script>
