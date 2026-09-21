<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.reports.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.reports.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="calculateReportCards"
          :disabled="!selectedGroupId || calculating"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90 disabled:opacity-50"
        >
          <Calculator class="size-4" />
          {{ calculating ? 'Menghitung...' : t('school.reports.calculateButton') }}
        </button>
      </div>
    </div>

    <!-- Toolbar Selector -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-xl border border-border/60 bg-card p-4 shadow-sm">
      <div class="flex items-center gap-3">
        <label class="text-xs font-semibold text-foreground">
          Pilih Rombongan Belajar:
        </label>
        <select
          v-model="selectedGroupId"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          @change="loadLedger"
        >
          <option value="">-- Pilih Rombel --</option>
          <option v-for="g in studyGroups" :key="g.id" :value="g.id">{{ g.name }}</option>
        </select>
      </div>

      <div v-if="ledgerData" class="text-xs text-muted-foreground">
        Total Siswa: <strong class="text-foreground">{{ ledgerData.students?.length || 0 }}</strong> • Total Mapel: <strong class="text-foreground">{{ ledgerData.subjects?.length || 0 }}</strong>
      </div>
    </div>

    <!-- Ledger Matrix Table -->
    <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
      <div v-if="loading" class="p-8 text-center text-sm text-muted-foreground">
        <div class="flex items-center justify-center gap-2">
          <div class="size-4 animate-spin rounded-full border-2 border-primary border-t-transparent" />
          Memuat leger nilai rombel...
        </div>
      </div>

      <div v-else-if="!ledgerData || !ledgerData.students || ledgerData.students.length === 0" class="p-8 text-center text-sm text-muted-foreground">
        <FileSpreadsheet class="size-8 mx-auto text-muted-foreground/50 mb-2" />
        {{ t('school.reports.empty') }}
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead class="border-b border-border/60 bg-muted/50 font-semibold text-muted-foreground">
            <tr>
              <th class="w-10 px-3 py-3 text-center border-r border-border/40">No</th>
              <th class="min-w-44 px-3 py-3 border-r border-border/40">Nama Siswa</th>
              <th class="w-24 px-3 py-3 text-center border-r border-border/40">NISN</th>

              <!-- Subject Headers -->
              <th
                v-for="sub in ledgerData.subjects"
                :key="sub.id"
                class="min-w-24 px-2 py-3 text-center border-r border-border/40"
                :title="sub.name"
              >
                {{ sub.code }}
              </th>

              <th class="w-20 px-3 py-3 text-center border-r border-border/40 bg-primary/5 font-bold text-primary">
                Rata-rata
              </th>

              <th class="w-24 px-2 py-3 text-center border-r border-border/40">
                Kehadiran (S/I/A)
              </th>

              <th class="w-24 px-3 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr
              v-for="(st, index) in ledgerData.students"
              :key="st.student_id"
              class="hover:bg-muted/30 transition-colors"
            >
              <td class="px-3 py-2.5 text-center font-mono text-muted-foreground border-r border-border/40">
                {{ Number(index) + 1 }}
              </td>

              <td class="px-3 py-2.5 font-semibold text-foreground border-r border-border/40">
                {{ st.full_name }}
              </td>

              <td class="px-3 py-2.5 text-center font-mono text-muted-foreground border-r border-border/40">
                {{ st.nisn || '-' }}
              </td>

              <!-- Subject Scores -->
              <td
                v-for="sub in ledgerData.subjects"
                :key="sub.id"
                class="px-2 py-2.5 text-center font-mono border-r border-border/40"
              >
                <span
                  v-if="st.subject_scores?.[sub.id]?.score !== null && st.subject_scores?.[sub.id]?.score !== undefined"
                  :class="[
                    'font-bold',
                    st.subject_scores[sub.id].score >= 85 ? 'text-emerald-600' :
                    st.subject_scores[sub.id].score >= 75 ? 'text-blue-600' : 'text-amber-600'
                  ]"
                >
                  {{ st.subject_scores[sub.id].score }}
                  <span class="text-[10px] font-normal text-muted-foreground">({{ st.subject_scores[sub.id].letter_grade }})</span>
                </span>
                <span v-else class="text-muted-foreground/40">-</span>
              </td>

              <td class="px-3 py-2.5 text-center font-mono font-bold text-primary border-r border-border/40 bg-primary/5">
                {{ st.average_score }}
              </td>

              <td class="px-2 py-2.5 text-center font-mono text-muted-foreground border-r border-border/40">
                {{ st.attendance?.sick || 0 }} / {{ st.attendance?.permission || 0 }} / {{ st.attendance?.absent || 0 }}
              </td>

              <td class="px-3 py-2.5 text-right">
                <button
                  @click="openReportCardModal(st.student_id)"
                  class="inline-flex items-center gap-1 rounded bg-primary/10 border border-primary/20 px-2 py-1 text-[11px] font-semibold text-primary hover:bg-primary/20 transition"
                  title="Lihat Raport"
                >
                  <Eye class="size-3" />
                  Raport
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Preview Cetak Raport Kurikulum Merdeka -->
    <div
      v-if="showReportModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto"
    >
      <div class="w-full max-w-4xl rounded-2xl border border-border bg-card p-6 shadow-xl space-y-6 my-auto max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-border/60 pb-3 no-print">
          <h3 class="text-base font-bold text-foreground">Lembar Raport Kurikulum Merdeka</h3>
          <div class="flex items-center gap-2">
            <button
              @click="printReport"
              class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
            >
              <Printer class="size-3.5" />
              {{ t('school.reports.printReport') }}
            </button>
            <button @click="showReportModal = false" class="text-muted-foreground hover:text-foreground">
              <X class="size-5" />
            </button>
          </div>
        </div>

        <!-- REPORT CARD DOCUMENT PREVIEW -->
        <div id="printable-report" class="bg-white text-slate-900 p-6 rounded-xl border border-slate-200 shadow-sm space-y-6 text-sm">
          <!-- Kop Surat -->
          <div class="text-center border-b-2 border-slate-900 pb-3 space-y-0.5">
            <div class="text-xs uppercase tracking-wider font-semibold text-slate-600">Pemerintah Daerah Provinsi Jawa Barat • Dinas Pendidikan</div>
            <h2 class="text-lg font-bold uppercase tracking-tight text-slate-900">SMK NEGERI 1 TALAGA</h2>
            <div class="text-xs text-slate-600">Jalan Raya Talaga - Cikijing KM. 2, Majalengka, Jawa Barat • NPSN: 20268712</div>
          </div>

          <div class="text-center">
            <h3 class="text-sm font-bold uppercase tracking-wider underline">LAPORAN HASIL BELAJAR (RAPORT SISWA)</h3>
          </div>

          <!-- Info Siswa -->
          <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="space-y-1">
              <div>Nama Siswa: <strong>{{ studentReport?.student?.full_name }}</strong></div>
              <div>NISN / NIS: <strong>{{ studentReport?.student?.nisn || '-' }} / {{ studentReport?.student?.nis || '-' }}</strong></div>
              <div>Kompetensi Keahlian: <strong>{{ studentReport?.student?.department?.name || 'PPLG' }}</strong></div>
            </div>
            <div class="space-y-1 text-right sm:text-left">
              <div>Kelas / Rombel: <strong>{{ studentReport?.study_group?.name }}</strong></div>
              <div>Fase: <strong>E (Kelas X)</strong></div>
              <div>Semester / TA: <strong>{{ studentReport?.academic_year?.name || '2026/2027 Ganjil' }}</strong></div>
            </div>
          </div>

          <!-- Tabel Capaian Kompetensi -->
          <table class="w-full text-left text-xs border border-slate-400 border-collapse">
            <thead>
              <tr class="bg-slate-100 text-slate-800 border-b border-slate-400">
                <th class="w-10 px-3 py-2 text-center border-r border-slate-400">No</th>
                <th class="w-48 px-3 py-2 border-r border-slate-400">Mata Pelajaran</th>
                <th class="w-16 px-2 py-2 text-center border-r border-slate-400">Nilai Akhir</th>
                <th class="px-3 py-2">Capaian Kompetensi Pembelajaran</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-300">
              <tr v-for="(rc, idx) in studentReport?.report_cards" :key="rc.id">
                <td class="px-3 py-2.5 text-center font-mono border-r border-slate-300">{{ Number(idx) + 1 }}</td>
                <td class="px-3 py-2 font-semibold border-r border-slate-300">{{ rc.subject?.name }}</td>
                <td class="px-2 py-2 text-center font-bold font-mono border-r border-slate-300">{{ rc.final_score }}</td>
                <td class="px-3 py-2 text-[11px] leading-relaxed text-slate-700 space-y-1">
                  <div>• <strong>Capaian Tertinggi:</strong> {{ rc.achieved_competency || 'Menunjukkan pemahaman yang sangat baik.' }}</div>
                  <div v-if="rc.improved_competency" class="text-slate-500">• <strong>Perlu Peningkatan:</strong> {{ rc.improved_competency }}</div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Kehadiran & Catatan -->
          <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="rounded border border-slate-300 p-3 space-y-1">
              <div class="font-bold border-b border-slate-200 pb-1">Rekapitulasi Kehadiran</div>
              <div>Sakit (S): <strong>{{ studentReport?.attendance?.sick || 0 }} hari</strong></div>
              <div>Izin (I): <strong>{{ studentReport?.attendance?.permission || 0 }} hari</strong></div>
              <div>Tanpa Keterangan (A): <strong>{{ studentReport?.attendance?.absent || 0 }} hari</strong></div>
            </div>

            <div class="rounded border border-slate-300 p-3 space-y-1">
              <div class="font-bold border-b border-slate-200 pb-1">Catatan Wali Kelas</div>
              <p class="text-[11px] italic text-slate-600 leading-relaxed">
                Tingkatkan terus kedisiplinan dan keaktifan dalam menyelesaikan tugas berbasis proyek. Pertahankan prestasi dan karakter positif.
              </p>
            </div>
          </div>

          <!-- Tanda Tangan -->
          <div class="grid grid-cols-2 pt-8 text-center text-xs">
            <div>
              <div class="text-slate-600">Mengetahui,</div>
              <div class="text-slate-600 mb-14">Orang Tua / Wali Siswa,</div>
              <div class="font-bold underline">...................................................</div>
            </div>

            <div>
              <div class="text-slate-600">Majalengka, {{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</div>
              <div class="text-slate-600 mb-14">Wali Kelas,</div>
              <div class="font-bold underline">{{ studentReport?.homeroom_teacher || 'Drs. H. Ahmad Sudrajat, M.Pd' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Calculator,
  Eye,
  Printer,
  X,
  FileSpreadsheet,
} from 'lucide-vue-next';
import {
  SchoolService,
  type StudyGroupData,
} from '../../services/schoolService';

const { t } = useI18n();

const studyGroups = ref<StudyGroupData[]>([]);
const selectedGroupId = ref<number | ''>('');
const ledgerData = ref<any>(null);
const loading = ref(false);
const calculating = ref(false);

const showReportModal = ref(false);
const studentReport = ref<any>(null);

async function loadInitialData() {
  try {
    const res = await SchoolService.getStudyGroups();
    if (res.data?.success) {
      studyGroups.value = Array.isArray(res.data.data) ? res.data.data : ((res.data.data as any)?.data ?? []);
      if (studyGroups.value[0]) {
        selectedGroupId.value = studyGroups.value[0].id;
        await loadLedger();
      }
    }
  } catch (err) {
    console.error('Failed to load study groups for ledger', err);
  }
}

async function loadLedger() {
  if (!selectedGroupId.value) return;
  loading.value = true;
  try {
    const res = await SchoolService.getLedger(Number(selectedGroupId.value));
    if (res.data?.success) {
      ledgerData.value = res.data.data;
    }
  } catch (err) {
    console.error('Failed to load ledger', err);
  } finally {
    loading.value = false;
  }
}

async function calculateReportCards() {
  if (!selectedGroupId.value) return;
  calculating.value = true;
  try {
    await SchoolService.calculateReportCards(Number(selectedGroupId.value));
    await loadLedger();
  } catch (err) {
    console.error('Failed to calculate report cards', err);
  } finally {
    calculating.value = false;
  }
}

async function openReportCardModal(studentId: number) {
  try {
    const res = await SchoolService.getStudentReport(studentId);
    if (res.data?.success) {
      studentReport.value = res.data.data;
      showReportModal.value = true;
    }
  } catch (err) {
    console.error('Failed to load student report card', err);
  }
}

function printReport() {
  window.print();
}

onMounted(() => {
  loadInitialData();
});
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  #printable-report, #printable-report * {
    visibility: visible;
  }
  #printable-report {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    border: none !important;
    box-shadow: none !important;
  }
  .no-print {
    display: none !important;
  }
}
</style>
