<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CalendarDays,
    CalendarPlus,
    Database,
    LayoutGrid,
    QrCode,
    ScanLine,
    Shield,
    Tag,
    Ticket,
    University,
    User,
    UserCheck,
    UsersRound,
} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

// All navigation is now in one data structure
const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Institutions',
        href: '/admin/institutions',
        icon: University,
    },
    // This is now a dropdown parent item
    {
        title: 'User Settings',
        icon: UsersRound,
        children: [
            {
                title: 'Users',
                href: '/admin/users',
                icon: User,
            },
            {
                title: 'Roles',
                href: '/admin/roles',
                icon: Shield,
            },
        ],
        href: '',
    },
    {
        title: 'Events',
        href: '',
        icon: CalendarDays,
        children: [
            {
                title: 'Manage Categories',
                href: '/admin/event-categories',
                icon: Tag,
            },
            {
                title: 'Manage Events',
                href: '/admin/events',
                icon: CalendarPlus,
            },
            {
                title: 'QR Data',
                href: '/admin/events/static-qrs',
                icon: QrCode,
            },
            {
                title: 'Ticket Scanner',
                href: '/admin/events/scanner',
                icon: ScanLine,
            },
        ],
    },
    {
        title: 'Data Center',
        href: '',
        icon: Database,
        children: [
            {
                title: 'Event Attendees',
                href: '/admin/data/attendees',
                icon: Ticket,
            },
            {
                title: 'Event Attendences',
                href: '/admin/data/attendances',
                icon: UserCheck,
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Documentation',
        href: '',
        external: true,
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- NavMain now handles the dropdown logic automatically -->
            <NavMain :items="mainNavItems" label="Main Navigation" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
