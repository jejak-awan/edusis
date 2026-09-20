/**
 * Returns wrapper classes for the chrome header.
 * For active themes (Sarangenge) and frontend shells, sticky positioning is applied
 * to this outer container so the header stays pinned at top-0 with proper z-index.
 */
export function headerChromeWrapClass(opts: {
  sticky?: boolean;
}): string {
  if (opts.sticky) return 'sticky top-0 z-[100] overflow-visible';
  return 'relative z-50 overflow-visible';
}

export function isHeaderStickySetting(value: unknown, defaultValue = true): boolean {
  if (value === undefined || value === null) return defaultValue;
  if (value === false || value === 0 || value === '0' || value === 'false' || value === 'off') {
    return false;
  }
  return true;
}
