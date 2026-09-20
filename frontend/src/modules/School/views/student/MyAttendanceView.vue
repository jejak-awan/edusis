<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link to="/school/student/dashboard" class="hover:text-foreground">Portal Siswa</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Presensi</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Log Presensi Siswa</h1>
        <p class="text-sm text-muted-foreground">Catatan riwayat kehadiran harian melalui mesin presensi dan verifikasi wali kelas.</p>
      </div>

      <div class="flex items-center gap-2">
        <select
          v-model="selectedMonth"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option :value="9">September 2026</option>
          <option :value="8">Agustus 2026</option>
          <option :value="7">Juli 2026</option>
        </select>
      </div>
    </div>

    <!-- Attendance Summary Metrics -->
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm text-center">
        <span class="text-xs text-muted-foreground">Hadir Tepat Waktu</span>
        <p class="mt-1 text-2xl font-bold text-emerald-500">21 Hari</p>
      </div>
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm text-center">
        <span class="text-xs text-muted-foreground">Sakit (Keterangan Dokter)</span>
        <p class="mt-1 text-2xl font-bold text-amber-500">1 Hari</p>
      </div>
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm text-center">
        <span class="text-xs text-muted-foreground">Izin (Permohonan Ortu)</span>
        <p class="mt-1 text-2xl font-bold text-blue-500">0 Hari</p>
      </div>
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm text-center">
        <span class="text-xs text-muted-foreground">Tanpa Keterangan (Alpa)</span>
        <p class="mt-1 text-2xl font-bold text-rose-500">0 Hari</p>
      </div>
    </div>

    <!-- Attendance History Log Table -->
    <div class="rounded-xl border border-border/60 bg-card shadow-sm overflow-hidden">
      <div class="border-b border-border/60 px-5 py-3 bg-muted/20">
        <h3 class="text-sm font-semibold text-foreground">Riwayat Kehadiran Bulan Berjalan</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border bg-muted/40 text-muted-foreground uppercase tracking-wider text-[11px]">
            <tr>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Hari</th>
              <th class="px-4 py-3 text-center">Waktu Masuk</th>
              <th class="px-4 py-3 text-center">Waktu Pulang</th>
              <th class="px-4 py-3 text-center">Status</th>
              <th class="px-4 py-3">Keterangan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-for="(log, idx) in attendanceLogs" :key="idx" class="hover:bg-muted/10 transition">
              <td class="px-4 py-3 font-medium text-foreground">{{ log.date }}</td>
              <td class="px-4 py-3 text-muted-foreground">{{ log.day }}</td>
              <td class="px-4 py-3 text-center font-mono">{{ log.check_in || '-' }}</td>
              <td class="px-4 py-3 text-center font-mono">{{ log.check_out || '-' }}</td>
              <td class="px-4 py-3 text-center">
                <span :class="['px-2.5 py-0.5 rounded-full text-[10px] font-bold', getStatusClass(log.status)]">
                  {{ getStatusLabel(log.status) }}
                </span>
              </td>
              <td class="px-4 py-3 text-muted-foreground">{{ log.notes || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const selectedMonth = ref(9);

const attendanceLogs = ref([
  { date: '2026-09-18', day: 'Jumat', check_in: '06:45', check_out: '15:30', status: 'present', notes: 'Tepat waktu' },
  { date: '2026-09-17', day: 'Kamis', check_in: '06:50', check_out: '15:30', status: 'present', notes: 'Tepat waktu' },
  { date: '2026-09-16', day: 'Rabu', check_in: '06:42', check_out: '15:30', status: 'present', notes: 'Tepat waktu' },
  { date: '2026-09-15', day: 'Selasa', check_in: '06:48', check_out: '15:30', status: 'present', notes: 'Tepat waktu' },
  { date: '2026-09-14', day: 'Senin', check_in: '06:40', check_out: '15:30', status: 'present', notes: 'Upacara bendera' },
  { date: '2026-09-11', day: 'Jumat', check_in: '-', check_out: '-', status: 'sick', notes: 'Surat dokter terverifikasi' },
]);

function getStatusLabel(status: string): string {
  switch (status) {
    case 'present': return 'Hadir';
    case 'sick': return 'Sakit';
    case 'permission': return 'Izin';
    case 'absent': return 'Alpa';
    default: return status;
  }
}

function getStatusClass(status: string): string {
  switch (status) {
    case 'present': return 'bg-emerald-500/10 text-emerald-500';
    case 'sick': return 'bg-amber-500/10 text-amber-500';
    case 'permission': return 'bg-blue-500/10 text-blue-500';
    case 'absent': return 'bg-rose-500/10 text-rose-500';
    default: return 'bg-muted text-muted-foreground';
  }
}
</script>
