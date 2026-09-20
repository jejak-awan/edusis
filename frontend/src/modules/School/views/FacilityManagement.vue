<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground">{{ t('school.facilities.title') }}</h1>
        <p class="text-sm text-muted-foreground">{{ t('school.facilities.subtitle') }}</p>
      </div>

      <div class="flex items-center gap-2">
        <button
          v-if="activeTab === 'rooms'"
          @click="openRoomModal()"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.facilities.btnAddRoom') }}
        </button>

        <button
          v-if="activeTab === 'assets'"
          @click="openAssetModal()"
          class="inline-flex items-center gap-1.5 rounded-lg bg-primary px-3.5 py-2 text-xs font-semibold text-primary-foreground shadow-sm transition hover:bg-primary/90"
        >
          <Plus class="size-4" />
          {{ t('school.facilities.btnAddAsset') }}
        </button>

        <button
          v-if="activeTab === 'tickets'"
          @click="openTicketModal()"
          class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700"
        >
          <AlertTriangle class="size-4" />
          {{ t('school.facilities.btnReportIssue') }}
        </button>
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.facilities.totalRooms') }}</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ (rooms || []).length }} Ruang</div>
        </div>
        <div class="size-10 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
          <Building2 class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.facilities.totalAssets') }}</div>
          <div class="text-2xl font-bold text-foreground mt-1">{{ totalAssetsCount }} Unit</div>
        </div>
        <div class="size-10 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
          <Box class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.facilities.activeTickets') }}</div>
          <div class="text-2xl font-bold text-amber-600 mt-1">{{ pendingTicketsCount }} Tiket</div>
        </div>
        <div class="size-10 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
          <Wrench class="size-5" />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card p-4 shadow-sm flex items-center justify-between">
        <div>
          <div class="text-xs text-muted-foreground font-medium">{{ t('school.facilities.resolvedTickets') }}</div>
          <div class="text-2xl font-bold text-primary mt-1">{{ resolvedTicketsCount }} Selesai</div>
        </div>
        <div class="size-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
          <CheckCircle2 class="size-5" />
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-border/60">
      <nav class="flex space-x-6">
        <button
          @click="activeTab = 'rooms'"
          :class="[
            activeTab === 'rooms'
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-muted-foreground hover:text-foreground',
            'border-b-2 py-3 text-xs transition flex items-center gap-1.5'
          ]"
        >
          <Building2 class="size-4" />
          {{ t('school.facilities.tabRooms') }} ({{ rooms.length }})
        </button>

        <button
          @click="activeTab = 'assets'"
          :class="[
            activeTab === 'assets'
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-muted-foreground hover:text-foreground',
            'border-b-2 py-3 text-xs transition flex items-center gap-1.5'
          ]"
        >
          <Box class="size-4" />
          {{ t('school.facilities.tabAssets') }} ({{ assets.length }})
        </button>

        <button
          @click="activeTab = 'tickets'"
          :class="[
            activeTab === 'tickets'
              ? 'border-primary text-primary font-semibold'
              : 'border-transparent text-muted-foreground hover:text-foreground',
            'border-b-2 py-3 text-xs transition flex items-center gap-1.5'
          ]"
        >
          <Wrench class="size-4" />
          {{ t('school.facilities.tabTickets') }} ({{ tickets.length }})
        </button>
      </nav>
    </div>

    <!-- TAB 1: ROOMS -->
    <div v-if="activeTab === 'rooms'" class="space-y-4">
      <div class="flex items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm">
          <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
          <input
            v-model="roomSearch"
            type="text"
            :placeholder="t('school.facilities.searchRooms')"
            class="w-full rounded-lg border border-border/60 bg-background pl-9 pr-3 py-1.5 text-xs text-foreground placeholder:text-muted-foreground focus:ring-1 focus:ring-primary"
          />
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border/60 bg-muted/40 text-muted-foreground font-medium">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">{{ t('school.facilities.roomCode') }}</th>
              <th class="p-3">{{ t('school.facilities.roomName') }}</th>
              <th class="p-3">{{ t('school.facilities.roomType') }}</th>
              <th class="p-3">{{ t('school.facilities.capacityFloor') }}</th>
              <th class="p-3">{{ t('school.facilities.condition') }}</th>
              <th class="p-3">{{ t('school.facilities.assetsCount') }}</th>
              <th class="p-3 text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-if="loadingRooms">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                <Loader2 class="size-5 animate-spin mx-auto mb-1 text-primary" />
                {{ t('school.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="filteredRooms.length === 0">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                {{ t('school.common.noData') }}
              </td>
            </tr>
            <tr v-for="(room, index) in filteredRooms" :key="room.id" class="hover:bg-muted/20">
              <td class="p-3 text-muted-foreground">{{ Number(index) + 1 }}</td>
              <td class="p-3 font-mono font-bold text-primary">{{ room.code }}</td>
              <td class="p-3">
                <div class="font-semibold text-foreground">{{ room.name }}</div>
                <div class="text-[11px] text-muted-foreground">{{ room.description || '-' }}</div>
              </td>
              <td class="p-3">
                <span class="inline-flex items-center rounded-md bg-blue-500/10 px-2 py-0.5 text-[11px] font-medium text-blue-600">
                  {{ formatRoomType(room.type) }}
                </span>
              </td>
              <td class="p-3 text-foreground">
                {{ room.capacity }} Kursi • Lt. {{ room.floor }}
              </td>
              <td class="p-3">
                <span :class="getConditionBadge(room.condition)">
                  {{ formatCondition(room.condition) }}
                </span>
              </td>
              <td class="p-3 text-foreground font-medium">
                {{ room.assets_count || 0 }} Aset
              </td>
              <td class="p-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button
                    @click="openRoomModal(room)"
                    class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition"
                    title="Edit Ruangan"
                  >
                    <Edit class="size-3.5" />
                  </button>
                  <button
                    @click="deleteRoom(room.id)"
                    class="rounded p-1 text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition"
                    title="Hapus"
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

    <!-- TAB 2: ASSETS -->
    <div v-if="activeTab === 'assets'" class="space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm w-full">
          <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
          <input
            v-model="assetSearch"
            type="text"
            :placeholder="t('school.facilities.searchAssets')"
            class="w-full rounded-lg border border-border/60 bg-background pl-9 pr-3 py-1.5 text-xs text-foreground placeholder:text-muted-foreground focus:ring-1 focus:ring-primary"
          />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="selectedCategory"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.facilities.allCategories') }}</option>
            <option value="elektronik">Elektronik</option>
            <option value="mebeler">Mebeler</option>
            <option value="alat_praktik">Alat Praktik</option>
            <option value="kendaraan">Kendaraan</option>
            <option value="buku">Buku</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border/60 bg-muted/40 text-muted-foreground font-medium">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">{{ t('school.facilities.assetCode') }}</th>
              <th class="p-3">{{ t('school.facilities.assetName') }}</th>
              <th class="p-3">{{ t('school.facilities.roomLocation') }}</th>
              <th class="p-3">{{ t('school.facilities.category') }}</th>
              <th class="p-3">{{ t('school.facilities.quantity') }}</th>
              <th class="p-3">{{ t('school.facilities.condition') }}</th>
              <th class="p-3 text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-if="loadingAssets">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                <Loader2 class="size-5 animate-spin mx-auto mb-1 text-primary" />
                {{ t('school.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="filteredAssets.length === 0">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                {{ t('school.common.noData') }}
              </td>
            </tr>
            <tr v-for="(asset, index) in filteredAssets" :key="asset.id" class="hover:bg-muted/20">
              <td class="p-3 text-muted-foreground">{{ Number(index) + 1 }}</td>
              <td class="p-3 font-mono font-bold text-primary">{{ asset.code }}</td>
              <td class="p-3">
                <div class="font-semibold text-foreground">{{ asset.name }}</div>
                <div class="text-[11px] text-muted-foreground">
                  {{ asset.brand_model || '-' }} • Tahun {{ asset.acquisition_year || '-' }} • {{ asset.source_of_fund || 'BOS' }}
                </div>
              </td>
              <td class="p-3 text-foreground font-medium">
                {{ asset.room?.name || 'Gudang / Tanpa Ruang' }}
              </td>
              <td class="p-3">
                <span class="inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium text-foreground">
                  {{ formatAssetCategory(asset.category) }}
                </span>
              </td>
              <td class="p-3 text-foreground font-bold">{{ asset.quantity }} Unit</td>
              <td class="p-3">
                <span :class="getConditionBadge(asset.condition)">
                  {{ formatCondition(asset.condition) }}
                </span>
              </td>
              <td class="p-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button
                    @click="openAssetModal(asset)"
                    class="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition"
                    title="Edit Aset"
                  >
                    <Edit class="size-3.5" />
                  </button>
                  <button
                    @click="deleteAsset(asset.id)"
                    class="rounded p-1 text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition"
                    title="Hapus"
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

    <!-- TAB 3: MAINTENANCE TICKETS -->
    <div v-if="activeTab === 'tickets'" class="space-y-4">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative flex-1 max-w-sm w-full">
          <Search class="absolute left-3 top-2.5 size-4 text-muted-foreground" />
          <input
            v-model="ticketSearch"
            type="text"
            :placeholder="t('school.facilities.searchTickets')"
            class="w-full rounded-lg border border-border/60 bg-background pl-9 pr-3 py-1.5 text-xs text-foreground placeholder:text-muted-foreground focus:ring-1 focus:ring-primary"
          />
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="selectedTicketStatus"
            class="rounded-lg border border-border/60 bg-background px-3 py-1.5 text-xs text-foreground focus:ring-1 focus:ring-primary"
          >
            <option value="">{{ t('school.facilities.allStatuses') }}</option>
            <option value="open">Open (Baru)</option>
            <option value="in_progress">In Progress</option>
            <option value="resolved">Resolved (Selesai)</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>

      <div class="rounded-xl border border-border/60 bg-card overflow-hidden shadow-sm">
        <table class="w-full text-left text-xs">
          <thead class="border-b border-border/60 bg-muted/40 text-muted-foreground font-medium">
            <tr>
              <th class="p-3">#</th>
              <th class="p-3">{{ t('school.facilities.ticketNumber') }}</th>
              <th class="p-3">{{ t('school.facilities.issue') }}</th>
              <th class="p-3">{{ t('school.facilities.locationAsset') }}</th>
              <th class="p-3">{{ t('school.facilities.priority') }}</th>
              <th class="p-3">{{ t('school.facilities.status') }}</th>
              <th class="p-3">{{ t('school.facilities.cost') }}</th>
              <th class="p-3 text-right">{{ t('school.common.actions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border/60">
            <tr v-if="loadingTickets">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                <Loader2 class="size-5 animate-spin mx-auto mb-1 text-primary" />
                {{ t('school.common.loading') }}
              </td>
            </tr>
            <tr v-else-if="filteredTickets.length === 0">
              <td colspan="8" class="p-6 text-center text-muted-foreground">
                {{ t('school.common.noData') }}
              </td>
            </tr>
            <tr v-for="(ticket, index) in filteredTickets" :key="ticket.id" class="hover:bg-muted/20">
              <td class="p-3 text-muted-foreground">{{ Number(index) + 1 }}</td>
              <td class="p-3 font-mono font-bold text-primary">{{ ticket.ticket_number }}</td>
              <td class="p-3 max-w-xs">
                <div class="font-semibold text-foreground">{{ ticket.issue_description }}</div>
                <div v-if="ticket.resolution_notes" class="text-[11px] text-emerald-600 mt-0.5">
                  Solusi: {{ ticket.resolution_notes }}
                </div>
                <div class="text-[10px] text-muted-foreground mt-0.5">
                  Pelapor: {{ ticket.reporter?.full_name || 'Staff' }} • {{ formatDate(ticket.created_at) }}
                </div>
              </td>
              <td class="p-3">
                <div class="font-medium text-foreground">{{ ticket.room?.name || 'Ruang ?' }}</div>
                <div class="text-[11px] text-muted-foreground">{{ ticket.asset?.name || 'Sarana Umum' }}</div>
              </td>
              <td class="p-3">
                <span :class="getPriorityBadge(ticket.priority)">
                  {{ formatPriority(ticket.priority) }}
                </span>
              </td>
              <td class="p-3">
                <span :class="getTicketStatusBadge(ticket.status)">
                  {{ formatTicketStatus(ticket.status) }}
                </span>
              </td>
              <td class="p-3 font-mono font-semibold text-foreground">
                {{ ticket.cost > 0 ? formatRupiah(ticket.cost) : '-' }}
              </td>
              <td class="p-3 text-right">
                <div class="flex items-center justify-end gap-1">
                  <button
                    v-if="ticket.status !== 'resolved' && ticket.status !== 'cancelled'"
                    @click="openResolveModal(ticket)"
                    class="inline-flex items-center gap-1 rounded bg-emerald-600 px-2 py-1 text-[11px] font-semibold text-white hover:bg-emerald-700 transition"
                  >
                    <CheckCircle2 class="size-3" />
                    {{ t('school.facilities.btnResolve') }}
                  </button>
                  <span v-else class="text-[11px] text-muted-foreground font-medium">
                    {{ ticket.status === 'resolved' ? 'Terselesaikan' : 'Dibatalkan' }}
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL 1: ADD / EDIT ROOM -->
    <div v-if="showRoomModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-md rounded-xl border border-border/60 bg-card p-5 shadow-lg space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">
            {{ editingRoom ? t('school.facilities.editRoom') : t('school.facilities.addRoom') }}
          </h3>
          <button @click="showRoomModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="saveRoom" class="space-y-3 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.roomCode') }}</label>
            <input
              v-model="roomForm.code"
              type="text"
              required
              placeholder="e.g. LAB-RPL-02, R-XI-PPLG"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.roomName') }}</label>
            <input
              v-model="roomForm.name"
              type="text"
              required
              placeholder="e.g. Lab Komputer 2, Ruang Teori"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.roomType') }}</label>
              <select
                v-model="roomForm.type"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="classroom">Kelas Teori</option>
                <option value="laboratory">Laboratorium</option>
                <option value="workshop">Bengkel / Workshop</option>
                <option value="library">Perpustakaan</option>
                <option value="hall">Aula / Auditorium</option>
                <option value="office">Kantor / TU</option>
                <option value="other">Lainnya</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.condition') }}</label>
              <select
                v-model="roomForm.condition"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.capacity') }}</label>
              <input
                v-model.number="roomForm.capacity"
                type="number"
                min="1"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.floor') }}</label>
              <input
                v-model.number="roomForm.floor"
                type="number"
                min="1"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.description') }}</label>
            <textarea
              v-model="roomForm.description"
              rows="2"
              placeholder="Catatan fasilitas ruangan..."
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showRoomModal = false"
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

    <!-- MODAL 2: ADD / EDIT ASSET -->
    <div v-if="showAssetModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-lg rounded-xl border border-border/60 bg-card p-5 shadow-lg space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">
            {{ editingAsset ? t('school.facilities.editAsset') : t('school.facilities.addAsset') }}
          </h3>
          <button @click="showAssetModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="saveAsset" class="space-y-3 text-xs">
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.assetCode') }}</label>
              <input
                v-model="assetForm.code"
                type="text"
                required
                placeholder="e.g. AST-PC-01"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.category') }}</label>
              <select
                v-model="assetForm.category"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="elektronik">Elektronik</option>
                <option value="mebeler">Mebeler</option>
                <option value="alat_praktik">Alat Praktik</option>
                <option value="kendaraan">Kendaraan</option>
                <option value="buku">Buku</option>
                <option value="lainnya">Lainnya</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.assetName') }}</label>
            <input
              v-model="assetForm.name"
              type="text"
              required
              placeholder="e.g. PC Workstation Dell Optiplex"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.roomLocation') }}</label>
              <select
                v-model="assetForm.room_id"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option :value="null">-- Tanpa Ruang / Gudang --</option>
                <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.code }} - {{ r.name }}</option>
              </select>
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.brandModel') }}</label>
              <input
                v-model="assetForm.brand_model"
                type="text"
                placeholder="e.g. Dell Optiplex 7090"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="grid grid-cols-3 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.quantity') }}</label>
              <input
                v-model.number="assetForm.quantity"
                type="number"
                min="1"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.acquisitionYear') }}</label>
              <input
                v-model.number="assetForm.acquisition_year"
                type="number"
                min="1990"
                max="2099"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.condition') }}</label>
              <select
                v-model="assetForm.condition"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="baik">Baik</option>
                <option value="rusak_ringan">Rusak Ringan</option>
                <option value="rusak_berat">Rusak Berat</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.sourceOfFund') }}</label>
            <input
              v-model="assetForm.source_of_fund"
              type="text"
              placeholder="e.g. BOS Reguler, DAK Fisik, Komite"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            />
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showAssetModal = false"
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

    <!-- MODAL 3: REPORT TICKET -->
    <div v-if="showTicketModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-md rounded-xl border border-border/60 bg-card p-5 shadow-lg space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">{{ t('school.facilities.reportTicket') }}</h3>
          <button @click="showTicketModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="submitTicket" class="space-y-3 text-xs">
          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.roomLocation') }}</label>
            <select
              v-model="ticketForm.room_id"
              required
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            >
              <option value="" disabled>-- Pilih Ruangan --</option>
              <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.code }} - {{ r.name }}</option>
            </select>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.assetTarget') }}</label>
            <select
              v-model="ticketForm.asset_id"
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            >
              <option :value="null">-- Sarana Umum / Bukan Aset Tertentu --</option>
              <option v-for="a in assets" :key="a.id" :value="a.id">{{ a.code }} - {{ a.name }}</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.reporterStaff') }}</label>
              <select
                v-model="ticketForm.reporter_staff_id"
                required
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="" disabled>-- Pilih Staff --</option>
                <option v-for="s in staffList" :key="s.id" :value="s.id">{{ s.full_name }}</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.priority') }}</label>
              <select
                v-model="ticketForm.priority"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="rendah">Rendah</option>
                <option value="sedang">Sedang</option>
                <option value="tinggi">Tinggi</option>
                <option value="darurat">Darurat</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.issueDescription') }}</label>
            <textarea
              v-model="ticketForm.issue_description"
              rows="3"
              required
              placeholder="Deskripsikan kerusakan sarana atau fasilitas..."
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showTicketModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.facilities.btnSubmitReport') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 4: RESOLVE TICKET -->
    <div v-if="showResolveModal && resolvingTicket" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
      <div class="w-full max-w-md rounded-xl border border-border/60 bg-card p-5 shadow-lg space-y-4">
        <div class="flex items-center justify-between border-b border-border/60 pb-3">
          <h3 class="text-sm font-bold text-foreground">
            {{ t('school.facilities.resolveTicketTitle') }}: {{ resolvingTicket.ticket_number }}
          </h3>
          <button @click="showResolveModal = false" class="text-muted-foreground hover:text-foreground">
            <X class="size-4" />
          </button>
        </div>

        <form @submit.prevent="submitResolve" class="space-y-3 text-xs">
          <div class="rounded-lg bg-muted/40 p-2.5">
            <div class="font-semibold text-foreground">{{ resolvingTicket.issue_description }}</div>
            <div class="text-[11px] text-muted-foreground mt-0.5">
              Lokasi: {{ resolvingTicket.room?.name }} • Prioritas: {{ formatPriority(resolvingTicket.priority) }}
            </div>
          </div>

          <div>
            <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.resolutionNotes') }}</label>
            <textarea
              v-model="resolveForm.resolution_notes"
              rows="3"
              required
              placeholder="Catatan penanganan teknisi, suku cadang diganti, dll..."
              class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
            ></textarea>
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.statusResult') }}</label>
              <select
                v-model="resolveForm.status"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              >
                <option value="resolved">Resolved (Selesai)</option>
                <option value="in_progress">Dalam Pengerjaan</option>
                <option value="cancelled">Dibatalkan</option>
              </select>
            </div>

            <div>
              <label class="block font-medium text-foreground mb-1">{{ t('school.facilities.repairCost') }} (Rp)</label>
              <input
                v-model.number="resolveForm.cost"
                type="number"
                min="0"
                placeholder="0"
                class="w-full rounded-lg border border-border/60 bg-background px-3 py-2 text-xs text-foreground focus:ring-1 focus:ring-primary"
              />
            </div>
          </div>

          <div class="flex items-center justify-end gap-2 border-t border-border/60 pt-3">
            <button
              type="button"
              @click="showResolveModal = false"
              class="rounded-lg border border-border/60 bg-background px-4 py-2 text-xs font-semibold text-foreground hover:bg-muted/40 transition"
            >
              {{ t('school.common.cancel') }}
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700 transition disabled:opacity-50"
            >
              {{ saving ? t('school.common.saving') : t('school.facilities.btnConfirmResolution') }}
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
  Building2,
  Box,
  Wrench,
  CheckCircle2,
  Plus,
  Search,
  Edit,
  Trash2,
  AlertTriangle,
  X,
  Loader2,
} from 'lucide-vue-next';
import {
  SchoolService,
  type RoomData,
  type AssetData,
  type MaintenanceTicketData,
  type StaffData,
} from '../services/schoolService';

const { t } = useI18n();

const activeTab = ref<'rooms' | 'assets' | 'tickets'>('rooms');
const rooms = ref<RoomData[]>([]);
const assets = ref<AssetData[]>([]);
const tickets = ref<MaintenanceTicketData[]>([]);
const staffList = ref<StaffData[]>([]);

const loadingRooms = ref(false);
const loadingAssets = ref(false);
const loadingTickets = ref(false);
const saving = ref(false);

const roomSearch = ref('');
const assetSearch = ref('');
const ticketSearch = ref('');
const selectedCategory = ref('');
const selectedTicketStatus = ref('');

// Modals state
const showRoomModal = ref(false);
const editingRoom = ref<RoomData | null>(null);
const roomForm = reactive({
  code: '',
  name: '',
  type: 'classroom',
  capacity: 36,
  floor: 1,
  condition: 'baik',
  description: '',
});

const showAssetModal = ref(false);
const editingAsset = ref<AssetData | null>(null);
const assetForm = reactive({
  code: '',
  name: '',
  room_id: null as number | null,
  category: 'elektronik',
  brand_model: '',
  acquisition_year: new Date().getFullYear(),
  quantity: 1,
  condition: 'baik',
  source_of_fund: 'BOS Reguler',
  notes: '',
});

const showTicketModal = ref(false);
const ticketForm = reactive({
  room_id: '' as unknown as number,
  asset_id: null as number | null,
  reporter_staff_id: '' as unknown as number,
  issue_description: '',
  priority: 'sedang',
});

const showResolveModal = ref(false);
const resolvingTicket = ref<MaintenanceTicketData | null>(null);
const resolveForm = reactive({
  resolution_notes: '',
  status: 'resolved',
  cost: 0,
});

// Computed Stats & Filtered
const totalAssetsCount = computed(() => {
  return (assets.value || []).reduce((acc, a) => acc + (a.quantity || 1), 0);
});

const pendingTicketsCount = computed(() => {
  return (tickets.value || []).filter((t) => t.status === 'open' || t.status === 'in_progress').length;
});

const resolvedTicketsCount = computed(() => {
  return (tickets.value || []).filter((t) => t.status === 'resolved').length;
});

const filteredRooms = computed(() => {
  const list = rooms.value || [];
  if (!roomSearch.value) return list;
  const q = roomSearch.value.toLowerCase();
  return list.filter(
    (r) => r.code.toLowerCase().includes(q) || r.name.toLowerCase().includes(q)
  );
});

const filteredAssets = computed(() => {
  const list = assets.value || [];
  return list.filter((a) => {
    const matchesSearch =
      !assetSearch.value ||
      a.code.toLowerCase().includes(assetSearch.value.toLowerCase()) ||
      a.name.toLowerCase().includes(assetSearch.value.toLowerCase());
    const matchesCat = !selectedCategory.value || a.category === selectedCategory.value;
    return matchesSearch && matchesCat;
  });
});

const filteredTickets = computed(() => {
  const list = tickets.value || [];
  return list.filter((t) => {
    const matchesSearch =
      !ticketSearch.value ||
      t.ticket_number.toLowerCase().includes(ticketSearch.value.toLowerCase()) ||
      t.issue_description.toLowerCase().includes(ticketSearch.value.toLowerCase());
    const matchesStatus = !selectedTicketStatus.value || t.status === selectedTicketStatus.value;
    return matchesSearch && matchesStatus;
  });
});

// Helpers
function formatRoomType(type: string) {
  const map: Record<string, string> = {
    classroom: 'Kelas Teori',
    laboratory: 'Laboratorium',
    workshop: 'Bengkel / Workshop',
    library: 'Perpustakaan',
    hall: 'Auditorium / Aula',
    office: 'Ruang Kantor',
    other: 'Lainnya',
  };
  return map[type] || type;
}

function formatAssetCategory(cat: string) {
  const map: Record<string, string> = {
    elektronik: 'Elektronik',
    mebeler: 'Mebeler',
    alat_praktik: 'Alat Praktik',
    kendaraan: 'Kendaraan',
    buku: 'Buku',
    lainnya: 'Lainnya',
  };
  return map[cat] || cat;
}

function formatCondition(cond: string) {
  const map: Record<string, string> = {
    baik: 'Baik',
    rusak_ringan: 'Rusak Ringan',
    rusak_berat: 'Rusak Berat',
  };
  return map[cond] || cond;
}

function getConditionBadge(cond: string) {
  if (cond === 'baik') return 'inline-flex items-center rounded-md bg-emerald-500/10 px-2 py-0.5 text-[11px] font-medium text-emerald-600';
  if (cond === 'rusak_ringan') return 'inline-flex items-center rounded-md bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-600';
  return 'inline-flex items-center rounded-md bg-rose-500/10 px-2 py-0.5 text-[11px] font-medium text-rose-600';
}

function formatPriority(priority: string) {
  const map: Record<string, string> = {
    rendah: 'Rendah',
    sedang: 'Sedang',
    tinggi: 'Tinggi',
    darurat: 'Darurat',
  };
  return map[priority] || priority;
}

function getPriorityBadge(priority: string) {
  if (priority === 'darurat') return 'inline-flex items-center rounded-md bg-rose-600 px-2 py-0.5 text-[11px] font-semibold text-white';
  if (priority === 'tinggi') return 'inline-flex items-center rounded-md bg-rose-500/10 px-2 py-0.5 text-[11px] font-medium text-rose-600';
  if (priority === 'sedang') return 'inline-flex items-center rounded-md bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-600';
  return 'inline-flex items-center rounded-md bg-blue-500/10 px-2 py-0.5 text-[11px] font-medium text-blue-600';
}

function formatTicketStatus(status: string) {
  const map: Record<string, string> = {
    open: 'Open (Baru)',
    in_progress: 'Dalam Proses',
    resolved: 'Selesai',
    cancelled: 'Dibatalkan',
  };
  return map[status] || status;
}

function getTicketStatusBadge(status: string) {
  if (status === 'resolved') return 'inline-flex items-center rounded-md bg-emerald-500/10 px-2 py-0.5 text-[11px] font-medium text-emerald-600';
  if (status === 'in_progress') return 'inline-flex items-center rounded-md bg-blue-500/10 px-2 py-0.5 text-[11px] font-medium text-blue-600';
  if (status === 'cancelled') return 'inline-flex items-center rounded-md bg-muted px-2 py-0.5 text-[11px] font-medium text-muted-foreground';
  return 'inline-flex items-center rounded-md bg-amber-500/10 px-2 py-0.5 text-[11px] font-medium text-amber-600';
}

function formatRupiah(amount: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
}

function formatDate(isoString: string) {
  if (!isoString) return '-';
  const d = new Date(isoString);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

// Data loaders
async function loadRooms() {
  loadingRooms.value = true;
  try {
    const res = await SchoolService.getRooms();
    const raw = res.data?.data ?? res.data;
    rooms.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
  } catch (err) {
    console.error(err);
  } finally {
    loadingRooms.value = false;
  }
}

async function loadAssets() {
  loadingAssets.value = true;
  try {
    const res = await SchoolService.getAssets();
    const raw = res.data?.data ?? res.data;
    assets.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
  } catch (err) {
    console.error(err);
  } finally {
    loadingAssets.value = false;
  }
}

async function loadTickets() {
  loadingTickets.value = true;
  try {
    const res = await SchoolService.getTickets({ per_page: 100 });
    const raw = res.data?.data ?? res.data;
    tickets.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
  } catch (err) {
    console.error(err);
  } finally {
    loadingTickets.value = false;
  }
}

async function loadStaff() {
  try {
    const res = await SchoolService.getStaff({ per_page: 100 });
    const raw = res.data?.data ?? res.data;
    staffList.value = Array.isArray(raw) ? raw : (raw?.data ?? []);
  } catch (err) {
    console.error(err);
  }
}

// Room Actions
function openRoomModal(room?: RoomData) {
  if (room) {
    editingRoom.value = room;
    roomForm.code = room.code;
    roomForm.name = room.name;
    roomForm.type = room.type;
    roomForm.capacity = room.capacity;
    roomForm.floor = room.floor;
    roomForm.condition = room.condition;
    roomForm.description = room.description || '';
  } else {
    editingRoom.value = null;
    roomForm.code = '';
    roomForm.name = '';
    roomForm.type = 'classroom';
    roomForm.capacity = 36;
    roomForm.floor = 1;
    roomForm.condition = 'baik';
    roomForm.description = '';
  }
  showRoomModal.value = true;
}

async function saveRoom() {
  saving.value = true;
  try {
    if (editingRoom.value) {
      await SchoolService.updateRoom(editingRoom.value.id, roomForm);
    } else {
      await SchoolService.storeRoom(roomForm);
    }
    showRoomModal.value = false;
    await loadRooms();
  } catch (err) {
    console.error(err);
  } finally {
    saving.value = false;
  }
}

async function deleteRoom(id: number) {
  if (!confirm('Apakah Anda yakin ingin menghapus ruangan ini?')) return;
  try {
    await SchoolService.deleteRoom(id);
    await loadRooms();
  } catch (err) {
    console.error(err);
  }
}

// Asset Actions
function openAssetModal(asset?: AssetData) {
  if (asset) {
    editingAsset.value = asset;
    assetForm.code = asset.code;
    assetForm.name = asset.name;
    assetForm.room_id = asset.room_id;
    assetForm.category = asset.category;
    assetForm.brand_model = asset.brand_model || '';
    assetForm.acquisition_year = asset.acquisition_year || new Date().getFullYear();
    assetForm.quantity = asset.quantity;
    assetForm.condition = asset.condition;
    assetForm.source_of_fund = asset.source_of_fund || 'BOS';
    assetForm.notes = asset.notes || '';
  } else {
    editingAsset.value = null;
    assetForm.code = '';
    assetForm.name = '';
    assetForm.room_id = rooms.value[0]?.id ?? null;
    assetForm.category = 'elektronik';
    assetForm.brand_model = '';
    assetForm.acquisition_year = new Date().getFullYear();
    assetForm.quantity = 1;
    assetForm.condition = 'baik';
    assetForm.source_of_fund = 'BOS Reguler';
    assetForm.notes = '';
  }
  showAssetModal.value = true;
}

async function saveAsset() {
  saving.value = true;
  try {
    if (editingAsset.value) {
      await SchoolService.updateAsset(editingAsset.value.id, assetForm);
    } else {
      await SchoolService.storeAsset(assetForm);
    }
    showAssetModal.value = false;
    await loadAssets();
  } catch (err) {
    console.error(err);
  } finally {
    saving.value = false;
  }
}

async function deleteAsset(id: number) {
  if (!confirm('Hapus aset ini dari inventaris?')) return;
  try {
    await SchoolService.deleteAsset(id);
    await loadAssets();
  } catch (err) {
    console.error(err);
  }
}

// Ticket Actions
function openTicketModal() {
  ticketForm.room_id = rooms.value[0]?.id ?? ('' as unknown as number);
  ticketForm.asset_id = null;
  ticketForm.reporter_staff_id = staffList.value[0]?.id ?? ('' as unknown as number);
  ticketForm.issue_description = '';
  ticketForm.priority = 'sedang';
  showTicketModal.value = true;
}

async function submitTicket() {
  saving.value = true;
  try {
    await SchoolService.storeTicket(ticketForm);
    showTicketModal.value = false;
    await loadTickets();
  } catch (err) {
    console.error(err);
  } finally {
    saving.value = false;
  }
}

function openResolveModal(ticket: MaintenanceTicketData) {
  resolvingTicket.value = ticket;
  resolveForm.resolution_notes = '';
  resolveForm.status = 'resolved';
  resolveForm.cost = 0;
  showResolveModal.value = true;
}

async function submitResolve() {
  if (!resolvingTicket.value) return;
  saving.value = true;
  try {
    await SchoolService.resolveTicket(resolvingTicket.value.id, resolveForm);
    showResolveModal.value = false;
    await loadTickets();
  } catch (err) {
    console.error(err);
  } finally {
    saving.value = false;
  }
}

onMounted(() => {
  loadRooms();
  loadAssets();
  loadTickets();
  loadStaff();
});
</script>
