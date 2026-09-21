<template>
  <div class="space-y-6">
    <div v-if="!results || results.length === 0" class="rounded-2xl border border-dashed border-border p-12 text-center text-xs text-muted-foreground">
      <Vote class="size-10 mx-auto mb-2 opacity-30 text-primary" />
      Belum ada data suara masuk pada pemilihan ini.
    </div>

    <div v-else class="space-y-4">
      <!-- Candidate Result Bars -->
      <div
        v-for="(item, idx) in sortedResults"
        :key="item.candidate_id"
        class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm space-y-3 transition hover:border-primary/40"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span
              :class="[
                'size-8 rounded-xl font-black text-xs flex items-center justify-center shadow-sm',
                idx === 0
                  ? 'bg-amber-500 text-white'
                  : 'bg-primary/10 text-primary border border-primary/20'
              ]"
            >
              #0{{ item.candidate_number || idx + 1 }}
            </span>
            <div>
              <div class="flex items-center gap-2">
                <h4 class="text-sm font-bold text-foreground">{{ item.candidate_name }}</h4>
                <span
                  v-if="idx === 0 && item.votes > 0"
                  class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-bold text-amber-600 dark:text-amber-400 border border-amber-500/20"
                >
                  <Trophy class="size-3" /> Unggul Sementara
                </span>
              </div>
              <p v-if="item.vice_name" class="text-xs text-muted-foreground">Wakil: {{ item.vice_name }}</p>
            </div>
          </div>

          <div class="text-right">
            <div class="text-lg font-black font-mono text-primary">
              {{ item.percentage.toFixed(1) }}%
            </div>
            <div class="text-[11px] font-medium text-muted-foreground">
              {{ formatNumber(item.votes) }} Suara Masuk
            </div>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="relative w-full bg-muted/60 rounded-full h-3 overflow-hidden">
          <div
            :class="[
              'h-full rounded-full transition-all duration-700 ease-out',
              idx === 0 ? 'bg-gradient-to-r from-amber-500 to-indigo-600' : 'bg-primary'
            ]"
            :style="{ width: `${Math.max(item.percentage, 1)}%` }"
          ></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Vote, Trophy } from 'lucide-vue-next';

export interface CandidateVoteResult {
  candidate_id: number | string;
  candidate_name: string;
  vice_name?: string;
  candidate_number?: number | string;
  votes: number;
  percentage: number;
}

const props = defineProps<{
  results: CandidateVoteResult[];
}>();

const sortedResults = computed(() => {
  return [...props.results].sort((a, b) => b.votes - a.votes);
});

function formatNumber(val: number): string {
  return new Intl.NumberFormat('id-ID').format(val);
}
</script>
