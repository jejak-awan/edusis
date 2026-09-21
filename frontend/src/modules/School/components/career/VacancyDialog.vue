<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs animate-in fade-in"
    @click.self="$emit('update:open', false)"
  >
    <div class="w-full max-w-2xl max-h-[90vh] flex flex-col rounded-2xl border border-border/60 bg-card shadow-2xl overflow-hidden">
      <!-- Modal Header -->
      <div class="flex items-center justify-between border-b border-border/60 p-5">
        <div>
          <h3 class="text-base font-bold text-foreground">
            {{ isEditing ? 'Ubah Lowongan Pekerjaan (BKK)' : 'Tambah Lowongan Pekerjaan Baru (BKK)' }}
          </h3>
          <p class="text-xs text-muted-foreground mt-0.5">
            Publikasikan lowongan kerja dari mitra industri sekolah untuk alumni dan siswa tingkat akhir.
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

      <!-- Form Inputs (Scrollable) -->
      <form @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto p-5 space-y-4 text-xs">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-foreground mb-1">
              Nama Perusahaan Mitra <span class="text-destructive">*</span>
            </label>
            <input
              v-model="form.company_name"
              type="text"
              required
              placeholder="Contoh: PT Telkom Akses / Astra Honda"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Posisi / Judul Pekerjaan <span class="text-destructive">*</span>
            </label>
            <input
              v-model="form.job_title"
              type="text"
              required
              placeholder="Contoh: Teknisi Jaringan / Junior Programmer"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
            <label class="block font-semibold text-foreground mb-1">
              Tipe Pekerjaan <span class="text-destructive">*</span>
            </label>
            <select
              v-model="form.job_type"
              required
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="fulltime">Purna Waktu (Full-time)</option>
              <option value="internship">Magang / Prakerin</option>
              <option value="parttime">Paruh Waktu (Part-time)</option>
              <option value="contract">Kontrak Proyek</option>
              <option value="freelance">Freelance</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Sistem Kerja <span class="text-destructive">*</span>
            </label>
            <select
              v-model="form.workplace_type"
              required
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="on_site">Di Kantor / On-site</option>
              <option value="hybrid">Campuran / Hybrid</option>
              <option value="remote">Jarak Jauh / Remote</option>
            </select>
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Status Publikasi
            </label>
            <select
              v-model="form.status"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="published">Dibuka (Published)</option>
              <option value="draft">Draf (Draft)</option>
              <option value="closed">Ditutup (Closed)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block font-semibold text-foreground mb-1">
              Lokasi Penempatan
            </label>
            <input
              v-model="form.location"
              type="text"
              placeholder="Contoh: Bandung, DKI Jakarta, Kawasan Industri"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Kisaran Gaji / Insentif
            </label>
            <input
              v-model="form.salary_range"
              type="text"
              placeholder="Contoh: Rp 4.500.000 - Rp 6.000.000 / UMR"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
            <label class="block font-semibold text-foreground mb-1">
              Batas Waktu Lamaran
            </label>
            <input
              v-model="form.application_deadline"
              type="date"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Email Rekrutmen
            </label>
            <input
              v-model="form.contact_email"
              type="email"
              placeholder="recruitment@mitra.com"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              No. Kontak / WhatsApp
            </label>
            <input
              v-model="form.contact_phone"
              type="text"
              placeholder="0812xxxxxxx"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
        </div>

        <div>
          <label class="block font-semibold text-foreground mb-1">
            Deskripsi Pekerjaan & Tanggung Jawab <span class="text-destructive">*</span>
          </label>
          <textarea
            v-model="form.description"
            rows="3"
            required
            placeholder="Jelaskan peran tugas utama sehari-hari..."
            class="w-full rounded-lg border border-border bg-background p-2.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
        </div>

        <div>
          <label class="block font-semibold text-foreground mb-1">
            Persyaratan & Kualifikasi Pelamar
          </label>
          <textarea
            v-model="form.requirements"
            rows="3"
            placeholder="Syarat jurusan, keterampilan teknis, sertifikasi, dll..."
            class="w-full rounded-lg border border-border bg-background p-2.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          ></textarea>
        </div>

        <!-- Dialog Actions -->
        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-border/60">
          <button
            type="button"
            class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
            @click="$emit('update:open', false)"
          >
            Batal
          </button>
          <button
            type="submit"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90 transition shadow-sm"
          >
            {{ isEditing ? 'Simpan Perubahan' : 'Pasang Lowongan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { X } from 'lucide-vue-next';
import type { JobVacancyData } from '../../types/career';

const props = defineProps<{
  open: boolean;
  vacancy?: JobVacancyData | null;
  isEditing?: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void;
  (e: 'save', payload: Partial<JobVacancyData>): void;
}>();

const form = ref<Partial<JobVacancyData>>({
  company_name: '',
  job_title: '',
  job_type: 'fulltime',
  workplace_type: 'on_site',
  location: '',
  salary_range: '',
  description: '',
  requirements: '',
  benefits: '',
  application_deadline: '',
  contact_email: '',
  contact_phone: '',
  status: 'published',
});

watch(
  () => props.vacancy,
  (val) => {
    if (val && props.isEditing) {
      form.value = { ...val };
    } else {
      form.value = {
        company_name: '',
        job_title: '',
        job_type: 'fulltime',
        workplace_type: 'on_site',
        location: '',
        salary_range: '',
        description: '',
        requirements: '',
        benefits: '',
        application_deadline: '',
        contact_email: '',
        contact_phone: '',
        status: 'published',
      };
    }
  },
  { immediate: true }
);

function handleSubmit() {
  emit('save', { ...form.value });
  emit('update:open', false);
}
</script>
