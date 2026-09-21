<template>
  <div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.staff' }" class="hover:text-foreground">Kepegawaian</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Registrasi PTK</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Tambah Pendidik & Tenaga Kependidikan</h1>
        <p class="text-sm text-muted-foreground">
          Daftarkan data guru atau staf tata usaha baru ke dalam pangkalan data sekolah.
        </p>
      </div>

      <router-link
        :to="{ name: 'school.staff' }"
        class="inline-flex items-center gap-1.5 rounded-lg border border-border px-3.5 py-2 text-xs font-semibold text-muted-foreground hover:bg-muted transition"
      >
        <ArrowLeft class="size-4" />
        Kembali ke Daftar
      </router-link>
    </div>

    <!-- Error Banner -->
    <div v-if="errorMessage" class="rounded-lg border border-destructive/30 bg-destructive/10 p-4 text-xs text-destructive">
      {{ errorMessage }}
    </div>

    <!-- Staff Form -->
    <StaffForm
      :loading="isSubmitting"
      @submit="handleCreateStaff"
      @cancel="$router.push({ name: 'school.staff' })"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { ArrowLeft } from 'lucide-vue-next';
import StaffForm from '../../components/staff/StaffForm.vue';
import { HrService } from '../../services/hrService';
import type { Staff } from '../../types';

const router = useRouter();
const isSubmitting = ref(false);
const errorMessage = ref<string | null>(null);

async function handleCreateStaff(payload: Partial<Staff>) {
  isSubmitting.value = true;
  errorMessage.value = null;

  try {
    const res = await HrService.createStaff(payload);
    if (res.data?.success) {
      router.push({ name: 'school.staff' });
    } else {
      errorMessage.value = res.data?.message || 'Gagal menyimpan data pegawai baru.';
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Terjadi kesalahan saat menambahkan data pegawai.';
  } finally {
    isSubmitting.value = false;
  }
}
</script>
