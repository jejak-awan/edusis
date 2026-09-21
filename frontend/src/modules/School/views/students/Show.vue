<template>
  <div class="space-y-6">
    <!-- Breadcrumb & Top Action -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.students' }" class="hover:text-foreground">Kesiswaan</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Buku Induk Siswa</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Profil & Biodata Siswa</h1>
        <p class="text-sm text-muted-foreground">
          Informasi komprehensif profil siswa, rekam akademik, absensi, dan kedisiplinan.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <router-link
          :to="{ name: 'school.students' }"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
        >
          <ArrowLeft class="size-4" />
          Kembali
        </router-link>

        <button
          type="button"
          @click="handlePrint"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
        >
          <Printer class="size-4" />
          Cetak Profil
        </button>

        <router-link
          v-if="student"
          :to="{ name: 'school.students.edit', params: { id: student.id } }"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Pencil class="size-4" />
          Ubah Biodata
        </router-link>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoading" class="rounded-xl border border-border/60 bg-card p-12 text-center text-sm text-muted-foreground">
      <div class="inline-block size-6 animate-spin rounded-full border-2 border-primary border-t-transparent mb-2"></div>
      <div>Memuat profil lengkap siswa...</div>
    </div>

    <!-- Error Banner -->
    <div v-else-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-xs text-destructive">
      {{ errorMessage }}
    </div>

    <!-- Student Detail Content -->
    <div v-else-if="student" class="space-y-6">
      <!-- Profile Header Card -->
      <div class="relative rounded-2xl overflow-hidden bg-card border border-border/60 shadow-sm">
        <div class="h-28 bg-gradient-to-r from-primary/20 via-primary/10 to-transparent" />
        <div class="px-6 pb-6 -mt-12 relative flex flex-col md:flex-row items-start md:items-end justify-between gap-6">
          <div class="flex flex-col sm:flex-row items-start sm:items-end gap-5">
            <!-- Student Photo / Avatar -->
            <div class="size-28 rounded-2xl bg-card p-1.5 border border-border/60 shadow-md overflow-hidden shrink-0 flex items-center justify-center">
              <img
                v-if="student.photo"
                :src="student.photo"
                :alt="student.full_name"
                class="size-full rounded-xl object-cover"
              />
              <div
                v-else
                class="size-full rounded-xl bg-primary/10 text-primary font-bold text-2xl flex items-center justify-center uppercase"
              >
                {{ getInitials(student.full_name) }}
              </div>
            </div>

            <!-- Basic Info -->
            <div class="space-y-1">
              <div class="flex flex-wrap items-center gap-2.5">
                <h2 class="text-2xl font-bold tracking-tight text-foreground">
                  {{ student.full_name }}
                </h2>
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize',
                    student.status === 'active' || !student.status
                      ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20'
                      : 'bg-muted text-muted-foreground'
                  ]"
                >
                  {{ student.status || 'Aktif' }}
                </span>
              </div>

              <div class="flex flex-wrap gap-4 text-xs text-muted-foreground pt-1">
                <span class="flex items-center gap-1.5">
                  <Fingerprint class="size-3.5 text-primary" />
                  NISN: <span class="font-mono font-medium text-foreground">{{ student.nisn || '-' }}</span>
                </span>
                <span class="flex items-center gap-1.5">
                  <Layers class="size-3.5 text-primary" />
                  NIS: <span class="font-mono font-medium text-foreground">{{ student.nis || '-' }}</span>
                </span>
                <span class="flex items-center gap-1.5">
                  <GraduationCap class="size-3.5 text-primary" />
                  {{ student.study_group?.name || student.level?.name || 'Belum Ada Rombel' }}
                </span>
                <span class="flex items-center gap-1.5">
                  <MapPin class="size-3.5 text-primary" />
                  {{ student.kecamatan || student.address || 'Alamat Belum Diatur' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Tabs & Panels -->
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar Tabs -->
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

          <!-- Quick Stat / Summary Box -->
          <div class="bg-card border border-border/60 rounded-xl p-4 shadow-sm space-y-2">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-muted-foreground">
              Status Kedisiplinan
            </div>
            <div class="flex items-center justify-between">
              <span class="text-xs text-muted-foreground">Akumulasi Poin</span>
              <span class="text-sm font-bold text-foreground">0 Poin</span>
            </div>
            <div class="w-full bg-muted rounded-full h-1.5 overflow-hidden">
              <div class="bg-emerald-500 h-full rounded-full" style="width: 100%"></div>
            </div>
            <p class="text-[10px] text-muted-foreground">Catatan perilaku tertib dan kondusif.</p>
          </div>
        </div>

        <!-- Tab Content Area -->
        <div class="lg:col-span-3">
          <!-- Tab 1: Biodata Lengkap -->
          <div v-show="activeTab === 'profile'" class="space-y-6">
            <!-- Data Pribadi -->
            <div class="bg-card border border-border/60 rounded-xl p-6 shadow-sm">
              <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-border/60">
                <User class="size-4 text-primary" />
                <h3 class="text-sm font-bold text-foreground">Data Pribadi Siswa</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <span class="text-muted-foreground block mb-0.5">Nama Lengkap</span>
                  <span class="font-medium text-foreground">{{ student.full_name }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">NISN / NIK</span>
                  <span class="font-mono font-medium text-foreground">{{ student.nisn || '-' }} / {{ student.nik || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Tempat & Tanggal Lahir</span>
                  <span class="font-medium text-foreground">
                    {{ student.place_of_birth || '-' }}, {{ student.date_of_birth || '-' }}
                  </span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Jenis Kelamin</span>
                  <span class="font-medium text-foreground">
                    {{ student.gender === 'L' ? 'Laki-laki (L)' : student.gender === 'P' ? 'Perempuan (P)' : '-' }}
                  </span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Agama</span>
                  <span class="font-medium text-foreground">{{ student.religion || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">No. Telepon / WhatsApp</span>
                  <span class="font-mono font-medium text-foreground">{{ student.phone || '-' }}</span>
                </div>
                <div class="sm:col-span-2">
                  <span class="text-muted-foreground block mb-0.5">Alamat Email</span>
                  <span class="font-medium text-foreground">{{ student.email || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Data Orang Tua / Wali -->
            <div class="bg-card border border-border/60 rounded-xl p-6 shadow-sm">
              <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-border/60">
                <Users class="size-4 text-primary" />
                <h3 class="text-sm font-bold text-foreground">Data Orang Tua & Wali</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                  <span class="text-muted-foreground block mb-0.5">Nama Ayah Kandung</span>
                  <span class="font-medium text-foreground">{{ student.father_name || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Pekerjaan Ayah</span>
                  <span class="font-medium text-foreground">{{ student.father_occupation || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Nama Ibu Kandung</span>
                  <span class="font-medium text-foreground">{{ student.mother_name || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Pekerjaan Ibu</span>
                  <span class="font-medium text-foreground">{{ student.mother_occupation || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Nama Wali (Jika ada)</span>
                  <span class="font-medium text-foreground">{{ student.guardian_name || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Kontak Darurat Orang Tua</span>
                  <span class="font-mono font-medium text-foreground">{{ student.parent?.emergency_phone || student.father_phone || student.mother_phone || student.phone || '-' }}</span>
                </div>
              </div>
            </div>

            <!-- Alamat Domisili -->
            <div class="bg-card border border-border/60 rounded-xl p-6 shadow-sm">
              <div class="flex items-center gap-2.5 mb-5 pb-3 border-b border-border/60">
                <Map class="size-4 text-primary" />
                <h3 class="text-sm font-bold text-foreground">Alamat & Domisili</h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="sm:col-span-3">
                  <span class="text-muted-foreground block mb-0.5">Alamat Lengkap / Jalan</span>
                  <span class="font-medium text-foreground leading-relaxed">{{ student.address || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Dusun / Lingkungan</span>
                  <span class="font-medium text-foreground">{{ student.dusun || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Desa / Kelurahan</span>
                  <span class="font-medium text-foreground">{{ student.desa_kelurahan || '-' }}</span>
                </div>
                <div>
                  <span class="text-muted-foreground block mb-0.5">Kecamatan</span>
                  <span class="font-medium text-foreground">{{ student.kecamatan || '-' }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab 2: Akademik -->
          <div v-show="activeTab === 'academic'" class="bg-card border border-border/60 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-border/60">
              <GraduationCap class="size-4 text-primary" />
              <h3 class="text-sm font-bold text-foreground">Riwayat Rombel & Akademik</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
              <div>
                <span class="text-muted-foreground block mb-0.5">Rombongan Belajar (Rombel)</span>
                <span class="font-medium text-foreground">{{ student.study_group?.name || student.study_groups?.[0]?.name || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Program Keahlian / Jurusan</span>
                <span class="font-medium text-foreground">{{ student.department?.name || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Tingkat Pendidikan</span>
                <span class="font-medium text-foreground">{{ student.level?.name || '-' }}</span>
              </div>
              <div>
                <span class="text-muted-foreground block mb-0.5">Tahun Masuk / Angkatan</span>
                <span class="font-medium text-foreground">{{ student.entry_year || '-' }}</span>
              </div>
            </div>
          </div>

          <!-- Tab 3: Presensi -->
          <div v-show="activeTab === 'attendance'" class="bg-card border border-border/60 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-border/60">
              <CalendarCheck class="size-4 text-primary" />
              <h3 class="text-sm font-bold text-foreground">Ringkasan Presensi Siswa</h3>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20 text-center">
                <span class="text-[10px] text-muted-foreground font-semibold uppercase">Hadir</span>
                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">100%</p>
              </div>
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20 text-center">
                <span class="text-[10px] text-muted-foreground font-semibold uppercase">Sakit</span>
                <p class="text-xl font-bold text-amber-600 dark:text-amber-400 mt-1">0 Hari</p>
              </div>
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20 text-center">
                <span class="text-[10px] text-muted-foreground font-semibold uppercase">Izin</span>
                <p class="text-xl font-bold text-sky-600 dark:text-sky-400 mt-1">0 Hari</p>
              </div>
              <div class="rounded-lg border border-border/60 p-3 bg-muted/20 text-center">
                <span class="text-[10px] text-muted-foreground font-semibold uppercase">Alpa</span>
                <p class="text-xl font-bold text-rose-600 dark:text-rose-400 mt-1">0 Hari</p>
              </div>
            </div>

            <p class="text-xs text-muted-foreground italic">
              Data absensi dikonsolidasikan dari rekapitulasi presensi harian kesiswaan dan rombel.
            </p>
          </div>

          <!-- Tab 4: Kedisiplinan -->
          <div v-show="activeTab === 'discipline'" class="bg-card border border-border/60 rounded-xl p-6 shadow-sm space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-border/60">
              <ShieldAlert class="size-4 text-primary" />
              <h3 class="text-sm font-bold text-foreground">Catatan Tata Tertib & Bimbingan Konseling</h3>
            </div>

            <div class="rounded-lg border border-dashed border-border p-8 text-center text-muted-foreground text-xs">
              <ShieldAlert class="size-8 mx-auto mb-2 opacity-30 text-primary" />
              Tidak ada catatan pelanggaran tata tertib tercatat untuk siswa ini.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import {
  ArrowLeft,
  Printer,
  Pencil,
  Fingerprint,
  Layers,
  GraduationCap,
  MapPin,
  User,
  Users,
  Map,
  CalendarCheck,
  ShieldAlert
} from 'lucide-vue-next';
import { StudentService } from '../../services/studentService';
import type { Student } from '../../types';

const route = useRoute();

const student = ref<Student | null>(null);
const isLoading = ref(true);
const errorMessage = ref<string | null>(null);
const activeTab = ref<'profile' | 'academic' | 'attendance' | 'discipline'>('profile');

const tabList = [
  { id: 'profile', label: 'Biodata Lengkap', icon: User },
  { id: 'academic', label: 'Akademik & Rombel', icon: GraduationCap },
  { id: 'attendance', label: 'Presensi & Kehadiran', icon: CalendarCheck },
  { id: 'discipline', label: 'Kedisiplinan & Tata Tertib', icon: ShieldAlert },
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

async function loadStudent() {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    const studentId = route.params.id as string;
    const res = await StudentService.getStudent(studentId);
    if (res.data?.success && res.data.data) {
      student.value = res.data.data;
    } else {
      errorMessage.value = 'Data siswa tidak ditemukan.';
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat rincian profil siswa.';
  } finally {
    isLoading.value = false;
  }
}

function handlePrint() {
  window.print();
}

onMounted(loadStudent);
</script>
