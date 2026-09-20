import type { RouteRecordRaw } from 'vue-router';

const infraRoutes: RouteRecordRaw[] = [
    {
        path: 'infra/file-manager',
        name: 'file-manager',
        component: () => import('@/modules/Core/Infra/views/file-manager/Index.vue'),
        meta: {
            permission: 'manage files',
            title: 'infra.fileManager.title',
            breadcrumb: 'infra.fileManager.title',
        },
    },
    {
        path: 'file-manager',
        name: 'file-manager-alias',
        component: () => import('@/modules/Core/Infra/views/file-manager/Index.vue'),
        meta: {
            permission: 'manage files',
            title: 'infra.fileManager.title',
            breadcrumb: 'infra.fileManager.title',
        },
    },
];

export default infraRoutes;
