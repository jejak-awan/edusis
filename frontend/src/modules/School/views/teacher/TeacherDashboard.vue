<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Portal Pendidik</h1>
        <p class="text-sm text-muted-foreground">Selamat datang di ruang kerja guru. Pantau jadwal mengajar, presensi kelas, dan pengisian jurnal hari ini.</p>
      </div>

      <div class="flex items-center gap-2">
        <router-link
          to="/school/teacher/journals"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          <BookOpen class="size-4" />
          Isi Jurnal Mengajar
        </router-link>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Jadwal Mengajar Hari Ini</span>
          <Calendar class="size-4 text-primary" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">{{ todaySchedules.length }} Sesi</p>
        <p class="text-xs text-muted-foreground mt-1">{{ currentDayName }}, {{ currentDateFormatted }}</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Jurnal Terisi Bulan Ini</span>
          <CheckCircle2 class="size-4 text-emerald-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">{{ journalsCount }} Pertemuan</p>
        <p class="text-xs text-muted-foreground mt-1">Status kelengkapan administrasi</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Rata-rata Kehadiran Siswa</span>
          <Users class="size-4 text-blue-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">96.4%</p>
        <p class="text-xs text-emerald-500 mt-1 font-medium">+1.2% dari pekan lalu</p>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">Penilaian Aktif</span>
          <Award class="size-4 text-amber-500" />
        </div>
        <p class="mt-2 text-2xl font-bold text-foreground">3 TP</p>
        <p class="text-xs text-muted-foreground mt-1">Tujuan Pembelajaran aktif</p>
      </div>
    </div>

    <!-- Main Content: Schedule Today & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left 2 Cols: Today's Schedule -->
      <div class="lg:col-span-2 space-y-4">
        <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
              <Clock class="size-4 text-primary" />
              Agenda & Jadwal Hari Ini
            </h2>
            <router-link to="/school/teacher/schedule" class="text-xs text-primary hover:underline">
              Lihat Seluruh Jadwal &rarr;
            </router-link>
          </div>

          <div v-if="loading" class="py-8 text-center text-xs text-muted-foreground">
            Memuat jadwal mengajar...
          </div>

          <div v-else-if="todaySchedules.length === 0" class="py-8 text-center text-xs text-muted-foreground">
            Tidak ada jadwal tatap muka mengajar hari ini.
          </div>

          <div v-else class="space-y-3">
            <div
              v-for="(item, idx) in todaySchedules"
              :key="idx"
              class="flex items-center justify-between rounded-lg border border-border/60 bg-muted/20 p-4 transition hover:bg-muted/40"
            >
              <div class="flex items-center gap-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary font-bold text-xs">
                  {{ item.start_time?.slice(0, 5) }}
                </div>
                <div>
                  <h4 class="text-sm font-semibold text-foreground">{{ item.subject?.name || 'Mata Pelajaran' }}</h4>
                  <p class="text-xs text-muted-foreground">
                    Kelas {{ item.study_group?.name || '-' }} &bull; Ruang {{ item.room?.name || 'Kelas' }}
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <router-link
                  :to="`/school/teacher/journals?schedule_id=${item.id}`"
                  class="rounded-md border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-muted transition"
                >
                  Buka Jurnal
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Col: Quick Links & Alerts -->
      <div class="space-y-6">
        <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4">
          <h2 class="text-base font-semibold text-foreground flex items-center gap-2">
            <BookOpen class="size-4 text-primary" />
            Menu Cepat Guru
          </h2>

          <div class="grid grid-cols-1 gap-2">
            <router-link
              to="/school/teacher/schedule"
              class="flex items-center justify-between p-3 rounded-lg border border-border/60 hover:bg-muted/30 transition text-xs font-medium text-foreground"
            >
              <span class="flex items-center gap-2">
                <Calendar class="size-4 text-primary" />
                Jadwal Mingguan Saya
              </span>
              <span>&rarr;</span>
            </router-link>

            <router-link
              to="/school/teacher/journals"
              class="flex items-center justify-between p-3 rounded-lg border border-border/60 hover:bg-muted/30 transition text-xs font-medium text-foreground"
            >
              <span class="flex items-center gap-2">
                <BookOpen class="size-4 text-emerald-500" />
                Catatan Jurnal Mengajar
              </span>
              <span>&rarr;</span>
            </router-link>

            <router-link
              to="/school/attendance"
              class="flex items-center justify-between p-3 rounded-lg border border-border/60 hover:bg-muted/30 transition text-xs font-medium text-foreground"
            >
              <span class="flex items-center gap-2">
                <Users class="size-4 text-blue-500" />
                Presensi Siswa Harian
              </span>
              <span>&rarr;</span>
            </router-link>

            <router-link
              to="/school/grades"
              class="flex items-center justify-between p-3 rounded-lg border border-border/60 hover:bg-muted/30 transition text-xs font-medium text-foreground"
            >
              <span class="flex items-center gap-2">
                <Award class="size-4 text-amber-500" />
                Input Nilai & Asesmen
              </span>
              <span>&rarr;</span>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import {
  Calendar,
  Clock,
  BookOpen,
  Users,
  Award,
  CheckCircle2,
} from 'lucide-vue-next';
import { AcademicService } from '../../services/academicService';
import type { Schedule } from '../../services/types';

const loading = ref(false);
const todaySchedules = ref<Schedule[]>([]);
const journalsCount = ref(18);

const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const now = new Date();
const currentDayName = computed(() => days[now.getDay()]);
const currentDateFormatted = computed(() =>
  now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
);

async function loadSchedules() {
  loading.value = true;
  try {
    const dayOfWeek = now.getDay();
    const res = await AcademicService.getSchedules({ day_of_week: dayOfWeek });
    if (res.data?.success) {
      todaySchedules.value = res.data.data || [];
    }
  } catch (err) {
    console.error('Failed to load teacher schedules', err);
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadSchedules();
});
</script>
