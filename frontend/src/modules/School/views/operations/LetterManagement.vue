<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.letters.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.letters.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="openRegisterModal"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <MailPlus class="size-4" />
          {{ t('school.letters.registerLetter') }}
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.letters.incomingTotal') }}</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ incomingCount }} Surat</div>
        </div>
        <div class="size-10 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
          <Inbox class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.letters.outgoingTotal') }}</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ outgoingCount }} Surat</div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <Send class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.letters.pendingDisposition') }}</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">{{ pendingDispositionCount }} Surat</div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Clock class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.letters.processedDisposition') }}</div>
          <div class="text-2xl font-bold text-purple-600 mt-1">{{ processedDispositionCount }} Surat</div>
        </div>
        <div class="size-10 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>
    </div>

    <!-- Filter & Tabs -->
    <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm space-y-4">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <!-- Tabs -->
        <div class="inline-flex rounded-lg border border-border/60 bg-muted/30 p-1">
          <button
            @click="activeTab = 'incoming'; loadLetters()"
            :class="[
              'rounded-md px-4 py-1.5 text-xs font-semibold transition',
              activeTab === 'incoming' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
            ]"
          >
            <Inbox class="inline-block mr-1 size-3.5" />
            {{ t('school.letters.tabIncoming') }}
          </button>
          <button
            @click="activeTab = 'outgoing'; loadLetters()"
            :class="[
              'rounded-md px-4 py-1.5 text-xs font-semibold transition',
              activeTab === 'outgoing' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
            ]"
          >
            <Send class="inline-block mr-1 size-3.5" />
            {{ t('school.letters.tabOutgoing') }}
          </button>
        </div>

        <!-- Search and Category Filter -->
        <div class="flex flex-wrap items-center gap-2">
          <select
            v-model="filterCategory"
            @change="loadLetters"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.letters.allCategories') }}</option>
            <option value="invitation">{{ t('school.letters.catInvitation') }}</option>
            <option value="assignment">{{ t('school.letters.catAssignment') }}</option>
            <option value="decree">{{ t('school.letters.catDecree') }}</option>
            <option value="certificate">{{ t('school.letters.catCertificate') }}</option>
            <option value="circular">{{ t('school.letters.catCircular') }}</option>
            <option value="general">{{ t('school.letters.catGeneral') }}</option>
          </select>

          <div class="relative">
            <input
              v-model="searchQuery"
              @keyup.enter="loadLetters"
              type="text"
              :placeholder="t('school.letters.searchPlaceholder')"
              class="w-56 rounded-lg border border-border/60 bg-background pl-8 pr-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-primary"
            />
            <Search class="absolute left-2.5 top-2 size-3.5 text-muted-foreground" />
          </div>

          <button
            @click="loadLetters"
            class="rounded-lg border border-border/60 bg-card px-3 py-1.5 text-xs font-medium text-foreground hover:bg-muted/40 transition"
          >
            {{ t('school.common.filter') }}
          </button>
        </div>
      </div>

      <!-- Letters Table -->
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-border/60 bg-muted/20 text-muted-foreground">
              <th class="py-2.5 px-3 font-semibold w-12 text-center">#</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.letters.numberAndDate') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ activeTab === 'incoming' ? t('school.letters.sender') : t('school.letters.recipient') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.letters.subject') }}</th>
              <th class="py-2.5 px-3 font-semibold">{{ t('school.letters.category') }}</th>
              <th v-if="activeTab === 'incoming'" class="py-2.5 px-3 font-semibold">{{ t('school.letters.disposition') }}</th>
              <th class="py-2.5 px-3 font-semibold text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/40">
            <tr v-if="loading" class="text-center text-muted-foreground">
              <td colspan="7" class="py-8">{{ t('school.common.loading') }}</td>
            </tr>
            <tr v-else-if="letters.length === 0" class="text-center text-muted-foreground">
              <td colspan="7" class="py-8">{{ t('school.letters.empty') }}</td>
            </tr>
            <tr
              v-else
              v-for="(letter, index) in letters"
              :key="letter.id"
              class="hover:bg-muted/10 transition"
            >
              <td class="py-3 px-3 text-center text-muted-foreground font-mono">{{ Number(index) + 1 }}</td>
              <td class="py-3 px-3">
                <div class="font-semibold text-foreground font-mono">{{ letter.letter_number }}</div>
                <div class="text-[11px] text-muted-foreground mt-0.5">{{ letter.letter_date }}</div>
              </td>
              <td class="py-3 px-3">
                <div class="font-medium text-foreground">{{ activeTab === 'incoming' ? letter.sender : letter.recipient }}</div>
                <div v-if="activeTab === 'incoming' && letter.reference_number" class="text-[11px] text-muted-foreground font-mono">
                  Ref: {{ letter.reference_number }}
                </div>
              </td>
              <td class="py-3 px-3 max-w-xs">
                <div class="font-medium text-foreground line-clamp-1">{{ letter.subject }}</div>
                <div v-if="letter.notes" class="text-[11px] text-muted-foreground line-clamp-1 mt-0.5">{{ letter.notes }}</div>
              </td>
              <td class="py-3 px-3">
                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-primary/10 text-primary uppercase">
                  {{ letter.category }}
                </span>
              </td>
              <td v-if="activeTab === 'incoming'" class="py-3 px-3">
                <div v-if="letter.disposition_to" class="space-y-0.5">
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold',
                      letter.disposition_status === 'completed' ? 'bg-emerald-500/10 text-emerald-600' :
                      letter.disposition_status === 'processed' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600'
                    ]"
                  >
                    {{ letter.disposition_status }}
                  </span>
                  <div class="text-[11px] text-foreground font-medium">{{ letter.disposition_to }}</div>
                </div>
                <span v-else class="text-muted-foreground italic text-[11px]">{{ t('school.letters.noDisposition') }}</span>
              </td>
              <td class="py-3 px-3 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <button
                    v-if="activeTab === 'incoming'"
                    @click="openDispositionModal(letter)"
                    class="rounded-md border border-border/60 bg-background px-2.5 py-1 text-[11px] font-medium text-primary hover:bg-muted/40 transition"
                  >
                    <FileText class="inline-block size-3 mr-1" />
                    {{ t('school.letters.btnDisposition') }}
                  </button>
                  <button
                    @click="deleteLetter(letter.id)"
                    class="rounded-md p-1 text-muted-foreground hover:text-destructive transition"
                    :title="t('school.common.delete')"
                  >
                    <Trash2 class="size-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Registrasi Surat Baru -->
    <div
      v-if="showRegisterModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-xl rounded-xl border border-border/60 bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h2 class="text-base font-semibold text-foreground">
            {{ activeTab === 'incoming' ? t('school.letters.modalTitleIncoming') : t('school.letters.modalTitleOutgoing') }}
          </h2>
          <button @click="showRegisterModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="saveLetter" class="space-y-4 text-xs">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formType') }}</label>
              <select
                v-model="form.type"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="incoming">{{ t('school.letters.typeIncoming') }}</option>
                <option value="outgoing">{{ t('school.letters.typeOutgoing') }}</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formCategory') }}</label>
              <select
                v-model="form.category"
                @change="suggestLetterNumber"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="invitation">{{ t('school.letters.catInvitation') }}</option>
                <option value="assignment">{{ t('school.letters.catAssignment') }}</option>
                <option value="decree">{{ t('school.letters.catDecree') }}</option>
                <option value="certificate">{{ t('school.letters.catCertificate') }}</option>
                <option value="circular">{{ t('school.letters.catCircular') }}</option>
                <option value="general">{{ t('school.letters.catGeneral') }}</option>
              </select>
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="block font-medium text-foreground">{{ t('school.letters.formNumber') }}</label>
              <button
                type="button"
                @click="suggestLetterNumber"
                class="text-[11px] text-primary hover:underline"
              >
                {{ t('school.letters.btnAutoGenerate') }}
              </button>
            </div>
            <input
              v-model="form.letter_number"
              type="text"
              required
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs font-mono text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div v-if="form.type === 'incoming'">
            <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formRefNumber') }}</label>
            <input
              v-model="form.reference_number"
              type="text"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formSender') }}</label>
              <input
                v-model="form.sender"
                type="text"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formRecipient') }}</label>
              <input
                v-model="form.recipient"
                type="text"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formSubject') }}</label>
            <input
              v-model="form.subject"
              type="text"
              required
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formLetterDate') }}</label>
              <input
                v-model="form.letter_date"
                type="date"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formReceivedOrSentDate') }}</label>
              <input
                v-model="form.received_or_sent_date"
                type="date"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formNotes') }}</label>
            <textarea
              v-model="form.notes"
              rows="2"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showRegisterModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.common.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Disposisi Surat -->
    <div
      v-if="showDispositionModal && selectedLetter"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >
      <div class="w-full max-w-lg rounded-xl border border-border/60 bg-card p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <div>
            <h2 class="text-base font-semibold text-foreground">{{ t('school.letters.modalDispositionTitle') }}</h2>
            <div class="text-xs text-muted-foreground font-mono mt-0.5">{{ selectedLetter.letter_number }}</div>
          </div>
          <button @click="showDispositionModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <div class="rounded-lg bg-muted/20 p-3 border border-border/40 text-xs space-y-1">
          <div><span class="text-muted-foreground">{{ t('school.letters.sender') }}:</span> <strong class="text-foreground">{{ selectedLetter.sender }}</strong></div>
          <div><span class="text-muted-foreground">{{ t('school.letters.subject') }}:</span> <span class="text-foreground">{{ selectedLetter.subject }}</span></div>
        </div>

        <form @submit.prevent="saveDisposition" class="space-y-4 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formDispositionTo') }}</label>
            <input
              v-model="dispositionForm.disposition_to"
              type="text"
              required
              :placeholder="t('school.letters.placeholderDispositionTo')"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formDispositionInstruction') }}</label>
            <textarea
              v-model="dispositionForm.disposition_instruction"
              rows="3"
              required
              :placeholder="t('school.letters.placeholderDispositionInstruction')"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.letters.formDispositionStatus') }}</label>
            <select
              v-model="dispositionForm.disposition_status"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            >
              <option value="pending">{{ t('school.letters.dispPending') }}</option>
              <option value="processed">{{ t('school.letters.dispProcessed') }}</option>
              <option value="completed">{{ t('school.letters.dispCompleted') }}</option>
            </select>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showDispositionModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.letters.btnSaveDisposition') }}
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
  Inbox,
  Send,
  MailPlus,
  Clock,
  CheckCircle2,
  Search,
  FileText,
  Trash2,
  X,
} from 'lucide-vue-next';
import { SchoolService, type LetterData } from '../../services/schoolService';

