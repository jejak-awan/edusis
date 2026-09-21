<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.students.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.students.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openRegisterModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <UserPlus class="size-4" />
          {{ t('school.students.addStudent') }}
        </button>
      </div>
    </div>

    <!-- Toolbar Filters -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
      <div class="relative w-full sm:max-w-xs">
        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="t('school.students.searchPlaceholder')"
          class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          @input="onSearch"
        />
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <select
          v-model="selectedDepartment"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadStudents"
        >
          <option value="">{{ t('school.students.filterDepartment') }}</option>
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">
            {{ dept.code }} - {{ dept.name }}
          </option>
        </select>

        <select
          v-model="selectedStatus"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
          @change="loadStudents"
        >
          <option value="">{{ t('school.students.filterStatus') }}</option>
          <option value="active">Aktif</option>
          <option value="graduated">Lulus</option>
          <option value="transferred">Mutasi Keluar</option>
          <option value="dropped_out">Dikeluarkan</option>
        </select>
      </div>
    </div>

    <!-- Student Data Table -->
    <div class="overflow-x-auto rounded-xl border border-border/60 bg-card shadow-sm">
      <table class="w-full text-left text-sm">
        <thead class="border-b border-border/60 bg-muted/30 text-xs text-muted-foreground">
          <tr>
            <th class="px-4 py-3 font-semibold">{{ t('school.students.nisn') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('school.students.fullName') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('school.students.department') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('school.students.studyGroup') }}</th>
            <th class="px-4 py-3 font-semibold text-center">{{ t('school.students.creditScore') }}</th>
            <th class="px-4 py-3 font-semibold">Orang Tua / Bantuan</th>
            <th class="px-4 py-3 font-semibold text-center">{{ t('school.students.status') }}</th>
            <th class="px-4 py-3 font-semibold text-right">{{ t('school.students.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border/40">
          <tr v-for="student in students" :key="student.id" class="hover:bg-muted/10 transition">
            <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
              <div>{{ student.nisn || '-' }}</div>
              <div class="text-[11px] text-muted-foreground/60">NIS: {{ student.nis || '-' }}</div>
            </td>
            <td class="px-4 py-3">
              <div class="font-semibold text-foreground">{{ student.full_name }}</div>
              <div class="text-xs text-muted-foreground">
                {{ student.gender === 'L' ? 'Laki-laki' : 'Perempuan' }} • {{ student.place_of_birth || '-' }}, {{ student.date_of_birth || '-' }}
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex rounded bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary">
                {{ student.department?.code || '-' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs font-medium text-foreground">
              {{ student.study_groups?.[0]?.name || '-' }}
            </td>
            <td class="px-4 py-3 text-center">
              <span
                :class="[
                  'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-bold',
                  (student.total_credit_score ?? 100) >= 95
                    ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                    : (student.total_credit_score ?? 100) >= 75
                    ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400'
                    : 'bg-red-500/10 text-red-600 dark:text-red-400'
                ]"
              >
                <ShieldCheck v-if="(student.total_credit_score ?? 100) >= 95" class="size-3" />
                <ShieldAlert v-else class="size-3" />
                {{ student.total_credit_score ?? 100 }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs">
              <div class="text-foreground font-medium">{{ student.parent?.father_name || student.parent?.mother_name || '-' }}</div>
              <div class="flex gap-1 mt-0.5">
                <span v-if="student.parent?.is_kip" class="rounded bg-blue-500/10 px-1.5 py-0.2 text-[10px] font-semibold text-blue-600">
                  KIP
                </span>
                <span v-if="student.parent?.is_pkh" class="rounded bg-purple-500/10 px-1.5 py-0.2 text-[10px] font-semibold text-purple-600">
                  PKH
                </span>
                <span v-if="!student.parent?.is_kip && !student.parent?.is_pkh" class="text-muted-foreground text-[11px]">
                  Umum
                </span>
              </div>
            </td>
            <td class="px-4 py-3 text-center">
              <span class="inline-flex rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-600">
                Aktif
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-1.5">
                <button
                  @click="openQuickPointModal(student)"
                  class="rounded p-1.5 text-muted-foreground hover:bg-amber-500/10 hover:text-amber-600 transition"
                  title="Catat Poin Perilaku"
                >
                  <ShieldAlert class="size-4" />
                </button>
                <router-link
                  :to="{ name: 'school.students.edit', params: { id: student.id } }"
                  class="rounded p-1.5 text-muted-foreground hover:bg-primary/10 hover:text-primary transition"
                  title="Ubah Data Siswa"
                >
                  <Edit2 class="size-4" />
                </router-link>
                <button
                  @click="openStudentDetail(student.id)"
                  class="rounded p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition"
                  title="Lihat Profil"
                >
                  <Eye class="size-4" />
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="!students.length">
            <td colspan="8" class="py-12 text-center text-xs text-muted-foreground">
              {{ t('school.students.empty') }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Quick Award Point Modal -->
    <div v-if="showPointModal" class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm p-4">
      <div class="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/50 pb-3">
          <h3 class="text-base font-bold text-foreground">Catat Poin: {{ activeStudentTarget?.full_name }}</h3>
          <button @click="showPointModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <div class="space-y-3">
          <div>
            <label class="text-xs font-medium text-muted-foreground">Kategori Poin</label>
            <select
              v-model="pointForm.category_id"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              @change="onCategoryChange"
            >
              <option value="">Pilih Kategori...</option>
              <optgroup label="Pelanggaran (Negatif)">
                <option
                  v-for="cat in pointCategories.filter(c => c.type === 'pelanggaran')"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.name }} ({{ cat.default_points }} poin)
                </option>
              </optgroup>
              <optgroup label="Prestasi (Positif)">
                <option
                  v-for="cat in pointCategories.filter(c => c.type === 'prestasi')"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.name }} (+{{ cat.default_points }} poin)
                </option>
              </optgroup>
            </select>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Nilai Poin (Bisa disesuaikan)</label>
            <input
              v-model.number="pointForm.points"
              type="number"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Deskripsi / Kronologi Kejadian</label>
            <textarea
              v-model="pointForm.description"
              rows="3"
              placeholder="Jelaskan detail kejadian..."
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            ></textarea>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Tanggal</label>
            <input
              v-model="pointForm.date"
              type="date"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            />
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-border/40">
          <button
            @click="showPointModal = false"
            class="rounded-lg border border-border px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-muted"
          >
            Batal
          </button>
          <button
            @click="submitPointForm"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90"
          >
            Simpan Poin
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import {
  Search,
  UserPlus,
  ShieldCheck,
  ShieldAlert,
  Eye,
  Edit2,
  X,
} from 'lucide-vue-next';
import { SchoolService } from '../../services/schoolService';

const { t } = useI18n();
const router = useRouter();

const students = ref<any[]>([]);
const departments = ref<any[]>([]);
const pointCategories = ref<any[]>([]);

const searchQuery = ref('');
const selectedDepartment = ref('');
const selectedStatus = ref('');

const showPointModal = ref(false);
const activeStudentTarget = ref<any>(null);
const pointForm = ref({
  student_id: 0,
  category_id: '',
  points: 0,
  description: '',
  date: new Date().toISOString().split('T')[0],
});

let debounceTimer: any = null;
function onSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    loadStudents();
  }, 300);
}

