<template>
  <div class="space-y-6">
    <!-- Header & Event Selector -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.osis.title') }}</h1>
          <span
            v-if="selectedEvent"
            :class="[
              'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold uppercase tracking-wider',
              selectedEvent.status === 'active' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-muted text-muted-foreground'
            ]"
          >
            <span class="size-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            {{ selectedEvent.type.toUpperCase() }}
          </span>
        </div>
        <p class="text-sm text-muted-foreground mt-0.5">{{ t('school.osis.subtitle') }}</p>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <!-- Event Switcher Dropdown -->
        <div class="flex items-center gap-2">
          <label class="text-xs font-semibold text-muted-foreground hidden sm:inline">{{ t('school.osis.selectEvent') }}:</label>
          <select
            v-model="selectedEventId"
            @change="handleEventChange"
            class="h-9 rounded-lg border border-border/60 bg-card px-3 text-xs font-medium text-foreground focus:ring-1 focus:ring-primary shadow-sm"
          >
            <option v-for="ev in votingEvents" :key="ev.id" :value="ev.id">
              [{{ ev.type.toUpperCase() }}] {{ ev.title }}
            </option>
          </select>
        </div>

        <!-- Action Buttons depending on Tab -->
        <button
          @click="showNewEventModal = true"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg border border-border/60 bg-card px-3 text-xs font-semibold text-foreground shadow-sm hover:bg-muted/40 transition"
        >
          <Plus class="size-3.5" />
          <span class="hidden sm:inline">{{ t('school.osis.btnNewEvent') }}</span>
        </button>

        <button
          v-if="currentTab === 'evoting' || currentTab === 'kiosk'"
          @click="openKioskTab"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          <Vote class="size-3.5" />
          {{ t('school.osis.btnCastVote') }}
        </button>

        <button
          v-if="currentTab === 'dpt'"
          @click="showGenerateModal = true"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          <Key class="size-3.5" />
          {{ t('school.osis.btnGenerateTokens') }}
        </button>

        <button
          v-if="currentTab === 'candidates'"
          @click="showAddCandidateModal = true"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          <UserPlus class="size-3.5" />
          {{ t('school.osis.btnAddCandidate') }}
        </button>

        <button
          v-if="currentTab === 'extracurriculars'"
          @click="showAddEkskulModal = true"
          class="inline-flex h-9 items-center gap-1.5 rounded-lg bg-primary px-3.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          <Plus class="size-3.5" />
          {{ t('school.osis.btnAddEkskul') }}
        </button>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex overflow-x-auto rounded-lg border border-border/60 bg-muted/30 p-1">
      <button
        @click="currentTab = 'evoting'"
        :class="[
          'rounded-md px-4 py-2 text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5',
          currentTab === 'evoting' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <BarChart3 class="size-3.5" />
        {{ t('school.osis.tabEvoting') }}
      </button>

      <button
        @click="currentTab = 'kiosk'"
        :class="[
          'rounded-md px-4 py-2 text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5',
          currentTab === 'kiosk' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <Vote class="size-3.5 text-primary" />
        {{ t('school.osis.tabKiosk') }}
      </button>

      <button
        @click="currentTab = 'dpt'"
        :class="[
          'rounded-md px-4 py-2 text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5',
          currentTab === 'dpt' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <ShieldCheck class="size-3.5" />
        {{ t('school.osis.tabDpt') }}
      </button>

      <button
        @click="currentTab = 'candidates'"
        :class="[
          'rounded-md px-4 py-2 text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5',
          currentTab === 'candidates' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <Users class="size-3.5" />
        {{ t('school.osis.tabCandidates') }}
      </button>

      <button
        @click="currentTab = 'extracurriculars'"
        :class="[
          'rounded-md px-4 py-2 text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5',
          currentTab === 'extracurriculars' ? 'bg-card text-foreground shadow-sm' : 'text-muted-foreground hover:text-foreground'
        ]"
      >
        <Activity class="size-3.5" />
        {{ t('school.osis.tabExtracurriculars') }}
      </button>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 1: E-VOTING & REALTIME QUICK COUNT                       -->
    <!-- ============================================================ -->
    <div v-if="currentTab === 'evoting'" class="space-y-6">
      <!-- Active Event Summary Banner -->
      <div v-if="selectedEvent" class="relative overflow-hidden rounded-xl border border-primary/30 bg-gradient-to-r from-primary/10 via-primary/5 to-transparent p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
          <div class="space-y-1.5">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-primary/20 text-primary uppercase tracking-wider">
                {{ getEventCategoryLabel(selectedEvent.type) }}
              </span>
              <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-medium bg-muted text-muted-foreground">
                <Lock class="size-3" />
                {{ getVotingModeLabel(selectedEvent.voting_mode) }}
              </span>
            </div>
            <h2 class="text-xl font-bold text-foreground">{{ selectedEvent.title }}</h2>
            <p class="text-xs text-muted-foreground max-w-2xl leading-relaxed">
              {{ selectedEvent.description || t('school.osis.voteIntegrityBadge') }}
            </p>
          </div>

          <!-- Quick Stat Counters -->
          <div class="grid grid-cols-3 gap-3 bg-card/90 backdrop-blur-md p-4 rounded-xl border border-border/60 shadow-sm min-w-[280px]">
            <div class="text-center">
              <div class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">{{ t('school.osis.totalVotesIn') }}</div>
              <div class="text-2xl font-black text-foreground font-mono mt-1">
                {{ quickCountData?.total_ballots ?? 0 }}
              </div>
            </div>
            <div class="text-center border-x border-border/60">
              <div class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">{{ t('school.osis.totalDpt') }}</div>
              <div class="text-2xl font-black text-foreground font-mono mt-1">
                {{ quickCountData?.total_dpt ?? 0 }}
              </div>
            </div>
            <div class="text-center">
              <div class="text-[10px] uppercase font-bold text-muted-foreground tracking-wider">{{ t('school.osis.turnout') }}</div>
              <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono mt-1">
                {{ quickCountData?.turnout_percentage ?? 0 }}%
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Count Visual Cards -->
      <div v-if="quickCountData && quickCountData.candidates.length > 0" class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div
          v-for="cand in quickCountData.candidates"
          :key="cand.id"
          class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-4 hover:border-primary/40 transition group"
        >
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
              <div class="size-14 rounded-2xl bg-gradient-to-br from-primary to-primary/80 text-primary-foreground font-black text-2xl flex items-center justify-center shadow-lg shadow-primary/20">
                0{{ cand.candidate_number }}
              </div>
              <div>
                <div class="font-bold text-base text-foreground group-hover:text-primary transition">
                  {{ cand.title }}
                </div>
                <div class="text-xs text-muted-foreground">
                  {{ cand.leader_name }}
                  <span v-if="cand.vice_name && cand.vice_name !== '-'"> & {{ cand.vice_name }}</span>
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-2xl font-black text-foreground font-mono">
                {{ cand.percentage }}%
              </div>
              <div class="text-[11px] text-muted-foreground font-mono">
                {{ cand.vote_count }} Suara
              </div>
            </div>
          </div>

          <!-- Dynamic Percentage Progress Bar -->
          <div class="space-y-1">
            <div class="h-3.5 w-full rounded-full bg-muted/40 overflow-hidden p-0.5">
              <div
                class="h-full rounded-full bg-gradient-to-r from-primary to-primary/70 transition-all duration-1000 shadow-sm"
                :style="{ width: `${cand.percentage || 0}%` }"
              ></div>
            </div>
          </div>

          <!-- Vision & Mission Preview -->
          <div v-if="cand.vision" class="bg-muted/20 p-3.5 rounded-lg border border-border/40 text-xs space-y-1.5">
            <div>
              <span class="font-bold text-foreground">Visi:</span>
              <span class="text-muted-foreground ml-1.5">{{ cand.vision }}</span>
            </div>
            <div v-if="cand.mission">
              <span class="font-bold text-foreground">Misi:</span>
              <span class="text-muted-foreground ml-1.5 line-clamp-2 whitespace-pre-line">{{ cand.mission }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="rounded-xl border border-dashed border-border/60 bg-card p-12 text-center text-muted-foreground">
        <Users class="mx-auto size-10 text-muted-foreground/50 mb-3" />
        <p class="text-sm font-medium">{{ t('school.osis.empty') }}</p>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 2: BILIK SUARA KIOSK & TPS DIGITAL (ALA JA-EVOTING)       -->
    <!-- ============================================================ -->
    <div v-if="currentTab === 'kiosk'" class="space-y-6">
      <div class="max-w-2xl mx-auto rounded-2xl border border-border/60 bg-card p-8 shadow-xl text-center space-y-6">
        <div class="size-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center mx-auto shadow-inner">
          <Vote class="size-8" />
        </div>

        <div>
          <h2 class="text-xl font-bold text-foreground">{{ t('school.osis.modalVoteTitle') }}</h2>
          <p class="text-xs text-muted-foreground mt-1 max-w-md mx-auto">
            {{ t('school.osis.enterTokenPrompt') }}
          </p>
        </div>

        <!-- Token Input Form -->
        <div class="max-w-xs mx-auto space-y-3">
          <input
            v-model="kioskToken"
            type="text"
            maxlength="8"
            :placeholder="t('school.osis.tokenPlaceholder')"
            class="w-full text-center text-2xl font-mono font-black tracking-widest uppercase rounded-xl border border-border/60 bg-background px-4 py-3 text-foreground focus:ring-2 focus:ring-primary shadow-sm"
          />
          <button
            @click="verifyKioskToken"
            :disabled="kioskToken.trim().length !== 8"
            class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-primary-foreground shadow-md hover:bg-primary/90 transition disabled:opacity-50"
          >
            {{ t('school.osis.verifyToken') }}
          </button>
        </div>

        <!-- Voter Security & Integrity Guarantee Notice -->
        <div class="rounded-xl bg-muted/20 p-4 border border-border/40 text-xs text-muted-foreground flex items-center justify-center gap-2">
          <ShieldCheck class="size-4 text-emerald-500 shrink-0" />
          <span>{{ t('school.osis.voteIntegrityBadge') }}</span>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 3: DAFTAR PEMILIH TETAP (DPT) & TOKEN BILIK SUARA         -->
    <!-- ============================================================ -->
    <div v-if="currentTab === 'dpt'" class="space-y-4">
      <!-- Filter Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-card p-4 rounded-xl border border-border/60">
        <div class="flex items-center gap-2 flex-1 max-w-md">
          <Search class="size-4 text-muted-foreground shrink-0" />
          <input
            v-model="dptSearch"
            type="text"
            :placeholder="t('school.osis.searchVoters')"
            class="w-full rounded-lg border-0 bg-transparent text-xs text-foreground placeholder:text-muted-foreground focus:ring-0"
            @input="loadVoters"
          />
        </div>

        <div class="flex items-center gap-2">
          <select
            v-model="dptFilterStatus"
            @change="loadVoters"
            class="h-8 rounded-lg border border-border/60 bg-background px-2.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">Semua Status DPT</option>
            <option value="true">{{ t('school.osis.statusVoted') }}</option>
            <option value="false">{{ t('school.osis.statusNotVoted') }}</option>
          </select>
        </div>
      </div>

      <!-- DPT Table -->
      <div class="overflow-hidden rounded-xl border border-border/60 bg-card shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="border-b border-border/60 bg-muted/30 text-muted-foreground font-semibold">
              <tr>
                <th class="p-3.5">#</th>
                <th class="p-3.5">{{ t('school.osis.colVoterName') }}</th>
                <th class="p-3.5">{{ t('school.osis.colIdentifier') }}</th>
                <th class="p-3.5">{{ t('school.osis.colToken') }}</th>
                <th class="p-3.5">{{ t('school.osis.colStatus') }}</th>
                <th class="p-3.5">Waktu Memilih</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border/40 text-foreground">
              <tr v-for="(voter, idx) in votersList" :key="voter.id" class="hover:bg-muted/20 transition">
                <td class="p-3.5 font-mono text-muted-foreground">{{ idx + 1 }}</td>
                <td class="p-3.5 font-medium">
                  {{ voter.voter_name }}
                  <span class="text-[10px] text-muted-foreground ml-1 font-normal">({{ voter.voter_type }})</span>
                </td>
                <td class="p-3.5 font-mono text-muted-foreground">{{ voter.voter_identifier || '-' }}</td>
                <td class="p-3.5 font-mono">
                  <div class="inline-flex items-center gap-1.5 bg-muted/40 px-2.5 py-1 rounded-md border border-border/60 font-bold tracking-wider">
                    <span>{{ voter.token }}</span>
                    <button
                      @click="copyToken(voter.token)"
                      class="text-muted-foreground hover:text-foreground transition"
                      title="Salin Token"
                    >
                      <Copy class="size-3" />
                    </button>
                  </div>
                </td>
                <td class="p-3.5">
                  <span
                    :class="[
                      'inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold',
                      voter.has_voted ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600'
                    ]"
                  >
                    {{ voter.has_voted ? t('school.osis.statusVoted') : t('school.osis.statusNotVoted') }}
                  </span>
                </td>
                <td class="p-3.5 text-muted-foreground font-mono text-[11px]">
                  {{ voter.voted_at ? new Date(voter.voted_at).toLocaleString() : '-' }}
                </td>
              </tr>
              <tr v-if="votersList.length === 0">
                <td colspan="6" class="p-8 text-center text-muted-foreground">
                  Belum ada pemilih terdaftar dalam DPT acara ini.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 4: PASANGAN CALON / KANDIDAT                             -->
    <!-- ============================================================ -->
    <div v-if="currentTab === 'candidates'" class="space-y-6">
      <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div
          v-for="cand in candidates"
          :key="cand.id"
          class="rounded-xl border border-border/60 bg-card p-6 shadow-sm space-y-4"
        >
          <div class="flex items-center gap-4">
            <div class="size-14 rounded-xl bg-primary/10 text-primary font-black text-2xl flex items-center justify-center border border-primary/20 shadow-inner">
              #{{ cand.candidate_number }}
            </div>
            <div>
              <div class="text-xs font-bold uppercase tracking-wider text-primary">Kandidat 0{{ cand.candidate_number }}</div>
              <div class="font-bold text-base text-foreground mt-0.5">
                {{ cand.title }}
              </div>
            </div>
          </div>

          <div class="space-y-3 text-xs">
            <div>
              <div class="font-semibold text-foreground mb-1">{{ t('school.osis.vision') }}</div>
              <div class="text-muted-foreground bg-muted/20 p-3 rounded-lg border border-border/40">
                {{ cand.vision || '-' }}
              </div>
            </div>

            <div>
              <div class="font-semibold text-foreground mb-1">{{ t('school.osis.mission') }}</div>
              <div class="text-muted-foreground bg-muted/20 p-3 rounded-lg border border-border/40 whitespace-pre-line">
                {{ cand.mission || '-' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- TAB 5: EKSTRAKURIKULER                                       -->
    <!-- ============================================================ -->
    <div v-if="currentTab === 'extracurriculars'" class="space-y-4">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="ekskul in extracurriculars"
          :key="ekskul.id"
          class="rounded-xl border border-border/60 bg-card p-5 shadow-sm space-y-3 hover:border-primary/40 transition"
        >
          <div class="flex items-start justify-between">
            <div>
              <span
                :class="[
                  'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold mb-1',
                  ekskul.category === 'wajib' ? 'bg-amber-500/10 text-amber-600' : 'bg-blue-500/10 text-blue-600'
                ]"
              >
                {{ ekskul.category.toUpperCase() }}
              </span>
              <h3 class="font-bold text-sm text-foreground">{{ ekskul.name }}</h3>
            </div>
            <span
              :class="[
                'size-2 rounded-full mt-1',
                ekskul.is_active ? 'bg-emerald-500' : 'bg-muted-foreground'
              ]"
            ></span>
          </div>

          <p v-if="ekskul.description" class="text-xs text-muted-foreground line-clamp-2">
            {{ ekskul.description }}
          </p>

          <div class="space-y-1.5 border-t border-border/40 pt-2.5 text-xs">
            <div class="flex items-center gap-1.5 text-muted-foreground">
              <UserCheck class="size-3.5 text-primary" />
              <span>Pembina: <strong class="text-foreground">{{ ekskul.advisor?.full_name || '-' }}</strong></span>
            </div>
            <div class="flex items-center gap-1.5 text-muted-foreground">
              <Calendar class="size-3.5 text-primary" />
              <span>Jadwal: {{ ekskul.schedule_day || '-' }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-muted-foreground">
              <MapPin class="size-3.5 text-primary" />
              <span>Lokasi: {{ ekskul.location || '-' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL: BILIK SUARA COBLOS KANDIDAT                           -->
    <!-- ============================================================ -->
    <div
      v-if="showVoteModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-xl rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-border/60 pb-4">
          <div>
            <h2 class="text-lg font-bold text-foreground">{{ t('school.osis.modalVoteTitle') }}</h2>
            <div class="text-xs text-muted-foreground">{{ t('school.osis.modalVoteSubtitle') }}</div>
          </div>
          <button @click="showVoteModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-5" />
          </button>
        </div>

        <!-- Token verified info -->
        <div class="bg-primary/5 border border-primary/20 rounded-xl p-3 flex items-center justify-between text-xs">
          <span class="text-muted-foreground">Token Pemilih Aktif:</span>
          <span class="font-mono font-bold text-primary tracking-widest text-sm">{{ kioskToken }}</span>
        </div>

        <!-- Candidate Cards to Vote -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div
            v-for="cand in candidates"
            :key="cand.id"
            @click="selectedCandidateId = cand.id"
            :class="[
              'cursor-pointer rounded-xl border p-4 text-center transition space-y-2',
              selectedCandidateId === cand.id ? 'border-primary bg-primary/10 shadow-md ring-2 ring-primary/40' : 'border-border/60 bg-card hover:border-border'
            ]"
          >
            <div class="size-12 rounded-xl bg-primary text-primary-foreground font-black text-xl flex items-center justify-center mx-auto shadow-sm">
              0{{ cand.candidate_number }}
            </div>
            <div class="font-bold text-sm text-foreground">
              {{ cand.title }}
            </div>
            <div class="text-xs text-muted-foreground line-clamp-2">
              {{ cand.vision || '-' }}
            </div>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-4">
          <button
            type="button"
            @click="showVoteModal = false"
            class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
          >
            {{ t('school.common.cancel') }}
          </button>
          <button
            type="button"
            @click="submitVote"
            :disabled="saving || !selectedCandidateId"
            class="rounded-lg bg-primary px-5 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
          >
            {{ saving ? t('school.common.saving') : t('school.osis.btnConfirmVote') }}
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL: BUKTI TANDA TERIMA SUARA DIGITAL                      -->
    <!-- ============================================================ -->
    <div
      v-if="showReceiptModal && voteReceipt"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-md rounded-2xl border border-border/60 bg-card p-6 shadow-2xl text-center space-y-5">
        <div class="size-16 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center mx-auto">
          <CheckCircle2 class="size-8" />
        </div>

        <div class="space-y-1">
          <h2 class="text-lg font-bold text-foreground">{{ t('school.osis.voteSuccessMessage') }}</h2>
          <p class="text-xs text-muted-foreground">{{ t('school.osis.receiptNote') }}</p>
        </div>

        <div class="rounded-xl border border-border/60 bg-muted/20 p-4 space-y-2">
          <div class="text-[11px] uppercase tracking-wider font-semibold text-muted-foreground">{{ t('school.osis.receiptCode') }}</div>
          <div class="text-2xl font-black font-mono tracking-widest text-primary">
            {{ voteReceipt.receipt_code }}
          </div>
          <div class="text-xs text-muted-foreground pt-1 border-t border-border/40">
            Kandidat: <strong class="text-foreground">{{ voteReceipt.candidate_name }}</strong>
          </div>
        </div>

        <button
          @click="showReceiptModal = false; currentTab = 'evoting'"
          class="w-full rounded-xl bg-primary px-4 py-2.5 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition"
        >
          Lihat Realtime Quick Count
        </button>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL: BATCH GENERATE TOKEN DPT                              -->
    <!-- ============================================================ -->
    <div
      v-if="showGenerateModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-md rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h2 class="text-base font-bold text-foreground">{{ t('school.osis.modalGenerateTitle') }}</h2>
          <button @click="showGenerateModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <p class="text-xs text-muted-foreground">{{ t('school.osis.modalGenerateDesc') }}</p>

        <div class="space-y-3 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">Target Sumber Pemilih:</label>
            <select
              v-model="generateForm.source"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            >
              <option value="all_students">{{ t('school.osis.sourceAllStudents') }}</option>
              <option value="staff">{{ t('school.osis.sourceStaff') }}</option>
              <option value="all_school">{{ t('school.osis.sourceAllSchool') }}</option>
            </select>
          </div>
        </div>

        <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
          <button
            type="button"
            @click="showGenerateModal = false"
            class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
          >
            {{ t('school.common.cancel') }}
          </button>
          <button
            type="button"
            @click="handleGenerateTokens"
            :disabled="saving"
            class="rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 transition disabled:opacity-50"
          >
            {{ saving ? t('school.common.saving') : t('school.osis.btnGenerateTokens') }}
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL: BUAT AGENDA PEMILIHAN / POLLING BARU                  -->
    <!-- ============================================================ -->
    <div
      v-if="showNewEventModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-lg rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h2 class="text-base font-bold text-foreground">{{ t('school.osis.modalNewEventTitle') }}</h2>
          <button @click="showNewEventModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="createEvent" class="space-y-3 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.osis.formEventTitle') }}</label>
            <input
              v-model="newEventForm.title"
              type="text"
              required
              placeholder="Contoh: Pemilihan Formatur MPK 2026/2027"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.osis.formEventType') }}</label>
              <select
                v-model="newEventForm.type"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="osis">{{ t('school.osis.typeOsis') }}</option>
                <option value="mpk">{{ t('school.osis.typeMpk') }}</option>
                <option value="guru_favorit">{{ t('school.osis.typeGuruFavorit') }}</option>
                <option value="ekskul">{{ t('school.osis.typeEkskul') }}</option>
                <option value="polling">{{ t('school.osis.typePolling') }}</option>
                <option value="komite">{{ t('school.osis.typeKomite') }}</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.osis.formVotingMode') }}</label>
              <select
                v-model="newEventForm.voting_mode"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="hybrid">{{ t('school.osis.modeHybrid') }}</option>
                <option value="token_kiosk">{{ t('school.osis.modeTokenKiosk') }}</option>
                <option value="online_login">{{ t('school.osis.modeOnlineLogin') }}</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.osis.formStartDate') }}</label>
              <input
                v-model="newEventForm.start_date"
                type="datetime-local"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.osis.formEndDate') }}</label>
              <input
                v-model="newEventForm.end_date"
                type="datetime-local"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showNewEventModal = false"
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

    <!-- ============================================================ -->
    <!-- MODAL: TAMBAH KANDIDAT / PASLON BARU                        -->
    <!-- ============================================================ -->
    <div
      v-if="showAddCandidateModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
    >
      <div class="w-full max-w-lg rounded-2xl border border-border/60 bg-card p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h2 class="text-base font-bold text-foreground">{{ t('school.osis.btnAddCandidate') }}</h2>
          <button @click="showAddCandidateModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="createCandidate" class="space-y-3 text-xs">
          <div class="grid grid-cols-3 gap-3">
            <div>
              <label class="block font-medium text-foreground mb-1">Nomor Urut:</label>
              <input
                v-model.number="newCandidateForm.candidate_number"
                type="number"
                min="1"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div class="col-span-2">
              <label class="block font-medium text-foreground mb-1">Nama Paslon / Opsi:</label>
              <input
                v-model="newCandidateForm.title"
                type="text"
                required
                placeholder="Contoh: PASLON 03 - Nama & Nama"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.osis.vision') }}</label>
            <textarea
              v-model="newCandidateForm.vision"
              rows="2"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.osis.mission') }}</label>
            <textarea
              v-model="newCandidateForm.mission"
              rows="3"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showAddCandidateModal = false"
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
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import {
  BarChart3,
  Users,
  Activity,
  Vote,
  UserPlus,
  Plus,
  Calendar,
  MapPin,
  UserCheck,
  X,
  Key,
  ShieldCheck,
  Lock,
  Search,
  Copy,
  CheckCircle2,
} from 'lucide-vue-next';
import {
  SchoolService,
  type VoteEventData,
  type VoteCandidateData,
  type VoteVoterData,
  type VotingQuickCountData,
  type ExtracurricularData,
} from '../services/schoolService';

const { t } = useI18n();

const currentTab = ref<'evoting' | 'kiosk' | 'dpt' | 'candidates' | 'extracurriculars'>('evoting');

// Events
const votingEvents = ref<VoteEventData[]>([]);
const selectedEventId = ref<number>(0);
const selectedEvent = ref<VoteEventData | null>(null);

// Active Event Data
const candidates = ref<VoteCandidateData[]>([]);
const quickCountData = ref<VotingQuickCountData | null>(null);
const votersList = ref<VoteVoterData[]>([]);
const extracurriculars = ref<ExtracurricularData[]>([]);

// Modals
const showVoteModal = ref(false);
const showReceiptModal = ref(false);
const showGenerateModal = ref(false);
const showNewEventModal = ref(false);
const showAddCandidateModal = ref(false);
const showAddEkskulModal = ref(false);
const saving = ref(false);

// Voting State
const kioskToken = ref('');
const selectedCandidateId = ref<number | null>(null);
const voteReceipt = ref<{ receipt_code: string; candidate_name: string; voted_at: string } | null>(null);

// Filters
const dptSearch = ref('');
const dptFilterStatus = ref('');

// Forms
const generateForm = reactive({
  source: 'all_students',
});

const newEventForm = reactive({
  title: '',
  type: 'osis',
  voting_mode: 'hybrid',
  start_date: new Date().toISOString().slice(0, 16),
  end_date: new Date(Date.now() + 3 * 86400000).toISOString().slice(0, 16),
});

const newCandidateForm = reactive({
  candidate_number: 1,
  title: '',
  vision: '',
  mission: '',
});

const getEventCategoryLabel = (type: string) => {
  switch (type) {
    case 'osis': return t('school.osis.typeOsis');
    case 'mpk': return t('school.osis.typeMpk');
    case 'guru_favorit': return t('school.osis.typeGuruFavorit');
    case 'ekskul': return t('school.osis.typeEkskul');
    case 'polling': return t('school.osis.typePolling');
    case 'komite': return t('school.osis.typeKomite');
    default: return type.toUpperCase();
  }
};

const getVotingModeLabel = (mode: string) => {
  switch (mode) {
    case 'hybrid': return t('school.osis.modeHybrid');
    case 'token_kiosk': return t('school.osis.modeTokenKiosk');
    case 'online_login': return t('school.osis.modeOnlineLogin');
    default: return mode;
  }
};

const loadVotingEvents = async () => {
  try {
    const res = await SchoolService.getVotingEvents();
    votingEvents.value = res.data.data || [];
    if (votingEvents.value.length > 0 && !selectedEventId.value) {
      const first = votingEvents.value[0];
      if (first) {
        selectedEventId.value = first.id;
        await selectEvent(first.id);
      }
    }
  } catch (err) {
    console.error('Failed to load voting events:', err);
  }
};

const selectEvent = async (eventId: number) => {
  try {
    const res = await SchoolService.showVotingEvent(eventId);
    selectedEvent.value = res.data.data;
    candidates.value = selectedEvent.value?.candidates || [];
    newCandidateForm.candidate_number = (candidates.value.length + 1);
    await Promise.all([
      loadQuickCount(eventId),
      loadVoters(),
    ]);
  } catch (err) {
    console.error('Failed to select voting event:', err);
  }
};

const handleEventChange = async () => {
  if (selectedEventId.value) {
    await selectEvent(selectedEventId.value);
  }
};

const loadQuickCount = async (eventId: number) => {
  try {
    const res = await SchoolService.getVotingQuickCount(eventId);
    quickCountData.value = res.data.data;
  } catch (err) {
    console.error('Failed to load quick count:', err);
  }
};

const loadVoters = async () => {
  if (!selectedEventId.value) return;
  try {
    const params: Record<string, unknown> = {};
    if (dptSearch.value) params.search = dptSearch.value;
    if (dptFilterStatus.value !== '') params.has_voted = dptFilterStatus.value;
    const res = await SchoolService.getVotingVoters(selectedEventId.value, params);
    votersList.value = res.data.data.data || res.data.data || [];
  } catch (err) {
    console.error('Failed to load voters:', err);
  }
};

const loadExtracurriculars = async () => {
  try {
    const res = await SchoolService.getExtracurriculars();
    extracurriculars.value = res.data.data || [];
  } catch (err) {
    console.error('Failed to load extracurriculars:', err);
  }
};

const copyToken = (token: string) => {
  navigator.clipboard.writeText(token);
  alert(t('school.osis.copied'));
};

const openKioskTab = () => {
  currentTab.value = 'kiosk';
};

const verifyKioskToken = () => {
  const token = kioskToken.value.trim().toUpperCase();
  if (token.length !== 8) return;
  kioskToken.value = token;
  const firstCand = candidates.value[0];
  if (firstCand) selectedCandidateId.value = firstCand.id;
  showVoteModal.value = true;
};

const submitVote = async () => {
  if (!selectedEventId.value || !selectedCandidateId.value || !kioskToken.value) return;
  saving.value = true;
  try {
    const res = await SchoolService.castVoteEngine(selectedEventId.value, {
      candidate_id: selectedCandidateId.value,
      token: kioskToken.value,
      mode: 'kiosk',
    });

    voteReceipt.value = res.data.data;
    showVoteModal.value = false;
    showReceiptModal.value = true;
    kioskToken.value = '';

    await Promise.all([
      loadQuickCount(selectedEventId.value),
      loadVoters(),
    ]);
  } catch (err: any) {
    alert(err?.response?.data?.message || t('school.osis.invalidToken'));
  } finally {
    saving.value = false;
  }
};

const handleGenerateTokens = async () => {
  if (!selectedEventId.value) return;
  saving.value = true;
  try {
    await SchoolService.generateVotingVoters(selectedEventId.value, {
      source: generateForm.source,
    });
    alert(t('school.osis.tokenGeneratedSuccess'));
    showGenerateModal.value = false;
    await Promise.all([
      loadVoters(),
      loadQuickCount(selectedEventId.value),
    ]);
  } catch (err: any) {
    alert(err?.response?.data?.message || 'Gagal membuat token DPT');
  } finally {
    saving.value = false;
  }
};

const createEvent = async () => {
  saving.value = true;
  try {
    const res = await SchoolService.storeVotingEvent({
      ...newEventForm,
      start_date: new Date(newEventForm.start_date).toISOString(),
      end_date: new Date(newEventForm.end_date).toISOString(),
    });
    showNewEventModal.value = false;
    await loadVotingEvents();
    if (res.data.data?.id) {
      selectedEventId.value = res.data.data.id;
      await selectEvent(res.data.data.id);
    }
  } catch (err: any) {
    alert(err?.response?.data?.message || 'Gagal membuat agenda pemilihan');
  } finally {
    saving.value = false;
  }
};

const createCandidate = async () => {
  if (!selectedEventId.value) return;
  saving.value = true;
  try {
    await SchoolService.addVotingCandidate(selectedEventId.value, {
      ...newCandidateForm,
    });
    showAddCandidateModal.value = false;
    await selectEvent(selectedEventId.value);
  } catch (err: any) {
    alert(err?.response?.data?.message || 'Gagal menambahkan kandidat');
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadVotingEvents();
  loadExtracurriculars();
});
</script>
