<template>
  <div
    :class="[
      'relative flex flex-col justify-between rounded-2xl border-2 p-6 transition select-none cursor-pointer overflow-hidden',
      selected
        ? 'border-primary bg-primary/10 shadow-xl shadow-primary/20 ring-2 ring-primary/40'
        : 'border-border/60 bg-card hover:border-primary/40 hover:shadow-md'
    ]"
    @click="$emit('select', candidate)"
  >
    <!-- Number Badge -->
    <div class="absolute top-4 left-4 size-9 rounded-xl bg-primary text-primary-foreground font-black text-sm flex items-center justify-center shadow-sm">
      {{ formatNumber(candidate.candidate_number) }}
    </div>

    <!-- Vision/Mission Info Button -->
    <button
      type="button"
      class="absolute top-4 right-4 rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition"
      title="Lihat Visi & Misi"
      @click.stop="showDetailModal = true"
    >
      <Info class="size-4" />
    </button>

    <!-- Main Candidate Content -->
    <div class="my-4 text-center space-y-3">
      <!-- Photo / Avatar -->
      <div class="mx-auto size-28 rounded-2xl bg-muted overflow-hidden border-2 border-primary/20 shadow-inner flex items-center justify-center">
        <img
          v-if="candidate.photo_url || (candidate as any).photo"
          :src="candidate.photo_url || (candidate as any).photo"
          :alt="candidate.name"
          class="size-full object-cover"
        />
        <UserCheck v-else class="size-12 text-muted-foreground/50" />
      </div>

      <!-- Names -->
      <div>
        <h3 class="text-base font-bold text-foreground">
          {{ candidate.name }}
        </h3>
        <p v-if="candidate.vice_name" class="text-xs text-muted-foreground mt-0.5">
          Wakil: <span class="font-medium text-foreground">{{ candidate.vice_name }}</span>
        </p>
      </div>

      <!-- Tagline / Vision Preview -->
      <p class="text-xs text-muted-foreground line-clamp-2 italic px-2">
        "{{ candidate.vision || 'Mewujudkan kepengurusan OSIS yang berintegritas, mandiri, dan berkarakter.' }}"
      </p>
    </div>

    <!-- Vote Button / Indicator -->
    <button
      type="button"
      :class="[
        'w-full py-2.5 rounded-xl text-xs font-bold transition shadow-sm',
        selected
          ? 'bg-primary text-primary-foreground'
          : 'bg-muted text-muted-foreground hover:bg-muted/80 hover:text-foreground'
      ]"
    >
      {{ selected ? '✓ Terpilih (Klik Konfirmasi)' : 'Pilih Paslon Ini' }}
    </button>

    <!-- Vision & Mission Modal -->
    <div
      v-if="showDetailModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
      @click.self="showDetailModal = false"
    >
      <div class="w-full max-w-lg rounded-2xl border border-border/60 bg-card p-6 shadow-2xl text-left space-y-4">
        <div class="flex items-start justify-between border-b border-border/60 pb-3">
          <div>
            <span class="text-xs font-bold text-primary">PASLON NO. {{ formatNumber(candidate.candidate_number) }}</span>
            <h4 class="text-lg font-bold text-foreground mt-0.5">{{ candidate.name }}</h4>
            <p v-if="candidate.vice_name" class="text-xs text-muted-foreground">Wakil: {{ candidate.vice_name }}</p>
          </div>
          <button
            type="button"
            class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
            @click="showDetailModal = false"
          >
            <X class="size-4" />
          </button>
        </div>

        <!-- Vision -->
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Visi Pasangan Calon</span>
          <p class="text-xs font-medium text-foreground mt-1 leading-relaxed bg-muted/20 p-3 rounded-lg border border-border/60">
            {{ candidate.vision || 'Mewujudkan lingkungan sekolah yang unggul dan inklusif berlandaskan profil pelajar Pancasila.' }}
          </p>
        </div>

        <!-- Mission -->
        <div>
          <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Misi Pasangan Calon</span>
          <div class="text-xs text-foreground mt-1 leading-relaxed bg-muted/20 p-3 rounded-lg border border-border/60 whitespace-pre-line">
            {{ candidate.mission || '1. Meningkatkan partisipasi aktif siswa dalam kegiatan ekstrakurikuler.\n2. Mengembangkan sarana aspirasi siswa yang terbuka dan transparan.\n3. Menyelenggarakan kegiatan keagamaan dan sosial kemasyarakatan.' }}
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button
            type="button"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90 transition"
            @click="showDetailModal = false"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { UserCheck, Info, X } from 'lucide-vue-next';
import type { CandidateData } from '../../types/osis';

const props = defineProps<{
  candidate: CandidateData;
  selected?: boolean;
}>();

defineEmits<{
  (e: 'select', candidate: CandidateData): void;
}>();

const showDetailModal = ref(false);

function formatNumber(num?: number | string): string {
  if (!num) return '01';
  const n = Number(num);
  return n < 10 ? `0${n}` : `${n}`;
}
</script>