const { t } = useI18n();

const activeTab = ref<'incoming' | 'outgoing'>('incoming');
const letters = ref<LetterData[]>([]);
const loading = ref(false);
const saving = ref(false);
const searchQuery = ref('');
const filterCategory = ref('');

const showRegisterModal = ref(false);
const showDispositionModal = ref(false);
const selectedLetter = ref<LetterData | null>(null);

const form = reactive({
  type: 'incoming' as 'incoming' | 'outgoing',
  letter_number: '',
  reference_number: '',
  category: 'invitation',
  sender: '',
  recipient: '',
  subject: '',
  letter_date: new Date().toISOString().split('T')[0],
  received_or_sent_date: new Date().toISOString().split('T')[0],
  notes: '',
});

const dispositionForm = reactive({
  disposition_to: '',
  disposition_instruction: '',
  disposition_status: 'pending',
});

const incomingCount = computed(() => letters.value.filter(l => l.type === 'incoming').length);
const outgoingCount = computed(() => letters.value.filter(l => l.type === 'outgoing').length);
const pendingDispositionCount = computed(() => letters.value.filter(l => l.type === 'incoming' && l.disposition_status === 'pending').length);
const processedDispositionCount = computed(() => letters.value.filter(l => l.type === 'incoming' && (l.disposition_status === 'processed' || l.disposition_status === 'completed')).length);

