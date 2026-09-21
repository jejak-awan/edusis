<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.staff.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.staff.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openAddModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <UserPlus class="size-4" />
          {{ t('school.staff.addStaff') }}
        </button>
      </div>
    </div>

    <!-- KPI Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Total GTK Terdaftar</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ staffList.length }} Orang</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <Users class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Guru & Pendidik</div>
          <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
            {{ staffList.filter(s => (s.staff_type || s.role_title || '').toLowerCase().includes('guru') || s.staff_type === 'teacher').length }} Guru
          </div>
        </div>
        <div class="size-10 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <GraduationCap class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Tenaga Kependidikan</div>
          <div class="text-2xl font-bold text-sky-600 dark:text-sky-400 mt-1">
            {{ staffList.filter(s => !(s.staff_type || s.role_title || '').toLowerCase().includes('guru') && s.staff_type !== 'teacher').length }} Tenaga
          </div>
        </div>
        <div class="size-10 rounded-lg bg-sky-500/10 text-sky-600 flex items-center justify-center">
          <Briefcase class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Status ASN (PNS/PPPK)</div>
          <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
            {{ staffList.filter(s => (s.employment_status || '').toUpperCase() === 'PNS' || (s.employment_status || '').toUpperCase() === 'PPPK').length }} Pegawai
          </div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <Award class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
      <div class="relative w-full sm:max-w-xs">
        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('school.staff.searchPlaceholder')"
          class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          @input="onSearch"
        />
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <select
          v-model="selectedType"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadStaff"
        >
          <option value="">{{ t('school.staff.filterType') }}</option>
          <option value="teacher">{{ t('school.staff.teacher') }}</option>
          <option value="admin">{{ t('school.staff.admin') }}</option>
          <option value="lab">{{ t('school.staff.lab') }}</option>
          <option value="librarian">{{ t('school.staff.librarian') }}</option>
          <option value="security">{{ t('school.staff.security') }}</option>
          <option value="cleaning">{{ t('school.staff.cleaning') }}</option>
          <option value="other">{{ t('school.staff.other') }}</option>
        </select>

        <select
          v-model="selectedStatus"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadStaff"
        >
          <option value="">{{ t('school.staff.filterStatus') }}</option>
          <option value="PNS">PNS</option>
          <option value="PPPK">PPPK</option>
          <option value="Honorer">Honorer / GTT / PTT</option>
          <option value="GTY">GTY / Pegawai Yayasan</option>
        </select>
      </div>
    </div>

    <!-- Staff Table -->
    <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="border-b border-border/60 bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
            <tr>
              <th class="px-4 py-3">{{ t('school.staff.name') }}</th>
              <th class="px-4 py-3">{{ t('school.staff.type') }}</th>
              <th class="px-4 py-3">{{ t('school.staff.nip') }} / {{ t('school.staff.nuptk') }}</th>
              <th class="px-4 py-3">{{ t('school.staff.employmentStatus') }}</th>
              <th class="px-4 py-3">{{ t('school.staff.phone') }}</th>
              <th class="px-4 py-3 text-right">{{ t('school.staff.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-if="loading" class="hover:bg-muted/30">
              <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
                <div class="flex items-center justify-center gap-2">
                  <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
                  Memuat data GTK...
                </div>
              </td>
            </tr>

            <tr v-else-if="staffList.length === 0" class="hover:bg-muted/30">
              <td colspan="6" class="px-4 py-8 text-center text-sm text-muted-foreground">
                {{ t('school.staff.empty') }}
              </td>
            </tr>

            <tr v-for="staff in staffList" :key="staff.id" class="hover:bg-muted/30 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="size-9 rounded-full bg-primary/10 text-primary font-bold text-xs flex items-center justify-center">
                    {{ getInitials(staff.full_name) }}
                  </div>
                  <div>
                    <div class="font-semibold text-foreground flex items-center gap-1.5">
                      {{ staff.full_name }}
                      <span
                        :class="[
                          'text-[10px] px-1.5 py-0.5 rounded font-medium',
                          staff.gender === 'L' ? 'bg-blue-500/10 text-blue-600' : 'bg-pink-500/10 text-pink-600'
                        ]"
                      >
                        {{ staff.gender === 'L' ? 'L' : 'P' }}
                      </span>
                    </div>
                    <div class="text-xs text-muted-foreground">
                      {{ staff.email || 'Tanpa email' }}
                    </div>
                  </div>
                </div>
              </td>

              <td class="px-4 py-3">
                <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium', getStaffTypeBadgeClass(staff.staff_type || staff.role_title)]">
                  {{ formatStaffType(staff.staff_type || staff.role_title) }}
                </span>
              </td>

              <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                <div v-if="staff.nip">NIP: {{ staff.nip }}</div>
                <div v-if="staff.nuptk">NUPTK: {{ staff.nuptk }}</div>
                <div v-if="!staff.nip && !staff.nuptk">-</div>
              </td>

              <td class="px-4 py-3">
                <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium uppercase', getStatusBadgeClass(staff.employment_status)]">
                  {{ staff.employment_status }}
                </span>
              </td>

              <td class="px-4 py-3 text-xs text-muted-foreground">
                <div v-if="staff.phone" class="flex items-center gap-1">
                  <Phone class="size-3 text-primary" />
                  {{ staff.phone }}
                </div>
                <span v-else>-</span>
              </td>

              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button
                    @click="openEditModal(staff)"
                    class="rounded-md p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition"
                    title="Edit GTK"
                  >
                    <Pen class="size-4" />
                  </button>
                  <button
                    @click="deleteStaff(staff.id)"
                    class="rounded-md p-1.5 text-muted-foreground hover:bg-red-500/10 hover:text-red-600 transition"
                    title="Hapus GTK"
                  >
                    <Trash2 class="size-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Form Add/Edit Staff -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-xl rounded-2xl border border-border bg-card p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-lg font-bold text-foreground">
            {{ isEditing ? 'Edit Data Tenaga GTK' : t('school.staff.addStaff') }}
          </h3>
          <button @click="showModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <form @submit.prevent="submitStaffForm" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">
              {{ t('school.staff.name') }} *
            </label>
            <input
              v-model="form.full_name"
              type="text"
              required
              placeholder="Contoh: Drs. H. Ahmad Sudrajat, M.Pd"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.staff.type') }} *
              </label>
              <select
                v-model="form.staff_type"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="teacher">{{ t('school.staff.teacher') }}</option>
                <option value="admin">{{ t('school.staff.admin') }}</option>
                <option value="lab">{{ t('school.staff.lab') }}</option>
                <option value="librarian">{{ t('school.staff.librarian') }}</option>
                <option value="security">{{ t('school.staff.security') }}</option>
                <option value="cleaning">{{ t('school.staff.cleaning') }}</option>
                <option value="other">{{ t('school.staff.other') }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Jenis Kelamin *
              </label>
              <select
                v-model="form.gender"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="L">Laki-laki (L)</option>
                <option value="P">Perempuan (P)</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.staff.nip') }} (18 Digit)
              </label>
              <input
                v-model="form.nip"
                type="text"
                placeholder="198001012005011001"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.staff.nuptk') }} (16 Digit)
              </label>
              <input
                v-model="form.nuptk"
                type="text"
                placeholder="1234567890123456"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.staff.employmentStatus') }} *
              </label>
              <select
                v-model="form.employment_status"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="PNS">PNS</option>
                <option value="PPPK">PPPK</option>
                <option value="Honorer">Honorer / GTT / PTT</option>
                <option value="GTY">GTY / Pegawai Yayasan</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                NIK (No. KTP)
              </label>
              <input
                v-model="form.nik"
                type="text"
                placeholder="3210000000000001"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.staff.phone') }}
              </label>
              <input
                v-model="form.phone"
                type="text"
                placeholder="081234567890"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.staff.email') }}
              </label>
              <input
                v-model="form.email"
                type="email"
                placeholder="guru@sekolah.sch.id"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-4">
            <button
              type="button"
              @click="showModal = false"
              class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
            >
              {{ t('school.staff.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 disabled:opacity-50"
            >
              <Save class="size-4" />
              {{ saving ? 'Menyimpan...' : t('school.staff.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Users,
  UserPlus,
  GraduationCap,
  Briefcase,
  Award,
  Search,
  Pen,
  Trash2,
  X,
  Save,
  Phone,
} from 'lucide-vue-next';
import { SchoolService, type StaffData } from '../../services/schoolService';

const { t } = useI18n();

const staffList = ref<StaffData[]>([]);
const loading = ref(false);
const saving = ref(false);
const searchQuery = ref('');
const selectedType = ref('');
const selectedStatus = ref('');

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = ref({
  full_name: '',
  staff_type: 'teacher',
  role_title: 'Guru Mata Pelajaran',
  gender: 'L' as 'L' | 'P',
  nip: '',
  nuptk: '',
  nik: '',
  employment_status: 'PNS',
  phone: '',
  email: '',
  is_active: true,
});

async function loadStaff() {
  loading.value = true;
  try {
    const params: Record<string, unknown> = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (selectedType.value) params.staff_type = selectedType.value;
    if (selectedStatus.value) params.employment_status = selectedStatus.value;

    const res = await SchoolService.getStaff(params);
    if (res.data?.success) {
      staffList.value = Array.isArray(res.data.data) ? res.data.data : ((res.data.data as any)?.data ?? []);
    }
  } catch (err) {
    console.error('Failed to load staff list', err);
  } finally {
    loading.value = false;
  }
}

let searchTimer: any = null;
function onSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => {
    loadStaff();
  }, 300);
}

function openAddModal() {
  isEditing.value = false;
  editingId.value = null;
  form.value = {
    full_name: '',
    staff_type: 'teacher',
    role_title: 'Guru Mata Pelajaran',
    gender: 'L',
    nip: '',
    nuptk: '',
    nik: '',
    employment_status: 'PNS',
    phone: '',
    email: '',
    is_active: true,
  };
  showModal.value = true;
}

function openEditModal(staff: StaffData) {
  isEditing.value = true;
  editingId.value = staff.id;
  form.value = {
    full_name: staff.full_name,
    staff_type: staff.staff_type || (staff.role_title?.toLowerCase().includes('guru') ? 'teacher' : 'admin'),
    role_title: staff.role_title || 'Guru Mata Pelajaran',
    gender: staff.gender,
    nip: staff.nip || '',
    nuptk: staff.nuptk || '',
    nik: staff.nik || '',
    employment_status: staff.employment_status || 'PNS',
    phone: staff.phone || '',
    email: staff.email || '',
    is_active: staff.is_active,
  };
  showModal.value = true;
}

async function submitStaffForm() {
  saving.value = true;
  try {
    const roleTitleMap: Record<string, string> = {
      teacher: 'Guru Mata Pelajaran',
      admin: 'Staf Tata Usaha',
      lab: 'Laboran',
      librarian: 'Pustakawan',
      security: 'Petugas Keamanan',
      cleaning: 'Petugas Kebersihan',
      other: 'Tenaga Kependidikan',
    };
    const payload = {
      ...form.value,
      role_title: roleTitleMap[form.value.staff_type] || form.value.role_title,
    };

    if (isEditing.value && editingId.value) {
      await SchoolService.updateStaff(editingId.value, payload);
    } else {
      await SchoolService.storeStaff(payload);
    }
    showModal.value = false;
    await loadStaff();
  } catch (err) {
    console.error('Failed to save staff', err);
  } finally {
    saving.value = false;
  }
}

async function deleteStaff(id: number) {
  if (!confirm('Apakah Anda yakin ingin menghapus data GTK ini?')) return;
  try {
    await SchoolService.deleteStaff(id);
    await loadStaff();
  } catch (err) {
    console.error('Failed to delete staff', err);
  }
}

function getInitials(name: string): string {
  if (!name) return '??';
  const clean = name.replace(/^(drs|dra|prof|dr|ir|h|hj)\.?\s+/gi, '').trim();
  const parts = clean.split(' ');
  const first = parts[0]?.[0];
  const second = parts[1]?.[0];
  if (first && second) {
    return (first + second).toUpperCase();
  }
  return name.slice(0, 2).toUpperCase();
}

function formatStaffType(type?: string): string {
  if (!type) return '-';
  const map: Record<string, string> = {
    teacher: 'Guru',
    admin: 'Tata Usaha',
    lab: 'Laboran',
    librarian: 'Pustakawan',
    security: 'Keamanan',
    cleaning: 'Kebersihan',
    other: 'Lainnya',
  };
  return map[type] || type;
}

function getStaffTypeBadgeClass(type?: string): string {
  if (!type) return 'bg-muted text-muted-foreground border border-border';
  const lower = type.toLowerCase();
  if (lower.includes('guru') || lower === 'teacher') {
    return 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-500/20';
  }
  if (lower.includes('tu') || lower.includes('tata') || lower === 'admin') {
    return 'bg-sky-500/10 text-sky-700 dark:text-sky-400 border border-sky-500/20';
  }
  if (lower.includes('lab') || lower.includes('pustaka')) {
    return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20';
  }
  return 'bg-muted text-muted-foreground border border-border';
}

function getStatusBadgeClass(status?: string): string {
  const s = (status || '').toLowerCase();
  if (s === 'pns') {
    return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20';
  }
  if (s === 'pppk') {
    return 'bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-500/20';
  }
  if (s.includes('honorer') || s.includes('gtt') || s.includes('ptt')) {
    return 'bg-purple-500/10 text-purple-700 dark:text-purple-400 border border-purple-500/20';
  }
  return 'bg-muted text-muted-foreground border border-border';
}

onMounted(() => {
  loadStaff();
});
</script>
