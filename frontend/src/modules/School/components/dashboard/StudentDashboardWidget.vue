<template>
  <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-bold text-foreground flex items-center gap-2">
        <UserCheck class="size-4 text-emerald-500" />
        Portal Siswa & Buku Raport
      </h3>
      <router-link :to="{ name: 'school.student.dashboard' }" class="text-xs text-primary hover:underline">
        Buka Portal &rarr;
      </router-link>
    </div>

    <p class="text-xs text-muted-foreground leading-relaxed">
      Akses mandiri capaian hasil belajar, buku raport Merdeka, log presensi, serta bilik suara e-voting OSIS.
    </p>

    <div class="grid grid-cols-2 gap-2 pt-1">
      <router-link
        :to="{ name: 'school.student.reports' }"
        class="inline-flex items-center justify-center gap-1 rounded-lg border border-border bg-background py-2 text-xs font-medium text-foreground hover:bg-muted transition"
      >
        <FileText class="size-3.5 text-primary" />
        Raport Digital
      </router-link>
      <router-link
        :to="{ name: 'school.student.attendance' }"
        class="inline-flex items-center justify-center gap-1 rounded-lg bg-emerald-500/10 py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20 transition"
      >
        <CalendarCheck class="size-3.5" />
        Presensi Saya
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { UserCheck, FileText, CalendarCheck } from 'lucide-vue-next';
</script>
