<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    items: NavItem[];
    label?: string;
    customClass?: string;
}>();

const page = usePage();
const openDropdowns = ref<Record<string, boolean>>({});

function toggleDropdown(title: string) {
    openDropdowns.value[title] = !openDropdowns.value[title];
}
</script>

<template>
    <SidebarGroup class="px-2 py-0" :class="customClass">
        <SidebarGroupLabel v-if="label">{{ label }}</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- If item has children, render a dropdown -->
                <SidebarMenuItem v-if="item.children">
                    <SidebarMenuButton
                        @click="toggleDropdown(item.title)"
                        class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                    >
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                        <ChevronDown class="ml-auto h-5 w-5 transition-transform" :class="{ 'rotate-180': openDropdowns[item.title] }" />
                    </SidebarMenuButton>

                    <transition
                        enter-active-class="transition-all duration-200 ease-out"
                        leave-active-class="transition-all duration-200 ease-in"
                        enter-from-class="max-h-0 opacity-0"
                        enter-to-class="max-h-screen opacity-100"
                        leave-from-class="max-h-screen opacity-100"
                        leave-to-class="max-h-0 opacity-0"
                    >
                        <div v-if="openDropdowns[item.title]" class="overflow-hidden">
                            <!-- Recursively use NavMain for sub-items -->
                            <NavMain :items="item.children" class="pt-2 pl-4" />
                        </div>
                    </transition>
                </SidebarMenuItem>

                <!-- Otherwise, render a regular link -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                        <Link :href="item.href!">
                            <component :is="item.icon" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
