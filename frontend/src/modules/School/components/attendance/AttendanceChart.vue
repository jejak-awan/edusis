<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between text-xs">
      <span class="font-bold text-foreground">Tren Kehadiran Bulanan</span>
      <div class="flex items-center gap-3 text-[11px]">
        <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 font-medium">
          <span class="size-2 rounded-full bg-emerald-500"></span> Hadir
        </span>
        <span class="flex items-center gap-1.5 text-amber-600 font-medium">
          <span class="size-2 rounded-full bg-amber-500"></span> Sakit
        </span>
        <span class="flex items-center gap-1.5 text-sky-600 font-medium">
          <span class="size-2 rounded-full bg-sky-500"></span> Izin
        </span>
        <span class="flex items-center gap-1.5 text-rose-600 font-medium">
          <span class="size-2 rounded-full bg-rose-500"></span> Alpa
        </span>
      </div>
    </div>

    <!-- Bar Chart Grid -->
    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 pt-2">
      <div
        v-for="item in monthlyData"
        :key="item.month"
        class="flex flex-col items-center gap-2"
      >
        <div class="relative w-full h-36 bg-muted/30 rounded-xl p-1 flex flex-col justify-end overflow-hidden border border-border/40">
          <!-- Stacked Bars -->
          <div
            class="w-full bg-rose-500/80 rounded-t-sm transition-all duration-500"
            :style="{ height: `${item.alpa}%` }"
            :title="`Alpa: ${item.alpa}%`"
          ></div>
          <div
            class="w-full bg-sky-500/80 transition-all duration-500"
            :style="{ height: `${item.permit}%` }"
            :title="`Izin: ${item.permit}%`"
          ></div>
          <div
            class="w-full bg-amber-500/80 transition-all duration-500"
            :style="{ height: `${item.sick}%` }"
            :title="`Sakit: ${item.sick}%`"
          ></div>
          <div
            class="w-full bg-emerald-500 rounded-b-lg transition-all duration-500"
            :style="{ height: `${item.present}%` }"
            :title="`Hadir: ${item.present}%`"
          ></div>
        </div>

        <div class="text-center space-y-0.5">
          <span class="text-[11px] font-bold font-mono text-foreground block">{{ item.present }}%</span>
          <span class="text-[10px] text-muted-foreground uppercase tracking-wider block">{{ item.month }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
export interface MonthlyAttendanceItem {
  month: string;
  present: number;
  sick: number;
  permit: number;
  alpa: number;
}

const props = withDefaults(
  defineProps<{
    monthlyData?: MonthlyAttendanceItem[];
  }>(),
  {
    monthlyData: () => [
      { month: 'Jul', present: 96, sick: 2, permit: 1, alpa: 1 },
      { month: 'Agu', present: 94, sick: 3, permit: 2, alpa: 1 },
      { month: 'Sep', present: 97, sick: 1, permit: 1, alpa: 1 },
      { month: 'Okt', present: 95, sick: 2, permit: 2, alpa: 1 },
      { month: 'Nov', present: 93, sick: 4, permit: 2, alpa: 1 },
      { month: 'Des', present: 98, sick: 1, permit: 1, alpa: 0 },
    ],
  }
);
</script>
