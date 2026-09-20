import { describe, expect, it } from 'vitest';
import { headerChromeWrapClass, isHeaderStickySetting } from '@/modules/Layout/layouts/headerChromeWrap';

describe('headerChromeWrapClass', () => {
    it('puts sticky on the layout wrapper so Sarangenge / platform chrome can stick', () => {
        expect(headerChromeWrapClass({ sticky: true })).toBe('sticky top-0 z-[100] overflow-visible');
    });

    it('drops sticky when the setting is off', () => {
        expect(headerChromeWrapClass({ sticky: false })).toBe('relative z-50 overflow-visible');
        expect(headerChromeWrapClass({})).toBe('relative z-50 overflow-visible');
    });
});

describe('isHeaderStickySetting', () => {
    it('treats checkbox false-y values as off', () => {
        expect(isHeaderStickySetting(false)).toBe(false);
        expect(isHeaderStickySetting(0)).toBe(false);
        expect(isHeaderStickySetting('false')).toBe(false);
        expect(isHeaderStickySetting(true)).toBe(true);
        expect(isHeaderStickySetting(undefined)).toBe(true);
    });
});
