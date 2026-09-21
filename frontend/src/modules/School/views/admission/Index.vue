<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.ppdb.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.ppdb.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openRegisterModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <UserPlus class="size-4" />
          {{ t('school.ppdb.registerApplicant') }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.ppdb.totalApplicants') }}</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ applicants.length }} Calon</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <GraduationCap class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.ppdb.trackPrestasi') }}</div>
          <div class="text-2xl font-bold text-indigo-600 mt-1">
            {{ applicants.filter(a => a.track.includes('prestasi')).length }} Calon
          </div>
        </div>
        <div class="size-10 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <Award class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.ppdb.verified') }}</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">
            {{ applicants.filter(a => a.status === 'verified').length }} Calon
          </div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Clock class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.ppdb.admitted') }}</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">
            {{ applicants.filter(a => a.status === 'accepted').length }} Siswa
          </div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filters & Table -->
    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
          <!-- Jalur Filter -->
          <select
            v-model="filterTrack"
            @change="loadApplicants"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.ppdb.allTracks') }}</option>
            <option value="zonasi">{{ t('school.ppdb.trackZonasi') }}</option>
            <option value="afirmasi">{{ t('school.ppdb.trackAfirmasi') }}</option>
            <option value="prestasi_akademik">{{ t('school.ppdb.trackPrestasiAkademik') }}</option>
            <option value="prestasi_non_akademik">{{ t('school.ppdb.trackPrestasiNonAkademik') }}</option>
            <option value="perpindahan_ortu">{{ t('school.ppdb.trackPerpindahan') }}</option>
          </select>

          <!-- Status Filter -->
          <select
            v-model="filterStatus"
            @change="loadApplicants"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.ppdb.allStatuses') }}</option>
            <option value="submitted">{{ t('school.ppdb.statusSubmitted') }}</option>
            <option value="verified">{{ t('school.ppdb.statusVerified') }}</option>
            <option value="accepted">{{ t('school.ppdb.statusAccepted') }}</option>
            <option value="rejected">{{ t('school.ppdb.statusRejected') }}</option>
          </select>
        </div>

        <!-- Search -->
        <div class="relative">
          <input
            v-model="searchQuery"
            @keyup.enter="loadApplicants"
            type="text"
            :placeholder="t('school.ppdb.searchPlaceholder')"
            class="w-64 rounded-lg border border-border/60 bg-background pl-8 pr-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          />
          <Search class="absolute left-2.5 top-2 size-3.5 text-muted-foreground" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-border/60 bg-muted/20 text-muted-foreground">
              <th class="py-2.5 px-3 font-semibold w-12 text-center">#</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.ppdb.regNumber') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.ppdb.applicantName') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.ppdb.departmentChoice') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.ppdb.track') }}</th>
              <th class="py-2.5 px-3 font-semibold text-center">{{ t('school.ppdb.avgScore') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.common.status') }}</th>
              <th class="py-2.5 px-3 font-semibold text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-if="loading" class="text-center text-muted-foreground">
              <td colspan="8" class="py-8">{{ t('school.common.loading') }}</td>
            </tr>
            <tr v-else-if="applicants.length === 0" class="text-center text-muted-foreground">
              <td colspan="8" class="py-8">{{ t('school.ppdb.empty') }}</td>
            </tr>
            <tr
              v-else
              v-for="(app, index) in applicants"
              :key="app.id"
              class="hover:bg-muted/10 transition"
            >
              <td class="py-3 px-3 text-center text-muted-foreground font-mono">{{ Number(index) + 1 }}</td>
              <td class="py-3 px-3 font-mono font-semibold text-foreground">{{ app.registration_number }}</td>
              <td class="py-3 px-3">
                <div class="font-medium text-foreground">{{ app.full_name }}</div>
                <div class="text-[11px] text-muted-foreground font-mono">
                  NISN: {{ app.nisn || '-' }} | Asal: {{ app.previous_school || '-' }}
                </div>
              </td>
              <td class="py-3 px-3">
                <div class="font-medium text-foreground">{{ app.department?.name || '-' }}</div>
                <div v-if="app.second_choice_department" class="text-[11px] text-muted-foreground">
                  Pil 2: {{ app.second_choice_department.name }}
                </div>
              </td>
              <td class="py-3 px-3">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-indigo-500/10 text-indigo-600 uppercase">
                  {{ app.track.replace('_', ' ') }}
                </span>
              </td>
              <td class="py-3 px-3 text-center font-semibold text-foreground">
                {{ app.average_report_score }}
              </td>
              <td class="py-3 px-3">
                <span
                  :class="[
                    'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold',
                    app.status === 'accepted' ? 'bg-emerald-500/10 text-emerald-600' :
                    app.status === 'verified' ? 'bg-blue-500/10 text-blue-600' :
                    app.status === 'rejected' ? 'bg-red-500/10 text-red-600' : 'bg-amber-500/10 text-amber-600'
                  ]"
                >
                  {{ app.status }}
                </span>
              </td>
              <td class="py-3 px-3 text-right">
                <button
                  @click="openDetailModal(app)"
                  class="rounded-md border border-border/60 bg-background px-2.5 py-1 text-[11px] font-medium text-primary hover:bg-muted/40 transition"
                >
                  <Eye class="inline-block size-3 mr-1" />
                  {{ t('school.ppdb.btnVerifyDetail') }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Registrasi Calon Baru -->
    <div
      v-if="showRegisterModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-xl max-h-[90vh] overflow-y-auto rounded-xl border border-border/60 bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h2 class="text-base font-semibold text-foreground">{{ t('school.ppdb.modalTitleRegister') }}</h2>
          <button @click="showRegisterModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="saveApplicant" class="space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formFullName') }}</label>
              <input
                v-model="form.full_name"
                type="text"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formNisn') }}</label>
              <input
                v-model="form.nisn"
                type="text"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formGender') }}</label>
              <select
                v-model="form.gender"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formPhone') }}</label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formTrack') }}</label>
              <select
                v-model="form.track"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="zonasi">{{ t('school.ppdb.trackZonasi') }}</option>
                <option value="afirmasi">{{ t('school.ppdb.trackAfirmasi') }}</option>
                <option value="prestasi_akademik">{{ t('school.ppdb.trackPrestasiAkademik') }}</option>
                <option value="prestasi_non_akademik">{{ t('school.ppdb.trackPrestasiNonAkademik') }}</option>
                <option value="perpindahan_ortu">{{ t('school.ppdb.trackPerpindahan') }}</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formDepartment') }}</label>
              <select
                v-model="form.department_id"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                  {{ dept.name }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formPreviousSchool') }}</label>
              <input
                v-model="form.previous_school"
                type="text"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formAvgScore') }}</label>
              <input
                v-model.number="form.average_report_score"
                type="number"
                step="0.01"
                min="0"
                max="100"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.ppdb.formParentName') }}</label>
            <input
              v-model="form.parent_name"
              type="text"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showRegisterModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.common.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Detail & Verifikasi -->
    <div
      v-if="showDetailModal && selectedApplicant"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-lg rounded-xl border border-border/60 bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <div>
            <h2 class="text-base font-semibold text-foreground">{{ selectedApplicant.full_name }}</h2>
            <div class="text-xs text-muted-foreground font-mono">{{ selectedApplicant.registration_number }}</div>
          </div>
          <button @click="showDetailModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <div class="space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-2 bg-muted/20 p-3 rounded-lg border border-border/40">
            <div><span class="text-muted-foreground">NISN:</span> <span class="font-mono text-foreground font-semibold">{{ selectedApplicant.nisn || '-' }}</span></div>
            <div><span class="text-muted-foreground">Jalur:</span> <span class="text-foreground font-semibold uppercase">{{ selectedApplicant.track }}</span></div>
            <div><span class="text-muted-foreground">Pilihan 1:</span> <span class="text-foreground font-semibold">{{ selectedApplicant.department?.name }}</span></div>
            <div><span class="text-muted-foreground">Nilai Rata-rata:</span> <span class="text-foreground font-bold text-sm">{{ selectedApplicant.average_report_score }}</span></div>
            <div><span class="text-muted-foreground">Asal Sekolah:</span> <span class="text-foreground">{{ selectedApplicant.previous_school || '-' }}</span></div>
            <div><span class="text-muted-foreground">Orang Tua:</span> <span class="text-foreground">{{ selectedApplicant.parent_name || '-' }}</span></div>
          </div>

          <div v-if="selectedApplicant.notes" class="rounded-lg bg-amber-500/10 p-2.5 border border-amber-500/20 text-amber-700 dark:text-amber-400 text-xs">
            <strong>Catatan:</strong> {{ selectedApplicant.notes }}
          </div>

          <!-- Tindakan Verifikasi -->
          <div class="space-y-2 border-t border-border/60 pt-3">
            <div class="font-semibold text-foreground">{{ t('school.ppdb.verificationActions') }}</div>
            <div class="flex flex-wrap items-center gap-2">
              <button
                @click="updateStatus('verified')"
                :disabled="saving || selectedApplicant.status === 'verified'"
                class="rounded-lg border border-blue-500/40 bg-blue-500/10 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-500/20 transition disabled:opacity-50"
              >
                {{ t('school.ppdb.btnMarkVerified') }}
              </button>

              <button
                @click="admitApplicant"
                :disabled="saving || selectedApplicant.status === 'accepted'"
                class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50"
              >
                {{ t('school.ppdb.btnAdmitStudent') }}
              </button>

              <button
                @click="updateStatus('rejected')"
                :disabled="saving || selectedApplicant.status === 'rejected'"
                class="rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-500/20 transition disabled:opacity-50"
              >
                {{ t('school.ppdb.btnReject') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  GraduationCap,
  Award,
  Clock,
  CheckCircle2,
  Search,
  UserPlus,
  Eye,
  X,
} from 'lucide-vue-next';
import {
  SchoolService,
  type PpdbData,
  type DepartmentData,
} from '../../services/schoolService';

const { t } = useI18n();

const applicants = ref<PpdbData[]>([]);
const departments = ref<DepartmentData[]>([]);
const loading = ref(false);
const saving = ref(false);

const filterTrack = ref('');
const filterStatus = ref('');
const searchQuery = ref('');

const showRegisterModal = ref(false);
const showDetailModal = ref(false);
const selectedApplicant = ref<PpdbData | null>(null);

const form = reactive({
  full_name: '',
  nisn: '',
  gender: 'L' as 'L' | 'P',
  phone: '',
  track: 'prestasi_akademik' as 'zonasi' | 'afirmasi' | 'prestasi_akademik' | 'prestasi_non_akademik' | 'perpindahan_ortu',
  department_id: 1,
  previous_school: '',
  average_report_score: 80,
  parent_name: '',
});

const loadApplicants = async () => {
  loading.value = true;
  try {
    const res = await SchoolService.getPpdbList({
      track: filterTrack.value || undefined,
      status: filterStatus.value || undefined,
      search: searchQuery.value || undefined,
    });
    const rawApp = res.data.data;
    applicants.value = Array.isArray(rawApp) ? rawApp : ((rawApp as any)?.data ?? []);
  } catch (err) {
    console.error('Failed to load PPDB applicants:', err);
  } finally {
    loading.value = false;
  }
};

const loadDepartments = async () => {
  try {
    const res = await SchoolService.getDepartments();
    const rawDept = res.data.data;
    departments.value = Array.isArray(rawDept) ? rawDept : ((rawDept as any)?.data ?? []);
    const firstDept = departments.value[0];
    if (firstDept) {
      form.department_id = firstDept.id;
    }
  } catch (err) {
    console.error('Failed to load departments:', err);
  }
};

const openRegisterModal = () => {
  form.full_name = '';
  form.nisn = '';
  form.gender = 'L';
  form.phone = '';
  form.previous_school = '';
  form.average_report_score = 80;
  form.parent_name = '';
  showRegisterModal.value = true;
};

const saveApplicant = async () => {
  saving.value = true;
  try {
    await SchoolService.storePpdb({ ...form });
    showRegisterModal.value = false;
    await loadApplicants();
  } catch (err) {
    console.error('Failed to store applicant:', err);
  } finally {
    saving.value = false;
  }
};

const openDetailModal = (app: PpdbData) => {
  selectedApplicant.value = app;
  showDetailModal.value = true;
};

const updateStatus = async (status: string) => {
  if (!selectedApplicant.value) return;
  saving.value = true;
  try {
    await SchoolService.verifyPpdb(selectedApplicant.value.id, {
      status,
      notes: `Status diubah menjadi ${status} oleh panitia PPDB.`,
    });
    showDetailModal.value = false;
    await loadApplicants();
  } catch (err) {
    console.error('Failed to update status:', err);
  } finally {
    saving.value = false;
  }
};

const admitApplicant = async () => {
  if (!selectedApplicant.value) return;
  if (!confirm(t('school.ppdb.confirmAdmit'))) return;
  saving.value = true;
  try {
    await SchoolService.admitPpdb(selectedApplicant.value.id);
    showDetailModal.value = false;
    await loadApplicants();
  } catch (err) {
    console.error('Failed to admit applicant:', err);
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadDepartments();
  loadApplicants();
});
</script>
