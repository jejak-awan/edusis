<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.journals.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.journals.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openAddModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.journals.addJournal') }}
        </button>
      </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Jurnal KBM Terlaksana</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">
            {{ journals.length }} Sesi
          </div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <BookCheck class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Total Rombel Aktif KBM</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ studyGroups.length }} Rombel</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <School class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Guru Pengampu KBM</div>
          <div class="text-2xl font-bold text-indigo-600 mt-1">{{ teachers.length }} Pendidik</div>
        </div>
        <div class="size-10 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <GraduationCap class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
      <div class="flex flex-wrap items-center gap-2">
        <select
          v-model="filterGroupId"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadJournals"
        >
          <option value="">Semua Rombel</option>
          <option v-for="g in studyGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
        </select>

        <select
          v-model="filterSubjectId"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadJournals"
        >
          <option value="">Semua Mata Pelajaran</option>
          <option v-for="sub in subjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
        </select>
      </div>

      <div class="flex items-center gap-2">
        <input
          v-model="filterDate"
          type="date"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadJournals"
        />
      </div>
    </div>

    <!-- Journals Feed / Card List -->
    <div class="space-y-4">
      <div v-if="loading" class="rounded-xl border border-border/60 bg-card p-8 text-center text-sm text-muted-foreground">
        <div class="flex items-center justify-center gap-2">
          <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
          Memuat rekaman jurnal KBM...
        </div>
      </div>

      <div v-else-if="journals.length === 0" class="rounded-xl border border-border/60 bg-card p-8 text-center text-sm text-muted-foreground">
        <ClipboardList class="size-8 mx-auto text-muted-foreground/50 mb-2" />
        {{ t('school.journals.empty') }}
      </div>

      <div
        v-for="journal in journals"
        :key="journal.id"
        class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3 hover:border-primary/40 transition-colors"
      >
        <!-- Card Header -->
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/40 pb-3">
          <div class="flex items-center gap-3">
            <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
              {{ journal.teaching_hour }}
            </div>
            <div>
              <div class="flex items-center gap-2 font-bold text-foreground">
                <span>{{ journal.subject?.name || 'Mata Pelajaran' }}</span>
                <span class="rounded bg-muted px-2 py-0.5 text-xs font-normal text-muted-foreground">
                  {{ journal.study_group?.name || 'Rombel' }}
                </span>
              </div>
              <div class="text-xs text-muted-foreground">
                Pengampu: <strong class="text-foreground">{{ journal.teacher?.full_name || 'Guru' }}</strong> • Tanggal: {{ journal.date }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase bg-emerald-500/10 text-emerald-600">
              {{ journal.status || 'Terlaksana' }}
            </span>

            <button
              @click="openEditModal(journal)"
              class="rounded-md p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition"
              title="Edit Jurnal"
            >
              <Pen class="size-4" />
            </button>
            <button
              @click="deleteJournal(journal.id)"
              class="rounded-md p-1 text-muted-foreground hover:bg-red-500/10 hover:text-red-600 transition"
              title="Hapus Jurnal"
            >
              <Trash2 class="size-4" />
            </button>
          </div>
        </div>

        <!-- Topic & Activities -->
        <div class="space-y-1.5">
          <h4 class="text-sm font-semibold text-foreground">
            {{ journal.topic }}
          </h4>
          <p v-if="journal.learning_activity" class="text-xs text-muted-foreground leading-relaxed">
            {{ journal.learning_activity }}
          </p>
        </div>

        <!-- Student Attendance Summary Chips -->
        <div class="flex flex-wrap items-center gap-2 pt-1">
          <span class="text-xs font-medium text-muted-foreground">Presensi Kelas:</span>
          <span class="inline-flex items-center gap-1 rounded-md bg-emerald-500/10 px-2 py-0.5 text-xs font-medium text-emerald-600">
            Hadir: {{ journal.present_count }}
          </span>
          <span class="inline-flex items-center gap-1 rounded-md bg-blue-500/10 px-2 py-0.5 text-xs font-medium text-blue-600">
            Izin: {{ journal.permit_count }}
          </span>
          <span class="inline-flex items-center gap-1 rounded-md bg-amber-500/10 px-2 py-0.5 text-xs font-medium text-amber-600">
            Sakit: {{ journal.sick_count }}
          </span>
          <span class="inline-flex items-center gap-1 rounded-md bg-red-500/10 px-2 py-0.5 text-xs font-medium text-red-600">
            Alpa: {{ journal.absent_count }}
          </span>
        </div>

        <!-- Reflection / Notes -->
        <div v-if="journal.notes_reflection" class="rounded-lg bg-muted/40 p-3 text-xs text-muted-foreground border-l-2 border-primary">
          <strong class="text-foreground">Refleksi / Catatan KBM:</strong> {{ journal.notes_reflection }}
        </div>
      </div>
    </div>

    <!-- Modal Form Write/Edit Journal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-2xl rounded-2xl border border-border bg-card p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-lg font-bold text-foreground">
            {{ isEditing ? 'Edit Jurnal Pembelajaran KBM' : t('school.journals.addJournal') }}
          </h3>
          <button @click="showModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <form @submit.prevent="submitJournalForm" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.journals.studyGroup') }} *
              </label>
              <select
                v-model="form.study_group_id"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="">-- Pilih Rombel --</option>
                <option v-for="g in studyGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.journals.subject') }} *
              </label>
              <select
                v-model="form.subject_id"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="">-- Pilih Mata Pelajaran --</option>
                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.journals.teacher') }} *
              </label>
              <select
                v-model="form.teacher_id"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="">-- Pilih Guru --</option>
                <option v-for="tea in teachers" :key="tea.id" :value="tea.id">{{ tea.full_name }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.journals.date') }} *
              </label>
              <input
                v-model="form.date"
                type="date"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                Jam Pelajaran Ke- *
              </label>
              <div class="flex items-center gap-2">
                <input
                  v-model.number="form.lesson_hour_start"
                  type="number"
                  min="1"
                  max="12"
                  required
                  placeholder="Mulai"
                  class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
                />
                <span class="text-xs text-muted-foreground">s/d</span>
                <input
                  v-model.number="form.lesson_hour_end"
                  type="number"
                  min="1"
                  max="12"
                  required
                  placeholder="Selesai"
                  class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-semibold text-foreground mb-1">
                {{ t('school.journals.status') }} *
              </label>
              <select
                v-model="form.status"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="completed">{{ t('school.journals.completed') }}</option>
                <option value="rescheduled">{{ t('school.journals.rescheduled') }}</option>
                <option value="cancelled">{{ t('school.journals.cancelled') }}</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">
              {{ t('school.journals.topic') }} *
            </label>
            <input
              v-model="form.topic"
              type="text"
              required
              placeholder="Contoh: Pengenalan Clean Architecture pada Laravel & Vue"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">
              {{ t('school.journals.activities') }}
            </label>
            <textarea
              v-model="form.learning_activity"
              rows="3"
              placeholder="Jelaskan tahapan pembelajaran, metode diskusi, atau praktikum yang dilakukan..."
              class="w-full rounded-lg border border-border bg-background p-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <!-- Attendance counts -->
          <div class="rounded-xl border border-border/60 bg-muted/20 p-3 space-y-2">
            <label class="block text-xs font-semibold text-foreground">
              Rekapitulasi Kehadiran Siswa di Kelas
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
              <div>
                <span class="text-[11px] text-emerald-600 font-semibold">Hadir (H)</span>
                <input
                  v-model.number="form.present_count"
                  type="number"
                  min="0"
                  class="h-8 w-full rounded-lg border border-border bg-background px-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary mt-1"
                />
              </div>

              <div>
                <span class="text-[11px] text-blue-600 font-semibold">Izin (I)</span>
                <input
                  v-model.number="form.permit_count"
                  type="number"
                  min="0"
                  class="h-8 w-full rounded-lg border border-border bg-background px-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary mt-1"
                />
              </div>

              <div>
                <span class="text-[11px] text-amber-600 font-semibold">Sakit (S)</span>
                <input
                  v-model.number="form.sick_count"
                  type="number"
                  min="0"
                  class="h-8 w-full rounded-lg border border-border bg-background px-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary mt-1"
                />
              </div>

              <div>
                <span class="text-[11px] text-red-600 font-semibold">Alpa (A)</span>
                <input
                  v-model.number="form.absent_count"
                  type="number"
                  min="0"
                  class="h-8 w-full rounded-lg border border-border bg-background px-2 text-xs focus:outline-none focus:ring-1 focus:ring-primary mt-1"
                />
              </div>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-foreground mb-1">
              {{ t('school.journals.reflection') }}
            </label>
            <textarea
              v-model="form.notes_reflection"
              rows="2"
              placeholder="Catatan kendala, pemahaman murid, atau tindak lanjut materi berikutnya..."
              class="w-full rounded-lg border border-border bg-background p-3 text-xs focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-4">
            <button
              type="button"
              @click="showModal = false"
              class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted"
            >
              {{ t('school.staff.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 disabled:opacity-50"
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
  BookCheck,
  School,
  GraduationCap,
  Plus,
  Pen,
  Trash2,
  X,
  Save,
  ClipboardList,
} from 'lucide-vue-next';
import {
  SchoolService,
  type TeachingJournalData,
  type StudyGroupData,
  type SubjectData,
  type StaffData,
} from '../services/schoolService';

const { t } = useI18n();

const journals = ref<TeachingJournalData[]>([]);
const studyGroups = ref<StudyGroupData[]>([]);
const subjects = ref<SubjectData[]>([]);
const teachers = ref<StaffData[]>([]);

const loading = ref(false);
const saving = ref(false);
const filterGroupId = ref('');
const filterSubjectId = ref('');
const filterDate = ref('');

const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);

const form = ref({
  study_group_id: '',
  subject_id: '',
  teacher_id: '',
  date: new Date().toISOString().split('T')[0],
  lesson_hour_start: 1,
  lesson_hour_end: 2,
  topic: '',
  learning_activity: '',
  present_count: 0,
  sick_count: 0,
  permit_count: 0,
  absent_count: 0,
  notes_reflection: '',
  status: 'completed',
});

async function loadInitialData() {
  try {
    const [groupsRes, subjRes, staffRes] = await Promise.all([
      SchoolService.getStudyGroups(),
      SchoolService.getSubjects(),
      SchoolService.getStaff({ role_title: 'Guru' }),
    ]);

    if (groupsRes.data?.success) studyGroups.value = Array.isArray(groupsRes.data.data) ? groupsRes.data.data : ((groupsRes.data.data as any)?.data ?? []);
    if (subjRes.data?.success) subjects.value = Array.isArray(subjRes.data.data) ? subjRes.data.data : ((subjRes.data.data as any)?.data ?? []);
    if (staffRes.data?.success) teachers.value = Array.isArray(staffRes.data.data) ? staffRes.data.data : ((staffRes.data.data as any)?.data ?? []);

    await loadJournals();
  } catch (err) {
    console.error('Failed to load initial data for journals', err);
  }
}

async function loadJournals() {
  loading.value = true;
  try {
    const params: Record<string, unknown> = {};
    if (filterGroupId.value) params.study_group_id = filterGroupId.value;
    if (filterSubjectId.value) params.subject_id = filterSubjectId.value;
    if (filterDate.value) params.date = filterDate.value;

    const res = await SchoolService.getJournals(params);
    if (res.data?.success) {
      journals.value = Array.isArray(res.data.data) ? res.data.data : ((res.data.data as any)?.data ?? []);
    }
  } catch (err) {
    console.error('Failed to load journals', err);
  } finally {
    loading.value = false;
  }
}

function openAddModal() {
  isEditing.value = false;
  editingId.value = null;
  form.value = {
    study_group_id: studyGroups.value[0]?.id ? String(studyGroups.value[0].id) : '',
    subject_id: subjects.value[0]?.id ? String(subjects.value[0].id) : '',
    teacher_id: teachers.value[0]?.id ? String(teachers.value[0].id) : '',
    date: new Date().toISOString().split('T')[0],
    lesson_hour_start: 1,
    lesson_hour_end: 2,
    topic: '',
    learning_activity: '',
    present_count: 32,
    sick_count: 0,
    permit_count: 0,
    absent_count: 0,
    notes_reflection: '',
    status: 'completed',
  };
  showModal.value = true;
}

function openEditModal(journal: TeachingJournalData) {
  isEditing.value = true;
  editingId.value = journal.id;
  const match = (journal.teaching_hour || '').match(/(\d+)\s*[-s/d]*\s*(\d+)?/);
  const startHour = match && match[1] ? Number(match[1]) : (journal.lesson_hour_start || 1);
  const endHour = match && match[2] ? Number(match[2]) : (journal.lesson_hour_end || startHour + 1);

  form.value = {
    study_group_id: String(journal.study_group_id),
    subject_id: String(journal.subject_id),
    teacher_id: String(journal.teacher_id),
    date: journal.date,
    lesson_hour_start: startHour,
    lesson_hour_end: endHour,
    topic: journal.topic,
    learning_activity: journal.learning_activity || journal.activities || '',
    present_count: journal.present_count || 0,
    sick_count: journal.sick_count || 0,
    permit_count: journal.permit_count || journal.permission_count || 0,
    absent_count: journal.absent_count || 0,
    notes_reflection: journal.notes_reflection || '',
    status: journal.status || 'completed',
  };
  showModal.value = true;
}

async function submitJournalForm() {
  saving.value = true;
  try {
    const teachingHour = `Jam ${form.value.lesson_hour_start} - ${form.value.lesson_hour_end}`;
    const payload = {
      study_group_id: Number(form.value.study_group_id),
      subject_id: Number(form.value.subject_id),
      teacher_id: Number(form.value.teacher_id),
      date: form.value.date,
      teaching_hour: teachingHour,
      topic: form.value.topic,
      learning_activity: form.value.learning_activity,
      present_count: form.value.present_count,
      sick_count: form.value.sick_count,
      permit_count: form.value.permit_count,
      absent_count: form.value.absent_count,
      notes_reflection: form.value.notes_reflection,
    };

    if (isEditing.value && editingId.value) {
      await SchoolService.updateJournal(editingId.value, payload);
    } else {
      await SchoolService.storeJournal(payload);
    }
    showModal.value = false;
    await loadJournals();
  } catch (err) {
    console.error('Failed to save journal', err);
  } finally {
    saving.value = false;
  }
}

async function deleteJournal(id: number) {
  if (!confirm('Apakah Anda yakin ingin menghapus jurnal pembelajaran ini?')) return;
  try {
    await SchoolService.deleteJournal(id);
    await loadJournals();
  } catch (err) {
    console.error('Failed to delete journal', err);
  }
}

onMounted(() => {
  loadInitialData();
});
</script>
