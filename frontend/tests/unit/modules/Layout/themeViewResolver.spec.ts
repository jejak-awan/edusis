import { describe, expect, it } from 'vitest';
import {
    BUNDLED_FRONTEND_THEME_SLUGS,
    buildThemeViewResolveCandidates,
    findThemeViewKey,
} from '@/modules/Layout/utils/themeViewResolver';

const modules = {
    '/src/modules/Layout/views/themes/Index.vue': () => Promise.resolve({}),
    '/src/modules/Layout/views/themes/ThemeCustomizer.vue': () => Promise.resolve({}),
    '/src/modules/Layout/views/themes/sarangenge/components/layout/Header.vue': () => Promise.resolve({}),
    '/src/modules/Layout/views/themes/sarangenge/components/layout/Footer.vue': () => Promise.resolve({}),
    '/src/modules/Layout/views/themes/sarangenge/pages/Home.vue': () => Promise.resolve({}),
    '/src/modules/Layout/views/themes/custom_child/pages/SpecialPage.vue': () => Promise.resolve({}),
};

describe('themeViewResolver', () => {
    it('falls back to bundled sarangenge baseline when theme slug is missing', () => {
        expect(buildThemeViewResolveCandidates(null)).toEqual([...BUNDLED_FRONTEND_THEME_SLUGS]);
        expect(BUNDLED_FRONTEND_THEME_SLUGS[0]).toBe('sarangenge');
        expect(buildThemeViewResolveCandidates({ name: 'X', slug: '', type: 'frontend' })).toEqual([
            ...BUNDLED_FRONTEND_THEME_SLUGS,
        ]);
    });

    it('isolates candidate themes strictly to [slug, parent_theme] and never leaks peer themes', () => {
        const candidates = buildThemeViewResolveCandidates({
            name: 'Custom Child Theme',
            slug: 'custom_child',
            parent_theme: 'sarangenge',
            type: 'frontend',
        });
        expect(candidates).toEqual(['custom_child', 'sarangenge']);
        expect(candidates).not.toContain('other_peer');
    });

    it('resolves components/Header from sarangenge when sarangenge views exist', () => {
        const key = findThemeViewKey(modules, ['sarangenge'], 'components/Header');
        expect(key).toContain('sarangenge/components/layout/Header.vue');
    });

    it('does NOT leak peer theme page to sarangenge when searching for child-only page', () => {
        const candidates = buildThemeViewResolveCandidates({
            name: 'Sarangenge',
            slug: 'sarangenge',
            type: 'frontend',
        });
        const key = findThemeViewKey(modules, candidates, 'pages/SpecialPage');
        expect(key).toBeUndefined();
    });

    it('auto-heals legacy pruned themes (janari, layung, sareupna) to sarangenge baseline', () => {
        expect(buildThemeViewResolveCandidates({ name: 'Janari', slug: 'janari', type: 'frontend' })).toEqual([
            'sarangenge',
        ]);
        expect(buildThemeViewResolveCandidates({ name: 'Layung', slug: 'layung', type: 'frontend' })).toEqual([
            'sarangenge',
        ]);
        expect(buildThemeViewResolveCandidates({ name: 'Sareupna', slug: 'sareupna', type: 'frontend' })).toEqual([
            'sarangenge',
        ]);

        const key = findThemeViewKey(modules, ['janari'], 'pages/Home');
        expect(key).toContain('sarangenge/pages/Home.vue');
    });
});

