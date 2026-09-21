<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.dashboard' }" class="hover:text-foreground">Sekolah</router-link>
          <span>/</span>
          <router-link :to="{ name: 'school.graduation.alumni' }" class="hover:text-foreground">Alumni</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Bursa Kerja Khusus (BKK)</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Bursa Kerja Khusus & Karir Alumni</h1>
        <p class="text-sm text-muted-foreground">
          Penyaluran tenaga kerja lulusan SMK/SMA, peluang magang industri, dan pelacakan lamaran kerja siswa & alumni.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          @click="openCreateModal"
        >
          <Plus class="size-4" />
          Pasang Lowongan Baru
        </button>
      </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Lowongan Dibuka</span>
          <div class="text-2xl font-bold font-mono text-primary mt-1">{{ activeVacanciesCount }} Posisi</div>
        </div>
        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <Briefcase class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Mitra Industri Aktif</span>
          <div class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400 mt-1">
            {{ uniqueCompaniesCount }} Perusahaan
          </div>
        </div>
        <div class="size-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <Building2 class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Lamaran Masuk</span>
          <div class="text-2xl font-bold font-mono text-amber-600 dark:text-amber-400 mt-1">
            {{ totalApplicationsCount }} Berkas
          </div>
        </div>
        <div class="size-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <FileText class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Kandidat Diterima</span>
          <div class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">
            {{ acceptedCandidatesCount }} Orang
          </div>
        </div>
        <div class="size-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="relative flex-1 max-w-sm">
        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari posisi pekerjaan, mitra, atau kota penempatan..."
          class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
        />
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <select
          v-model="selectedJobType"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option value="">Semua Tipe Kerja</option>
          <option value="fulltime">Purna Waktu (Full-time)</option>
          <option value="internship">Magang (Internship)</option>
          <option value="parttime">Paruh Waktu (Part-time)</option>
          <option value="contract">Kontrak Proyek</option>
        </select>

        <select
          v-model="selectedWorkplace"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option value="">Semua Sistem Kerja</option>
          <option value="on_site">Di Kantor (On-site)</option>
          <option value="hybrid">Campuran (Hybrid)</option>
          <option value="remote">Jarak Jauh (Remote)</option>
        </select>
      </div>
    </div>

    <!-- Vacancies Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="vacancy in filteredVacancies"
        :key="vacancy.id"
        class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-primary/40 transition group"
      >
        <div class="space-y-3">
          <!-- Card Header & Badges -->
          <div class="flex items-start justify-between gap-2">
            <div class="flex flex-wrap items-center gap-1.5">
              <span
                :class="[
                  'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                  getJobTypeBadgeClass(vacancy.job_type)
                ]"
              >
                {{ formatJobType(vacancy.job_type) }}
              </span>
              <span
                class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-muted text-muted-foreground capitalize"
              >
                {{ formatWorkplace(vacancy.workplace_type) }}
              </span>
            </div>

            <span
              :class="[
                'size-2 rounded-full',
                vacancy.status === 'published' ? 'bg-emerald-500' : 'bg-muted-foreground'
              ]"
              :title="vacancy.status === 'published' ? 'Dibuka' : 'Ditutup'"
            ></span>
          </div>

          <!-- Position & Company -->
          <div>
            <h3 class="text-base font-bold text-foreground group-hover:text-primary transition line-clamp-1">
              {{ vacancy.job_title }}
            </h3>
            <div class="flex items-center gap-1.5 text-xs font-medium text-foreground/80 mt-1">
              <Building2 class="size-3.5 text-muted-foreground" />
              <span>{{ vacancy.company_name }}</span>
            </div>
          </div>

          <!-- Meta Information -->
          <div class="space-y-1.5 text-xs text-muted-foreground border-t border-border/50 pt-2.5">
            <div v-if="vacancy.location" class="flex items-center gap-2">
              <MapPin class="size-3.5 text-primary shrink-0" />
              <span class="truncate">{{ vacancy.location }}</span>
            </div>
            <div v-if="vacancy.salary_range" class="flex items-center gap-2 font-mono">
              <DollarSign class="size-3.5 text-emerald-600 dark:text-emerald-400 shrink-0" />
              <span class="text-foreground font-semibold">{{ vacancy.salary_range }}</span>
            </div>
            <div v-if="vacancy.application_deadline" class="flex items-center gap-2">
              <Calendar class="size-3.5 text-amber-500 shrink-0" />
              <span>Batas: {{ vacancy.application_deadline }}</span>
            </div>
          </div>

          <!-- Description -->
          <p class="text-xs text-muted-foreground line-clamp-2">
            {{ vacancy.description }}
          </p>
        </div>

        <!-- Card Footer -->
        <div class="border-t border-border/50 pt-3 flex items-center justify-between gap-2">
          <button
            type="button"
            class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-lg border border-border py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
            @click="openApplicationsModal(vacancy)"
          >
            <Users class="size-3.5 text-primary" />
            <span>{{ vacancy.applications_count || vacancy.applications?.length || 0 }} Pelamar</span>
          </button>

          <button
            type="button"
            class="rounded-lg border border-border p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition"
            title="Edit Lowongan"
            @click="openEditModal(vacancy)"
          >
            <Pencil class="size-3.5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Vacancy Dialog (Create / Edit) -->
    <VacancyDialog
      :open="showFormModal"
      :is-editing="isEditing"
      :vacancy="currentVacancy"
      @update:open="showFormModal = $event"
      @save="handleSaveVacancy"
    />

    <!-- Applications List Drawer / Modal -->
    <div
      v-if="showAppsModal && currentVacancy"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs animate-in fade-in"
      @click.self="showAppsModal = false"
    >
      <div class="w-full max-w-2xl max-h-[85vh] flex flex-col rounded-2xl border border-border/60 bg-card shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between border-b border-border/60 p-5">
          <div>
            <h3 class="text-base font-bold text-foreground">
              Daftar Pelamar: {{ currentVacancy.job_title }}
            </h3>
            <p class="text-xs text-muted-foreground">{{ currentVacancy.company_name }} &bull; {{ selectedVacancyApps.length }} berkas masuk</p>
          </div>
          <button
            type="button"
            class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
            @click="showAppsModal = false"
          >
            <X class="size-4" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 divide-y divide-border/50 text-xs">
          <div
            v-for="app in selectedVacancyApps"
            :key="app.id"
            class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
          >
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <span class="font-bold text-foreground text-sm">{{ app.applicant_name }}</span>
                <span
                  :class="[
                    'px-2 py-0.5 rounded text-[10px] font-bold uppercase',
                    getStatusBadgeClass(app.status)
                  ]"
                >
                  {{ app.status }}
                </span>
              </div>
              <p class="text-muted-foreground text-xs">
                Email: {{ app.applicant_email }} &bull; Telp: {{ app.applicant_phone }}
              </p>
              <div class="flex items-center gap-3 pt-1">
                <a
                  v-if="app.resume_url"
                  :href="app.resume_url"
                  target="_blank"
                  rel="noopener"
                  class="text-primary underline flex items-center gap-1 hover:text-primary/80"
                >
                  <FileText class="size-3" />
                  Lihat CV / Resume
                </a>
                <a
                  v-if="app.portfolio_url"
                  :href="app.portfolio_url"
                  target="_blank"
                  rel="noopener"
                  class="text-primary underline flex items-center gap-1 hover:text-primary/80"
                >
                  <ExternalLink class="size-3" />
                  Portofolio
                </a>
              </div>
            </div>

            <!-- Change Status Selector -->
            <div class="flex items-center gap-2">
              <select
                :value="app.status"
                class="h-8 rounded-lg border border-border bg-background px-2.5 text-xs text-foreground focus:outline-none"
                @change="updateAppStatus(app.id, ($event.target as HTMLSelectElement).value)"
              >
                <option value="submitted">Submitted</option>
                <option value="reviewed">Reviewed</option>
                <option value="shortlisted">Shortlisted</option>
                <option value="interview">Interview</option>
                <option value="accepted">Accepted (Diterima)</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>

          <div v-if="selectedVacancyApps.length === 0" class="py-12 text-center text-muted-foreground">
            Belum ada berkas lamaran masuk untuk lowongan ini.
          </div>
        </div>

        <div class="border-t border-border/60 p-4 flex justify-end">
          <button
            type="button"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90"
            @click="showAppsModal = false"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Briefcase,
  Building2,
  FileText,
  CheckCircle2,
  Plus,
  Search,
  MapPin,
  DollarSign,
  Calendar,
  Users,
  Pencil,
  X,
  ExternalLink
} from 'lucide-vue-next';
import VacancyDialog from '../../components/career/VacancyDialog.vue';
import type { JobVacancyData, JobApplicationData } from '../../types/career';

