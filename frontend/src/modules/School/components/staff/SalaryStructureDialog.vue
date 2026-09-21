<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs animate-in fade-in"
    @click.self="$emit('update:open', false)"
  >
    <div class="w-full max-w-md rounded-2xl border border-border/60 bg-card p-6 shadow-xl space-y-5">
      <!-- Modal Header -->
      <div class="flex items-start justify-between">
        <div>
          <h3 class="text-lg font-bold tracking-tight text-foreground">
            Struktur Gaji & Tunjangan
          </h3>
          <p class="text-xs text-muted-foreground mt-0.5">
            Komponen penghasilan bulanan untuk <span class="font-semibold text-foreground">{{ staffName }}</span>.
          </p>
        </div>
        <button
          type="button"
          class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition"
          @click="$emit('update:open', false)"
        >
          <X class="size-4" />
        </button>
      </div>

      <!-- Form Inputs -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-foreground mb-1">
            Gaji Pokok (Rp) <span class="text-destructive">*</span>
          </label>
          <input
            v-model.number="form.base_salary"
            type="number"
            min="0"
            required
            class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            placeholder="0"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-foreground mb-1">
            Tunjangan Transportasi (Rp)
          </label>
          <input
            v-model.number="form.transport_allowance"
            type="number"
            min="0"
            class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            placeholder="0"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-foreground mb-1">
            Tunjangan Makan (Rp)
          </label>
          <input
            v-model.number="form.meal_allowance"
            type="number"
            min="0"
            class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            placeholder="0"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-foreground mb-1">
            Tunjangan Jabatan / Lainnya (Rp)
          </label>
          <input
            v-model.number="form.other_allowance"
            type="number"
            min="0"
            class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            placeholder="0"
          />
        </div>

        <!-- Total Calculation -->
        <div class="rounded-xl border border-border/60 bg-muted/20 p-3.5 flex items-center justify-between">
          <span class="text-xs font-semibold text-muted-foreground">Total Estimasi Gaji:</span>
          <span class="text-base font-bold font-mono text-primary">
            Rp {{ formatNumber(totalSalary) }}
          </span>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-2.5 pt-2">
          <button
            type="button"
            class="rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
            @click="$emit('update:open', false)"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
          >
            <div v-if="loading" class="size-3.5 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></div>
            <Save v-else class="size-4" />
            Simpan Struktur
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { X, Save } from 'lucide-vue-next';

const props = defineProps<{
  open: boolean;
  staffName: string;
  initialData?: Record<string, unknown>;
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:open', val: boolean): void;
  (e: 'submit', payload: Record<string, number>): void;
}>();

const form = ref({
  base_salary: 0,
  transport_allowance: 0,
  meal_allowance: 0,
  other_allowance: 0,
});

watch(
  () => props.initialData,
  (newData) => {
    if (newData) {
      form.value = {
        base_salary: Number(newData.base_salary || 0),
        transport_allowance: Number(newData.transport_allowance || 0),
        meal_allowance: Number(newData.meal_allowance || 0),
        other_allowance: Number(newData.other_allowance || 0),
      };
    }
  },
  { immediate: true }
);

const totalSalary = computed(() => {
  return (
    (form.value.base_salary || 0) +
    (form.value.transport_allowance || 0) +
    (form.value.meal_allowance || 0) +
    (form.value.other_allowance || 0)
  );
});

function formatNumber(num: number): string {
  return new Intl.NumberFormat('id-ID').format(num);
}

function handleSubmit() {
  emit('submit', { ...form.value });
}
</script>
