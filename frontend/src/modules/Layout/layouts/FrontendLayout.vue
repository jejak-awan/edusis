<template>
  <div 
    class="frontend-layout min-h-screen flex flex-col bg-background text-foreground"
    :class="[rootClasses, activeThemeClass]"
    :style="rootStyles"
    v-bind="themeRootDataAttrs"
  >
    <!-- CASE 1: FULL WIDTH (Default) & HYBRID -->
    <!-- For Hybrid: Header/Footer are here (full), Main is constrained below -->
    <!-- For Full: Everything is here -->
    <template v-if="activeTheme && (layoutStyle === 'full' || layoutStyle === 'hybrid')">
      <div :class="headerChromeClass">
        <ThemePageResolver :page="chromeHeaderPage" />
      </div>
      <PluginSlot name="after_header" />
      
      <!-- Main Content -->
      <!-- Hybrid: Main is boxed | Full: Main is full -->
      <main class="main-content flex-1 w-full relative z-0 flex flex-col min-h-0">
        <div
          class="flex-1 flex flex-col min-h-0 w-full"
          :class="{
            'mx-auto': layoutStyle === 'hybrid',
          }"
          :style="hybridContentStyles"
        >
          <router-view v-slot="{ Component }">
            <div
              :key="route.path"
              class="w-full h-full flex-1 flex flex-col page-enter route-shell"
            >
              <component :is="Component" />
            </div>
          </router-view>
        </div>
      </main>

      <PluginSlot name="before_footer" />
      <div class="mt-auto relative z-10">
        <ThemePageResolver :page="chromeFooterPage" />
      </div>
    </template>


    <!-- CASE 2: BOXED, WIDE, FRAMED -->
    <!-- Everything wraps inside a container -->
    <div 
      v-else-if="activeTheme"
      class="layout-wrapper mx-auto flex flex-col min-h-screen bg-background shadow-2xl overflow-visible"
      :class="wrapperClasses"
      :style="wrapperStyles"
    >
      <div :class="headerChromeClass">
        <ThemePageResolver :page="chromeHeaderPage" />
      </div>
      <PluginSlot name="after_header" />
      
      <main class="main-content flex-1 w-full relative z-0 flex flex-col min-h-0">
        <router-view v-slot="{ Component }">
          <div
            :key="route.path"
            class="w-full h-full flex-1 flex flex-col page-enter route-shell"
          >
            <component :is="Component" />
          </div>
        </router-view>
      </main>

      <PluginSlot name="before_footer" />
      <div class="mt-auto">
        <ThemePageResolver :page="chromeFooterPage" />
      </div>
    </div>
    
    <!-- Initial loading guard: avoid double-mounting route component -->
    <div
      v-else-if="loading"
      class="flex-1 flex items-center justify-center bg-background"
      aria-live="polite"
      aria-busy="true"
    >
      <div class="flex items-center gap-3 text-muted-foreground">
        <span class="inline-block w-4 h-4 rounded-full border-2 border-current border-t-transparent animate-spin" />
        <span class="text-sm font-medium">Memuat tema...</span>
      </div>
    </div>

    <!-- FALLBACK: Degrade gracefully when theme API is unavailable -->
    <div 
      v-else
      class="flex-1 flex flex-col bg-background"
    >
      <div
        v-if="showThemeServiceNotice"
        class="px-4 py-2 text-xs text-center text-amber-700 dark:text-amber-300 bg-amber-500/10 border-b border-amber-500/30"
      >
        Theme service sedang tidak tersedia. Menampilkan mode fallback sementara.
      </div>
      <main class="main-content flex-1 w-full">
        <router-view v-slot="{ Component }">
          <div
            :key="route.path"
            class="w-full h-full flex-1 flex flex-col page-enter route-shell"
          >
            <component :is="Component" />
          </div>
        </router-view>
      </main>
    </div>

    <!-- Floating Back to Top — theme tokens (primary), not hardcoded amber/slate -->
    <button
      v-if="showBackToTop"
      type="button"
      class="layout-back-to-top fixed bottom-8 right-8 z-[9999] w-11 h-11 rounded-full flex items-center justify-center transition-all duration-300 transform hover:scale-105 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background bg-foreground text-background border border-border shadow-lg hover:bg-primary hover:text-primary-foreground hover:border-primary"
      style="position: fixed !important; bottom: 2rem !important; right: 2rem !important; left: auto !important; top: auto !important; z-index: 999999 !important;"
      :aria-label="backToTopLabel"
      :title="backToTopLabel"
      @click="scrollToTop"
    >
      <ArrowUp class="w-5 h-5 stroke-[2.5]" />
    </button>

    <!-- Global Floating Overlay (Plugin Slot for Floating Social Dock, Hotline, etc.) -->
    <PluginSlot v-if="!useMemberShell" name="floating_overlay" />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, onUnmounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useTheme } from '@/modules/Layout/composables/useTheme'
