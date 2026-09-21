<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.attendance.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.attendance.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <div class="flex items-center gap-1.5 rounded-lg border border-border bg-card px-3 py-1.5 text-xs text-muted-foreground shadow-sm">
          <Calendar class="size-3.5 text-primary" />
          <input
            v-model="selectedDate"
            type="date"
            class="bg-transparent text-foreground focus:outline-none"
            @change="onDateChange"
          />
        </div>
      </div>
    </div>

    <!-- Mode Tabs -->
    <div class="flex border-b border-border/60 overflow-x-auto gap-2">
      <button
        @click="currentTab = 'students'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'students'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <Users class="inline size-4 mr-1.5" />
        {{ t('school.attendance.tabs.students') }}
      </button>

      <button
        @click="currentTab = 'staff'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'staff'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <UserCheck class="inline size-4 mr-1.5" />
        {{ t('school.attendance.tabs.staff') }}
      </button>
    </div>

    <!-- TAB 1: STUDENT ATTENDANCE -->
    <div v-if="currentTab === 'students'" class="space-y-6">
      <!-- Toolbar Filter & Quick Actions -->
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
        <div class="flex flex-wrap items-center gap-3">
          <label class="text-xs font-semibold text-foreground">
            {{ t('school.attendance.selectGroup') }}:
          </label>
          <select
            v-model="selectedGroupId"
            class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            @change="loadStudentAttendance"
          >
            <option value="">-- Pilih Rombel --</option>
            <option v-for="group in studyGroups" :key="group.id" :value="group.id">
              {{ group.name }} ({{ group.academic_year?.name || 'Aktif' }})
            </option>
          </select>
        </div>

        <div v-if="selectedGroupId && studentAttendance.length > 0" class="flex flex-wrap items-center gap-2">
          <button
            @click="setAllPresent"
            type="button"
            class="inline-flex items-center gap-1 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-500/20 transition"
          >
            <CheckCircle2 class="size-3.5" />
            {{ t('school.attendance.markAllPresent') }}
          </button>

          <button
            @click="saveStudentAttendance"
            :disabled="savingStudent"
            type="button"
            class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
          >
            <Save class="size-3.5" />
            {{ savingStudent ? 'Menyimpan...' : t('school.attendance.saveAttendance') }}
          </button>
        </div>
      </div>

      <!-- Attendance Stats Cards -->
      <div v-if="selectedGroupId && studentAttendance.length > 0" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="rounded-xl border border-border/60 bg-card p-3 shadow-sm text-center">
          <div class="text-[11px] text-muted-foreground uppercase font-medium">Total Siswa</div>
          <div class="text-xl font-bold text-foreground mt-0.5">{{ studentAttendance.length }}</div>
        </div>

        <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/5 p-3 shadow-sm text-center">
          <div class="text-[11px] text-emerald-600 font-medium">Hadir (H)</div>
          <div class="text-xl font-bold text-emerald-600 mt-0.5">
            {{ studentAttendance.filter(s => s.status === 'H').length }}
          </div>
        </div>

        <div class="rounded-xl border border-blue-500/20 bg-blue-500/5 p-3 shadow-sm text-center">
          <div class="text-[11px] text-blue-600 font-medium">Izin (I)</div>
          <div class="text-xl font-bold text-blue-600 mt-0.5">
            {{ studentAttendance.filter(s => s.status === 'I').length }}
          </div>
        </div>

        <div class="rounded-xl border border-amber-500/20 bg-amber-500/5 p-3 shadow-sm text-center">
          <div class="text-[11px] text-amber-600 font-medium">Sakit (S)</div>
          <div class="text-xl font-bold text-amber-600 mt-0.5">
            {{ studentAttendance.filter(s => s.status === 'S').length }}
          </div>
        </div>

        <div class="rounded-xl border border-red-500/20 bg-red-500/5 p-3 shadow-sm text-center">
          <div class="text-[11px] text-red-600 font-medium">Alpa (A)</div>
          <div class="text-xl font-bold text-red-600 mt-0.5">
            {{ studentAttendance.filter(s => s.status === 'A').length }}
          </div>
        </div>
      </div>

      <!-- Success Notification Banner -->
      <div v-if="saveSuccess" class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-700 dark:text-emerald-300 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <CheckCircle2 class="size-4 text-emerald-600" />
          {{ t('school.attendance.savedSuccess') }}
        </div>
        <button @click="saveSuccess = false" class="text-emerald-700 hover:text-emerald-900">
          <X class="size-3.5" />
        </button>
      </div>

      <!-- Student Attendance Table -->
      <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
        <div v-if="!selectedGroupId" class="p-8 text-center text-sm text-muted-foreground">
          <School class="size-8 mx-auto text-muted-foreground/50 mb-2" />
          {{ t('school.attendance.empty') }}
        </div>

        <div v-else-if="loadingStudents" class="p-8 text-center text-sm text-muted-foreground">
          <div class="flex items-center justify-center gap-2">
            <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
            Memuat data presensi rombel...
          </div>
        </div>

        <div v-else-if="studentAttendance.length === 0" class="p-8 text-center text-sm text-muted-foreground">
          Tidak ada siswa terdaftar pada rombel ini.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="border-b border-border/60 bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              <tr>
                <th class="w-12 px-4 py-3 text-center">No</th>
                <th class="px-4 py-3">{{ t('school.students.fullName') }}</th>
                <th class="px-4 py-3">NISN / L-P</th>
                <th class="px-4 py-3 text-center">Status Kehadiran</th>
                <th class="px-4 py-3">{{ t('school.attendance.notes') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
              <tr v-for="(record, index) in studentAttendance" :key="record.student_id" class="hover:bg-muted/30 transition-colors">
                <td class="px-4 py-3 text-center font-mono text-xs text-muted-foreground">
                  {{ index + 1 }}
                </td>

                <td class="px-4 py-3">
                  <div class="font-semibold text-foreground">{{ record.full_name }}</div>
                </td>

                <td class="px-4 py-3 text-xs text-muted-foreground">
                  <div class="flex items-center gap-1.5 font-mono">
                    {{ record.nisn || '-' }}
                    <span
                      :class="[
                        'text-[10px] px-1 py-0.2 rounded font-medium',
                        record.gender === 'L' ? 'bg-blue-500/10 text-blue-600' : 'bg-pink-500/10 text-pink-600'
                      ]"
                    >
                      {{ record.gender }}
                    </span>
                  </div>
                </td>

                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-1">
                    <button
                      type="button"
                      @click="record.status = 'H'"
                      :class="[
                        'size-8 rounded-lg text-xs font-bold transition flex items-center justify-center',
                        record.status === 'H'
                          ? 'bg-emerald-600 text-white shadow-sm ring-2 ring-emerald-500/30'
                          : 'bg-muted text-muted-foreground hover:bg-emerald-500/10 hover:text-emerald-600'
                      ]"
                      title="Hadir"
                    >
                      H
                    </button>

                    <button
                      type="button"
                      @click="record.status = 'I'"
                      :class="[
                        'size-8 rounded-lg text-xs font-bold transition flex items-center justify-center',
                        record.status === 'I'
                          ? 'bg-blue-600 text-white shadow-sm ring-2 ring-blue-500/30'
                          : 'bg-muted text-muted-foreground hover:bg-blue-500/10 hover:text-blue-600'
                      ]"
                      title="Izin"
                    >
                      I
                    </button>

                    <button
                      type="button"
                      @click="record.status = 'S'"
                      :class="[
                        'size-8 rounded-lg text-xs font-bold transition flex items-center justify-center',
                        record.status === 'S'
                          ? 'bg-amber-600 text-white shadow-sm ring-2 ring-amber-500/30'
                          : 'bg-muted text-muted-foreground hover:bg-amber-500/10 hover:text-amber-600'
                      ]"
                      title="Sakit"
                    >
                      S
                    </button>

                    <button
                      type="button"
                      @click="record.status = 'A'"
                      :class="[
                        'size-8 rounded-lg text-xs font-bold transition flex items-center justify-center',
                        record.status === 'A'
                          ? 'bg-red-600 text-white shadow-sm ring-2 ring-red-500/30'
                          : 'bg-muted text-muted-foreground hover:bg-red-500/10 hover:text-red-600'
                      ]"
                      title="Alpa"
                    >
                      A
                    </button>
                  </div>
                </td>

                <td class="px-4 py-3">
                  <input
                    v-model="record.notes"
                    type="text"
                    placeholder="Catatan izin / surat dokter..."
                    class="h-8 w-full rounded-lg border border-border bg-background px-2.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 2: STAFF ATTENDANCE LOG -->
    <div v-else-if="currentTab === 'staff'" class="space-y-6">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
        <div class="flex items-center gap-2">
          <Clock class="size-4 text-primary" />
          <span class="text-xs font-semibold text-foreground">Log Kehadiran Tenaga GTK Tanggal: {{ selectedDate }}</span>
        </div>

        <button
          @click="openStaffCheckInModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-1.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          <UserCheck class="size-3.5" />
          Catat Presensi GTK
        </button>
      </div>

      <!-- Staff Attendance Table -->
      <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
        <div v-if="loadingStaffAtt" class="p-8 text-center text-sm text-muted-foreground">
          <div class="flex items-center justify-center gap-2">
            <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
            Memuat log presensi GTK...
          </div>
        </div>

        <div v-else-if="staffAttendanceList.length === 0" class="p-8 text-center text-sm text-muted-foreground">
          Belum ada rekaman presensi GTK pada tanggal ini.
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="border-b border-border/60 bg-muted/40 text-xs font-semibold uppercase tracking-wider text-muted-foreground">
              <tr>
                <th class="px-4 py-3">Nama Pegawai</th>
                <th class="px-4 py-3">Jenis GTK</th>
                <th class="px-4 py-3">Jam Masuk</th>
                <th class="px-4 py-3">Jam Pulang</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Keterangan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/40">
              <tr v-for="att in staffAttendanceList" :key="att.id" class="hover:bg-muted/30 transition-colors">
                <td class="px-4 py-3 font-semibold text-foreground">
                  {{ att.staff?.full_name || 'Staff' }}
                </td>
                <td class="px-4 py-3 text-xs text-muted-foreground">
                  {{ att.staff?.role_title || att.staff?.staff_type || '-' }}
                </td>
                <td class="px-4 py-3 font-mono text-xs">
                  {{ att.time_in ? att.time_in.slice(0, 5) : '-' }}
                </td>
                <td class="px-4 py-3 font-mono text-xs">
                  {{ att.time_out ? att.time_out.slice(0, 5) : '-' }}
                </td>
                <td class="px-4 py-3">
                  <span
                    :class="[
                      'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                      att.status === 'present' ? 'bg-emerald-500/10 text-emerald-600' :
                      att.status === 'late' ? 'bg-amber-500/10 text-amber-600' :
                      att.status === 'absent' ? 'bg-red-500/10 text-red-600' : 'bg-blue-500/10 text-blue-600'
                    ]"
                  >
                    {{ att.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-xs text-muted-foreground">
                  {{ att.notes || '-' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Staff Check-In -->
    <div
      v-if="showStaffModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-md rounded-2xl border border-border bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-base font-bold text-foreground">Catat Presensi Masuk GTK</h3>
          <button @click="showStaffModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <form @submit.prevent="submitStaffCheckIn" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">Pilih Pegawai GTK *</label>
            <select
              v-model="staffForm.staff_id"
              required
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="">-- Pilih GTK --</option>
              <option v-for="st in allStaff" :key="st.id" :value="st.id">
                {{ st.full_name }} ({{ st.role_title }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">Status Kehadiran *</label>
            <select
              v-model="staffForm.status"
              required
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="present">Hadir Tepat Waktu</option>
              <option value="late">Terlambat</option>
              <option value="permit">Izin / Dinas Luar</option>
              <option value="sick">Sakit</option>
              <option value="absent">Alpa</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">Catatan / Keterangan</label>
            <input
              v-model="staffForm.notes"
              type="text"
              placeholder="Contoh: Terlambat macet, izin seminar"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-4">
            <button
              type="button"
              @click="showStaffModal = false"
              class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
            >
              Batal
            </button>
            <button
              type="submit"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90"
            >
              Simpan Presensi
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
  Calendar,
  Users,
  UserCheck,
  CheckCircle2,
  Save,
  School,
  Clock,
  X,
} from 'lucide-vue-next';
import {
  SchoolService,
  type StudyGroupData,
  type StudentAttendanceItem,
  type StaffData,
  type StaffAttendanceData,
} from '../services/schoolService';

const { t } = useI18n();

const currentTab = ref<'students' | 'staff'>('students');
const selectedDate = ref<string>(new Date().toISOString().split('T')[0] || '');
const selectedGroupId = ref<number | ''>('');
const studyGroups = ref<StudyGroupData[]>([]);
const studentAttendance = ref<StudentAttendanceItem[]>([]);
const loadingStudents = ref(false);
const savingStudent = ref(false);
const saveSuccess = ref(false);

// Staff Attendance
const allStaff = ref<StaffData[]>([]);
const staffAttendanceList = ref<StaffAttendanceData[]>([]);
const loadingStaffAtt = ref(false);
const showStaffModal = ref(false);
const staffForm = ref({
  staff_id: '',
  status: 'present',
  notes: '',
});

async function loadInitialData() {
  try {
    const [groupsRes, staffRes] = await Promise.all([
      SchoolService.getStudyGroups(),
      SchoolService.getStaff(),
    ]);

    if (groupsRes.data?.success) {
      studyGroups.value = Array.isArray(groupsRes.data.data) ? groupsRes.data.data : ((groupsRes.data.data as any)?.data ?? []);
      const first = studyGroups.value[0];
      if (first) {
        selectedGroupId.value = first.id;
        await loadStudentAttendance();
      }
    }

    if (staffRes.data?.success) {
      allStaff.value = Array.isArray(staffRes.data.data) ? staffRes.data.data : ((staffRes.data.data as any)?.data ?? []);
    }

    await loadStaffAttendance();
  } catch (err) {
    console.error('Failed to load initial attendance data', err);
  }
}

async function loadStudentAttendance() {
  if (!selectedGroupId.value) return;
  loadingStudents.value = true;
  saveSuccess.value = false;
  try {
    const res = await SchoolService.getStudentAttendance({
      study_group_id: Number(selectedGroupId.value),
      date: selectedDate.value || new Date().toISOString().slice(0, 10),
    });
    if (res.data?.success) {
      const data = res.data.data;
      studentAttendance.value = Array.isArray(data) ? data : ((data as any)?.attendances ?? (data as any)?.data ?? []);
    }
  } catch (err) {
    console.error('Failed to load student attendance', err);
  } finally {
    loadingStudents.value = false;
  }
}

async function loadStaffAttendance() {
  loadingStaffAtt.value = true;
  try {
    const res = await SchoolService.getStaffAttendance({
      date: selectedDate.value || new Date().toISOString().slice(0, 10),
    });
    if (res.data?.success) {
      const data = res.data.data;
      staffAttendanceList.value = Array.isArray(data) ? data : ((data as any)?.attendances ?? (data as any)?.data ?? []);
    }
  } catch (err) {
    console.error('Failed to load staff attendance', err);
  } finally {
    loadingStaffAtt.value = false;
  }
}

function onDateChange() {
  if (currentTab.value === 'students') {
    loadStudentAttendance();
  } else {
    loadStaffAttendance();
  }
}

function setAllPresent() {
  studentAttendance.value.forEach(record => {
    record.status = 'H';
  });
}

async function saveStudentAttendance() {
  if (!selectedGroupId.value) return;
  savingStudent.value = true;
  saveSuccess.value = false;
  try {
    const payload = {
      study_group_id: Number(selectedGroupId.value),
      date: selectedDate.value || new Date().toISOString().slice(0, 10),
      records: studentAttendance.value.map(r => ({
        student_id: r.student_id,
        status: r.status,
        notes: r.notes ?? undefined,
      })),
    };
    await SchoolService.saveBulkStudentAttendance(payload);
    saveSuccess.value = true;
  } catch (err) {
    console.error('Failed to save student attendance', err);
  } finally {
    savingStudent.value = false;
  }
}

function openStaffCheckInModal() {
  staffForm.value = {
    staff_id: '',
    status: 'present',
    notes: '',
  };
  showStaffModal.value = true;
}

async function submitStaffCheckIn() {
  if (!staffForm.value.staff_id) return;
  try {
    await SchoolService.recordStaffCheckIn({
      staff_id: Number(staffForm.value.staff_id),
      date: selectedDate.value || new Date().toISOString().slice(0, 10),
      time_in: new Date().toTimeString().slice(0, 8),
      status: staffForm.value.status,
      notes: staffForm.value.notes || undefined,
    });
    showStaffModal.value = false;
    await loadStaffAttendance();
  } catch (err) {
    console.error('Failed to record staff check in', err);
  }
}

onMounted(() => {
  loadInitialData();
});
</script>
