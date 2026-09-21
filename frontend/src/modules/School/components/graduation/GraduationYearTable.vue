<template>
  <div class="rounded-2xl border border-border/60 bg-card shadow-sm overflow-hidden">
    <div class="border-b border-border/60 bg-muted/20 px-6 py-3.5 flex items-center justify-between">
      <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">
        Rekapitulasi Angkatan Kelulusan & Keterserapan Alumni
      </h3>
      <span class="text-xs text-muted-foreground font-mono">{{ cohorts.length }} Angkatan</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs whitespace-nowrap">
        <thead class="bg-muted/40 border-b border-border/60 text-[11px] font-semibold text-muted-foreground">
          <tr>
            <th class="py-3 px-4">Tahun Lulus</th>
            <th class="py-3 px-4 text-center">Total Alumni</th>
            <th class="py-3 px-3 text-center text-emerald-600">Bekerja</th>
            <th class="py-3 px-3 text-center text-indigo-600">Kuliah (PTN/PTS)</th>
            <th class="py-3 px-3 text-center text-amber-600">Wirausaha</th>
            <th class="py-3 px-3 text-center text-slate-500">Mencari Kerja</th>
            <th class="py-3 px-4 text-center">Tingkat Penyerapan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border/40">
          <tr
            v-for="item in cohorts"
            :key="item.year"
            class="hover:bg-muted/20 transition cursor-pointer"
            @click="$emit('select-year', item.year)"
          >
            <td class="py-3.5 px-4 font-mono font-bold text-foreground">{{ item.year }}</td>
            <td class="py-3.5 px-4 text-center font-mono font-bold">{{ item.total_graduates }} Orang</td>
            <td class="py-3.5 px-3 text-center font-mono text-emerald-600 font-semibold">{{ item.working }}</td>
            <td class="py-3.5 px-3 text-center font-mono text-indigo-600 font-semibold">{{ item.studying }}</td>
            <td class="py-3.5 px-3 text-center font-mono text-amber-600 font-semibold">{{ item.entrepreneur }}</td>
            <td class="py-3.5 px-3 text-center font-mono text-slate-500">{{ item.job_seeking }}</td>
            <td class="py-3.5 px-4">
              <div class="flex items-center justify-center gap-2">
                <div class="w-20 bg-muted rounded-full h-2 overflow-hidden">
                  <div
                    class="h-full bg-emerald-500 rounded-full"
                    :style="{ width: `${item.absorption_rate}%` }"
                  ></div>
                </div>
                <span class="font-mono font-bold text-xs text-emerald-600">{{ item.absorption_rate }}%</span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup lang="ts">
export interface GraduationCohort {
  year: string;
  total_graduates: number;
  working: number;
  studying: number;
  entrepreneur: number;
  job_seeking: number;
  absorption_rate: number;
}

const props = withDefaults(
  defineProps<{
    cohorts?: GraduationCohort[];
  }>(),
  {
    cohorts: () => [
      { year: '2025', total_graduates: 320, working: 180, studying: 95, entrepreneur: 32, job_seeking: 13, absorption_rate: 95.9 },
      { year: '2024', total_graduates: 310, working: 195, studying: 80, entrepreneur: 25, job_seeking: 10, absorption_rate: 96.8 },
      { year: '2023', total_graduates: 295, working: 190, studying: 75, entrepreneur: 22, job_seeking: 8, absorption_rate: 97.3 },
      { year: '2022', total_graduates: 280, working: 185, studying: 68, entrepreneur: 20, job_seeking: 7, absorption_rate: 97.5 },
    ],
  }
);

defineEmits<{
  (e: 'select-year', year: string): void;
}>();
</script>
