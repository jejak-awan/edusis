<template>
  <div class="space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-1">
          <router-link :to="{ name: 'school.staff' }" class="hover:text-foreground">Kepegawaian</router-link>
          <span>/</span>
          <span class="text-foreground font-medium">Perbarui Data PTK</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">Perbarui Data Pegawai</h1>
        <p class="text-sm text-muted-foreground">
          Perubahan pada biodata dan kualifikasi pegawai akan diperbarui di sistem kepegawaian.
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

    <!-- Loading State -->
    <div v-if="isLoading" class="rounded-xl border border-border/60 bg-card p-12 text-center text-sm text-muted-foreground">
      <div class="inline-block size-6 animate-spin rounded-full border-2 border-primary border-t-transparent mb-2"></div>
      <div>Memuat data pegawai...</div>
    </div>

    <!-- Staff Form -->
    <StaffForm
      v-else-if="staff"
      :initial-data="staff"
      :is-edit="true"
      :loading="isSubmitting"
      @submit="handleUpdateStaff"
      @cancel="$router.push({ name: 'school.staff' })"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowLeft } from 'lucide-vue-next';
import StaffForm from '../../components/staff/StaffForm.vue';
import { HrService } from '../../services/hrService';
import type { Staff } from '../../types';

const route = useRoute();
const router = useRouter();

const staff = ref<Staff | null>(null);
const isLoading = ref(true);
const isSubmitting = ref(false);
const errorMessage = ref<string | null>(null);

async function loadStaff() {
  isLoading.value = true;
  errorMessage.value = null;

  try {
    const staffId = route.params.id as string;
    const res = await HrService.getStaffById(staffId);
    if (res.data?.success && res.data.data) {
      staff.value = res.data.data;
    } else {
      errorMessage.value = 'Data pegawai tidak ditemukan.';
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Gagal memuat profil data pegawai.';
  } finally {
    isLoading.value = false;
  }
}

async function handleUpdateStaff(payload: Partial<Staff>) {
  if (!staff.value) return;

  isSubmitting.value = true;
  errorMessage.value = null;

  try {
    const staffId = route.params.id as string;
    const res = await HrService.updateStaff(staffId, payload);
    if (res.data?.success) {
      router.push({ name: 'school.staff' });
    } else {
      errorMessage.value = res.data?.message || 'Gagal memperbarui data pegawai.';
    }
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || 'Terjadi kesalahan saat memperbarui data pegawai.';
  } finally {
    isSubmitting.value = false;
  }
}

onMounted(loadStaff);
</script>
