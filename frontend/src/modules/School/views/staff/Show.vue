<template>
  <div class="space-y-6">
    <!-- Breadcrumb & Top Actions -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.staff' }" class="hover:text-foreground">Kepegawaian</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Buku Induk GTK</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Profil Pegawai & Pendidik</h1>
        <p class="text-sm text-muted-foreground">
          Rincian portofolio kepegawaian, biodata, kualifikasi akademik, dan hak remunerasi.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <router-link
          :to="{ name: 'school.staff' }"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
        >
          <ArrowLeft class="size-4" />
          Kembali
        </router-link>

        <button
          v-if="staff"
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
          @click="showSalaryDialog = true"
        >
          <Wallet class="size-4 text-primary" />
          Atur Gaji
        </button>

        <router-link
          v-if="staff"
          :to="{ name: 'school.staff.edit', params: { id: staff.id } }"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Pencil class="size-4" />
          Ubah Data
        </router-link>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="rounded-xl border border-border/60 bg-card p-12 text-center text-sm text-muted-foreground">
      <div class="inline-block size-6 animate-spin rounded-full border-2 border-primary border-t-transparent mb-2"></div>
      <div>Memuat rincian data pegawai...</div>
    </div>

    <!-- Error Banner -->
    <div v-else-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-xs text-destructive">
      {{ errorMessage }}
    </div>

    <!-- Staff Details -->
    <div v-else-if="staff" class="space-y-6">
      <!-- Header Banner -->
      <div class="relative rounded-2xl overflow-hidden bg-card border border-border/60 shadow-sm">
        <div class="h-28 bg-gradient-to-r from-primary/20 via-primary/10 to-transparent" />
        <div class="px-6 pb-6 -mt-12 relative flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">
            <!-- Staff Avatar -->
            <div class="size-28 rounded-2xl bg-card p-1.5 border border-border/60 shadow-md overflow-hidden shrink-0 flex items-center justify-center">
              <div class="size-full rounded-xl bg-primary/10 text-primary font-bold text-2xl flex items-center justify-center uppercase">
                {{ getInitials(staff.full_name) }}
              </div>
            </div>

            <!-- Basic Info -->
            <div class="space-y-1">
              <div class="flex flex-wrap items-center gap-2.5">
                <h2 class="text-2xl font-bold tracking-tight text-foreground">
                  {{ formatFormalName(staff) }}
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                  {{ staff.employment_status || 'PNS' }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                  {{ staff.role_title || staff.staff_type || 'Tenaga Pendidik' }}
                </span>
              </div>

              <div class="flex flex-wrap gap-4 text-xs text-muted-foreground pt-1">
                <span class="flex items-center gap-1.5">
                  <Fingerprint class="size-3.5 text-primary" />
                  NIP: <span class="font-mono font-medium text-foreground">{{ staff.nip || '-' }}</span>
                </span>
                <span class="flex items-center gap-1.5">
                  <Award class="size-3.5 text-primary" />
                  NUPTK: <span class="font-mono font-medium text-foreground">{{ staff.nuptk || '-' }}</span>
                </span>
                <span class="flex items-center gap-1.5">
                  <Mail class="size-3.5 text-primary" />
                  {{ staff.email || 'Email belum diisi' }}
                </span>
                <span class="flex items-center gap-1.5">
                  <Phone class="size-3.5 text-primary" />
                  {{ staff.phone || 'Nomor belum diisi' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs & Panels -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Navigation -->
        <div class="lg:col-span-1 space-y-3">
          <div class="bg-card border border-border/60 rounded-xl p-2.5 shadow-sm space-y-1">
            <button
              v-for="tab in tabList"
              :key="tab.id"
              type="button"
              :class="[
                'w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition',
                activeTab === tab.id
                  ? 'bg-primary text-primary-foreground shadow-sm'
                  : 'text-muted-foreground hover:bg-muted hover:text-foreground'
              ]"
              @click="activeTab = tab.id"
            >
              <component :is="tab.icon" class="size-4 shrink-0" />
              <span>{{ tab.label }}</span>
            </button>
          </div>

          <!-- Quick Remuneration Summary -->
          <div class="bg-card border border-border/60 rounded-xl p-4 shadow-sm space-y-2">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
              Estimasi Remunerasi
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-muted-foreground">Total Gaji Pokok</span>
              <span class="text-sm font-bold font-mono text-foreground">
                Rp {{ formatCurrency(salaryStructure.base_salary || 0) }}
              </span>
            </div>
            <button
              type="button"
              class="w-full mt-2 rounded-lg border border-border py-1.5 text-[11px] font-semibold text-primary hover:bg-muted transition"
              @click="showSalaryDialog = true"
            >
              Kelola Komponen Gaji
            </button>
          </div>
        </div>

        <!-- Panels -->
        <div class="lg:col-span-3 space-y-6">
          <!-- Tab 1: Biodata -->
          <div v-show="activeTab === 'biography'" class="space-y-6">
            <div class="bg-card border border-border/60 rounded-xl p-6 shadow-sm">
              <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-border/60">
                <User class="size-4 text-primary" />
                <h3 class="text-sm font-bold text-foreground">Data Identitas Pribadi</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <span class="text-muted-foreground block mb-0.5">Nama Lengkap & Gelar</span>
                  <span class="font-medium text-foreground">{{ formatFormalName(staff) }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">NIK (No. KTP)</span>
                  <span class="font-mono font-medium text-foreground">{{ staff.nik || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">NIP</span>
                  <span class="font-mono font-medium text-foreground">{{ staff.nip || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">NUPTK</span>
                  <span class="font-mono font-medium text-foreground">{{ staff.nuptk || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Jenis Kelamin</span>
                  <span class="font-medium text-foreground">
                    {{ staff.gender === 'L' ? 'Laki-laki (L)' : staff.gender === 'P' ? 'Perempuan (P)' : '-' }}
                  </span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Agama</span>
                  <span class="font-medium text-foreground">{{ (staff as any).religion || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Tempat & Tanggal Lahir</span>
                  <span class="font-medium text-foreground">
                    {{ (staff as any).place_of_birth || '-' }}, {{ (staff as any).date_of_birth || '-' }}
                  </span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">No. Telepon / WhatsApp</span>
                  <span class="font-mono font-medium text-foreground">{{ staff.phone || '-' }}</span>
                </div>
                <div class="sm:col-span-2">
                  <span class="text-muted-foreground block mb-0.5">Alamat Domisili</span>
                  <span class="font-medium text-foreground leading-relaxed">{{ (staff as any).address || '-' }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab 2: Kepegawaian -->
          <div v-show="activeTab === 'employment'" class="bg-card border border-border/60 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-border/60">
              <Briefcase class="size-4 text-primary" />
              <h3 class="text-sm font-bold text-foreground">Status Kepegawaian & Penugasan</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div>
                <span class="text-muted-foreground block mb-0.5">Status Kepegawaian</span>
                <span class="font-medium text-foreground">{{ staff.employment_status || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Jenis PTK</span>
                <span class="font-medium text-foreground capitalize">{{ staff.staff_type || staff.role_title || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Nomor SK Pengangkatan</span>
                <span class="font-medium text-foreground">{{ (staff as any).sk_pengangkatan || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">TMT Pengangkatan</span>
                <span class="font-medium text-foreground">{{ (staff as any).tmt_pengangkatan || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Nomor SK Penugasan</span>
                <span class="font-medium text-foreground">{{ (staff as any).sk_penugasan || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Status Sertifikasi Pendidik</span>
                <span
                  :class="[
                    'inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold',
                    staff.certification_status ? 'bg-emerald-500/10 text-emerald-600' : 'bg-muted text-muted-foreground'
                  ]"
                >
                  {{ staff.certification_status ? 'Tersertifikasi' : 'Belum Tersertifikasi' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Tab 3: Pendidikan -->
          <div v-show="activeTab === 'education'" class="bg-card border border-border/60 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-border/60">
              <GraduationCap class="size-4 text-primary" />
              <h3 class="text-sm font-bold text-foreground">Kualifikasi Akademik</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div>
                <span class="text-muted-foreground block mb-0.5">Pendidikan Terakhir</span>
                <span class="font-medium text-foreground">{{ staff.last_education || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Program Studi / Jurusan</span>
                <span class="font-medium text-foreground">{{ staff.major || '-' }}</span>
              </div>
            </div>
          </div>

          <!-- Tab 4: Komponen Gaji -->
          <div v-show="activeTab === 'salary'" class="bg-card border border-border/60 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-border/60">
              <div class="flex items-center gap-2.5">
                <Wallet class="size-4 text-primary" />
                <h3 class="text-sm font-bold text-foreground">Rincian Struktur Gaji Bulanan</h3>
              </div>
              <button
                type="button"
                class="rounded-lg bg-primary/10 text-primary px-3 py-1.5 text-xs font-semibold hover:bg-primary/20 transition"
                @click="showSalaryDialog = true"
              >
                Ubah Struktur
              </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20">
                <span class="text-muted-foreground block mb-0.5">Gaji Pokok</span>
                <span class="font-bold text-foreground text-sm font-mono">
                  Rp {{ formatCurrency(salaryStructure.base_salary || 0) }}
                </span>
              </div>
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20">
                <span class="text-muted-foreground block mb-0.5">Tunjangan Transportasi</span>
                <span class="font-bold text-foreground text-sm font-mono">
                  Rp {{ formatCurrency(salaryStructure.transport_allowance || 0) }}
                </span>
              </div>
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20">
                <span class="text-muted-foreground block mb-0.5">Tunjangan Makan</span>
                <span class="font-bold text-foreground text-sm font-mono">
                  Rp {{ formatCurrency(salaryStructure.meal_allowance || 0) }}
                </span>
              </div>
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20">
                <span class="text-muted-foreground block mb-0.5">Tunjangan Lainnya</span>
                <span class="font-bold text-foreground text-sm font-mono">
                  Rp {{ formatCurrency(salaryStructure.other_allowance || 0) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Salary Dialog -->
    <SalaryStructureDialog
      v-if="staff"
      v-model:open="showSalaryDialog"
      :staff-name="staff.full_name"
      :initial-data="salaryStructure"
      :loading="isSavingSalary"
      @submit="handleSaveSalary"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import {
  ArrowLeft,
  Pencil,
  Fingerprint,
  Award,
  Mail,
  Phone,
  User,
  Briefcase,
  GraduationCap,
  Wallet
} from 'lucide-vue-next';
import SalaryStructureDialog from '../../components/staff/SalaryStructureDialog.vue';
import { HrService } from '../../services/hrService';
import type { Staff } from '../../types';

const route = useRoute();

const staff = ref<Staff | null>(null);
const isLoading = ref(true);
const errorMessage = ref<string | null>(null);
const activeTab = ref<'biography' | 'employment' | 'education' | 'salary'>('biography');
const showSalaryDialog = ref(false);
const isSavingSalary = ref(false);
const salaryStructure = ref<Record<string, number>>({
  base_salary: 0,
  transport_allowance: 0,
  meal_allowance: 0,
  other_allowance: 0,
});

const tabList = [
  { id: 'biography', label: 'Biodata & Identitas', icon: User },
  { id: 'employment', label: 'Status Kepegawaian', icon: Briefcase },
  { id: 'education', label: 'Kualifikasi Akademik', icon: GraduationCap },
  { id: 'salary', label: 'Komponen Remunerasi', icon: Wallet },
] as const;

function getInitials(name?: string): string {
  if (!name) return 'ST';
  const parts = name.trim().split(' ').filter(Boolean);
  if (parts.length === 0) return 'ST';
  const first = parts[0] ?? '';
  if (parts.length === 1) return first.substring(0, 2).toUpperCase() || 'ST';
  const second = parts[1] ?? '';
  return ((first[0] ?? '') + (second[0] ?? '')).toUpperCase() || 'ST';
}

function formatFormalName(item: Staff): string {
  let name = item.full_name;
  if (item.title_prefix) name = `${item.title_prefix} ${name}`;
  if (item.title_suffix) name = `${name}, ${item.title_suffix}`;
  return name;
}

function formatCurrency(val: number): string {
  return new Intl.NumberFormat('id-ID').format(val);
}

async function loadStaff() {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    const staffId = route.params.id as string;
    const res = await HrService.getStaffById(staffId);
    if (res.data?.success && res.data.data) {
      staff.value = res.data.data;
    } else {
      errorMessage.value = 'Data pegawai tidak ditemukan.';
    }

    try {
      const salaryRes = await HrService.getSalaryStructure(staffId);
      if (salaryRes.data?.success && salaryRes.data.data) {
        salaryStructure.value = salaryRes.data.data as Record<string, number>;
      }
    } catch {
      // Structure might not be set yet, silent fallback
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat profil data pegawai.';
  } finally {
    isLoading.value = false;
  }
}

async function handleSaveSalary(payload: Record<string, number>) {
  if (!staff.value) return;

  isSavingSalary.value = true;
  try {
    await HrService.saveSalaryStructure(staff.value.id, payload);
    salaryStructure.value = { ...payload };
    showSalaryDialog.value = false;
  } catch (err: any) {
    alert(err.response?.data?.message || 'Gagal menyimpan struktur gaji.');
  } finally {
    isSavingSalary.value = false;
  }
}

onMounted(loadStaff);
</script>
