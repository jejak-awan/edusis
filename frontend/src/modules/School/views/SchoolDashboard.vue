<template>
  <div class="space-y-6">
    <!-- Hero School Institution Banner -->
    <div class="relative overflow-hidden rounded-2xl border border-border/60 bg-gradient-to-br from-card via-card/90 to-primary/5 p-6 md:p-8 shadow-sm">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="space-y-2">
          <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-md bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">
              <GraduationCap class="size-3.5" />
              NPSN: {{ schoolInfo?.npsn || '20246452' }}
            </span>
            <span class="inline-flex items-center rounded-md bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
              Akreditasi {{ schoolInfo?.accreditation || 'A' }}
            </span>
            <span class="inline-flex items-center rounded-md bg-muted px-2.5 py-1 text-xs font-medium text-muted-foreground">
              {{ schoolInfo?.curriculum || 'Kurikulum Merdeka' }}
            </span>
          </div>
          <h1 class="text-2xl font-bold tracking-tight text-foreground md:text-3xl">
            {{ schoolInfo?.name || 'JA-EduSIS — Sekolah Menengah Kejuruan' }}
          </h1>
          <p class="text-sm text-muted-foreground max-w-2xl">
            {{ schoolInfo?.address || 'Sistem Informasi Sekolah Terintegrasi' }}
            <span v-if="schoolInfo?.kabupaten_kota">• {{ schoolInfo.kabupaten_kota }}</span>
          </p>
        </div>

        <div class="flex items-center gap-3 self-start md:self-auto">
          <div class="rounded-xl border border-border/80 bg-background/80 backdrop-blur px-4 py-2.5 text-right shadow-sm">
            <div class="text-xs text-muted-foreground">{{ t('school.dashboard.activeYear') }}</div>
            <div class="text-base font-bold text-foreground">
              {{ activeAcademicYear?.name || '2026/2027' }}
            </div>
            <div class="text-[11px] font-medium text-primary">
              {{ activeAcademicYear?.active_semester?.name || 'Semester Ganjil' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">{{ t('school.dashboard.totalStudents') }}</span>
          <div class="flex size-9 items-center justify-center rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
            <Users class="size-4" />
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-bold tracking-tight text-foreground">{{ metrics.total_students || 0 }}</span>
          <span class="text-xs text-muted-foreground">Siswa</span>
        </div>
        <div class="mt-2 text-xs text-muted-foreground flex gap-2">
          <span>L: {{ metrics.total_male || 0 }}</span>
          <span>•</span>
          <span>P: {{ metrics.total_female || 0 }}</span>
        </div>
      </div>

      <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">{{ t('school.dashboard.totalClasses') }}</span>
          <div class="flex size-9 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
            <Layers class="size-4" />
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-bold tracking-tight text-foreground">{{ metrics.total_study_groups || 0 }}</span>
          <span class="text-xs text-muted-foreground">Kelas Aktif</span>
        </div>
        <div class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
          <CheckCircle2 class="size-3" />
          <span>Rombel Siap KBM</span>
        </div>
      </div>

      <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">{{ t('school.dashboard.totalDepartments') }}</span>
          <div class="flex size-9 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400">
            <Compass class="size-4" />
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-bold tracking-tight text-foreground">{{ metrics.total_departments || 0 }}</span>
          <span class="text-xs text-muted-foreground">Kompetensi Keahlian</span>
        </div>
        <div class="mt-2 text-xs text-muted-foreground">
          PPLG, TJKT, DKV
        </div>
      </div>

      <div class="group relative overflow-hidden rounded-xl border border-border/60 bg-card p-5 shadow-sm transition hover:shadow-md">
        <div class="flex items-center justify-between">
          <span class="text-xs font-medium text-muted-foreground">{{ t('school.dashboard.avgCreditScore') }}</span>
          <div class="flex size-9 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
            <ShieldCheck class="size-4" />
          </div>
        </div>
        <div class="mt-3 flex items-baseline gap-2">
          <span class="text-2xl font-bold tracking-tight text-foreground">98.4</span>
          <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">/ 100 Poin</span>
        </div>
        <div class="mt-2 text-xs text-muted-foreground">
          Indeks Kedisiplinan Baik
        </div>
      </div>
    </div>

    <!-- Quick Action Shortcuts -->
    <div class="flex flex-wrap gap-3">
      <router-link
        to="/manage/school/students"
        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow-sm transition hover:bg-primary/90"
      >
        <UserPlus class="size-4" />
        {{ t('school.dashboard.registerStudent') }}
      </router-link>

      <router-link
        to="/manage/school/discipline"
        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground shadow-sm transition hover:bg-muted"
      >
        <ShieldAlert class="size-4 text-amber-500" />
        {{ t('school.dashboard.recordPoint') }}
      </router-link>

      <router-link
        to="/manage/school/academic"
        class="inline-flex items-center gap-2 rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground shadow-sm transition hover:bg-muted"
      >
        <BookOpen class="size-4 text-indigo-500" />
        {{ t('school.dashboard.manageClasses') }}
      </router-link>
    </div>

    <!-- Two Columns: Recent Violations vs Achievements -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Recent Violations Card -->
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-border/50">
          <div class="flex items-center gap-2">
            <div class="flex size-8 items-center justify-center rounded-lg bg-red-500/10 text-red-600 dark:text-red-400">
              <AlertTriangle class="size-4" />
            </div>
            <div>
              <h3 class="text-sm font-semibold text-foreground">{{ t('school.dashboard.recentViolations') }}</h3>
              <p class="text-xs text-muted-foreground">Pencatatan kredit poin perilaku siswa</p>
            </div>
          </div>
          <router-link to="/manage/school/discipline" class="text-xs font-medium text-primary hover:underline">
            Lihat Semua
          </router-link>
        </div>

        <div class="mt-4 space-y-3">
          <div
            v-for="item in recentViolations"
            :key="item.id"
            class="flex items-start justify-between rounded-lg border border-border/40 bg-muted/20 p-3 text-sm"
          >
            <div class="space-y-1">
              <div class="font-medium text-foreground">
                {{ item.student?.full_name || 'Siswa' }}
              </div>
              <div class="text-xs text-muted-foreground">
                {{ item.credit_point?.description || item.sanction || 'Pelanggaran tata tertib' }}
              </div>
              <div class="text-[11px] text-muted-foreground">
                {{ item.violation_date }} • Jurusan {{ item.student?.department?.code || '-' }}
              </div>
            </div>
            <div class="text-right">
              <span
                v-if="item.sp_level && item.sp_level !== 'none'"
                class="inline-flex rounded bg-red-500/10 px-2 py-0.5 text-xs font-semibold text-red-600 uppercase"
              >
                {{ item.sp_level }}
              </span>
              <span
                v-else
                class="inline-flex rounded bg-amber-500/10 px-2 py-0.5 text-xs font-medium text-amber-600"
              >
                Peringatan
              </span>
            </div>
          </div>

          <div v-if="!recentViolations.length" class="py-8 text-center text-xs text-muted-foreground">
            <CheckCircle2 class="mx-auto size-8 text-emerald-500/40 mb-2" />
            {{ t('school.dashboard.noViolations') }}
          </div>
        </div>
      </div>

      <!-- Recent Achievements Card -->
      <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-border/50">
          <div class="flex items-center gap-2">
            <div class="flex size-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600 dark:text-amber-400">
              <Award class="size-4" />
            </div>
            <div>
              <h3 class="text-sm font-semibold text-foreground">{{ t('school.dashboard.recentAchievements') }}</h3>
              <p class="text-xs text-muted-foreground">Apresiasi & penghargaan prestasi siswa</p>
            </div>
          </div>
          <router-link to="/manage/school/discipline" class="text-xs font-medium text-primary hover:underline">
            Lihat Semua
          </router-link>
        </div>

        <div class="mt-4 space-y-3">
          <div
            v-for="item in recentAchievements"
            :key="item.id"
            class="flex items-start justify-between rounded-lg border border-border/40 bg-muted/20 p-3 text-sm"
          >
            <div class="space-y-1">
              <div class="font-medium text-foreground">
                {{ item.title }}
              </div>
              <div class="text-xs text-muted-foreground">
                {{ item.student?.full_name || 'Siswa' }} • {{ item.rank || 'Peringkat Terbaik' }}
              </div>
              <div class="text-[11px] text-muted-foreground">
                Tingkat {{ item.level }} • {{ item.event_date }}
              </div>
            </div>
            <span class="inline-flex rounded bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-600">
              +Prestasi
            </span>
          </div>

          <div v-if="!recentAchievements.length" class="py-8 text-center text-xs text-muted-foreground">
            <Award class="mx-auto size-8 text-muted-foreground/30 mb-2" />
            {{ t('school.dashboard.noAchievements') }}
          </div>
        </div>
      </div>
    </div>

    <!-- Departments Summary Distribution -->
    <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm">
      <h3 class="text-sm font-semibold text-foreground mb-4">Distribusi Siswa per Program Keahlian</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div
          v-for="dept in departmentsSummary"
          :key="dept.id"
          class="rounded-lg border border-border/50 bg-muted/10 p-4 transition hover:bg-muted/20"
        >
          <div class="text-xs font-bold text-primary">{{ dept.code }}</div>
          <div class="text-sm font-semibold text-foreground mt-1">{{ dept.name }}</div>
          <div class="mt-3 flex items-baseline justify-between text-xs text-muted-foreground">
            <span>Jumlah Siswa</span>
            <span class="text-base font-bold text-foreground">{{ dept.students_count || 0 }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Role-Tailored Portal Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <TeacherDashboardWidget />
      <StudentDashboardWidget />
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  GraduationCap,
  Users,
  Layers,
  Compass,
  ShieldCheck,
  ShieldAlert,
  UserPlus,
  BookOpen,
  AlertTriangle,
  Award,
  CheckCircle2,
} from 'lucide-vue-next';
import { logger } from '@/shared/utils/logger';
import { SchoolService } from '../services/schoolService';
import TeacherDashboardWidget from '../components/dashboard/TeacherDashboardWidget.vue';
import StudentDashboardWidget from '../components/dashboard/StudentDashboardWidget.vue';

import type { SchoolInfo, AcademicYearData, DepartmentData } from '../types';

const { t } = useI18n();

interface DashboardSchoolInfo extends Partial<SchoolInfo> {
  accreditation?: string;
  curriculum?: string;
  kabupaten_kota?: string;
}

interface DashboardViolation {
  id: number;
  violation_date: string;
  sanction?: string | null;
  student?: { full_name: string; nisn?: string; department?: { code?: string } };
  credit_point?: { category?: { name: string }; points: number; description?: string };
  sp_level?: string;
}

interface DashboardAchievement {
  id: number;
  event_date: string;
  title: string;
  level?: string;
  rank?: string;
  student?: { full_name: string };
}

const schoolInfo = ref<DashboardSchoolInfo | null>(null);
const activeAcademicYear = ref<AcademicYearData | null>(null);
const metrics = ref<Record<string, number>>({});
const departmentsSummary = ref<DepartmentData[]>([]);
const recentViolations = ref<DashboardViolation[]>([]);
const recentAchievements = ref<DashboardAchievement[]>([]);

async function loadDashboard() {
  try {
    const res = await SchoolService.getDashboard();
    if (res.data?.success) {
      const d = res.data.data;
      schoolInfo.value = d.school;
      activeAcademicYear.value = d.active_academic_year;
      metrics.value = d.metrics || {};
      departmentsSummary.value = d.departments_summary || [];
      recentViolations.value = d.recent_violations || [];
      recentAchievements.value = d.recent_achievements || [];
    }
  } catch (err) {
    logger.error('[School:Dashboard] Failed to load school dashboard', err);
  }
}

onMounted(() => {
  loadDashboard();
});
</script>
