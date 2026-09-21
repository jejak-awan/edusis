<template>
  <div class="relative w-full max-w-sm mx-auto overflow-hidden rounded-2xl border border-border/60 bg-black/90 p-4 shadow-xl text-white">
    <!-- Camera Header -->
    <div class="flex items-center justify-between pb-3 mb-2 border-b border-white/10 text-xs">
      <div class="flex items-center gap-2">
        <Camera class="size-4 text-primary" />
        <span class="font-semibold text-white">Pemindai QR Code Pemilih</span>
      </div>
      <button
        v-if="hasMultipleCameras"
        type="button"
        class="rounded p-1 text-white/70 hover:bg-white/10 hover:text-white transition"
        @click="switchCamera"
        title="Ganti Kamera"
      >
        <RefreshCw class="size-3.5" />
      </button>
    </div>

    <!-- Video Feed with Scanning Reticle -->
    <div class="relative aspect-square w-full rounded-xl overflow-hidden bg-slate-950 flex items-center justify-center border border-white/10">
      <video
        ref="videoRef"
        autoplay
        playsinline
        muted
        class="size-full object-cover"
      ></video>

      <!-- Scanning Overlay Frame -->
      <div class="pointer-events-none absolute inset-0 flex items-center justify-center p-8">
        <div class="relative size-48 rounded-xl border-2 border-primary/80 shadow-[0_0_20px_rgba(99,102,241,0.5)] flex items-center justify-center">
          <!-- Laser Scan Line Animation -->
          <div class="absolute inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-primary to-transparent animate-bounce"></div>
          
          <!-- Corner Accents -->
          <div class="absolute top-0 left-0 size-3 border-t-2 border-l-2 border-primary"></div>
          <div class="absolute top-0 right-0 size-3 border-t-2 border-r-2 border-primary"></div>
          <div class="absolute bottom-0 left-0 size-3 border-b-2 border-l-2 border-primary"></div>
          <div class="absolute bottom-0 right-0 size-3 border-b-2 border-r-2 border-primary"></div>
        </div>
      </div>

      <!-- Camera Error State -->
      <div v-if="cameraError" class="absolute inset-0 bg-slate-900/95 flex flex-col items-center justify-center p-6 text-center space-y-2">
        <AlertCircle class="size-8 text-rose-400" />
        <p class="text-xs font-semibold text-white">{{ cameraError }}</p>
        <p class="text-[11px] text-slate-400">Pastikan izin kamera diberikan atau gunakan input manual token di bawah.</p>
        <button
          type="button"
          class="mt-2 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary/90 transition"
          @click="startCamera"
        >
          Coba Lagi
        </button>
      </div>
    </div>

    <!-- Manual Token Fallback Input -->
    <div class="mt-4 pt-3 border-t border-white/10 space-y-2">
      <div class="text-[11px] text-slate-400 text-center">Atau masukkan 8-karakter token manual:</div>
      <div class="flex gap-2">
        <input
          v-model="manualToken"
          type="text"
          maxlength="16"
          placeholder="A7B9K2X4"
          class="h-9 w-full rounded-lg bg-white/10 border border-white/20 px-3 text-center font-mono text-xs uppercase tracking-widest text-white placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-primary"
          @keyup.enter="submitManualToken"
        />
        <button
          type="button"
          :disabled="!manualToken.trim()"
          class="shrink-0 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary/90 transition disabled:opacity-40"
          @click="submitManualToken"
        >
          Kirim
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Camera, RefreshCw, AlertCircle } from 'lucide-vue-next';

const emit = defineEmits<{
  (e: 'scan', token: string): void;
  (e: 'error', message: string): void;
}>();

const videoRef = ref<HTMLVideoElement | null>(null);
const cameraError = ref<string | null>(null);
const manualToken = ref('');
const hasMultipleCameras = ref(false);
const currentFacingMode = ref<'environment' | 'user'>('environment');
let streamInstance: MediaStream | null = null;
let animationFrameId: number | null = null;

async function startCamera() {
  cameraError.value = null;
  stopCamera();

  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      cameraError.value = 'Perangkat tidak mendukung akses kamera langsung.';
      return;
    }

    const devices = await navigator.mediaDevices.enumerateDevices();
    const videoDevices = devices.filter(d => d.kind === 'videoinput');
    hasMultipleCameras.value = videoDevices.length > 1;

    const constraints: MediaStreamConstraints = {
      video: {
        facingMode: { ideal: currentFacingMode.value },
        width: { ideal: 640 },
        height: { ideal: 640 },
      },
    };

    streamInstance = await navigator.mediaDevices.getUserMedia(constraints);
    if (videoRef.value) {
      videoRef.value.srcObject = streamInstance;
      await videoRef.value.play();
      startBarcodeDetection();
    }
  } catch (err: any) {
    cameraError.value = err.name === 'NotAllowedError'
      ? 'Izin kamera ditolak oleh peramban.'
      : 'Gagal menginisialisasi kamera video.';
    emit('error', cameraError.value);
  }
}

function stopCamera() {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
    animationFrameId = null;
  }
  if (streamInstance) {
    streamInstance.getTracks().forEach(track => track.stop());
    streamInstance = null;
  }
}

function switchCamera() {
  currentFacingMode.value = currentFacingMode.value === 'environment' ? 'user' : 'environment';
  startCamera();
}

function startBarcodeDetection() {
  // Use BarcodeDetector API if supported in browser
  if ('BarcodeDetector' in window) {
    const barcodeDetector = new (window as any).BarcodeDetector({ formats: ['qr_code'] });
    const detect = async () => {
      if (videoRef.value && videoRef.value.readyState === videoRef.value.HAVE_ENOUGH_DATA) {
        try {
          const barcodes = await barcodeDetector.detect(videoRef.value);
          if (barcodes.length > 0) {
            const raw = barcodes[0].rawValue;
            handleDetectedCode(raw);
            return;
          }
        } catch {
          // Silent frame skip
        }
      }
      animationFrameId = requestAnimationFrame(detect);
    };
    animationFrameId = requestAnimationFrame(detect);
  }
}

function handleDetectedCode(raw: string) {
  try {
    const parsed = JSON.parse(raw);
    if (parsed.token) {
      emit('scan', parsed.token);
      return;
    }
  } catch {
    // String might be plain token
  }
  emit('scan', raw.trim());
}

function submitManualToken() {
  if (!manualToken.value.trim()) return;
  emit('scan', manualToken.value.trim().toUpperCase());
  manualToken.value = '';
}

onMounted(startCamera);
onBeforeUnmount(stopCamera);
</script>
