<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.whatsapp.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.whatsapp.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openComposeModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700"
        >
          <Send class="size-4" />
          {{ t('school.whatsapp.btnCompose') }}
        </button>
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.whatsapp.totalSent') }}</div>
          <div class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.sent }} Pesan</div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.whatsapp.attendanceAlerts') }}</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">{{ stats.attendance_alerts }} Alert</div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Bell class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.whatsapp.announcements') }}</div>
          <div class="text-2xl font-bold text-primary mt-1">{{ stats.announcements }} Info</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <Megaphone class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.whatsapp.failedQueued') }}</div>
          <div class="text-2xl font-bold text-rose-600 mt-1">{{ (stats.failed || 0) + (stats.queued || 0) }}</div>
        </div>
        <div class="size-10 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center">
          <AlertCircle class="size-5" />
        </div>
      </div>
    </div>

    <!-- PRESENCE AUTO-ALERT DISPATCHER CARD -->
    <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/5 p-5 shadow-sm space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-emerald-500/20 pb-3">
        <div class="flex items-center gap-2.5">
          <div class="size-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center">
            <MessageSquare class="size-4" />
          </div>
          <div>
            <h3 class="text-sm font-bold text-foreground">{{ t('school.whatsapp.autoAlertTitle') }}</h3>
            <p class="text-xs text-muted-foreground">{{ t('school.whatsapp.autoAlertDesc') }}</p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <input
            v-model="alertDate"
            type="date"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          />
          <button
            @click="triggerAttendanceAlerts"
            :disabled="triggeringAlerts"
            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50"
          >
            <Send class="size-3.5" />
            {{ triggeringAlerts ? t('school.common.loading') : t('school.whatsapp.btnSendAutoAlert') }}
          </button>
        </div>
      </div>

      <div class="text-xs text-muted-foreground grid grid-cols-1 sm:grid-cols-3 gap-2">
        <div class="flex items-center gap-2">
          <span class="size-2 rounded-full bg-rose-500"></span>
          <span>Target Absensi: <strong>Alpa (A)</strong> & <strong>Sakit / Izin (S/I)</strong></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="size-2 rounded-full bg-blue-500"></span>
          <span>Nomor Tujuan: <strong>Data Ayah / Ibu / Wali Dapodik</strong></span>
        </div>
        <div class="flex items-center gap-2">
          <span class="size-2 rounded-full bg-emerald-500"></span>
          <span>Anti-Spam: <strong>Maks. 1 pesan alert per siswa / hari</strong></span>
        </div>
      </div>
    </div>

    <!-- LOGS TABLE & FILTERS -->
    <div class="space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm w-full">
          <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
          <input
            v-model="searchQuery"
            @input="debounceSearch"
            type="text"
            :placeholder="t('school.whatsapp.searchPlaceholder')"
            class="w-full rounded-lg border border-border/60 bg-background pl-9 pr-3 py-1.5 text-xs text-foreground placeholder:text-muted-foreground focus:ring-1 focus:ring-primary"
          />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="selectedMessageType"
            @change="loadLogs"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.whatsapp.allTypes') }}</option>
            <option value="attendance_alert">Presensi (Alert)</option>
            <option value="discipline_sp">Peringatan Disiplin (SP)</option>
            <option value="achievement">Prestasi Siswa</option>
            <option value="announcement">Pengumuman Sekolah</option>
            <option value="general">Pesan Umum</option>
          </select>

          <select
            v-model="selectedStatus"
            @change="loadLogs"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.whatsapp.allStatuses') }}</option>
            <option value="sent">Sent</option>
            <option value="delivered">Delivered</option>
            <option value="queued">Queued</option>
            <option value="failed">Failed</option>
          </select>
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border/60 bg-muted/40 text-muted-foreground font-medium">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">{{ t('school.whatsapp.colRecipient') }}</th>
              <th class="p-3">{{ t('school.whatsapp.colStudent') }}</th>
              <th class="p-3">{{ t('school.whatsapp.colType') }}</th>
              <th class="p-3">{{ t('school.whatsapp.colContent') }}</th>
              <th class="p-3">{{ t('school.whatsapp.colStatus') }}</th>
              <th class="p-3">{{ t('school.whatsapp.colTime') }}</th>
              <th class="p-3 text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-if="loadingLogs">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                <Loader2 class="size-5 animate-spin mx-auto mb-1 text-primary" />
                {{ t('school.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="logs.length === 0">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                {{ t('school.common.noData') }}
              </td>
            </tr>
            <tr v-for="(log, index) in logs" :key="log.id" class="hover:bg-muted/20">
              <td class="p-3 text-muted-foreground">{{ Number(index) + 1 }}</td>
              <td class="p-3">
                <div class="font-semibold text-foreground">{{ log.recipient_name || 'Wali Siswa' }}</div>
                <div class="font-mono text-[11px] text-muted-foreground">+{{ log.recipient_phone }}</div>
              </td>
              <td class="p-3 font-medium text-foreground">
                {{ log.student?.full_name || '-' }}
              </td>
              <td class="p-3">
                <span :class="getMessageTypeBadge(log.message_type)">
                  {{ formatMessageType(log.message_type) }}
                </span>
              </td>
              <td class="p-3 max-w-sm">
                <div class="line-clamp-2 text-foreground text-[11px] font-mono leading-relaxed bg-muted/20 p-1.5 rounded">
                  {{ log.content }}
                </div>
              </td>
              <td class="p-3">
                <span :class="getStatusBadge(log.status)">
                  {{ log.status }}
                </span>
              </td>
              <td class="p-3 text-[11px] text-muted-foreground whitespace-nowrap">
                {{ formatDateTime(log.sent_at || log.created_at) }}
              </td>
              <td class="p-3 text-right">
                <button
                  @click="resend(log.id)"
                  class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition"
                  title="Kirim Ulang"
                >
                  <RotateCw class="size-3.5" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- COMPOSE MESSAGE MODAL -->
    <div v-if="showComposeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-lg rounded-xl border border-border/60 bg-card p-5 shadow-lg space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">{{ t('school.whatsapp.composeTitle') }}</h3>
          <button @click="showComposeModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="submitSendMessage" class="space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.whatsapp.recipientPhone') }}</label>
              <input
                v-model="composeForm.recipient_phone"
                type="text"
                required
                placeholder="081234567890"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.whatsapp.recipientName') }}</label>
              <input
                v-model="composeForm.recipient_name"
                type="text"
                required
                placeholder="Bapak / Ibu Wali Murid"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.whatsapp.colType') }}</label>
              <select
                v-model="composeForm.message_type"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="attendance_alert">Presensi (Alert)</option>
                <option value="discipline_sp">Peringatan Disiplin (SP)</option>
                <option value="achievement">Prestasi Siswa</option>
                <option value="announcement">Pengumuman Sekolah</option>
                <option value="general">Pesan Umum</option>
              </select>
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.whatsapp.linkStudent') }} (Opsional)</label>
              <select
                v-model="composeForm.student_id"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option :value="null">-- Tanpa Kaitan Siswa --</option>
                <option v-for="std in students" :key="std.id" :value="std.id">{{ std.full_name }}</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.whatsapp.content') }}</label>
            <textarea
              v-model="composeForm.content"
              rows="4"
              required
              placeholder="Tuliskan pesan WhatsApp resmi dari pihak sekolah..."
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary font-sans"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showComposeModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="sending"
              class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50"
            >
              {{ sending ? t('school.common.saving') : t('school.whatsapp.btnSendNow') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  CheckCircle2,
  Bell,
  Megaphone,
  AlertCircle,
  MessageSquare,
  Send,
  Search,
  RotateCw,
  X,
  Loader2,
} from 'lucide-vue-next';
import {
  SchoolService,
  type WhatsAppLogData,
  type WhatsAppStatsData,
  type StudentData,
} from '../../services/schoolService';

const { t } = useI18n();

const logs = ref<WhatsAppLogData[]>([]);
const stats = ref<WhatsAppStatsData>({
  total: 0,
  sent: 0,
  failed: 0,
  attendance_alerts: 0,
  announcements: 0,
});
const students = ref<StudentData[]>([]);

const loadingLogs = ref(false);
const triggeringAlerts = ref(false);
const sending = ref(false);
const showComposeModal = ref(false);

const alertDate = ref(new Date().toISOString().slice(0, 10));
const searchQuery = ref('');
const selectedMessageType = ref('');
const selectedStatus = ref('');

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const composeForm = reactive({
  recipient_phone: '',
  recipient_name: '',
  message_type: 'announcement' as WhatsAppLogData['message_type'],
  content: '',
  student_id: null as number | null,
});

function debounceSearch() {
  if (searchTimeout) clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    loadLogs();
  }, 300);
}

function formatMessageType(type: string) {
  const map: Record<string, string> = {
    attendance_alert: 'Presensi (Alert)',
    discipline_sp: 'Disiplin (SP)',
    achievement: 'Prestasi',
    announcement: 'Pengumuman',
    general: 'Umum',
  };
  return map[type] || type;
}

function getMessageTypeBadge(type: string) {
  if (type === 'attendance_alert') return 'inline-flex items-center rounded-md bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-600';
  if (type === 'discipline_sp') return 'inline-flex items-center rounded-md bg-rose-500/10 px-2 py-0.5 text-[11px] font-medium text-rose-600';
  if (type === 'achievement') return 'inline-flex items-center rounded-md bg-purple-500/10 px-2 py-0.5 text-[11px] font-medium text-purple-600';
  if (type === 'announcement') return 'inline-flex items-center rounded-md bg-primary/10 px-2 py-0.5 text-[11px] font-medium text-primary';
  return 'inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium text-muted-foreground';
}

function getStatusBadge(status: string) {
  if (status === 'sent' || status === 'delivered') {
    return 'inline-flex items-center rounded-md bg-emerald-500/10 px-2 py-0.5 text-[11px] font-semibold text-emerald-600';
  }
  if (status === 'queued') {
    return 'inline-flex items-center rounded-md bg-blue-500/10 px-2 py-0.5 text-[11px] font-semibold text-blue-600';
  }
  return 'inline-flex items-center rounded-md bg-rose-500/10 px-2 py-0.5 text-[11px] font-semibold text-rose-600';
}

function formatDateTime(isoStr: string | null) {
  if (!isoStr) return '-';
  const d = new Date(isoStr);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
}

async function loadStats() {
  try {
    const res = await SchoolService.getWhatsAppStats();
    stats.value = res.data.data;
  } catch (err) {
    console.error(err);
  }
}

async function loadLogs() {
  loadingLogs.value = true;
  try {
    const params: Record<string, unknown> = {
      search: searchQuery.value || undefined,
      message_type: selectedMessageType.value || undefined,
      status: selectedStatus.value || undefined,
    };
    const res = await SchoolService.getWhatsAppLogs(params);
    const rawLogs = res.data.data;
    logs.value = Array.isArray(rawLogs) ? rawLogs : ((rawLogs as any)?.data ?? []);
  } catch (err) {
    console.error(err);
  } finally {
    loadingLogs.value = false;
  }
}

async function loadStudents() {
  try {
    const res = await SchoolService.getStudents({ per_page: 100 });
    students.value = Array.isArray(res.data.data) ? res.data.data : ((res.data.data as any)?.data ?? []);
  } catch (err) {
    console.error(err);
  }
}

async function triggerAttendanceAlerts() {
  triggeringAlerts.value = true;
  try {
    const res = await SchoolService.sendWhatsAppAttendanceAlerts({
      date: alertDate.value,
      statuses: ['A', 'alpa', 'S', 'sakit', 'I', 'izin'],
    });
    alert(`Otomasi Presensi Selesai: Berhasil mengirim ${res.data.data.sent_count ?? 0} notifikasi WhatsApp ke orang tua/wali siswa!`);
    await loadStats();
    await loadLogs();
  } catch (err) {
    console.error(err);
  } finally {
    triggeringAlerts.value = false;
  }
}

function openComposeModal() {
  composeForm.recipient_phone = '';
  composeForm.recipient_name = '';
  composeForm.message_type = 'announcement';
  composeForm.content = '';
  composeForm.student_id = null;
  showComposeModal.value = true;
}

async function submitSendMessage() {
  sending.value = true;
  try {
    await SchoolService.sendWhatsAppMessage(composeForm);
    showComposeModal.value = false;
    await loadStats();
    await loadLogs();
  } catch (err) {
    console.error(err);
  } finally {
    sending.value = false;
  }
}

async function resend(id: number) {
  try {
    await SchoolService.resendWhatsAppLog(id);
    await loadStats();
    await loadLogs();
  } catch (err) {
    console.error(err);
  }
}

onMounted(() => {
  loadStats();
  loadLogs();
  loadStudents();
});
</script>
