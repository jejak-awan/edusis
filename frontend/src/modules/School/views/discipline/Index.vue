<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.discipline.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.discipline.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openRecordModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <ShieldAlert class="size-4" />
          {{ t('school.discipline.recordButton') }}
        </button>
      </div>
    </div>

    <!-- Top KPI Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Pelanggaran Tercatat</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ violations.length }} Kasus</div>
        </div>
        <div class="size-10 rounded-lg bg-red-500/10 text-red-600 flex items-center justify-center">
          <AlertTriangle class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Surat Peringatan (SP)</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">
            {{ violations.filter(v => v.sp_level && v.sp_level !== 'none').length }} Siswa
          </div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <FileText class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">Prestasi Siswa</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">{{ achievements.length }} Piagam</div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <Award class="size-5" />
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-border/60 overflow-x-auto gap-2">
      <button
        @click="currentTab = 'violations'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'violations'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <AlertTriangle class="inline size-4 mr-1.5" />
        {{ t('school.discipline.violationsTab') }}
      </button>

      <button
        @click="currentTab = 'achievements'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'achievements'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <Award class="inline size-4 mr-1.5" />
        {{ t('school.discipline.achievementsTab') }}
      </button>

      <button
        @click="currentTab = 'categories'"
        :class="[
          'px-4 py-2.5 text-sm font-medium border-b-2 transition whitespace-nowrap',
          currentTab === 'categories'
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <BookOpen class="inline size-4 mr-1.5" />
        Kategori & Skor Regulasi
      </button>
    </div>

    <!-- Tab 1: Violations Table -->
    <div v-if="currentTab === 'violations'" class="space-y-4">
      <div class="overflow-x-auto rounded-xl border border-border/60 bg-card shadow-sm">
        <table class="w-full text-left text-sm">
          <thead class="border-b border-border/60 bg-muted/30 text-xs text-muted-foreground">
            <tr>
              <th class="px-4 py-3 font-semibold">Tanggal</th>
              <th class="px-4 py-3 font-semibold">Siswa & Jurusan</th>
              <th class="px-4 py-3 font-semibold">Pelanggaran</th>
              <th class="px-4 py-3 font-semibold text-center">Poin</th>
              <th class="px-4 py-3 font-semibold">Sanksi Edukatif</th>
              <th class="px-4 py-3 font-semibold text-center">Tingkat SP</th>
              <th class="px-4 py-3 font-semibold text-center">Status</th>
              <th class="px-4 py-3 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-for="violation in violations" :key="violation.id" class="hover:bg-muted/10 transition">
              <td class="px-4 py-3.5 text-xs text-muted-foreground whitespace-nowrap">
                {{ violation.violation_date }}
              </td>
              <td class="px-4 py-3.5">
                <div class="font-semibold text-foreground">{{ violation.student?.full_name || 'Siswa' }}</div>
                <div class="text-xs text-muted-foreground">Jurusan {{ violation.student?.department?.code || '-' }}</div>
              </td>
              <td class="px-4 py-3.5">
                <div class="text-foreground">{{ violation.credit_point?.description || '-' }}</div>
                <div class="text-xs text-muted-foreground">{{ violation.credit_point?.category?.name || 'Tata Tertib' }}</div>
              </td>
              <td class="px-4 py-3.5 text-center font-bold text-red-600">
                {{ violation.credit_point?.points || -5 }}
              </td>
              <td class="px-4 py-3.5 text-xs text-muted-foreground max-w-xs">
                {{ violation.sanction || 'Peringatan lisan' }}
              </td>
              <td class="px-4 py-3.5 text-center">
                <span
                  v-if="violation.sp_level && violation.sp_level !== 'none'"
                  class="inline-flex rounded bg-red-500/10 px-2 py-0.5 text-xs font-bold text-red-600 uppercase"
                >
                  {{ violation.sp_level }}
                </span>
                <span v-else class="text-xs text-muted-foreground">-</span>
              </td>
              <td class="px-4 py-3.5 text-center">
                <span
                  v-if="violation.is_resolved"
                  class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-600"
                >
                  <CheckCircle2 class="size-3" />
                  Selesai
                </span>
                <span
                  v-else
                  class="inline-flex rounded-full bg-amber-500/10 px-2 py-0.5 text-[11px] font-semibold text-amber-600"
                >
                  Pending
                </span>
              </td>
              <td class="px-4 py-3.5 text-right">
                <button
                  v-if="!violation.is_resolved"
                  @click="resolveViolation(violation.id)"
                  class="rounded bg-muted px-2.5 py-1 text-xs font-medium text-foreground hover:bg-primary hover:text-primary-foreground transition"
                >
                  Tandai Selesai
                </button>
              </td>
            </tr>

            <tr v-if="!violations.length">
              <td colspan="8" class="py-12 text-center text-xs text-muted-foreground">
                Tidak ada catatan pelanggaran aktif.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab 2: Achievements Wall -->
    <div v-if="currentTab === 'achievements'" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="ach in achievements"
          :key="ach.id"
          class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="inline-flex rounded bg-emerald-500/10 px-2 py-0.5 text-xs font-semibold text-emerald-600">
              Tingkat {{ ach.level }}
            </span>
            <span class="text-xs font-bold text-amber-500">
              {{ ach.rank || 'Prestasi' }}
            </span>
          </div>

          <div>
            <h3 class="text-base font-bold text-foreground">{{ ach.title }}</h3>
            <p class="text-xs text-muted-foreground mt-0.5">Event: {{ ach.event_name }}</p>
          </div>

          <div class="border-t border-border/40 pt-3 flex items-center justify-between text-xs text-muted-foreground">
            <span>{{ ach.student?.full_name }}</span>
            <span>{{ ach.event_date }}</span>
          </div>
        </div>

        <div v-if="!achievements.length" class="col-span-full py-12 text-center text-xs text-muted-foreground">
          Belum ada catatan prestasi yang terdaftar.
        </div>
      </div>
    </div>

    <!-- Tab 3: Point Categories Rulebook -->
    <div v-if="currentTab === 'categories'" class="space-y-4">
      <div class="overflow-x-auto rounded-xl border border-border/60 bg-card shadow-sm">
        <table class="w-full text-left text-sm">
          <thead class="border-b border-border/60 bg-muted/30 text-xs text-muted-foreground">
            <tr>
              <th class="px-4 py-3 font-semibold">Kategori Perilaku</th>
              <th class="px-4 py-3 font-semibold">Tipe</th>
              <th class="px-4 py-3 font-semibold text-center">Poin Baku</th>
              <th class="px-4 py-3 font-semibold">Deskripsi Aturan</th>
              <th class="px-4 py-3 font-semibold text-center">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-muted/10 transition">
              <td class="px-4 py-3 font-semibold text-foreground">{{ cat.name }}</td>
              <td class="px-4 py-3">
                <span
                  :class="[
                    'inline-flex rounded px-2 py-0.5 text-xs font-semibold capitalize',
                    cat.type === 'pelanggaran'
                      ? 'bg-red-500/10 text-red-600'
                      : 'bg-emerald-500/10 text-emerald-600'
                  ]"
                >
                  {{ cat.type }}
                </span>
              </td>
              <td class="px-4 py-3 text-center font-bold">
                <span :class="cat.type === 'pelanggaran' ? 'text-red-600' : 'text-emerald-600'">
                  {{ cat.default_points > 0 ? '+' + cat.default_points : cat.default_points }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-muted-foreground max-w-md">
                {{ cat.description || '-' }}
              </td>
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

    <!-- Record Point Modal -->
    <div v-if="showRecordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm p-4">
      <div class="w-full max-w-lg rounded-xl border border-border bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/50 pb-3">
          <h3 class="text-base font-bold text-foreground">{{ t('school.discipline.recordTitle') }}</h3>
          <button @click="showRecordModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <div class="space-y-3">
          <div>
            <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.selectStudent') }}</label>
            <select
              v-model="recordForm.student_id"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            >
              <option value="">Pilih Siswa...</option>
              <option v-for="std in students" :key="std.id" :value="std.id">
                {{ std.full_name }} ({{ std.nisn || '-' }} - {{ std.department?.code || '-' }})
              </option>
            </select>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.selectCategory') }}</label>
            <select
              v-model="recordForm.category_id"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              @change="onCategorySelect"
            >
              <option value="">Pilih Kategori...</option>
              <optgroup label="Pelanggaran (Negatif)">
                <option
                  v-for="cat in categories.filter(c => c.type === 'pelanggaran')"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.name }} ({{ cat.default_points }} poin)
                </option>
              </optgroup>
              <optgroup label="Prestasi (Positif)">
                <option
                  v-for="cat in categories.filter(c => c.type === 'prestasi')"
                  :key="cat.id"
                  :value="cat.id"
                >
                  {{ cat.name }} (+{{ cat.default_points }} poin)
                </option>
              </optgroup>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.points') }}</label>
              <input
                v-model.number="recordForm.points"
                type="number"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.date') }}</label>
              <input
                v-model="recordForm.date"
                type="date"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.description') }}</label>
            <textarea
              v-model="recordForm.description"
              rows="2"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            ></textarea>
          </div>

          <!-- Extra violation fields if selected category is violation -->
          <div v-if="selectedCatType === 'pelanggaran'" class="space-y-3 border-t border-border/40 pt-3">
            <div>
              <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.sanction') }}</label>
              <input
                v-model="recordForm.sanction"
                type="text"
                placeholder="Contoh: Bimbingan wali kelas & piket kebersihan"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>

            <div>
              <label class="text-xs font-medium text-muted-foreground">{{ t('school.discipline.spLevel') }}</label>
              <select
                v-model="recordForm.sp_level"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              >
                <option value="none">{{ t('school.discipline.spNone') }}</option>
                <option value="sp1">{{ t('school.discipline.sp1') }}</option>
                <option value="sp2">{{ t('school.discipline.sp2') }}</option>
                <option value="sp3">{{ t('school.discipline.sp3') }}</option>
              </select>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 pt-3 border-t border-border/40">
          <button
            @click="showRecordModal = false"
            class="rounded-lg border border-border px-4 py-2 text-xs font-medium text-muted-foreground hover:bg-muted"
          >
            Batal
          </button>
          <button
            @click="submitRecordForm"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90"
          >
            Simpan Catatan
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  ShieldAlert,
  AlertTriangle,
  FileText,
  Award,
  BookOpen,
  CheckCircle2,
  X,
} from 'lucide-vue-next';
import { SchoolService } from '../../services/schoolService';