import ThemePageResolver from '@/modules/Layout/components/themes/ThemePageResolver.vue'
import { useCustomizerPreviewProbe } from '@/modules/Layout/customizer/preview/useCustomizerPreviewProbe'
import { isCustomizerPreviewQuery, resolveAllowedCustomizerOrigins } from '@/modules/Layout/customizer/preview/protocol'
import '@/modules/Layout/customizer/preview/customizer-preview.css'
import { PluginSlot } from '@/shared/components'
import {
  ArrowUp,
} from 'lucide-vue-next'

import { headerChromeWrapClass, isHeaderStickySetting } from '@/modules/Layout/layouts/headerChromeWrap'
import { buildThemeViewResolveCandidates, findThemeViewKey } from '@/modules/Layout/utils/themeViewResolver'
import { useRoute } from 'vue-router'
import { useHead } from '@unhead/vue'
import { useSystemStore } from '@/modules/Core/System/stores/system'
import { applyFavicon, isGenericEngineFavicon, resolveFavicon } from '@/modules/Core/System/utils/favicon'
import { useThemeI18n } from '@/modules/Layout/composables/useThemeI18n'
import { resolveSarangengePublicSeo } from '@/modules/Layout/views/themes/sarangenge/composables/sarangengePublicSeo'

const { t } = useI18n({ useScope: 'global' })
const { t: tSarangenge } = useThemeI18n('sarangenge')
const { activeTheme, getSetting, loading, error, loadActiveTheme } = useTheme()
const systemStore = useSystemStore()

const backToTopLabel = computed(() => t('layout.frontend.backToTop'))
const route = useRoute()

/** Authenticated /member/* portal uses quiet MemberHeader/MemberFooter instead of marketing chrome. */
const useMemberShell = computed(() =>
  route.matched.some((record) => record.meta.memberShell === true),
)
const chromeHeaderPage = computed(() =>
  useMemberShell.value ? 'components/MemberHeader' : 'components/Header',
)
const chromeFooterPage = computed(() =>
  useMemberShell.value ? 'components/MemberFooter' : 'components/Footer',
)

useCustomizerPreviewProbe()
const lastThemeRetryAt = ref(0)
const RETRY_INTERVAL_MS = 15000
const prefetchedThemePages = new Set<string>()
const viewModules = import.meta.glob('@/modules/Layout/views/themes/**/*.vue') as Record<string, () => Promise<unknown>>
const CORE_PUBLIC_PATH_TO_PAGE: Record<string, string> = {
  '/': 'Home',
  '/about': 'About',
  '/team': 'Team',
  '/tim': 'Team',
  '/solutions': 'Solutions',
  '/solusi': 'Solutions',
  '/pricing': 'Pricing',
  '/blog': 'Blog',
  '/contact': 'Contact',
}

const activeThemeSlug = computed(
  () => (activeTheme.value as { slug?: string } | null)?.slug ?? '',
)

useHead(computed(() => {
  if (useMemberShell.value) {
    return {}
  }
  const siteName = String(
    systemStore.siteSettings?.site_name ||
    systemStore.siteSettings?.site_title ||
    getSetting('site_name', '') ||
    getSetting('site_title', '') ||
    getSetting('school_name', '') ||
    getSetting('brand_name', '') ||
    activeTheme.value?.name ||
    ''
  ).trim()
  const themePage = typeof route.meta.themePage === 'string' ? route.meta.themePage : undefined

  const seo = resolveSarangengePublicSeo({
    themePage,
    siteName: siteName || t('common.schoolName', 'Portal Sekolah'),
    t: tSarangenge,
  })

  if (!seo) {
    return {}
  }

  const origin = typeof window !== 'undefined' ? window.location.origin : ''
  const path = route.path === '/' ? '/' : route.path
  return {
    title: seo.title,
    meta: [
      { name: 'description', content: seo.description },
      { property: 'og:title', content: seo.title },
      { property: 'og:description', content: seo.description },
      { property: 'og:type', content: 'website' },
      { name: 'twitter:card', content: 'summary' },
      ...(origin ? [{ property: 'og:url', content: `${origin}${path}` }] : []),
    ],
  }
}))

const activeThemeClass = computed(() => {
  const slug = activeThemeSlug.value || 'sarangenge'
  return [`theme-${slug}`, 'sarangenge-theme']
})

