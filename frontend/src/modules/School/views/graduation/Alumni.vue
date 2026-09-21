<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.students' }" class="hover:text-foreground">Kesiswaan</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Tracer Study</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Direktori & Penelusuran Alumni</h1>
        <p class="text-sm text-muted-foreground">
          Basis data lulusan sekolah, pelacakan karir (bekerja, kuliah, wirausaha), dan jejaring ikatan alumni.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
          @click="handlePrint"
        >
          <Printer class="size-4" />
          Cetak Rekap Alumni
        </button>
      </div>
    </div>

    <!-- Cohort Year Summary Table -->
    <GraduationYearTable @select-year="filterByYear" />

    <!-- Filter & Search Bar -->
    <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="relative flex-1 max-w-sm">
        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama alumni, NISN, atau instansi..."
          class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
        />
      </div>

      <div class="flex items-center gap-2">
        <select
          v-model="selectedYear"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option value="">Semua Tahun Lulus</option>
          <option value="2025">Lulusan 2025</option>
          <option value="2024">Lulusan 2024</option>
          <option value="2023">Lulusan 2023</option>
          <option value="2022">Lulusan 2022</option>
        </select>

        <select
          v-model="selectedStatus"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option value="">Semua Status Karir</option>
          <option value="working">Bekerja</option>
          <option value="studying">Kuliah</option>
          <option value="entrepreneur">Wirausaha</option>
          <option value="job_seeking">Mencari Kerja</option>
        </select>
      </div>
    </div>

    <!-- Alumni Data Table -->
    <div class="rounded-2xl border border-border/60 bg-card shadow-sm overflow-hidden">
      <div class="border-b border-border/60 bg-muted/20 px-6 py-3.5 flex items-center justify-between">
        <h3 class="text-xs font-bold uppercase tracking-wider text-foreground">
          Daftar Rekam Jejak Alumni Terdaftar
        </h3>
        <span class="text-xs text-muted-foreground font-mono">{{ filteredAlumni.length }} Alumni</span>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs whitespace-nowrap">
          <thead class="bg-muted/40 border-b border-border/60 text-[11px] font-semibold text-muted-foreground">
            <tr>
              <th class="py-3 px-4 w-12 text-center">No</th>
              <th class="py-3 px-4 min-w-[180px]">Nama Alumni</th>
              <th class="py-3 px-4 w-28">NISN</th>
              <th class="py-3 px-3 w-24 text-center">Tahun Lulus</th>
              <th class="py-3 px-3 w-28 text-center">Status Karir</th>
              <th class="py-3 px-4 min-w-[200px]">Perusahaan / Perguruan Tinggi</th>
              <th class="py-3 px-4 w-32">Kontak / Email</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr
              v-for="(item, idx) in filteredAlumni"
              :key="item.id"
              class="hover:bg-muted/20 transition"
            >
              <td class="py-3.5 px-4 text-center font-mono text-muted-foreground">{{ idx + 1 }}</td>
              <td class="py-3.5 px-4 font-semibold text-foreground">
                <router-link
                  :to="{ name: 'school.students.show', params: { id: item.student_id } }"
                  class="hover:underline hover:text-primary"
                >
                  {{ item.name }}
                </router-link>
              </td>
              <td class="py-3.5 px-4 font-mono text-muted-foreground">{{ item.nisn }}</td>
              <td class="py-3.5 px-3 text-center font-mono font-bold">{{ item.graduation_year }}</td>
              <td class="py-3.5 px-3 text-center">
                <span
                  :class="[
                    'px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize',
                    item.status === 'working' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' :
                    item.status === 'studying' ? 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/20' :
                    item.status === 'entrepreneur' ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20' : 'bg-muted text-muted-foreground'
                  ]"
                >
                  {{ formatCareerStatus(item.status) }}
                </span>
              </td>
              <td class="py-3.5 px-4 font-medium text-foreground">
                {{ item.institution || '-' }}
              </td>
              <td class="py-3.5 px-4 text-muted-foreground font-mono text-[11px]">
                {{ item.phone || item.email || '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Printer, Search } from 'lucide-vue-next';
import GraduationYearTable from '../../components/graduation/GraduationYearTable.vue';

interface AlumniItem {
  id: number;
  student_id: number;
  name: string;
  nisn: string;
  graduation_year: string;
  status: 'working' | 'studying' | 'entrepreneur' | 'job_seeking';
  institution: string;
  phone?: string;
  email?: string;
}

const searchQuery = ref('');
const selectedYear = ref('');
const selectedStatus = ref('');

const alumniList = ref<AlumniItem[]>([
  {
    id: 1,
    student_id: 101,
    name: 'Dimas Prasetyo',
    nisn: '0048123910',
    graduation_year: '2025',
    status: 'working',
    institution: 'PT Telkom Indonesia (Software Engineer)',
    phone: '081234567891',
    email: 'dimas@gmail.com',
  },
  {
    id: 2,
    student_id: 102,
    name: 'Anisa Rahmawati',
    nisn: '0048123911',
    graduation_year: '2025',
    status: 'studying',
    institution: 'Institut Teknologi Bandung (Teknik Informatika)',
    phone: '081234567892',
    email: 'anisa@itb.ac.id',
  },
  {
    id: 3,
    student_id: 103,
    name: 'Bagus Pratama',
    nisn: '0038123912',
    graduation_year: '2024',
    status: 'entrepreneur',
    institution: 'Founder Digital Creative Studio',
    phone: '081234567893',
    email: 'bagus@agency.id',
  },
  {
    id: 4,
    student_id: 104,
    name: 'Siti Nurhaliza',
    nisn: '0038123913',
    graduation_year: '2024',
    status: 'working',
    institution: 'Bank Mandiri (Customer Representative)',
    phone: '081234567894',
    email: 'siti@bankmandiri.co.id',
  },
  {
    id: 5,
    student_id: 105,
    name: 'Rian Firmansyah',
    nisn: '0028123914',
    graduation_year: '2023',
    status: 'studying',
    institution: 'Universitas Indonesia (Sistem Informasi)',
    phone: '081234567895',
    email: 'rian@ui.ac.id',
  },
]);

const filteredAlumni = computed(() => {
  return alumniList.value.filter((item) => {
    const matchesSearch =
      !searchQuery.value.trim() ||
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.nisn.includes(searchQuery.value) ||
      item.institution.toLowerCase().includes(searchQuery.value.toLowerCase());

    const matchesYear = !selectedYear.value || item.graduation_year === selectedYear.value;
    const matchesStatus = !selectedStatus.value || item.status === selectedStatus.value;

    return matchesSearch && matchesYear && matchesStatus;
  });
});

function formatCareerStatus(status: string): string {
  switch (status) {
    case 'working': return 'Bekerja';
    case 'studying': return 'Kuliah';
    case 'entrepreneur': return 'Wirausaha';
    case 'job_seeking': return 'Mencari Kerja';
    default: return status;
  }
}

function filterByYear(year: string) {
  selectedYear.value = year;
}

function handlePrint() {
  window.print();
}
</script>
