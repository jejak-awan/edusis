import type { ThemeCustomizerExtension } from '@/modules/Layout/customizer/types/extension';
import { sarangengeCustomizerExtension } from '@/modules/Layout/views/themes/sarangenge/customizer';

/** Bundled theme customizer extensions (add new themes here when introduced). */
const extensionBySlug: Record<string, ThemeCustomizerExtension> = {
    sarangenge: sarangengeCustomizerExtension,
};

export function resolveThemeCustomizerExtension(slug: string): ThemeCustomizerExtension | null {
    const normalized = (slug || '').trim().toLowerCase();
    if (!normalized) return null;
    return extensionBySlug[normalized] ?? null;
}

export function getThemeBindingRegistry(slug: string) {
    return resolveThemeCustomizerExtension(slug)?.bindings ?? [];
}

export function getReservedManifestCategories(slug: string): Set<string> {
    const list = resolveThemeCustomizerExtension(slug)?.reservedManifestCategories ?? [];
    return new Set(list);
}