const searchQuery = ref('');
const selectedJobType = ref('');
const selectedWorkplace = ref('');

const showFormModal = ref(false);
const showAppsModal = ref(false);
const isEditing = ref(false);
const currentVacancy = ref<JobVacancyData | null>(null);

const vacancies = ref<JobVacancyData[]>([
  {
    id: 1,
    school_id: 1,
    company_name: 'PT Telkom Akses Regional',
    job_title: 'Junior Network Technician & Fiber Optic Specialist',
    job_type: 'fulltime',
    workplace_type: 'on_site',
    location: 'Bandung & Sekitarnya',
    salary_range: 'Rp 4.800.000 - Rp 6.200.000',
    description: 'Instalasi jaringan kabel optik FTTH, pemeliharaan ODP/ODC, dan terminasi perangkat jaringan pelanggan.',
    requirements: 'Lulusan Jurusan Teknik Komputer & Jaringan (TJKT), memiliki SIM C aktif.',
    application_deadline: '2026-10-31',
    status: 'published',
    applications_count: 8,
    applications: [
      {
        id: 101,
        vacancy_id: 1,
        applicant_name: 'Budi Santoso',
        applicant_email: 'budi.santoso@gmail.com',
        applicant_phone: '08123456781',
        resume_url: '#',
        portfolio_url: '#',
        status: 'shortlisted',
      },
      {
        id: 102,
        vacancy_id: 1,
        applicant_name: 'Rian Hidayat',
        applicant_email: 'rian.h@gmail.com',
        applicant_phone: '08123456782',
        resume_url: '#',
        status: 'interview',
      },
      {
        id: 103,
        vacancy_id: 1,
        applicant_name: 'Asep Saepudin',
        applicant_email: 'asep.s@gmail.com',
        applicant_phone: '08123456783',
        status: 'accepted',
      }
    ]
  },
  {
    id: 2,
    school_id: 1,
    company_name: 'Astra Honda Motor Authorized Dealer',
    job_title: 'Mekanik Sepeda Motor & Teknisi Servis Berkala',
    job_type: 'fulltime',
    workplace_type: 'on_site',
    location: 'Ciamis & Tasikmalaya',
    salary_range: 'Rp 3.800.000 - Rp 5.000.000',
    description: 'Perawatan mesin injeksi PGM-FI, overhaul mesin, sistem kelistrikan, dan penanganan keluhan teknis pelanggan.',
    requirements: 'Lulusan Jurusan Teknik Sepeda Motor (TBSM), disiplin, teliti, dan siap bekerja shift.',
    application_deadline: '2026-11-15',
    status: 'published',
    applications_count: 5,
    applications: [
      {
        id: 201,
        vacancy_id: 2,
        applicant_name: 'Dede Kurniawan',
        applicant_email: 'dede.k@gmail.com',
        applicant_phone: '08123456784',
        status: 'submitted',
      }
    ]
  },
  {
    id: 3,
    school_id: 1,
    company_name: 'PT Software Media Nusantara',
    job_title: 'Junior Frontend Developer (Vue 3 / TypeScript)',
    job_type: 'internship',
    workplace_type: 'hybrid',
    location: 'Bandung Digital Valley',
    salary_range: 'Rp 2.500.000 - Rp 3.500.000 (Uang Saku)',
    description: 'Pengembangan antarmuka sistem web, integrasi REST API, dan optimalisasi komponen Tailwind CSS.',
    requirements: 'Siswa kelas XII atau alumni PPLG/RPL, memahami Git, HTML, CSS, JavaScript/TypeScript.',
    application_deadline: '2026-12-01',
    status: 'published',
    applications_count: 12,
    applications: [
      {
        id: 301,
        vacancy_id: 3,
        applicant_name: 'Muhammad Raditya',
        applicant_email: 'raditya@sekolah.id',
        applicant_phone: '08123456785',
        status: 'accepted',
      }
    ]
  }
]);

