<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.guests.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.guests.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openCheckInModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <UserPlus class="size-4" />
          {{ t('school.guests.btnCheckIn') }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.guests.activeVisitors') }}</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">
            {{ activeVisitorsCount }} Tamu
          </div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Clock class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.guests.completedVisitors') }}</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">
            {{ completedVisitorsCount }} Tamu
          </div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.guests.totalVisits') }}</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ guests.length }} Tamu</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <Building2 class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filters & Guest List -->
    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <select
            v-model="filterStatus"
            @change="loadGuests"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.guests.allStatuses') }}</option>
            <option value="checked_in">{{ t('school.guests.statusCheckedIn') }}</option>
            <option value="checked_out">{{ t('school.guests.statusCheckedOut') }}</option>
          </select>
        </div>

        <div class="relative">
          <input
            v-model="searchQuery"
            @keyup.enter="loadGuests"
            type="text"
            :placeholder="t('school.guests.searchPlaceholder')"
            class="w-64 rounded-lg border border-border/60 bg-background pl-8 pr-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          />
          <Search class="absolute left-2.5 top-2 size-3.5 text-muted-foreground" />
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-border/60 bg-muted/20 text-muted-foreground">
              <th class="py-2.5 px-3 font-semibold w-12 text-center">#</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.guests.badge') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.guests.visitorName') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.guests.institution') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.guests.purpose') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.guests.targetPerson') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.guests.time') }}</th>
              <th class="py-2.5 px-3 font-semibold text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-if="loading" class="text-center text-muted-foreground">
              <td colspan="8" class="py-8">{{ t('school.common.loading') }}</td>
            </tr>
            <tr v-else-if="guests.length === 0" class="text-center text-muted-foreground">
              <td colspan="8" class="py-8">{{ t('school.guests.empty') }}</td>
            </tr>
            <tr
              v-else
              v-for="(guest, index) in guests"
              :key="guest.id"
              class="hover:bg-muted/10 transition"
            >
              <td class="py-3 px-3 text-center text-muted-foreground font-mono">{{ Number(index) + 1 }}</td>
              <td class="py-3 px-3">
                <span class="inline-flex items-center rounded-lg bg-primary/10 px-2 py-0.5 text-[11px] font-mono font-bold text-primary">
                  {{ guest.badge_number || '-' }}
                </span>
              </td>
              <td class="py-3 px-3">
                <div class="font-semibold text-foreground">{{ guest.visitor_name }}</div>
                <div class="text-[11px] text-muted-foreground font-mono">{{ guest.phone || '-' }}</div>
              </td>
              <td class="py-3 px-3">
                <div class="text-foreground">{{ guest.institution || '-' }}</div>
              </td>
              <td class="py-3 px-3 max-w-xs">
                <div class="text-foreground line-clamp-1 font-medium">{{ guest.purpose }}</div>
                <div v-if="guest.notes" class="text-[11px] text-muted-foreground line-clamp-1 mt-0.5">{{ guest.notes }}</div>
              </td>
              <td class="py-3 px-3">
                <div class="font-medium text-foreground">{{ guest.target_person }}</div>
              </td>
              <td class="py-3 px-3">
                <div class="text-[11px] text-foreground">
                  Masuk: <span class="font-mono font-medium">{{ formatTime(guest.check_in_at) }}</span>
                </div>
                <div v-if="guest.check_out_at" class="text-[11px] text-muted-foreground">
                  Keluar: <span class="font-mono font-medium">{{ formatTime(guest.check_out_at) }}</span>
                </div>
              </td>
              <td class="py-3 px-3 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <button
                    v-if="guest.status === 'checked_in'"
                    @click="checkoutGuest(guest.id)"
                    class="rounded-md border border-amber-500/40 bg-amber-500/10 px-2.5 py-1 text-[11px] font-semibold text-amber-600 hover:bg-amber-500/20 transition"
                  >
                    <LogOut class="inline-block size-3 mr-1" />
                    {{ t('school.guests.btnCheckOut') }}
                  </button>
                  <span
                    v-else
                    class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-emerald-500/10 text-emerald-600"
                  >
                    Selesai
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Check-In Tamu Baru -->
    <div
      v-if="showCheckInModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-lg rounded-xl border border-border/60 bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h2 class="text-base font-semibold text-foreground">{{ t('school.guests.modalTitleCheckIn') }}</h2>
          <button @click="showCheckInModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="saveGuest" class="space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formVisitorName') }}</label>
              <input
                v-model="form.visitor_name"
                type="text"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formPhone') }}</label>
              <input
                v-model="form.phone"
                type="text"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formInstitution') }}</label>
              <input
                v-model="form.institution"
                type="text"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formBadgeNumber') }}</label>
              <input
                v-model="form.badge_number"
                type="text"
                placeholder="e.g. TAMU-05"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs font-mono text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formIdentityType') }}</label>
              <select
                v-model="form.identity_type"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="KTP">KTP</option>
                <option value="SIM">SIM</option>
                <option value="Passport">Passport</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formIdentityNumber') }}</label>
              <input
                v-model="form.identity_number"
                type="text"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formTargetPerson') }}</label>
            <input
              v-model="form.target_person"
              type="text"
              required
              placeholder="e.g. Kepala Sekolah / Guru BK / Tata Usaha"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formPurpose') }}</label>
            <textarea
              v-model="form.purpose"
              rows="2"
              required
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.guests.formNotes') }}</label>
            <input
              v-model="form.notes"
              type="text"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showCheckInModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.guests.btnConfirmCheckIn') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  Clock,
  CheckCircle2,
  Building2,
  UserPlus,
  LogOut,
  Search,
  X,
} from 'lucide-vue-next';
import { SchoolService, type GuestBookData } from '../../services/schoolService';

