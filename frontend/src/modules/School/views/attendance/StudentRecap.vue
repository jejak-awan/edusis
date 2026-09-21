<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.attendance' }" class="hover:text-foreground">Presensi</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Rekapitulasi Individu</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Rekap Presensi Per Siswa</h1>
        <p class="text-sm text-muted-foreground">
          Buku riwayat kehadiran individual siswa, catatan ketidakhadiran, surat izin/sakit, dan rekapitulasi semester.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <router-link
          :to="{ name: 'school.attendance' }"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
        >
          <ArrowLeft class="size-4" />
          Kembali
        </router-link>

        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
          @click="handlePrint"
        >
          <Printer class="size-4" />
          Cetak Kartu Absensi
        </button>
      </div>
    </div>

    <!-- Student Search Toolbar -->
    <div class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm space-y-3">
      <label class="block text-xs font-semibold text-foreground">
        Cari Siswa Berdasarkan Nama atau NISN
      </label>
      <div class="flex gap-3">
        <div class="relative flex-1">
          <Search class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Ketik nama siswa atau NISN..."
            class="h-10 w-full rounded-xl border border-border bg-background pl-10 pr-4 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            @keyup.enter="handleSearch"
          />
        </div>
        <button
          type="button"
          class="rounded-xl bg-primary px-5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          @click="handleSearch"
        >
          Cari
        </button>
      </div>
    </div>

    <!-- Student Profile & Attendance Overview -->
    <div v-if="selectedStudent" class="space-y-6">
      <!-- Profile Banner -->
      <div class="rounded-2xl border border-border/60 bg-card p-6 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="size-16 rounded-2xl bg-primary/10 text-primary font-bold text-xl flex items-center justify-center uppercase">
            {{ selectedStudent.full_name?.substring(0, 2) || 'ST' }}
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-xl font-bold text-foreground">{{ selectedStudent.full_name }}</h2>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                Aktif
              </span>
            </div>
            <div class="flex flex-wrap gap-4 text-xs text-muted-foreground mt-1">
              <span>NISN: <span class="font-mono font-medium text-foreground">{{ selectedStudent.nisn || '-' }}</span></span>
              <span>NIS: <span class="font-mono font-medium text-foreground">{{ selectedStudent.nis || '-' }}</span></span>
              <span>Kelas: <span class="font-medium text-foreground">{{ selectedStudent.study_group?.name || 'Rombel A' }}</span></span>
            </div>
          </div>
        </div>

        <div class="text-right">
          <span class="text-xs text-muted-foreground block">Persentase Kehadiran</span>
          <div class="text-3xl font-black font-mono text-emerald-600 dark:text-emerald-400">97.8%</div>
        </div>
      </div>

      <!-- Stats Counters -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm text-center">
          <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Hadir Efektif</span>
          <div class="text-2xl font-bold font-mono text-emerald-600 mt-1">88 Hari</div>
        </div>
        <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm text-center">
          <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Sakit (S)</span>
          <div class="text-2xl font-bold font-mono text-amber-600 mt-1">1 Hari</div>
        </div>
        <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm text-center">
          <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Izin (I)</span>
          <div class="text-2xl font-bold font-mono text-sky-600 mt-1">1 Hari</div>
        </div>
        <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm text-center">
          <span class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">Alpa / Tanpa Ket (A)</span>
          <div class="text-2xl font-bold font-mono text-rose-600 mt-1">0 Hari</div>
        </div>
      </div>

      <!-- Absence History Log -->
      <div class="rounded-2xl border border-border/60 bg-card shadow-sm overflow-hidden">
        <div class="border-b border-border/60 bg-muted/20 px-6 py-3.5">
          <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">
            Riwayat Surat Izin & Ketidakhadiran
          </h3>
        </div>

        <div class="divide-y divide-border/40 text-xs">
          <div class="p-4 flex items-center justify-between hover:bg-muted/20 transition">
            <div class="flex items-center gap-3">
              <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/10 text-amber-600 border border-amber-500/20">
                Sakit
              </span>
              <div>
                <span class="font-medium text-foreground block">Senin, 15 September 2025</span>
                <span class="text-muted-foreground text-[11px]">Keterangan: Demam tinggi disertai surat dokter Puskesmas.</span>
              </div>
            </div>
            <span class="text-muted-foreground font-mono text-[11px]">Surat Terverifikasi</span>
          </div>

          <div class="p-4 flex items-center justify-between hover:bg-muted/20 transition">
            <div class="flex items-center gap-3">
              <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-500/10 text-sky-600 border border-sky-500/20">
                Izin
              </span>
              <div>
                <span class="font-medium text-foreground block">Kamis, 21 Agustus 2025</span>
                <span class="text-muted-foreground text-[11px]">Keterangan: Menghadiri pernikahan saudara kandung di luar kota.</span>
              </div>
            </div>
            <span class="text-muted-foreground font-mono text-[11px]">Surat Orang Tua</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { ArrowLeft, Printer, Search } from 'lucide-vue-next';
import { StudentService } from '../../services/studentService';
import type { Student } from '../../types';

const searchQuery = ref('');
const selectedStudent = ref<Student | null>(null);

async function handleSearch() {
  if (!searchQuery.value.trim()) return;

  try {
    const res = await StudentService.getStudents({ search: searchQuery.value.trim(), per_page: 1 });
    const list = res.data?.data?.data || (Array.isArray(res.data?.data) ? res.data.data : []);
    if (list.length > 0 && list[0]) {
      selectedStudent.value = list[0];
    }
  } catch {
    // Silent
  }
}

function handlePrint() {
  window.print();
}

onMounted(async () => {
  // Load default top student for demonstration
  try {
    const res = await StudentService.getStudents({ per_page: 1 });
    const list = res.data?.data?.data || (Array.isArray(res.data?.data) ? res.data.data : []);
    if (list.length > 0 && list[0]) {
      selectedStudent.value = list[0];
    }
  } catch {
    // Non-blocking
  }
});
</script>
