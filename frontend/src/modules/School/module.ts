import { defineAsyncComponent } from 'vue';
import type { AppModule } from '@/engine/types/module';
import schoolRoutes from './router';
import schoolNavigation from './navigation';

export const SchoolModule: AppModule = {
    id: 'school',
    name: 'School Information System',
    routes: schoolRoutes,
    navigation: schoolNavigation,
    dashboards: [
        {
            id: 'school-admin-dashboard',
            priority: 95,
            routeName: 'school.dashboard',
            condition: () => true,
            component: defineAsyncComponent(() => import('./views/SchoolDashboard.vue')),
        },
    ],
};

export default SchoolModule;