const themeRootDataAttrs = computed((): Record<string, string> => {
  const styleRaw = String(getSetting('theme_style', 'clean') ?? 'clean')
  const navRaw = String(getSetting('nav_style', 'glass') ?? 'glass')
  const btnRadiusRaw = String(getSetting('button_radius', '8px') ?? '8px')
  const btnShadowRaw = String(getSetting('button_shadow', 'subtle') ?? 'subtle')
  const breadcrumbSticky = Boolean(getSetting('breadcrumb_sticky', false))
  const headerStickyVal = isHeaderStickySetting(getSetting('header_sticky', true))
  const layoutStyleVal = String(getSetting('layout_style', 'full') ?? 'full')

  return {
    'data-theme-style': styleRaw,
    'data-theme-nav': navRaw,
    'data-button-radius': btnRadiusRaw,
    'data-button-shadow': btnShadowRaw,
    'data-breadcrumb-sticky': String(breadcrumbSticky),
    'data-header-sticky': String(headerStickyVal),
    'data-layout-style': layoutStyleVal,
  }
})

// Layout settings
const layoutStyle = computed(() => getSetting('layout_style', 'full') as string)
const containerMaxWidth = computed(() => {
  const style = layoutStyle.value
  if (style === 'boxed') return 1200
  if (style === 'wide') return 1480
  if (style === 'framed') return 1380
  return (getSetting('container_max_width', 1400) as number) || 1400
})
const boxedBgColor = computed(() => getSetting('boxed_bg_color', '') as string)
const boxedShadow = computed(() => getSetting('boxed_shadow', 'xl') as string)

// ROOT CLASSES (Outer most div)
const rootClasses = computed(() => {
  const style = layoutStyle.value
  return {
    'bg-slate-200/70 dark:bg-slate-950/90': style === 'boxed' || style === 'wide' || style === 'framed',
    'p-3 sm:p-6 lg:p-8 min-h-screen': style === 'framed',
    'py-4 sm:py-6 px-2 sm:px-4 min-h-screen': style === 'boxed' || style === 'wide',
  }
})

const rootStyles = computed(() => {
  const style = layoutStyle.value
  if (style === 'boxed' || style === 'wide' || style === 'framed') {
    if (boxedBgColor.value) {
      return { backgroundColor: boxedBgColor.value }
    }
  }
  return {}
})

// wrapper classes for Boxed/Wide/Framed
const wrapperClasses = computed(() => {
  const style = layoutStyle.value
  const shadow = boxedShadow.value
  const shadowClass = shadow === 'none' ? 'shadow-none' : `shadow-${shadow}`
  return {
    'rounded-2xl border border-border/80 overflow-visible ring-1 ring-black/5 dark:ring-white/10': style === 'framed',
    'rounded-xl border border-border/60 overflow-visible': style === 'boxed' || style === 'wide',
    [shadowClass]: true,
  }
})

const wrapperStyles = computed(() => {
  if (['boxed', 'wide', 'framed'].includes(layoutStyle.value)) {
    return { 
      maxWidth: `${containerMaxWidth.value}px`,
      width: '100%',
      margin: '0 auto',
    }
  }
  return {}
})

const hybridContentStyles = computed(() => {
  if (layoutStyle.value === 'hybrid') {
    return { maxWidth: `${containerMaxWidth.value}px`, margin: '0 auto' }
  }
  return {}
})

const enableBackToTop = computed(() => getSetting('back_to_top', true) as boolean)
const headerSticky = computed(() => isHeaderStickySetting(getSetting('header_sticky', true)))
const headerChromeClass = computed(() =>
  headerChromeWrapClass({
    sticky: headerSticky.value && !useMemberShell.value,
  }),
)
const showBackToTop = ref(false)
const showThemeServiceNotice = computed(() => !activeTheme.value && !loading.value && !!error.value)

