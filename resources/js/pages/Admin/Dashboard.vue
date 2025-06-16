<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Shield, University } from 'lucide-vue-next';
import PlaceholderPattern from '../../components/PlaceholderPattern.vue';

interface PageProps {
    stats: {
        rolesCount: number;
        institutionsCount: number;
    };
}

// Accept the props from the controller
const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <!-- Card 1: Total Roles -->
                <div
                    class="relative flex flex-col justify-between overflow-hidden rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div>
                        <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400">Total Roles</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ props.stats.rolesCount }}</p>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <Shield :size="80" :stroke-width="2" class="text-gray-900 dark:text-gray-100" />
                    </div>
                </div>

                <!-- Card 2: Total Institutions -->
                <div
                    class="relative flex flex-col justify-between overflow-hidden rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div>
                        <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400">Total Institutions</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ props.stats.institutionsCount }}</p>
                    </div>
                    <!-- Decorative Icon -->
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <University :size="80" :stroke-width="2" class="text-gray-900 dark:text-gray-100" />
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template>