const { t } = useI18n();

const guests = ref<GuestBookData[]>([]);
const loading = ref(false);
const saving = ref(false);
const filterStatus = ref('');
const searchQuery = ref('');
const showCheckInModal = ref(false);

const form = reactive({
  visitor_name: '',
  phone: '',
  institution: '',
  badge_number: '',
  identity_type: 'KTP' as 'KTP' | 'SIM' | 'Passport' | 'Lainnya',
  identity_number: '',
  target_person: '',
  purpose: '',
  notes: '',
});

const activeVisitorsCount = computed(() => guests.value.filter(g => g.status === 'checked_in').length);
const completedVisitorsCount = computed(() => guests.value.filter(g => g.status === 'checked_out').length);

const loadGuests = async () => {
  loading.value = true;
  try {
    const res = await SchoolService.getGuestBook({
      status: filterStatus.value || undefined,
      search: searchQuery.value || undefined,
    });
    guests.value = res.data.data.data || res.data.data || [];
  } catch (err) {
    console.error('Failed to load guest book records:', err);
  } finally {
    loading.value = false;
  }
};

const openCheckInModal = () => {
  form.visitor_name = '';
  form.phone = '';
  form.institution = '';
  form.badge_number = '';
  form.identity_type = 'KTP';
  form.identity_number = '';
  form.target_person = '';
  form.purpose = '';
  form.notes = '';
  showCheckInModal.value = true;
};

const saveGuest = async () => {
  saving.value = true;
  try {
    await SchoolService.storeGuest({ ...form });
    showCheckInModal.value = false;
    await loadGuests();
  } catch (err) {
    console.error('Failed to record check-in:', err);
  } finally {
    saving.value = false;
  }
};

const checkoutGuest = async (id: number) => {
  try {
    await SchoolService.checkoutGuest(id);
    await loadGuests();
  } catch (err) {
    console.error('Failed to checkout visitor:', err);
  }
};

const formatTime = (dateStr: string) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  loadGuests();
});
</script>
