<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.academic.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.academic.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          v-if="currentTab === 'years'"
          @click="openAddYearModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.academic.addYear') }}
        </button>

        <button
          v-if="currentTab === 'departments'"
          @click="openAddDeptModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.academic.addDepartment') }}
        </button>

        <button
          v-if="currentTab === 'groups'"
          @click="openAddGroupModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.academic.addGroup') }}
        </button>

        <button
          v-if="currentTab === 'subjects'"
          @click="openAddSubjectModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.academic.addSubject') }}
        </button>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex border-b border-border/60 overflow-x-auto gap-2">
      <button
        @click="currentTab = 'years'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'years'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <Calendar class="inline size-4 mr-1.5" />
        {{ t('school.academic.tabs.years') }}
      </button>

      <button
        @click="currentTab = 'departments'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'departments'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <Compass class="inline size-4 mr-1.5" />
        {{ t('school.academic.tabs.departments') }}
      </button>

      <button
        @click="currentTab = 'groups'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'groups'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <Layers class="inline size-4 mr-1.5" />
        {{ t('school.academic.tabs.studyGroups') }}
      </button>

      <button
        @click="currentTab = 'subjects'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'subjects'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <BookOpen class="inline size-4 mr-1.5" />
        {{ t('school.academic.tabs.subjects') }}
      </button>
    </div>

    <!-- Tab 1: Academic Years & Semesters -->
    <div v-if="currentTab === 'years'" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="year in academicYears"
          :key="year.id"
          class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-4 relative"
        >
          <div class="flex items-start justify-between">
            <div>
              <h3 class="text-base font-bold text-foreground">{{ year.name }}</h3>
              <p class="text-xs text-muted-foreground">
                {{ year.start_date || '-' }} s.d. {{ year.end_date || '-' }}
              </p>
            </div>
            <span
              v-if="year.is_active"
              class="inline-flex rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400"
            >
              {{ t('school.academic.active') }}
            </span>
            <button
              v-else
              @click="activateAcademicYear(year.id)"
              class="rounded border border-border px-2.5 py-1 text-xs font-medium text-muted-foreground hover:bg-muted hover:text-foreground"
            >
              {{ t('school.academic.activate') }}
            </button>
          </div>

          <div class="border-t border-border/40 pt-3 space-y-2">
            <div class="text-xs font-semibold text-muted-foreground">Semester:</div>
            <div
              v-for="sem in year.semesters"
              :key="sem.id"
              class="flex items-center justify-between rounded-lg bg-muted/20 px-3 py-2 text-xs"
            >
              <span class="font-medium text-foreground">{{ sem.name }}</span>
              <span
                v-if="sem.is_active"
                class="rounded bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary"
              >
                Aktif
              </span>
              <button
                v-else-if="year.is_active"
                @click="activateSemester(sem.id)"
                class="text-[11px] text-muted-foreground hover:text-foreground hover:underline"
              >
                Set Aktif
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 2: Departments -->
    <div v-if="currentTab === 'departments'" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="dept in departments"
          :key="dept.id"
          class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="inline-flex rounded-md bg-primary/10 px-2.5 py-1 text-xs font-bold text-primary">
              {{ dept.code }}
            </span>
            <span class="text-xs text-muted-foreground">
              {{ dept.students_count || 0 }} Siswa
            </span>
          </div>
          <div>
            <h3 class="text-base font-bold text-foreground">{{ dept.name }}</h3>
            <p class="text-xs text-muted-foreground mt-1 line-clamp-2">
              {{ dept.description || 'Kompetensi keahlian vokasi terstandar industri.' }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 3: Study Groups (Rombel) -->
    <div v-if="currentTab === 'groups'" class="space-y-4">
      <div class="overflow-x-auto rounded-xl border border-border/60 bg-card shadow-sm">
        <table class="w-full text-left text-sm">
          <thead class="border-b border-border/60 bg-muted/30 text-xs text-muted-foreground">
            <tr>
              <th class="px-4 py-3 font-semibold">Nama Rombel</th>
              <th class="px-4 py-3 font-semibold">Tingkat</th>
              <th class="px-4 py-3 font-semibold">Jurusan</th>
              <th class="px-4 py-3 font-semibold">Tahun Ajaran</th>
              <th class="px-4 py-3 font-semibold text-center">Kapasitas</th>
              <th class="px-4 py-3 font-semibold text-center">Jumlah Siswa</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-for="grp in studyGroups" :key="grp.id" class="hover:bg-muted/10 transition">
              <td class="px-4 py-3.5 font-semibold text-foreground">{{ grp.name }}</td>
              <td class="px-4 py-3.5 text-muted-foreground">{{ grp.level?.name || 'Tingkat X' }}</td>
              <td class="px-4 py-3.5">
                <span class="inline-flex rounded bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                  {{ grp.department?.code || '-' }}
                </span>
              </td>
              <td class="px-4 py-3.5 text-muted-foreground">{{ grp.academic_year?.name || '-' }}</td>
              <td class="px-4 py-3.5 text-center text-muted-foreground">{{ grp.capacity }} Siswa</td>
              <td class="px-4 py-3.5 text-center font-bold text-foreground">
                {{ grp.students_count || 0 }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab 4: Subjects (Mapel) -->
    <div v-if="currentTab === 'subjects'" class="space-y-4">
      <div class="overflow-x-auto rounded-xl border border-border/60 bg-card shadow-sm">
        <table class="w-full text-left text-sm">
          <thead class="border-b border-border/60 bg-muted/30 text-xs text-muted-foreground">
            <tr>
              <th class="px-4 py-3 font-semibold">Kode</th>
              <th class="px-4 py-3 font-semibold">Mata Pelajaran</th>
              <th class="px-4 py-3 font-semibold">Kelompok</th>
              <th class="px-4 py-3 font-semibold text-center">KKM</th>
              <th class="px-4 py-3 font-semibold text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-for="sub in subjects" :key="sub.id" class="hover:bg-muted/10 transition">
              <td class="px-4 py-3 font-mono text-xs text-primary">{{ sub.code }}</td>
              <td class="px-4 py-3 font-medium text-foreground">{{ sub.name }}</td>
              <td class="px-4 py-3">
                <span class="inline-flex rounded bg-muted px-2 py-0.5 text-xs text-muted-foreground">
                  {{ sub.group }}
                </span>
              </td>
              <td class="px-4 py-3 text-center font-semibold text-foreground">{{ sub.kkm }}</td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-600">
                  Aktif
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Add Year Modal -->
    <div v-if="showAddYearModal" class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm p-4">
      <div class="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-xl space-y-4">
        <h3 class="text-base font-bold text-foreground">Tambah Tahun Ajaran Baru</h3>
        <div class="space-y-3">
          <div>
            <label class="text-xs font-medium text-muted-foreground">Tahun Ajaran (contoh: 2027/2028)</label>
            <input
              v-model="yearForm.name"
              type="text"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-medium text-muted-foreground">Tanggal Mulai</label>
              <input
                v-model="yearForm.start_date"
                type="date"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">Tanggal Selesai</label>
              <input
                v-model="yearForm.end_date"
                type="date"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-border/40">
          <button
            @click="showAddYearModal = false"
            class="rounded-lg border border-border px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-muted"
          >
            Batal
          </button>
          <button
            @click="submitYearForm"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90"
          >
            Simpan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import { Plus, Calendar, Compass, Layers, BookOpen } from 'lucide-vue-next';
import { SchoolService } from '../services/schoolService';

const { t } = useI18n();

const currentTab = ref<'years' | 'departments' | 'groups' | 'subjects'>('years');
const academicYears = ref<any[]>([]);
const departments = ref<any[]>([]);
const studyGroups = ref<any[]>([]);
const subjects = ref<any[]>([]);

const showAddYearModal = ref(false);
const yearForm = ref({
  name: '',
  start_date: '',
  end_date: '',
  is_active: false,
});

async function loadData() {
  try {
    const [yearsRes, deptsRes, groupsRes, subsRes] = await Promise.all([
      SchoolService.getYears(),
      SchoolService.getDepartments(),
      SchoolService.getStudyGroups(),
      SchoolService.getSubjects(),
    ]);

    if (yearsRes.data?.success) academicYears.value = yearsRes.data.data;
    if (deptsRes.data?.success) departments.value = deptsRes.data.data;
    if (groupsRes.data?.success) studyGroups.value = groupsRes.data.data;
    if (subsRes.data?.success) subjects.value = subsRes.data.data;
  } catch (err) {
    console.error('Failed to load academic data', err);
  }
}

async function activateAcademicYear(id: number) {
  try {
    await SchoolService.activateYear(id);
    await loadData();
  } catch (err) {
    console.error('Failed to activate year', err);
  }
}

async function activateSemester(id: number) {
  try {
    await SchoolService.activateSemester(id);
    await loadData();
  } catch (err) {
    console.error('Failed to activate semester', err);
  }
}

function openAddYearModal() {
  yearForm.value = { name: '', start_date: '', end_date: '', is_active: false };
  showAddYearModal.value = true;
}

async function submitYearForm() {
  if (!yearForm.value.name) return;
  try {
    await SchoolService.storeYear(yearForm.value);
    showAddYearModal.value = false;
    await loadData();
  } catch (err) {
    console.error('Failed to save year', err);
  }
}

function openAddDeptModal() {
  alert('Form Tambah Jurusan siap diimplementasikan');
}
function openAddGroupModal() {
  alert('Form Tambah Rombel siap diimplementasikan');
}
function openAddSubjectModal() {
  alert('Form Tambah Mata Pelajaran siap diimplementasikan');
}

onMounted(() => {
  loadData();
});
</script>
