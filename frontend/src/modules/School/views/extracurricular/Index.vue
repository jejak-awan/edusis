<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.dashboard' }" class="hover:text-foreground">Sekolah</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Pengembangan Bakat</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Manajemen Ekstrakurikuler</h1>
        <p class="text-sm text-muted-foreground">
          Kelola cabang kegiatan ekstrakurikuler wajib & pilihan, pembina, jadwal latihan, dan rekam anggota siswa.
        </p>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
          @click="openCreateModal"
        >
          <Plus class="size-4" />
          Tambah Cabang Ekskul
        </button>
      </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Total Cabang Ekskul</span>
          <div class="text-2xl font-bold font-mono text-foreground mt-1">{{ extracurriculars.length }} Cabang</div>
        </div>
        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
          <Sparkles class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Ekskul Wajib</span>
          <div class="text-2xl font-bold font-mono text-amber-600 dark:text-amber-400 mt-1">
            {{ extracurriculars.filter(e => e.category === 'wajib').length }} Cabang
          </div>
        </div>
        <div class="size-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Award class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Ekskul Pilihan / Minat</span>
          <div class="text-2xl font-bold font-mono text-indigo-600 dark:text-indigo-400 mt-1">
            {{ extracurriculars.filter(e => e.category === 'pilihan').length }} Cabang
          </div>
        </div>
        <div class="size-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
          <Activity class="size-5" />
        </div>
      </div>

      <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-muted-foreground font-medium">Total Partisipasi Siswa</span>
          <div class="text-2xl font-bold font-mono text-emerald-600 dark:text-emerald-400 mt-1">
            {{ totalMembers }} Anggota
          </div>
        </div>
        <div class="size-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <Users class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="rounded-2xl border border-border/60 bg-card p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="relative flex-1 max-w-sm">
        <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama ekskul atau pembina..."
          class="h-9 w-full rounded-lg border border-border bg-background pl-9 pr-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
        />
      </div>

      <div class="flex items-center gap-2">
        <select
          v-model="selectedCategory"
          class="h-9 rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none"
        >
          <option value="">Semua Kategori</option>
          <option value="wajib">Wajib (Pramuka)</option>
          <option value="pilihan">Pilihan / Minat Bakat</option>
        </select>
      </div>
    </div>

    <!-- Extracurricular Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="item in filteredList"
        :key="item.id"
        class="rounded-2xl border border-border/60 bg-card p-5 shadow-sm space-y-4 flex flex-col justify-between hover:border-primary/40 transition"
      >
        <div class="space-y-3">
          <div class="flex items-start justify-between">
            <span
              :class="[
                'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                item.category === 'wajib'
                  ? 'bg-amber-500/10 text-amber-600 border border-amber-500/20'
                  : 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/20'
              ]"
            >
              {{ item.category === 'wajib' ? 'Ekskul Wajib' : 'Ekskul Pilihan' }}
            </span>

            <span
              :class="[
                'size-2 rounded-full',
                item.is_active ? 'bg-emerald-500' : 'bg-muted-foreground'
              ]"
              :title="item.is_active ? 'Aktif' : 'Nonaktif'"
            ></span>
          </div>

          <div>
            <h3 class="text-base font-bold text-foreground">{{ item.name }}</h3>
            <p class="text-xs text-muted-foreground mt-1 line-clamp-2">
              {{ item.description || 'Pengembangan potensi, minat, dan bakat siswa di bidang keterampilan non-akademik.' }}
            </p>
          </div>

          <div class="border-t border-border/50 pt-3 space-y-1.5 text-xs text-muted-foreground">
            <div class="flex items-center gap-2">
              <User class="size-3.5 text-primary" />
              <span>Pembina: <strong class="text-foreground">{{ item.advisor?.full_name || 'Dewan Pembina' }}</strong></span>
            </div>
            <div class="flex items-center gap-2">
              <Calendar class="size-3.5 text-primary" />
              <span>Jadwal: {{ item.schedule_day || 'Sabtu, 08:00 - 10:30 WIB' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <MapPin class="size-3.5 text-primary" />
              <span>Lokasi: {{ item.location || 'Lapangan / Ruang Ekskul' }}</span>
            </div>
            <div class="flex items-center gap-2">
              <Users class="size-3.5 text-primary" />
              <span>Jumlah Anggota: <strong class="text-foreground font-mono">{{ item.members_count || 32 }} Siswa</strong></span>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2 pt-2 border-t border-border/50">
          <button
            type="button"
            class="flex-1 rounded-lg border border-border py-2 text-xs font-semibold text-foreground hover:bg-muted transition"
            @click="openMembersModal(item)"
          >
            Anggota Siswa
          </button>
          <button
            type="button"
            class="rounded-lg border border-border p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition"
            @click="openEditModal(item)"
            title="Edit Cabang"
          >
            <Pencil class="size-3.5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <div
      v-if="showFormModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
      @click.self="showFormModal = false"
    >
      <div class="w-full max-w-lg rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-4">
        <div class="flex items-start justify-between border-b border-border/60 pb-3">
          <h3 class="text-base font-bold text-foreground">
            {{ isEditing ? 'Ubah Cabang Ekstrakurikuler' : 'Tambah Cabang Ekstrakurikuler' }}
          </h3>
          <button
            type="button"
            class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
            @click="showFormModal = false"
          >
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="handleSaveExtracurricular" class="space-y-4 text-xs">
          <div>
            <label class="block font-semibold text-foreground mb-1">
              Nama Ekstrakurikuler <span class="text-destructive">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              placeholder="Contoh: Pramuka, Palang Merah Remaja (PMR), Coding Club"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-semibold text-foreground mb-1">
                Kategori <span class="text-destructive">*</span>
              </label>
              <select
                v-model="form.category"
                required
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              >
                <option value="pilihan">Pilihan (Minat & Bakat)</option>
                <option value="wajib">Wajib (Pramuka)</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-foreground mb-1">
                Hari Latihan
              </label>
              <input
                v-model="form.schedule_day"
                type="text"
                placeholder="Sabtu, 08:00 - 10:00"
                class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Lokasi / Tempat Latihan
            </label>
            <input
              v-model="form.location"
              type="text"
              placeholder="Lapangan Basket / Lab Komputer 2"
              class="h-9 w-full rounded-lg border border-border bg-background px-3 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-semibold text-foreground mb-1">
              Deskripsi Singkat
            </label>
            <textarea
              v-model="form.description"
              rows="2"
              placeholder="Fokus kegiatan, tujuan dan prestasi..."
              class="w-full rounded-lg border border-border bg-background p-2.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="flex justify-end gap-2.5 pt-2 border-t border-border/60">
            <button
              type="button"
              class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted"
              @click="showFormModal = false"
            >
              Batal
            </button>
            <button
              type="submit"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90 transition shadow-sm"
            >
              Simpan
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Members Modal -->
    <div
      v-if="showMembersModal && currentEkskul"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs"
      @click.self="showMembersModal = false"
    >
      <div class="w-full max-w-xl rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-4">
        <div class="flex items-start justify-between border-b border-border/60 pb-3">
          <div>
            <h3 class="text-base font-bold text-foreground">
              Daftar Anggota: {{ currentEkskul.name }}
            </h3>
            <p class="text-xs text-muted-foreground">Siswa terdaftar dalam cabang ekstrakurikuler ini.</p>
          </div>
          <button
            type="button"
            class="rounded-lg p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
            @click="showMembersModal = false"
          >
            <X class="size-4" />
          </button>
        </div>

        <div class="max-h-80 overflow-y-auto divide-y divide-border/40 text-xs">
          <div
            v-for="(member, idx) in sampleMembers"
            :key="idx"
            class="py-2.5 flex items-center justify-between"
          >
            <div class="flex items-center gap-3">
              <span class="font-mono text-muted-foreground text-xs">{{ idx + 1 }}</span>
              <div>
                <span class="font-semibold text-foreground block">{{ member.name }}</span>
                <span class="text-muted-foreground text-[11px]">NISN: {{ member.nisn }} &bull; {{ member.class_name }}</span>
              </div>
            </div>
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-primary/10 text-primary capitalize">
              {{ member.role }}
            </span>
          </div>
        </div>

        <div class="flex justify-end pt-2 border-t border-border/60">
          <button
            type="button"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground hover:bg-primary/90"
            @click="showMembersModal = false"
          >
            Selesai
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
  Sparkles,
  Award,
  Activity,
  Users,
  Search,
  Plus,
  User,
  Calendar,
  MapPin,
  Pencil,
  X
} from 'lucide-vue-next';
import type { ExtracurricularData } from '../../types/extracurricular';

const searchQuery = ref('');
const selectedCategory = ref('');
const showFormModal = ref(false);
const showMembersModal = ref(false);
const isEditing = ref(false);
const currentEkskul = ref<ExtracurricularData | null>(null);

const form = ref<Partial<ExtracurricularData>>({
  name: '',
  category: 'pilihan',
  schedule_day: '',
  location: '',
  description: '',
  is_active: true,
});

const extracurriculars = ref<ExtracurricularData[]>([
  {
    id: 1,
    school_id: 1,
    name: 'Gerakan Pramuka (Gugus Depan)',
    category: 'wajib',
    advisor_staff_id: 1,
    schedule_day: 'Jumat, 14:00 - 16:30 WIB',
    location: 'Lapangan Utama & Sanggar Pramuka',
    description: 'Pendidikan kepanduan, kepemimpinan karakter, kedisiplinan, dan ketangkasan alam terbuka.',
    is_active: true,
    members_count: 240,
    advisor: {
      id: 1,
      school_id: 1,
      full_name: 'Hendra Gunawan, S.Pd',
      nip: '198205102008011003',
      nuptk: null,
      nik: null,
      title_prefix: null,
      title_suffix: 'S.Pd',
      gender: 'L',
      employment_status: 'PNS',
      role_title: 'Pembina Pramuka',
      last_education: 'S1',
      major: 'Pendidikan Jasmani',
      certification_status: true,
      phone: '081234567801',
      email: 'hendra@sekolah.id',
      is_active: true,
    },
  },
  {
    id: 2,
    school_id: 1,
    name: 'Palang Merah Remaja (PMR Wira)',
    category: 'pilihan',
    advisor_staff_id: 2,
    schedule_day: 'Sabtu, 08:30 - 11:00 WIB',
    location: 'Ruang UKS Terpadu',
    description: 'Pelatihan pertolongan pertama pada kecelakaan, donor darah, dan kesiapsiagaan bencana.',
    is_active: true,
    members_count: 48,
  },
  {
    id: 3,
    school_id: 1,
    name: 'Pasukan Pengibar Bendera (Paskibra)',
    category: 'pilihan',
    advisor_staff_id: 3,
    schedule_day: 'Rabu & Sabtu, 15:30 WIB',
    location: 'Lapangan Upacara',
    description: 'Pembentukan baris-berbaris presisi, kedisiplinan mental, dan tata upacara bendera kenegaraan.',
    is_active: true,
    members_count: 42,
  },
  {
    id: 4,
    school_id: 1,
    name: 'Coding & Robotika Club',
    category: 'pilihan',
    advisor_staff_id: 4,
    schedule_day: 'Kamis, 15:00 - 17:00 WIB',
    location: 'Lab Rekayasa Perangkat Lunak',
    description: 'Eksplorasi pemrograman web, mikrokontroler IoT, kecerdasan buatan, dan kompetisi lomba LKS.',
    is_active: true,
    members_count: 36,
  },
  {
    id: 5,
    school_id: 1,
    name: 'Rohani Islam (Rohis)',
    category: 'pilihan',
    advisor_staff_id: 5,
    schedule_day: 'Jumat, 12:30 - 14:00 WIB',
    location: 'Masjid Sekolah',
    description: 'Kajian keislaman, tahfidz Al-Qur\'an, mentoring ibadah, dan bakti sosial masyarakat.',
    is_active: true,
    members_count: 55,
  },
  {
    id: 6,
    school_id: 1,
    name: 'Futsal & Sepak Bola',
    category: 'pilihan',
    advisor_staff_id: 6,
    schedule_day: 'Selasa & Sabtu, 16:00 WIB',
    location: 'Gelanggang Olahraga',
    description: 'Latihan fisik, taktik permainan tim, dan persiapan kompetisi turnamen antar pelajar.',
    is_active: true,
    members_count: 40,
  },
]);

const sampleMembers = [
  { name: 'Muhammad Raditya', nisn: '0058291041', class_name: 'XII RPL 1', role: 'leader' },
  { name: 'Syifa Azzahra', nisn: '0058291042', class_name: 'XII RPL 1', role: 'secretary' },
  { name: 'Fikri Haikal', nisn: '0058291043', class_name: 'XII RPL 2', role: 'treasurer' },
  { name: 'Zahra Amalia', nisn: '0058291044', class_name: 'XII RPL 2', role: 'member' },
  { name: 'Dimas Prasetyo', nisn: '0058291045', class_name: 'XI TKJ 1', role: 'member' },
];

const totalMembers = computed(() => {
  return extracurriculars.value.reduce((acc, curr) => acc + (curr.members_count || 0), 0);
});

const filteredList = computed(() => {
  return extracurriculars.value.filter((item) => {
    const matchesSearch =
      !searchQuery.value.trim() ||
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (item.advisor?.full_name || '').toLowerCase().includes(searchQuery.value.toLowerCase());

    const matchesCategory = !selectedCategory.value || item.category === selectedCategory.value;

    return matchesSearch && matchesCategory;
  });
});

function openCreateModal() {
  isEditing.value = false;
  form.value = {
    name: '',
    category: 'pilihan',
    schedule_day: '',
    location: '',
    description: '',
    is_active: true,
  };
  showFormModal.value = true;
}

function openEditModal(item: ExtracurricularData) {
  isEditing.value = true;
  form.value = { ...item };
  showFormModal.value = true;
}

function openMembersModal(item: ExtracurricularData) {
  currentEkskul.value = item;
  showMembersModal.value = true;
}

function handleSaveExtracurricular() {
  if (isEditing.value) {
    const idx = extracurriculars.value.findIndex(e => e.id === form.value.id);
    if (idx !== -1) {
      extracurriculars.value[idx] = { ...extracurriculars.value[idx], ...form.value } as ExtracurricularData;
    }
  } else {
    const newId = Date.now();
    extracurriculars.value.push({
      id: newId,
      school_id: 1,
      name: form.value.name || 'Cabang Baru',
      category: form.value.category || 'pilihan',
      advisor_staff_id: null,
      schedule_day: form.value.schedule_day || null,
      location: form.value.location || null,
      description: form.value.description || null,
      is_active: true,
      members_count: 0,
    });
  }
  showFormModal.value = false;
}
</script>
