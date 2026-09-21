<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
    @click.self="$emit('update:open', false)"
  >
    <div class="w-full max-w-md rounded-2xl border border-border/60 bg-card p-6 shadow-2xl text-center space-y-5">
      <!-- Modal Header -->
      <div class="flex items-start justify-between">
        <div class="text-left">
          <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
            <KeyRound class="size-5 text-primary" />
            Otentikasi Pemilih
          </h3>
          <p class="text-xs text-muted-foreground mt-0.5">
            Pindai QR code kartu DPT atau masukkan 8 digit token Anda.
          </p>
        </div>
        <button
          type="button"
          class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
          @click="$emit('update:open', false)"
        >
          <X class="size-4" />
        </button>
      </div>

      <!-- Mode Switch (Input Manual vs Scan QR) -->
      <div class="flex rounded-xl bg-muted/40 p-1 border border-border/40">
        <button
          type="button"
          :class="[
            'flex-1 py-1.5 rounded-lg text-xs font-semibold transition',
            mode === 'token' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
          ]"
          @click="mode = 'token'"
        >
          Input Token
        </button>
        <button
          type="button"
          :class="[
            'flex-1 py-1.5 rounded-lg text-xs font-semibold transition',
            mode === 'qr' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
          ]"
          @click="mode = 'qr'"
        >
          Scan Kamera QR
        </button>
      </div>

      <!-- Mode 1: Manual Input -->
      <div v-if="mode === 'token'" class="space-y-4 pt-2">
        <div>
          <input
            v-model="tokenInput"
            type="text"
            maxlength="8"
            placeholder="A7B9K2X4"
            class="h-14 w-full rounded-xl border-2 border-primary/40 bg-background text-center font-mono text-2xl font-bold uppercase tracking-[0.25em] text-foreground focus:outline-none focus:border-primary"
            @keyup.enter="handleSubmit"
          />
        </div>

        <div v-if="errorMessage" class="text-xs text-destructive font-medium">
          {{ errorMessage }}
        </div>

        <button
          type="button"
          :disabled="tokenInput.trim().length < 6 || loading"
          class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-primary text-xs font-bold text-primary-foreground shadow-lg shadow-primary/20 hover:bg-primary/90 transition disabled:opacity-40"
          @click="handleSubmit"
        >
          <div v-if="loading" class="size-4 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></div>
          <Vote v-else class="size-4" />
          Buka Surat Suara
        </button>
      </div>

      <!-- Mode 2: QR Scanner -->
      <div v-else class="pt-2">
        <QrScanner
          @scan="handleQrDetected"
          @error="handleScanError"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { KeyRound, X, Vote } from 'lucide-vue-next';
import QrScanner from '../shared/QrScanner.vue';

const props = defineProps<{
  open: boolean;
  loading?: boolean;
  errorMessage?: string | null;
}>();

const emit = defineEmits<{
  (e: 'update:open', val: boolean): void;
  (e: 'submit', token: string): void;
}>();

const mode = ref<'token' | 'qr'>('token');
const tokenInput = ref('');

function handleSubmit() {
  if (tokenInput.value.trim().length >= 6) {
    emit('submit', tokenInput.value.trim().toUpperCase());
  }
}

function handleQrDetected(scannedToken: string) {
  tokenInput.value = scannedToken.toUpperCase();
  emit('submit', tokenInput.value);
}

function handleScanError(_err: string) {
  // Silent or switch to manual input
}
</script>
