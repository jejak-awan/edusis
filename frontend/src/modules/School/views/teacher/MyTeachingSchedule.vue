<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.teacher.dashboard' }" class="hover:text-foreground">Portal Guru</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Jadwal Mengajar</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Jadwal Mengajar Guru</h1>
        <p class="text-sm text-muted-foreground">Matriks agenda mengajar tatap muka mingguan terintegrasi ruang dan rombel.</p>
      </div>

      <div class="flex items-center gap-2">
        <select
          v-model="selectedDay"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option :value="0">Semua Hari</option>
          <option :value="1">Senin</option>
          <option :value="2">Selasa</option>
          <option :value="3">Rabu</option>
          <option :value="4">Kamis</option>
          <option :value="5">Jumat</option>
        </select>
      </div>
    </div>

    <!-- Timetable Grid -->
    <div v-if="loading" class="rounded-xl border border-border/60 bg-card p-12 text-center text-sm text-muted-foreground">
      Memuat jadwal mengajar...
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="schedule in filteredSchedules"
        :key="schedule.id"
        class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3 hover:border-primary/50 transition"
      >
        <div class="flex items-center justify-between">
          <span class="inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary">
            {{ getDayName(schedule.day_of_week) }}
          </span>
          <span class="text-xs font-medium text-muted-foreground">
            {{ schedule.start_time?.slice(0, 5) }} - {{ schedule.end_time?.slice(0, 5) }}
          </span>
        </div>

        <div>
          <h3 class="text-base font-bold text-foreground">{{ schedule.subject?.name || 'Mata Pelajaran' }}</h3>
          <p class="text-xs text-muted-foreground mt-0.5">Kode: {{ schedule.subject?.code || '-' }}</p>
        </div>

        <div class="border-t border-border/50 pt-3 flex items-center justify-between text-xs text-muted-foreground">
          <div>
            <span class="font-medium text-foreground">Kelas:</span> {{ schedule.study_group?.name || '-' }}
          </div>
          <div>
            <span class="font-medium text-foreground">Ruang:</span> {{ schedule.room?.name || 'Kelas' }}
          </div>
        </div>

        <div class="pt-2">
          <router-link
            :to="{ name: 'school.teacher.journals', query: { schedule_id: schedule.id } }"
            class="w-full inline-flex items-center justify-center gap-1.5 rounded-lg border border-border bg-background py-1.5 text-xs font-medium text-foreground hover:bg-muted transition"
          >
            <BookOpen class="size-3.5 text-primary" />
            Entri Jurnal Kelas Ini
          </router-link>
        </div>
      </div>

      <div
        v-if="filteredSchedules.length === 0"
        class="col-span-full rounded-xl border border-border/60 bg-card p-12 text-center text-sm text-muted-foreground"
      >
        Tidak ada jadwal mengajar pada hari yang dipilih.
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { BookOpen } from 'lucide-vue-next';
import { AcademicService } from '../../services/academicService';
import type { Schedule } from '../../services/types';

const schedules = ref<Schedule[]>([]);
const selectedDay = ref<number>(0);
const loading = ref(false);

const dayNames: Record<number, string> = {
  1: 'Senin',
  2: 'Selasa',
  3: 'Rabu',
  4: 'Kamis',
  5: 'Jumat',
  6: 'Sabtu',
  7: 'Minggu',
};

function getDayName(day: number): string {
  return dayNames[day] || `Hari ${day}`;
}

const filteredSchedules = computed(() => {
  if (selectedDay.value === 0) return schedules.value;
  return schedules.value.filter((s) => s.day_of_week === selectedDay.value);
});

async function loadSchedules() {
  loading.value = true;
  try {
    const res = await AcademicService.getSchedules();
    if (res.data?.success) {
      schedules.value = res.data.data || [];
    }
  } catch (err) {
    console.error('Failed to load schedules', err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadSchedules();
});
</script>
