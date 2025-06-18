<script setup>
// Semua logika dan impor yang berhubungan dengan Layout dipindahkan ke sini
import AppLogo from '@/components/AppLogo.vue';
import { useAppearance } from '@/composables/useAppearance';
import { Link, usePage } from '@inertiajs/vue3';
import { AlignRight, ClipboardList, House, Monitor, Moon, QrCode, Sun, Ticket, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// State untuk menu dropdown header
const showHeaderNav = ref(false);

// Logika untuk tema (dark mode)
const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' },
];
const { appearance, updateAppearance } = useAppearance();
const currentTheme = computed(() => {
    return tabs.find((tab) => tab.value === appearance.value) ?? tabs[2];
});
function cycleTheme() {
    const currentIndex = tabs.findIndex((tab) => tab.value === appearance.value);
    const nextIndex = (currentIndex + 1) % tabs.length;
    updateAppearance(tabs[nextIndex].value);
}

// Logika untuk navigasi bawah (menandai yang aktif)
const page = usePage();
const navigation = computed(() => [
    { name: 'Beranda', href: route('dashboard'), icon: House, current: page.url.startsWith('/dashboard') },
    { name: 'Aktivitas', href: route('activities.index'), icon: ClipboardList, current: page.url.startsWith('/activities') },
    { name: 'Ticket', href: route('histories.my-tickets'), icon: Ticket, current: page.url.startsWith('/histories') },
    { name: 'QR Scanner', href: route('scanner'), icon: QrCode, current: page.url.startsWith('/scanner') },
    { name: 'Profil', href: '#', icon: User, current: false },
]);
</script>

<template>
    <div class="flex min-h-screen flex-col bg-gray-100 font-sans dark:bg-gray-900">
        <header class="sticky top-0 z-20 bg-white px-5 py-4 shadow-md dark:border-b dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <AppLogo />
                </div>
                <div class="flex items-center space-x-2">
                    <button
                        @click="cycleTheme"
                        class="flex items-center gap-2 rounded-lg bg-neutral-100 p-1.5 pr-3 transition-colors hover:bg-neutral-200 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-neutral-800 dark:hover:bg-neutral-700"
                    >
                        <div class="rounded-md bg-white p-1 shadow-sm dark:bg-neutral-700">
                            <component :is="currentTheme.Icon" class="h-5 w-5 text-neutral-700 dark:text-neutral-100" />
                        </div>
                        <span class="text-sm font-medium text-neutral-600 dark:text-neutral-300">{{ currentTheme.label }}</span>
                    </button>
                    <button
                        @click="showHeaderNav = !showHeaderNav"
                        class="rounded-full p-1.5 hover:bg-gray-200 focus:outline-none dark:hover:bg-gray-700"
                    >
                        <AlignRight class="h-6 w-6 text-gray-600 dark:text-gray-300" />
                    </button>
                </div>
            </div>
            <nav v-if="showHeaderNav" class="mt-4 border-t pt-2 dark:border-gray-700">
                <Link href="#" class="block rounded-md px-2 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                    >Profil Saya</Link
                >
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    type="button"
                    class="block w-full rounded-md px-2 py-2 text-left text-red-500 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/20"
                    >Keluar</Link
                >
            </nav>
        </header>

        <main class="flex-grow overflow-y-auto p-5 pb-24">
            <slot />
            <slot name="footer" />
        </main>

        <nav class="fixed right-0 bottom-0 left-0 z-10 border-t border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
            <div class="grid grid-cols-5">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    :class="[
                        item.current
                            ? 'border-t-2 border-blue-600 text-blue-600 dark:border-blue-400 dark:text-blue-400'
                            : 'text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400',
                        'py-3 text-center',
                    ]"
                >
                    <component :is="item.icon" class="mx-auto h-6 w-6" />
                    <span class="block text-xs" :class="{ 'font-semibold': item.current }">{{ item.name }}</span>
                </Link>
            </div>
        </nav>
    </div>
</template>