const loadLetters = async () => {
  loading.value = true;
  try {
    const res = await SchoolService.getLetters({
      type: activeTab.value,
      category: filterCategory.value || undefined,
      search: searchQuery.value || undefined,
    });
    letters.value = res.data.data.data || res.data.data || [];
  } catch (err) {
    console.error('Failed to load letters:', err);
  } finally {
    loading.value = false;
  }
};

const openRegisterModal = () => {
  form.type = activeTab.value;
  form.letter_number = '';
  form.reference_number = '';
  form.category = 'invitation';
  form.sender = activeTab.value === 'incoming' ? '' : 'Kepala Sekolah';
  form.recipient = activeTab.value === 'incoming' ? 'Kepala Sekolah' : '';
  form.subject = '';
  form.letter_date = new Date().toISOString().split('T')[0];
  form.received_or_sent_date = new Date().toISOString().split('T')[0];
  form.notes = '';
  suggestLetterNumber();
  showRegisterModal.value = true;
};

const suggestLetterNumber = async () => {
  try {
    const res = await SchoolService.generateLetterNumber({
      type: form.type,
      category: form.category,
    });
    form.letter_number = res.data.data.suggested_number;
  } catch (err) {
    console.error('Failed to generate suggested number:', err);
  }
};

const saveLetter = async () => {
  saving.value = true;
  try {
    await SchoolService.storeLetter({ ...form });
    showRegisterModal.value = false;
    await loadLetters();
  } catch (err) {
    console.error('Failed to save letter:', err);
  } finally {
    saving.value = false;
  }
};

const openDispositionModal = (letter: LetterData) => {
  selectedLetter.value = letter;
  dispositionForm.disposition_to = letter.disposition_to || '';
  dispositionForm.disposition_instruction = letter.disposition_instruction || '';
  dispositionForm.disposition_status = letter.disposition_status || 'pending';
  showDispositionModal.value = true;
};

const saveDisposition = async () => {
  if (!selectedLetter.value) return;
  saving.value = true;
  try {
    await SchoolService.updateLetterDisposition(selectedLetter.value.id, { ...dispositionForm });
    showDispositionModal.value = false;
    await loadLetters();
  } catch (err) {
    console.error('Failed to save disposition:', err);
  } finally {
    saving.value = false;
  }
};

const deleteLetter = async (id: number) => {
  if (!confirm(t('school.common.confirmDelete'))) return;
  try {
    await SchoolService.deleteLetter(id);
    await loadLetters();
  } catch (err) {
    console.error('Failed to delete letter:', err);
  }
};

onMounted(() => {
  loadLetters();
});
</script>