async function loadStudents() {
  try {
    const params: Record<string, any> = {};
    if (searchQuery.value) params.search = searchQuery.value;
    if (selectedDepartment.value) params.department_id = selectedDepartment.value;
    if (selectedStatus.value) params.status = selectedStatus.value;

    const res = await SchoolService.getStudents(params);
    const raw = res.data?.data ?? res.data;
    students.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
  } catch (err) {
    console.error('Failed to load students', err);
  }
}

async function loadFilterData() {
  try {
    const [deptRes, catRes] = await Promise.all([
      SchoolService.getDepartments(),
      SchoolService.getDisciplineCategories(),
    ]);

    departments.value = Array.isArray(deptRes.data?.data) ? deptRes.data.data : ((deptRes.data as any)?.data ?? []);
    pointCategories.value = Array.isArray(catRes.data?.data) ? catRes.data.data : ((catRes.data as any)?.data ?? []);
  } catch (err) {
    console.error('Failed to load filter data', err);
  }
}

function openQuickPointModal(student: any) {
  activeStudentTarget.value = student;
  pointForm.value = {
    student_id: student.id,
    category_id: '',
    points: 0,
    description: '',
    date: new Date().toISOString().split('T')[0],
  };
  showPointModal.value = true;
}

function onCategoryChange() {
  const cat = pointCategories.value.find(c => c.id === Number(pointForm.value.category_id));
  if (cat) {
    pointForm.value.points = cat.default_points;
    if (!pointForm.value.description) {
      pointForm.value.description = cat.name;
    }
  }
}

async function submitPointForm() {
  if (!pointForm.value.category_id || !pointForm.value.description) return;
  try {
    await SchoolService.recordPoint(pointForm.value);
    showPointModal.value = false;
    await loadStudents();
  } catch (err) {
    console.error('Failed to record point', err);
  }
}

function openStudentDetail(id: number) {
  router.push({ name: 'school.students.edit', params: { id } });
}

function openRegisterModal() {
  router.push({ name: 'school.students.create' });
}

onMounted(async () => {
  await Promise.all([loadStudents(), loadFilterData()]);
});
</script>
