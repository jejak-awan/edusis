<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
    @click.self="$emit('update:open', false)"
  >
    <div class="w-full max-w-2xl rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-6">
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-border/60 pb-3">
        <div>
          <h3 class="text-base font-bold text-foreground flex items-center gap-2">
            <CreditCard class="size-4 text-primary" />
            Kartu Pemilih DPT Resmi
          </h3>
          <p class="text-xs text-muted-foreground">Format standar cetak kartu identitas bilik suara (85.6mm × 54mm).</p>
        </div>
        <div class="flex items-center gap-2">
          <button
            type="button"
            class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-muted transition"
            @click="handlePrint"
          >
            <Printer class="size-3.5" />
            Cetak Kartu
          </button>
          <button
            type="button"
            class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition"
            @click="$emit('update:open', false)"
          >
            <X class="size-4" />
          </button>
        </div>
      </div>

      <!-- Voter Card Layout (Standard ISO ID-1) -->
      <div class="flex justify-center p-4 bg-muted/30 rounded-xl overflow-x-auto">
        <div
          id="printable-voter-card"
          class="relative w-[85.6mm] h-[54mm] bg-gradient-to-br from-indigo-900 via-slate-900 to-indigo-950 text-white rounded-xl p-4 shadow-xl border border-indigo-400/30 flex flex-col justify-between overflow-hidden select-none shrink-0"
        >
          <!-- Watermark Background -->
          <div class="absolute -right-6 -bottom-6 size-36 rounded-full bg-primary/10 blur-xl pointer-events-none"></div>

          <!-- Card Top Bar -->
          <div class="flex items-center justify-between border-b border-white/10 pb-2">
            <div>
              <span class="text-[9px] font-black uppercase tracking-widest text-indigo-300 block">KARTU PEMILIH DPT</span>
              <span class="text-[10px] font-bold text-white block leading-tight">{{ eventName || 'PEMILIHAN OSIS' }}</span>
            </div>
            <div class="size-6 rounded-md bg-indigo-600 flex items-center justify-center font-black text-[10px] text-white">
              DPT
            </div>
          </div>

          <!-- Card Center (Student Info & Token) -->
          <div class="grid grid-cols-3 gap-2 items-center my-auto">
            <div class="col-span-2 space-y-1">
              <div>
                <span class="text-[8px] text-slate-400 block uppercase">Nama Pemilih</span>
                <span class="text-xs font-bold text-white block truncate leading-tight">{{ voter.name }}</span>
              </div>
              <div class="flex gap-3 text-[9px] text-slate-300">
                <div>
                  <span class="text-[8px] text-slate-400 block">NISN</span>
                  <span class="font-mono font-medium">{{ voter.nisn || '-' }}</span>
                </div>
                <div>
                  <span class="text-[8px] text-slate-400 block">Kelas</span>
                  <span class="font-medium">{{ voter.class_name || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Simulated QR Box with Token -->
            <div class="col-span-1 flex flex-col items-center justify-center text-center">
              <div class="size-16 rounded-lg bg-white p-1 flex items-center justify-center shadow-md">
                <!-- Fallback SVG QR Pattern -->
                <svg viewBox="0 0 24 24" class="size-full text-slate-900 fill-current">
                  <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 2h2v4h-2v-4zm-4-4h4v2h-4v-2zm6 0h2v2h-2v-2zm-6 4h2v2h-2v-2zm2 2h2v2h-2v-2z" />
                </svg>
              </div>
              <span class="text-[8px] font-mono tracking-widest text-indigo-300 font-bold mt-1">
                {{ voter.token || 'TOKEN-DPT' }}
              </span>
            </div>
          </div>

          <!-- Card Bottom Disclaimer -->
          <div class="border-t border-white/10 pt-1 flex items-center justify-between text-[7px] text-slate-400">
            <span>Berlaku 1x penggunaan suara</span>
            <span class="font-mono">STATUS: VALID</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { CreditCard, Printer, X } from 'lucide-vue-next';

export interface VoterCardData {
  id: number | string;
  name: string;
  nisn?: string;
  class_name?: string;
  token?: string;
}

const props = defineProps<{
  open: boolean;
  voter: VoterCardData;
  eventName?: string;
}>();

defineEmits<{
  (e: 'update:open', val: boolean): void;
}>();

function handlePrint() {
  window.print();
}
</script>