const activeVacanciesCount = computed(() => {
  return vacancies.value.filter(v => v.status === 'published').length;
});

const uniqueCompaniesCount = computed(() => {
  const set = new Set(vacancies.value.map(v => v.company_name));
  return set.size;
});

const totalApplicationsCount = computed(() => {
  return vacancies.value.reduce((acc, curr) => acc + (curr.applications?.length || curr.applications_count || 0), 0);
});

const acceptedCandidatesCount = computed(() => {
  let count = 0;
  for (const v of vacancies.value) {
    if (v.applications) {
      count += v.applications.filter(a => a.status === 'accepted').length;
    }
  }
  return count;
});

const filteredVacancies = computed(() => {
  return vacancies.value.filter((item) => {
    const q = searchQuery.value.toLowerCase().trim();
    const matchSearch =
      !q ||
      item.job_title.toLowerCase().includes(q) ||
      item.company_name.toLowerCase().includes(q) ||
      (item.location || '').toLowerCase().includes(q);

    const matchType = !selectedJobType.value || item.job_type === selectedJobType.value;
    const matchWorkplace = !selectedWorkplace.value || item.workplace_type === selectedWorkplace.value;

    return matchSearch && matchType && matchWorkplace;
  });
});

const selectedVacancyApps = computed<JobApplicationData[]>(() => {
  if (!currentVacancy.value || !currentVacancy.value.applications) return [];
  return currentVacancy.value.applications;
});

