import { describe, expect, it } from 'vitest';
import { lookupModuleLocale, looksLikeI18nPath } from '@/modules/Layout/customizer/i18n/lookupModuleLocale';
import { translateCustomizerNavKey } from '@/modules/Layout/customizer/i18n/translateCustomizerNavKey';

describe('lookupModuleLocale', () => {
    it('resolves Sarangenge customizer item keys from theme locale JSON', () => {
        expect(lookupModuleLocale('id', 'theme.sarangenge.customizer.sidebar.page_about')).toBe('Halaman Profil');
        expect(lookupModuleLocale('en', 'theme.sarangenge.customizer.sidebar.page_about')).toBe('About Page');
        expect(lookupModuleLocale('id', 'theme.sarangenge.customizer.sidebar.page_contact')).toBe('Halaman Kontak & PPDB');
    });

    it('falls back to id when the locale pack misses a key', () => {
        expect(lookupModuleLocale('xx', 'theme.sarangenge.customizer.sidebar.page_about')).toBe('Halaman Profil');
    });
});

describe('translateCustomizerNavKey', () => {
    const missingT = (key: string) => key;

    it('does not leak raw theme.sarangenge paths when vue-i18n misses', () => {
        expect(translateCustomizerNavKey(missingT, 'theme.sarangenge.customizer.sidebar.page_hero', 'en')).toBe(
            'Hero & Home Banner',
        );
        expect(translateCustomizerNavKey(missingT, 'theme.sarangenge.customizer.sidebar.page_about', 'id')).toBe(
            'Halaman Profil',
        );
        expect(
            looksLikeI18nPath(
                translateCustomizerNavKey(missingT, 'theme.sarangenge.customizer.sidebar.page_contact', 'id'),
            ),
        ).toBe(false);
    });

    it('humanizes the tail if the key is unknown', () => {
        expect(translateCustomizerNavKey(missingT, 'theme.sarangenge.customizer.items.not_a_real_key', 'id')).toBe(
            'not a real key',
        );
    });
});
