<template>
  <div class="rounded-2xl border border-border/60 bg-card shadow-sm overflow-hidden">
    <!-- Form Tabs -->
    <div class="border-b border-border/60 bg-muted/20 px-6 py-3 flex items-center gap-2 overflow-x-auto">
      <button
        type="button"
        :class="[
          'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition whitespace-nowrap',
          activeTab === 'biography'
            ? 'bg-primary text-primary-foreground shadow-sm'
            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
        ]"
        @click="activeTab = 'biography'"
      >
        <User class="size-4" />
        1. Biografi & Identitas
      </button>

      <button
        type="button"
        :class="[
          'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition whitespace-nowrap',
          activeTab === 'employment'
            ? 'bg-primary text-primary-foreground shadow-sm'
            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
        ]"
        @click="activeTab = 'employment'"
      >
        <Briefcase class="size-4" />
        2. Status Kepegawaian (PTK)
      </button>

      <button
        type="button"
        :class="[
          'inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition whitespace-nowrap',
          activeTab === 'education'
            ? 'bg-primary text-primary-foreground shadow-sm'
            : 'text-muted-foreground hover:bg-muted hover:text-foreground'
        ]"
        @click="activeTab = 'education'"
      >
        <GraduationCap class="size-4" />
        3. Pendidikan & Kualifikasi
      </button>
    </div>

    <!-- Form Body -->
    <form @submit.prevent="handleSubmit">
      <div class="p-6 space-y-6">
        <!-- Tab 1: Biografi -->
        <div v-show="activeTab === 'biography'" class="space-y-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-foreground mb-1">
                Nama Lengkap & Gelar <span class="text-destructive">*</span>
              </label>
              <input
                v-model="form.full_name"
                type="text"
                required
                placeholder="Contoh: Dr. Budi Santoso, M.Pd"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Gelar Depan
              </label>
              <input
                v-model="form.title_prefix"
                type="text"
                placeholder="Drs. / Ir. / Dr."
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Gelar Belakang
              </label>
              <input
                v-model="form.title_suffix"
                type="text"
                placeholder="S.Pd / M.Kom / M.Pd"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                NIK (No. KTP) <span class="text-destructive">*</span>
              </label>
              <input
                v-model="form.nik"
                type="text"
                required
                maxlength="16"
                placeholder="16 Digit NIK"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                NIP (Nomor Induk Pegawai)
              </label>
              <input
                v-model="form.nip"
                type="text"
                maxlength="18"
                placeholder="18 Digit NIP (Bagi ASN)"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                NUPTK
              </label>
              <input
                v-model="form.nuptk"
                type="text"
                maxlength="16"
                placeholder="16 Digit NUPTK Kemdikbudristek"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm font-mono text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Jenis Kelamin <span class="text-destructive">*</span>
              </label>
              <select
                v-model="form.gender"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Agama
              </label>
              <select
                v-model="form.religion"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="Islam">Islam</option>
                <option value="Kristen">Kristen</option>
                <option value="Katolik">Katolik</option>
                <option value="Hindu">Hindu</option>
                <option value="Buddha">Buddha</option>
                <option value="Khonghucu">Khonghucu</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Tempat Lahir
              </label>
              <input
                v-model="form.place_of_birth"
                type="text"
                placeholder="Kota / Kabupaten Lahir"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Tanggal Lahir
              </label>
              <input
                v-model="form.date_of_birth"
                type="date"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                No. Telepon / WhatsApp
              </label>
              <input
                v-model="form.phone"
                type="tel"
                placeholder="081234567890"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Alamat Email
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="nama@sekolah.id"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-semibold text-foreground mb-1">
                Alamat Tempat Tinggal
              </label>
              <textarea
                v-model="form.address"
                rows="2"
                placeholder="Alamat domisili lengkap sesuai KTP"
                class="w-full rounded-lg border border-border bg-background p-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Tab 2: Kepegawaian -->
        <div v-show="activeTab === 'employment'" class="space-y-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Status Kepegawaian <span class="text-destructive">*</span>
              </label>
              <select
                v-model="form.employment_status"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="PNS">PNS (Pegawai Negeri Sipil)</option>
                <option value="PPPK">PPPK (P3K)</option>
                <option value="Honorer">Honorer / Non-ASN</option>
                <option value="GTY">GTY / Pegawai Tetap Yayasan</option>
                <option value="GTT">GTT / Guru Tidak Tetap</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Jenis PTK / Tugas Pokok <span class="text-destructive">*</span>
              </label>
              <select
                v-model="form.staff_type"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="teacher">Guru / Tenaga Pendidik</option>
                <option value="admin">Tenaga Administrasi Sekolah (TAS/TU)</option>
                <option value="lab">Laboran</option>
                <option value="librarian">Pustakawan</option>
                <option value="security">Petugas Keamanan</option>
                <option value="cleaning">Petugas Kebersihan</option>
                <option value="other">Tenaga Kependidikan Lainnya</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Jabatan / Peran Tugas
              </label>
              <input
                v-model="form.role_title"
                type="text"
                placeholder="Contoh: Guru Mata Pelajaran / Kepala Lab"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Status Aktif
              </label>
              <select
                v-model="form.is_active"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option :value="true">Aktif</option>
                <option :value="false">Nonaktif / Keluar</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Nomor SK Pengangkatan
              </label>
              <input
                v-model="form.sk_pengangkatan"
                type="text"
                placeholder="No. SK Dinas / Yayasan"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                TMT Pengangkatan (Tanggal Mulai Tugas)
              </label>
              <input
                v-model="form.tmt_pengangkatan"
                type="date"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Nomor SK Penugasan
              </label>
              <input
                v-model="form.sk_penugasan"
                type="text"
                placeholder="No. SK Pembagian Tugas"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div class="flex items-center gap-3 pt-4">
              <input
                id="certification_status"
                v-model="form.certification_status"
                type="checkbox"
                class="size-4 rounded border-border text-primary focus:ring-primary"
              />
              <label for="certification_status" class="text-xs font-semibold text-foreground cursor-pointer">
                Sudah Sertifikasi Pendidik (Memiliki Serdik)
              </label>
            </div>
          </div>
        </div>

        <!-- Tab 3: Pendidikan -->
        <div v-show="activeTab === 'education'" class="space-y-6">
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Pendidikan Terakhir <span class="text-destructive">*</span>
              </label>
              <select
                v-model="form.last_education"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="S3">S3 (Doktor)</option>
                <option value="S2">S2 (Magister)</option>
                <option value="S1">S1 / D4 (Sarjana)</option>
                <option value="D3">D3 (Diploma)</option>
                <option value="SMA">SMA / SMK / Sederajat</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Program Studi / Jurusan
              </label>
              <input
                v-model="form.major"
                type="text"
                placeholder="Contoh: Pendidikan Matematika"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Action -->
      <div class="flex items-center justify-end gap-3 border-t border-border/60 bg-muted/10 px-6 py-4">
        <button
          type="button"
          class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
          @click="$emit('cancel')"
        >
          Batal
        </button>

        <button
          type="submit"
          :disabled="loading"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
        >
          <div v-if="loading" class="size-3.5 animate-spin rounded-full border-2 border-primary-foreground border-t-transparent"></div>
          <Save v-else class="size-4" />
          {{ isEdit ? 'Simpan Perubahan' : 'Daftarkan GTK' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue';
import { User, Briefcase, GraduationCap, Save } from 'lucide-vue-next';
import type { Staff } from '../../types';

interface StaffFormData {
  full_name: string;
  title_prefix: string;
  title_suffix: string;
  nik: string;
  nip: string;
  nuptk: string;
  gender: 'L' | 'P';
  religion: string;
  place_of_birth: string;
  date_of_birth: string;
  phone: string;
  email: string;
  address: string;
  employment_status: string;
  staff_type: string;
  role_title: string;
  is_active: boolean;
  sk_pengangkatan: string;
  tmt_pengangkatan: string;
  sk_penugasan: string;
  certification_status: boolean;
  last_education: string;
  major: string;
}

const props = defineProps<{
  initialData?: Partial<Staff> & Record<string, unknown>;
  isEdit?: boolean;
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', data: Partial<Staff>): void;
  (e: 'cancel'): void;
}>();

const activeTab = ref<'biography' | 'employment' | 'education'>('biography');

const form = reactive<StaffFormData>({
  full_name: props.initialData?.full_name || '',
  title_prefix: props.initialData?.title_prefix || '',
  title_suffix: props.initialData?.title_suffix || '',
  nik: props.initialData?.nik || '',
  nip: props.initialData?.nip || '',
  nuptk: props.initialData?.nuptk || '',
  gender: (props.initialData?.gender as 'L' | 'P') || 'L',
  religion: (props.initialData?.religion as string) || 'Islam',
  place_of_birth: (props.initialData?.place_of_birth as string) || '',
  date_of_birth: (props.initialData?.date_of_birth as string) || '',
  phone: props.initialData?.phone || '',
  email: props.initialData?.email || '',
  address: (props.initialData?.address as string) || '',
  employment_status: props.initialData?.employment_status || 'PNS',
  staff_type: props.initialData?.staff_type || 'teacher',
  role_title: props.initialData?.role_title || '',
  is_active: props.initialData?.is_active ?? true,
  sk_pengangkatan: (props.initialData?.sk_pengangkatan as string) || '',
  tmt_pengangkatan: (props.initialData?.tmt_pengangkatan as string) || '',
  sk_penugasan: (props.initialData?.sk_penugasan as string) || '',
  certification_status: props.initialData?.certification_status || false,
  last_education: props.initialData?.last_education || 'S1',
  major: props.initialData?.major || '',
});

function handleSubmit() {
  emit('submit', { ...form });
}
</script>