const handleScroll = () => {
  if (window.scrollY > 300 && enableBackToTop.value) {
    showBackToTop.value = true
  } else {
    showBackToTop.value = false
  }
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const retryThemeLoadIfNeeded = async (options?: { force?: boolean }) => {
  if (loading.value) return
  const force = options?.force === true
  const now = Date.now()
  if (!force && now - lastThemeRetryAt.value < RETRY_INTERVAL_MS) return
  lastThemeRetryAt.value = now
  try {
    await loadActiveTheme('frontend', { force })
  } catch {
    // silent: fallback UI handles service degradation
  }
}

const resolveThemeViewKey = (pageName: string): string | undefined => {
  const themeSlugs = buildThemeViewResolveCandidates(activeTheme.value)
  return findThemeViewKey(viewModules, themeSlugs, pageName)
}

const prefetchThemePage = (pageName: string) => {
  if (!activeTheme.value || loading.value) return
  const key = resolveThemeViewKey(pageName)
  if (!key || prefetchedThemePages.has(key)) return
  const loader = viewModules[key]
  if (!loader) return
  prefetchedThemePages.add(key)
  void loader().catch(() => {
    prefetchedThemePages.delete(key)
  })
}

const schedulePublicPrefetch = () => {
  if (route.path === '/') return
  if (typeof navigator !== 'undefined') {
    const connection = (navigator as Navigator & { connection?: { saveData?: boolean; effectiveType?: string } }).connection
    if (connection?.saveData) return
    if (connection?.effectiveType && ['slow-2g', '2g', '3g'].includes(connection.effectiveType)) return
  }
  const path = route.path.toLowerCase()
  const targets = Object.entries(CORE_PUBLIC_PATH_TO_PAGE)
    .filter(([targetPath]) => targetPath !== path)
    .map(([, page]) => page)

  const run = () => {
    targets.forEach(prefetchThemePage)
  }

  if (typeof window !== 'undefined' && typeof window.requestIdleCallback === 'function') {
    window.requestIdleCallback(run, { timeout: 2000 })
  } else {
    setTimeout(run, 0)
  }
}

// React immediately to back_to_top setting changes
watch(enableBackToTop, (enabled) => {
    if (!enabled) showBackToTop.value = false;
    else handleScroll();
});


const handleCustomizerSync = (event: MessageEvent) => {
  const allowed = resolveAllowedCustomizerOrigins(window.location.search, window.location.origin)
  if (!allowed.has(event.origin)) return

  if (event.data?.type === 'JA_CUSTOMIZER_THEME_BOOT' && event.data?.theme) {
    activeTheme.value = event.data.theme
    return
  }

  if (event.data?.type === 'JA_THEME_CUSTOMIZER_SYNC' && event.data?.theme) {
    const incoming = event.data.theme
    if (!activeTheme.value || activeTheme.value.slug !== incoming.slug) {
      activeTheme.value = { ...incoming }
      return
    }
    activeTheme.value = {
      ...activeTheme.value,
      ...incoming,
      settings: {
        ...(activeTheme.value.settings || {}),
        ...(incoming.settings || {}),
      },
    }
  }
}

onMounted(async () => {
  window.addEventListener('scroll', handleScroll)
  window.addEventListener('message', handleCustomizerSync)
  // Public site: force reconcile so a stale session snapshot cannot lock the wrong shell.
  // Customizer iframe: keep the draft from THEME_BOOT / sessionStorage — a force API reload
  // would wipe live sticky/menu edits before they paint.
  const inCustomizerPreview = typeof window !== 'undefined'
    && isCustomizerPreviewQuery(window.location.search)
  await retryThemeLoadIfNeeded({ force: !inCustomizerPreview })
  schedulePublicPrefetch()



  try {
    const { bootstrapPluginThemeBlocks } = await import('@/engine/plugins/pluginBootstrap')
    await bootstrapPluginThemeBlocks()
  } catch {
    /* ignore plugin bootstrap errors */
  }
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  window.removeEventListener('message', handleCustomizerSync)
})

watch(() => route.fullPath, () => {
  void retryThemeLoadIfNeeded()
  schedulePublicPrefetch()
})

watch(() => activeTheme.value?.slug, () => {
  prefetchedThemePages.clear()
  schedulePublicPrefetch()
})

const publicFaviconHref = computed(() => {
  const syncEnabled = Boolean(systemStore.getSetting('brand_sync_site_identity', false));
  const hasWl = Boolean(systemStore.appIdentity?.has_white_label);
  const brandFav = (systemStore.getSetting('brand_favicon') as string) || '';

  if (hasWl && syncEnabled && brandFav) {
    return brandFav;
  }

  return resolveFavicon([
    getSetting('site_favicon', ''),
    systemStore.siteSettings?.site_favicon,
    getSetting('brand_favicon', ''),
    systemStore.appIdentity?.app_favicon,
  ]);
});

const publicFaviconReady = computed(() =>
  activeTheme.value !== null || (!loading.value && !!error.value),
)

watch(
  [publicFaviconHref, publicFaviconReady],
  ([href, ready]) => {
    if (!href) return
    if (isGenericEngineFavicon(href) && !ready) return
    applyFavicon(href, { allowGeneric: ready, scope: 'site' })
  },
  { immediate: true },
)
</script>

<style scoped>
/* Page enter animation — pure CSS, no Vue transition lifecycle hooks */
@keyframes pageEnter {
  from {
    opacity: 0;
    transform: translateY(6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.page-enter {
  animation: pageEnter 0.25s ease-out;
}

.route-shell {
  contain: layout style;
}

@media (prefers-reduced-motion: reduce), (max-width: 1024px) {
  .page-enter {
    animation: none;
  }
}

/* Shadow utilities re-implementation because tailwind classes might be purged if dynamic */
.shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
.shadow-md { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
.shadow-lg { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1); }
.shadow-xl { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
</style>
