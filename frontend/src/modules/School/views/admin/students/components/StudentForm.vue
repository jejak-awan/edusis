<template>
  <div class="space-y-6">
    <!-- Tabs Header -->
    <div class="flex border-b border-border/60 bg-card/60 backdrop-blur-sm px-4 pt-3 rounded-t-xl overflow-x-auto">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        type="button"
        @click="activeTab = tab.id"
        :class="[
          'flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 transition whitespace-nowrap',
          activeTab === tab.id
            ? 'border-primary text-primary font-semibold'
            : 'border-transparent text-muted-foreground hover:text-foreground hover:border-border'
        ]"
      >
        <component :is="tab.icon" class="size-4" />
        {{ tab.label }}
      </button>
    </div>

    <!-- Form Content -->
    <form @submit.prevent="handleSubmit" class="rounded-b-xl border border-t-0 border-border/60 bg-card p-6 shadow-sm space-y-6">
      <!-- TAB 1: DATA PRIBADI -->
      <div v-show="activeTab === 'biodata'" class="space-y-5">
        <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
          <User class="size-4 text-primary" />
          Data Identitas Pribadi Siswa
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="text-xs font-medium text-muted-foreground">Nama Lengkap <span class="text-destructive">*</span></label>
            <input
              v-model="form.full_name"
              type="text"
              required
              placeholder="Contoh: Muhammad Fauzan"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">NISN (Nomor Induk Siswa Nasional)</label>
            <input
              v-model="form.nisn"
              type="text"
              maxlength="20"
              placeholder="10 digit nomor NISN"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">NIS (Nomor Induk Sekolah)</label>
            <input
              v-model="form.nis"
              type="text"
              maxlength="20"
              placeholder="Nomor induk lokal sekolah"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">NIK Siswa</label>
            <input
              v-model="form.nik"
              type="text"
              maxlength="20"
              placeholder="16 digit nomor induk kependudukan"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Jenis Kelamin <span class="text-destructive">*</span></label>
            <select
              v-model="form.gender"
              required
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Tempat Lahir</label>
            <input
              v-model="form.place_of_birth"
              type="text"
              placeholder="Kota / Kabupaten tempat lahir"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Tanggal Lahir</label>
            <input
              v-model="form.date_of_birth"
              type="date"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Agama</label>
            <select
              v-model="form.religion"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="">Pilih Agama</option>
              <option value="Islam">Islam</option>
              <option value="Kristen">Kristen</option>
              <option value="Katolik">Katolik</option>
              <option value="Hindu">Hindu</option>
              <option value="Buddha">Buddha</option>
              <option value="Khonghucu">Khonghucu</option>
            </select>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Status Kesiswaan</label>
            <select
              v-model="form.status"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option value="active">Aktif</option>
              <option value="graduated">Lulus</option>
              <option value="transferred">Mutasi Keluar</option>
              <option value="dropped_out">Dikeluarkan / DO</option>
            </select>
          </div>
        </div>
      </div>

      <!-- TAB 2: ALAMAT & KONTAK -->
      <div v-show="activeTab === 'address'" class="space-y-5">
        <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
          <MapPin class="size-4 text-primary" />
          Tempat Tinggal & Kontak Siswa
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="text-xs font-medium text-muted-foreground">Alamat Jalan / Blok / No. Rumah</label>
            <textarea
              v-model="form.address"
              rows="2"
              placeholder="Contoh: Jl. Raya Cijulang No. 45"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="text-xs font-medium text-muted-foreground">RT</label>
              <input
                v-model="form.rt"
                type="text"
                placeholder="001"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">RW</label>
              <input
                v-model="form.rw"
                type="text"
                placeholder="002"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Dusun / Kampung</label>
            <input
              v-model="form.dusun"
              type="text"
              placeholder="Nama Dusun / Kampung"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Kelurahan / Desa</label>
            <input
              v-model="form.desa_kelurahan"
              type="text"
              placeholder="Nama Desa / Kelurahan"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Kecamatan</label>
            <input
              v-model="form.kecamatan"
              type="text"
              placeholder="Nama Kecamatan"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Nomor HP / WhatsApp Siswa</label>
            <input
              v-model="form.phone"
              type="text"
              placeholder="08123456789"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Email Siswa</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="siswa@sekolah.sch.id"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
        </div>
      </div>

      <!-- TAB 3: DATA ORANG TUA & WALI -->
      <div v-show="activeTab === 'parents'" class="space-y-5">
        <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
          <Users class="size-4 text-primary" />
          Data Orang Tua & Wali Siswa
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Ayah -->
          <div class="space-y-3 rounded-lg border border-border/60 bg-muted/20 p-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Data Ayah Kandung</h4>
            <div>
              <label class="text-xs font-medium text-muted-foreground">Nama Lengkap Ayah</label>
              <input
                v-model="form.father_name"
                type="text"
                placeholder="Nama ayah"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">NIK Ayah</label>
              <input
                v-model="form.father_nik"
                type="text"
                maxlength="20"
                placeholder="NIK ayah (16 digit)"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">Pekerjaan Ayah</label>
              <input
                v-model="form.father_occupation"
                type="text"
                placeholder="Pekerjaan ayah"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">No. Telepon/HP Ayah</label>
              <input
                v-model="form.father_phone"
                type="text"
                placeholder="No HP Ayah"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
          </div>

          <!-- Ibu -->
          <div class="space-y-3 rounded-lg border border-border/60 bg-muted/20 p-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Data Ibu Kandung</h4>
            <div>
              <label class="text-xs font-medium text-muted-foreground">Nama Lengkap Ibu</label>
              <input
                v-model="form.mother_name"
                type="text"
                placeholder="Nama ibu"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">NIK Ibu</label>
              <input
                v-model="form.mother_nik"
                type="text"
                maxlength="20"
                placeholder="NIK ibu (16 digit)"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">Pekerjaan Ibu</label>
              <input
                v-model="form.mother_occupation"
                type="text"
                placeholder="Pekerjaan ibu"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
            <div>
              <label class="text-xs font-medium text-muted-foreground">No. Telepon/HP Ibu</label>
              <input
                v-model="form.mother_phone"
                type="text"
                placeholder="No HP Ibu"
                class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
              />
            </div>
          </div>

          <!-- Wali -->
          <div class="md:col-span-2 space-y-3 rounded-lg border border-border/60 bg-muted/20 p-4">
            <h4 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Data Wali (Opsional)</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="text-xs font-medium text-muted-foreground">Nama Lengkap Wali</label>
                <input
                  v-model="form.guardian_name"
                  type="text"
                  placeholder="Nama wali jika tinggal dengan wali"
                  class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
                />
              </div>
              <div>
                <label class="text-xs font-medium text-muted-foreground">No. Telepon Wali</label>
                <input
                  v-model="form.guardian_phone"
                  type="text"
                  placeholder="No telepon wali"
                  class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
                />
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB 4: AKADEMIK & ROMBEL -->
      <div v-show="activeTab === 'academic'" class="space-y-5">
        <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
          <GraduationCap class="size-4 text-primary" />
          Informasi Akademik & Penempatan Rombel
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-medium text-muted-foreground">Jurusan / Kompetensi Keahlian</label>
            <select
              v-model="form.department_id"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option :value="null">-- Tanpa Jurusan / Umum --</option>
              <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                {{ dept.code }} - {{ dept.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Rombongan Belajar (Rombel)</label>
            <select
              v-model="form.study_group_id"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            >
              <option :value="null">-- Belum Ditentukan --</option>
              <option v-for="group in studyGroups" :key="group.id" :value="group.id">
                Kelas {{ group.grade_level }} - {{ group.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Tahun Masuk / Angkatan</label>
            <input
              v-model.number="form.entry_year"
              type="number"
              min="2000"
              max="2099"
              placeholder="Contoh: 2026"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            />
          </div>

          <div>
            <label class="text-xs font-medium text-muted-foreground">Asal Sekolah (SMP / MTs)</label>
            <input
              v-model="form.previous_school"
              type="text"
              placeholder="Contoh: SMP Negeri 1 Cijulang"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none"
            />
          </div>
        </div>
      </div>

      <!-- TAB 5: KESEJAHTERAAN & BANTUAN -->
      <div v-show="activeTab === 'welfare'" class="space-y-5">
        <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
          <HeartHandshake class="size-4 text-primary" />
          Kesejahteraan & Bantuan Sosial
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="flex items-center gap-3 md:col-span-2 p-3 rounded-lg border border-border/60 bg-muted/10">
            <input
              id="is_kip"
              v-model="form.is_kip_recipient"
              type="checkbox"
              class="size-4 rounded border-border text-primary focus:ring-primary"
            />
            <label for="is_kip" class="text-xs font-medium text-foreground cursor-pointer">
              Siswa Penerima Program Indonesia Pintar (PIP) / Kartu Indonesia Pintar (KIP)
            </label>
          </div>

          <div v-if="form.is_kip_recipient" class="md:col-span-2">
            <label class="text-xs font-medium text-muted-foreground">Nomor Kartu Indonesia Pintar (KIP)</label>
            <input
              v-model="form.kip_number"
              type="text"
              placeholder="Nomor tercantum pada kartu KIP"
              class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
          </div>
        </div>
      </div>

      <!-- Form Actions Footer -->
      <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-6 border-t border-border/60">
        <button
          type="button"
          @click="$emit('cancel')"
          class="w-full sm:w-auto rounded-lg border border-border px-5 py-2.5 text-xs font-medium text-muted-foreground hover:bg-muted transition"
        >
          Batal & Kembali
        </button>

        <div class="flex items-center gap-3 w-full sm:w-auto">
          <button
            type="submit"
            :disabled="loading"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-2.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
          >
            <Save class="size-4" />
            <span>{{ isEdit ? 'Perbarui Data Siswa' : 'Simpan Siswa Baru' }}</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import {
  User,
  MapPin,
  Users,
  GraduationCap,
  HeartHandshake,
  Save,
} from 'lucide-vue-next';
import type { Department, StudyGroup, Student } from '../../../../services/types';

const props = defineProps<{
  initialData?: Partial<Student> | null;
  departments: Department[];
  studyGroups: StudyGroup[];
  isEdit?: boolean;
  loading?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', payload: Partial<Student>): void;
  (e: 'cancel'): void;
}>();

const tabs = [
  { id: 'biodata', label: '1. Data Pribadi', icon: User },
  { id: 'address', label: '2. Alamat & Kontak', icon: MapPin },
  { id: 'parents', label: '3. Orang Tua / Wali', icon: Users },
  { id: 'academic', label: '4. Akademik & Rombel', icon: GraduationCap },
  { id: 'welfare', label: '5. Bantuan & Sosial', icon: HeartHandshake },
];

const activeTab = ref('biodata');

const form = ref<Partial<Student>>({
  full_name: '',
  nisn: '',
  nis: '',
  nik: '',
  gender: 'L',
  place_of_birth: '',
  date_of_birth: '',
  religion: 'Islam',
  status: 'active',
  address: '',
  rt: '',
  rw: '',
  dusun: '',
  desa_kelurahan: '',
  kecamatan: '',
  phone: '',
  email: '',
  father_name: '',
  father_nik: '',
  father_occupation: '',
  father_phone: '',
  mother_name: '',
  mother_nik: '',
  mother_occupation: '',
  mother_phone: '',
  guardian_name: '',
  guardian_phone: '',
  department_id: undefined,
  study_group_id: undefined,
  entry_year: new Date().getFullYear(),
  previous_school: '',
  is_kip_recipient: false,
  kip_number: '',
});

watch(
  () => props.initialData,
  (val) => {
    if (val) {
      form.value = { ...form.value, ...val };
    }
  },
  { immediate: true }
);

function handleSubmit() {
  emit('submit', form.value);
}
</script>
