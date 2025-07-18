<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();
const page = usePage();
</script>

<template>
    <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`">
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuItem>
                        <!-- 2. If the link is external, render a standard <a> tag -->
                        <SidebarMenuButton
                            v-if="item.external"
                            class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                            as-child
                        >
                            <a :href="item.href" target="_blank" rel="noopener noreferrer">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </a>
                        </SidebarMenuButton>

                        <!-- 3. Otherwise (if internal), render Inertia's <Link> component -->
                        <SidebarMenuButton
                            v-else
                            class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
                            as-child
                            :is-active="item.href === page.url"
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
