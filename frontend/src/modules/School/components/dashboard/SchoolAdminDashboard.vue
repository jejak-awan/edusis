<template>
  <div class="space-y-6">
    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Total Siswa Aktif</span>
          <Users class="size-4 text-primary" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">{{ stats.active_students }}</p>
        <p class="text-xs text-muted-foreground mt-1">Terdaftar dalam rombel</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Pendidik & Tenaga Kependidikan</span>
          <UserCheck class="size-4 text-blue-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">{{ stats.staff_count }}</p>
        <p class="text-xs text-muted-foreground mt-1">Guru dan staf operasional</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Rombongan Belajar (Rombel)</span>
          <Layers class="size-4 text-emerald-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">{{ stats.study_groups }}</p>
        <p class="text-xs text-muted-foreground mt-1">Tingkat X, XI, dan XII</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Presensi Siswa Hari Ini</span>
          <CalendarCheck class="size-4 text-amber-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">96.8%</p>
        <p class="text-xs text-emerald-500 mt-1 font-medium">Tingkat kehadiran normal</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Users, UserCheck, Layers, CalendarCheck } from 'lucide-vue-next';

interface DashboardStats {
  active_students: number;
  staff_count: number;
  study_groups: number;
}

const props = defineProps<{
  stats?: DashboardStats;
}>();

const stats = computed<DashboardStats>(() => props.stats ?? {
  active_students: 842,
  staff_count: 56,
  study_groups: 24,
});
</script>
