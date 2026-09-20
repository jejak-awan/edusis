import type { CustomizerPreviewTargetConfig } from '@/modules/Layout/customizer/preview/protocol';
import sarangengeTargets from '@/modules/Layout/views/themes/sarangenge/customizer/preview.targets.json';

const bySlug: Record<string, Record<string, CustomizerPreviewTargetConfig>> = {
  sarangenge: sarangengeTargets.targets as Record<string, CustomizerPreviewTargetConfig>,
};

export function getThemePreviewTargets(slug: string): Record<string, CustomizerPreviewTargetConfig> {
  const key = String(slug || '').toLowerCase();
  if (bySlug[key]) return bySlug[key];
  return bySlug.sarangenge ?? {};
}

export function resolvePreviewNavItemId(
  targets: Record<string, CustomizerPreviewTargetConfig>,
  targetKey: string,
  mode?: 'design' | 'bindings',
): string | null {
  const cfg = targets[targetKey];
  if (!cfg) return null;
  if ((mode === 'bindings' || (!mode && cfg.mode === 'bindings')) && cfg.bindingsId) {
    return `comp-${cfg.bindingsId}`;
  }
  return cfg.navItemId;
}