function formatJobType(type: string): string {
  switch (type) {
    case 'fulltime': return 'Full-time';
    case 'internship': return 'Magang';
    case 'parttime': return 'Part-time';
    case 'contract': return 'Kontrak';
    case 'freelance': return 'Freelance';
    default: return type;
  }
}

function formatWorkplace(type: string): string {
  switch (type) {
    case 'on_site': return 'On-site';
    case 'hybrid': return 'Hybrid';
    case 'remote': return 'Remote';
    default: return type;
  }
}

function getJobTypeBadgeClass(type: string): string {
  switch (type) {
    case 'fulltime':
      return 'bg-primary/10 text-primary border border-primary/20';
    case 'internship':
      return 'bg-amber-500/10 text-amber-600 border border-amber-500/20';
    case 'parttime':
      return 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/20';
    default:
      return 'bg-muted text-muted-foreground border border-border';
  }
}

function getStatusBadgeClass(status: string): string {
  switch (status) {
    case 'accepted':
      return 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20';
    case 'rejected':
      return 'bg-destructive/10 text-destructive border border-destructive/20';
    case 'interview':
      return 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/20';
    case 'shortlisted':
      return 'bg-amber-500/10 text-amber-600 border border-amber-500/20';
    default:
      return 'bg-muted text-muted-foreground border border-border';
  }
}

function openCreateModal() {
  isEditing.value = false;
  currentVacancy.value = null;
  showFormModal.value = true;
}

function openEditModal(vacancy: JobVacancyData) {
  isEditing.value = true;
  currentVacancy.value = vacancy;
  showFormModal.value = true;
}

function openApplicationsModal(vacancy: JobVacancyData) {
  currentVacancy.value = vacancy;
  showAppsModal.value = true;
}

function handleSaveVacancy(payload: Partial<JobVacancyData>) {
  if (isEditing.value && currentVacancy.value) {
    const idx = vacancies.value.findIndex(v => v.id === currentVacancy.value?.id);
    if (idx !== -1) {
      vacancies.value[idx] = { ...vacancies.value[idx], ...payload } as JobVacancyData;
    }
  } else {
    const newId = Date.now();
    vacancies.value.unshift({
      id: newId,
      school_id: 1,
      company_name: payload.company_name || 'Mitra Baru',
      job_title: payload.job_title || 'Posisi Baru',
      job_type: payload.job_type || 'fulltime',
      workplace_type: payload.workplace_type || 'on_site',
      location: payload.location || '',
      salary_range: payload.salary_range || '',
      description: payload.description || '',
      requirements: payload.requirements || '',
      application_deadline: payload.application_deadline || '',
      status: payload.status || 'published',
      applications_count: 0,
      applications: [],
    });
  }
}

function updateAppStatus(appId: number, newStatus: string) {
  if (!currentVacancy.value || !currentVacancy.value.applications) return;
  const app = currentVacancy.value.applications.find(a => a.id === appId);
  if (app) {
    app.status = newStatus as any;
  }
}
</script>
