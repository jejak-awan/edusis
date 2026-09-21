<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Kelulusan & Penomoran SKL</h1>
        <p class="text-sm text-muted-foreground">
          Pengelolaan yudisium kelulusan tingkat akhir, penerbitan Surat Keterangan Lulus (SKL), dan transkrip nilai.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <router-link
          :to="{ name: 'school.graduation.alumni' }"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
        >
          <GraduationCap class="size-4 text-primary" />
          Direktori Alumni
        </router-link>

        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          @click="generateBatchSKL"
        >
          <FileText class="size-4" />
          Terbitkan SKL Massal
        </button>
      </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Calon Lulusan (Kelas Akhir)</span>
          <div class="text-2xl font-bold font-mono text-foreground mt-1">320 Siswa</div>
        </div>
        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <Users class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Dinyatakan Lulus (Yudisium)</span>
          <div class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">320 Siswa</div>
        </div>
        <div class="size-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Nomor SKL Terbit</span>
          <div class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400 mt-1">315 Terbit</div>
        </div>
        <div class="size-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <Award class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Transkrip Siap Unduh</span>
          <div class="text-2xl font-bold font-mono text-sky-600 dark:text-sky-400 mt-1">100%</div>
        </div>
        <div class="size-10 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center">
          <FileCheck class="size-5" />
        </div>
      </div>
    </div>

    <!-- Student Graduation Verification Table -->
    <div class="rounded-2xl border border-border/60 bg-card shadow-sm overflow-hidden">
      <div class="border-b border-border/60 bg-muted/20 px-6 py-3.5 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">
          Status Yudisium & Penomoran Dokumen SKL Siswa
        </h3>
        <span class="text-xs text-muted-foreground font-mono">Tahun Ajaran 2025/2026</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-muted/40 border-b border-border/60 text-[11px] font-semibold text-muted-foreground">
            <tr>
              <th class="py-3 px-4 w-12 text-center">No</th>
              <th class="py-3 px-4 min-w-[180px]">Nama Lengkap</th>
              <th class="py-3 px-4 w-28">NISN</th>
              <th class="py-3 px-4 w-32">Kelas Terakhir</th>
              <th class="py-3 px-4 min-w-[200px]">No. Register SKL</th>
              <th class="py-3 px-3 w-28 text-center">Status Yudisium</th>
              <th class="py-3 px-4 w-28 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr
              v-for="(row, idx) in candidateStudents"
              :key="row.id"
              class="hover:bg-muted/20 transition"
            >
              <td class="py-3.5 px-4 text-center font-mono text-muted-foreground">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-semibold text-foreground">{{ row.name }}</td>
              <td class="py-3.5 px-4 font-mono text-muted-foreground">{{ row.nisn }}</td>
              <td class="py-3.5 px-4 text-muted-foreground">{{ row.class_name }}</td>
              <td class="py-3.5 px-4 font-mono font-medium text-foreground">
                {{ row.certificate_number || 'Belum Terbit' }}
              </td>
              <td class="py-3.5 px-3 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                  LULUS
                </span>
              </td>
              <td class="py-3.5 px-4 text-center">
                <button
                  type="button"
                  class="rounded-lg border border-border px-2.5 py-1 text-[11px] font-semibold text-primary hover:bg-muted transition"
                  @click="printIndividualSKL(row)"
                >
                  Cetak SKL
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { GraduationCap, FileText, Users, CheckCircle2, Award, FileCheck } from 'lucide-vue-next';

interface CandidateGraduation {
  id: number;
  name: string;
  nisn: string;
  class_name: string;
  certificate_number: string;
  status: 'graduated' | 'pending';
}

const candidateStudents = ref<CandidateGraduation[]>([
  {
    id: 1,
    name: 'Muhammad Raditya',
    nisn: '0058291041',
    class_name: 'XII RPL 1',
    certificate_number: '421.5/084/SKL-SMK/2026',
    status: 'graduated',
  },
  {
    id: 2,
    name: 'Syifa Azzahra',
    nisn: '0058291042',
    class_name: 'XII RPL 1',
    certificate_number: '421.5/085/SKL-SMK/2026',
    status: 'graduated',
  },
  {
    id: 3,
    name: 'Fikri Haikal',
    nisn: '0058291043',
    class_name: 'XII RPL 2',
    certificate_number: '421.5/086/SKL-SMK/2026',
    status: 'graduated',
  },
  {
    id: 4,
    name: 'Zahra Amalia',
    nisn: '0058291044',
    class_name: 'XII RPL 2',
    certificate_number: '421.5/087/SKL-SMK/2026',
    status: 'graduated',
  },
]);

function generateBatchSKL() {
  alert('Penomoran SKL massal telah berhasil diperbarui dan disematkan hash digital.');
}

function printIndividualSKL(_student: CandidateGraduation) {
  window.print();
}
</script>
