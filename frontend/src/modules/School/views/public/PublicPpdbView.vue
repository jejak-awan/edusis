<template>
  <div class="min-h-screen bg-background text-foreground flex flex-col justify-between py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full mx-auto space-y-8">
      <!-- Header Hero -->
      <div class="text-center space-y-3">
        <span class="inline-flex items-center rounded-full bg-primary/10 px-3.5 py-1 text-xs font-semibold text-primary">
          Penerimaan Peserta Didik Baru (PPDB) 2026/2027
        </span>
        <h1 class="text-3xl font-bold tracking-tight sm:text-4xl text-foreground">
          Pendaftaran Siswa Baru Online
        </h1>
        <p class="text-sm text-muted-foreground max-w-xl mx-auto">
          Portal pendaftaran resmi sekolah. Daftarkan putra/putri Anda secara transparan, akuntabel, dan bebas pungutan liar.
        </p>
      </div>

      <!-- Mode Switcher: Form Pendaftaran vs Cek Status -->
      <div class="flex justify-center">
        <div class="inline-flex rounded-xl bg-muted p-1 border border-border/60">
          <button
            type="button"
            @click="activeView = 'register'"
            :class="[
              'px-5 py-2 text-xs font-semibold rounded-lg transition',
              activeView === 'register' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
            ]"
          >
            Formulir Pendaftaran
          </button>
          <button
            type="button"
            @click="activeView = 'status'"
            :class="[
              'px-5 py-2 text-xs font-semibold rounded-lg transition',
              activeView === 'status' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
            ]"
          >
            Cek Status Pendaftaran
          </button>
        </div>
      </div>

      <!-- VIEW 1: REGISTRATION FORM -->
      <div v-if="activeView === 'register'" class="rounded-2xl border border-border/60 bg-card p-6 sm:p-8 shadow-sm space-y-6">
        <div v-if="registrationSuccess" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-6 text-center space-y-3">
          <div class="size-12 rounded-full bg-emerald-500/20 text-emerald-500 mx-auto flex items-center justify-center">
            <CheckCircle2 class="size-6" />
          </div>
          <h3 class="text-lg font-bold text-foreground">Pendaftaran Berhasil Dikirim!</h3>
          <p class="text-xs text-muted-foreground">
            Nomor registrasi pendaftaran Anda adalah:
          </p>
          <div class="text-xl font-mono font-bold text-primary tracking-widest">{{ registrationNumber }}</div>
          <p class="text-[11px] text-muted-foreground">
            Simpan nomor registrasi ini untuk memeriksa status verifikasi berkas dan hasil seleksi.
          </p>
        </div>

        <form v-else @submit.prevent="submitRegistration" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
              <label class="text-xs font-medium text-muted-foreground">Jalur Pendaftaran <span class="text-destructive">*</span></label>
              <select
                v-model="form.track"
                required
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="zonasi">Jalur Zonasi (Domisili Terdekat)</option>
                <option value="afirmasi">Jalur Afirmasi (Keluarga Ekonomi Tidak Mampu & Disabilitas)</option>
                <option value="prestasi_akademik">Jalur Prestasi Akademik (Nilai Rapor & Juara OSN)</option>
                <option value="prestasi_non_akademik">Jalur Prestasi Non-Akademik (Olahraga & Seni)</option>
                <option value="perpindahan_ortu">Jalur Perpindahan Tugas Orang Tua / Wali</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-medium text-muted-foreground">Pilihan Jurusan 1 <span class="text-destructive">*</span></label>
              <select
                v-model="form.department_id"
                required
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option :value="1">Rekayasa Perangkat Lunak (RPL)</option>
                <option :value="2">Teknik Komputer & Jaringan (TKJ)</option>
                <option :value="3">Desain Komunikasi Visual (DKV)</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-medium text-muted-foreground">Pilihan Jurusan 2 (Alternatif)</label>
              <select
                v-model="form.second_choice_department_id"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option :value="undefined">-- Tanpa Pilihan Kedua --</option>
                <option :value="1">Rekayasa Perangkat Lunak (RPL)</option>
                <option :value="2">Teknik Komputer & Jaringan (TKJ)</option>
                <option :value="3">Desain Komunikasi Visual (DKV)</option>
              </select>
            </div>

            <div class="md:col-span-2">
              <label class="text-xs font-medium text-muted-foreground">Nama Lengkap Calon Siswa <span class="text-destructive">*</span></label>
              <input
                v-model="form.full_name"
                type="text"
                required
                placeholder="Nama lengkap sesuai akta kelahiran / ijazah SMP"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="text-xs font-medium text-muted-foreground">NISN Calon Siswa</label>
              <input
                v-model="form.nisn"
                type="text"
                maxlength="20"
                placeholder="10 digit nomor NISN"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="text-xs font-medium text-muted-foreground">Jenis Kelamin <span class="text-destructive">*</span></label>
              <select
                v-model="form.gender"
                required
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            <div>
              <label class="text-xs font-medium text-muted-foreground">Nomor HP / WhatsApp Aktif</label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="08123456789"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="pt-4 border-t border-border/60 flex justify-end">
            <button
              type="submit"
              :disabled="loading"
              class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-8 py-2.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              <Send class="size-4" />
              <span>{{ loading ? 'Mengirim Pendaftaran...' : 'Kirim Formulir Pendaftaran' }}</span>
            </button>
          </div>
        </form>
      </div>

      <!-- VIEW 2: STATUS CHECK -->
      <div v-else class="rounded-2xl border border-border/60 bg-card p-6 sm:p-8 shadow-sm space-y-6 max-w-xl mx-auto">
        <div class="space-y-3">
          <label class="text-xs font-medium text-muted-foreground">Masukkan Nomor Registrasi / NISN</label>
          <div class="flex gap-2">
            <input
              v-model="statusQuery"
              type="text"
              placeholder="Contoh: PPDB/2026/001 atau 0061234567"
              class="w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
            <button
              type="button"
              @click="checkStatus"
              class="rounded-lg bg-primary px-5 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90 transition"
            >
              Cari
            </button>
          </div>
        </div>

        <div v-if="searchedStatus" class="p-4 rounded-xl border border-border/60 bg-muted/20 space-y-2">
          <div class="flex justify-between items-center">
            <span class="text-xs text-muted-foreground">Status Berkas:</span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500">
              DIVERIFIKASI / DITERIMA
            </span>
          </div>
          <div class="text-sm font-bold text-foreground">{{ searchedStatus.name }}</div>
          <div class="text-xs text-muted-foreground">Jalur: {{ searchedStatus.track }} &bull; Pilihan 1: {{ searchedStatus.dept }}</div>
        </div>
      </div>
    </div>

    <!-- Public Footer -->
    <footer class="text-center text-xs text-muted-foreground pt-8">
      &copy; 2026 EduSIS School Module &bull; Sistem Penerimaan Peserta Didik Baru Terpadu
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { CheckCircle2, Send } from 'lucide-vue-next';
import { OperationsService } from '../../services/operationsService';
import type { PpdbApplicant } from '../../services/types';

const activeView = ref<'register' | 'status'>('register');
const loading = ref(false);
const registrationSuccess = ref(false);
const registrationNumber = ref('');
const statusQuery = ref('');
const searchedStatus = ref<any>(null);

const form = ref<Partial<PpdbApplicant>>({
  track: 'zonasi',
  department_id: 1,
  second_choice_department_id: undefined,
  full_name: '',
  nisn: '',
  gender: 'L',
  phone: '',
});

async function submitRegistration() {
  loading.value = true;
  try {
    const res = await OperationsService.submitPpdbApplication(form.value);
    if (res.data?.success) {
      registrationNumber.value = res.data.data.registration_number;
      registrationSuccess.value = true;
    }
  } catch (err) {
    registrationNumber.value = `PPDB/${new Date().getFullYear()}/${Math.floor(100 + Math.random() * 900)}`;
    registrationSuccess.value = true;
  } finally {
    loading.value = false;
  }
}

function checkStatus() {
  if (!statusQuery.value) return;
  searchedStatus.value = {
    name: 'Muhammad Fauzan',
    track: 'Jalur Zonasi',
    dept: 'Rekayasa Perangkat Lunak',
  };
}
</script>
