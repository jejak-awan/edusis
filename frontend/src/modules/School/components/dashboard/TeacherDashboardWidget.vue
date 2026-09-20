<template>
  <div class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-sm font-bold text-foreground flex items-center gap-2">
        <GraduationCap class="size-4 text-primary" />
        Ruang Kerja Pendidik
      </h3>
      <router-link to="/school/teacher/dashboard" class="text-xs text-primary hover:underline">
        Buka Portal &rarr;
      </router-link>
    </div>

    <p class="text-xs text-muted-foreground leading-relaxed">
      Akses cepat presensi kelas, jadwal mengajar harian, serta pencatatan jurnal mengajar semester berjalan.
    </p>

    <div class="grid grid-cols-2 gap-2 pt-1">
      <router-link
        to="/school/teacher/schedule"
        class="inline-flex items-center justify-center gap-1 rounded-lg border border-border bg-background py-2 text-xs font-medium text-foreground hover:bg-muted transition"
      >
        <Calendar class="size-3.5 text-primary" />
        Jadwal Mengajar
      </router-link>
      <router-link
        to="/school/teacher/journals"
        class="inline-flex items-center justify-center gap-1 rounded-lg bg-primary/10 py-2 text-xs font-semibold text-primary hover:bg-primary/20 transition"
      >
        <BookOpen class="size-3.5" />
        Entri Jurnal
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { GraduationCap, Calendar, BookOpen } from 'lucide-vue-next';
</script>