const { t } = useI18n();

const currentTab = ref<'violations' | 'achievements' | 'categories'>('violations');
const violations = ref<any[]>([]);
const achievements = ref<any[]>([]);
const categories = ref<any[]>([]);
const students = ref<any[]>([]);

const showRecordModal = ref(false);
const selectedCatType = ref('pelanggaran');
const recordForm = ref({
  student_id: '',
  category_id: '',
  points: 0,
  description: '',
  date: new Date().toISOString().split('T')[0],
  sanction: '',
  sp_level: 'none',
});

async function loadData() {
  try {
    const [violRes, achRes, catRes, stdRes] = await Promise.all([
      SchoolService.getViolations(),
      SchoolService.getAchievements(),
      SchoolService.getDisciplineCategories(),
      SchoolService.getStudents(),
    ]);

    if (violRes.data?.success) violations.value = violRes.data.data.data || [];
    if (achRes.data?.success) achievements.value = achRes.data.data.data || [];
    if (catRes.data?.success) categories.value = catRes.data.data || [];
    if (stdRes.data?.success) students.value = stdRes.data.data.data || [];
  } catch (err) {
    console.error('Failed to load discipline data', err);
  }
}

function openRecordModal() {
  recordForm.value = {
    student_id: '',
    category_id: '',
    points: 0,
    description: '',
    date: new Date().toISOString().split('T')[0],
    sanction: '',
    sp_level: 'none',
  };
  showRecordModal.value = true;
}

function onCategorySelect() {
  const cat = categories.value.find(c => c.id === Number(recordForm.value.category_id));
  if (cat) {
    selectedCatType.value = cat.type;
    recordForm.value.points = cat.default_points;
    if (!recordForm.value.description) {
      recordForm.value.description = cat.name;
    }
  }
}

async function submitRecordForm() {
  if (!recordForm.value.student_id || !recordForm.value.category_id) return;
  try {
    await SchoolService.recordPoint(recordForm.value);
    showRecordModal.value = false;
    await loadData();
  } catch (err) {
    console.error('Failed to record point', err);
  }
}

async function resolveViolation(id: number) {
  try {
    await SchoolService.resolveViolation(id, 'Telah dibina dan diselesaikan oleh BK.');
    await loadData();
  } catch (err) {
    console.error('Failed to resolve violation', err);
  }
}

onMounted(() => {
  loadData();
});
</script>
