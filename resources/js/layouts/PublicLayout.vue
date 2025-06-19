<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { useAppearance } from '@/composables/useAppearance';
import useSweetAlert from '@/composables/useSweetAlert';
import { type FlashProps } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { Menu, Monitor, Moon, Sun, X } from 'lucide-vue-next';
import { computed, ref, watchEffect } from 'vue';

// State for mobile menu
const isMobileMenuOpen = ref(false);

/// --- START: Theme Cycling Logic ---
type Appearance = 'light' | 'dark' | 'system';

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
    updateAppearance(tabs[nextIndex].value as Appearance);
}

// --- START: Logika Navigasi Baru ---
const page = usePage();

//----------------------------------------------------------------
// WATCHERS
//----------------------------------------------------------------
const { success, error } = useSweetAlert();

// Watcher ini akan berjalan di setiap halaman yang menggunakan layout ini
watchEffect(() => {
    const flash = page.props as FlashProps;
    if (flash?.flash?.success) {
        success(flash.flash.success);
        // Penting: Setel kembali flash ke null agar tidak terpicu lagi
        page.props.success = null;
    }
    if (flash?.flash?.error) {
        error(flash.flash.error);
        page.props.error = null;
    }
});

// Definisikan item navigasi utama di sini
const navigationItems = computed(() => [
    {
        title: 'Beranda',
        href: route('home'),
        current: page.url === '/',
    },
    {
        title: 'Kegiatan',
        href: route('kegiatan'), // Ganti dengan route('...') jika sudah ada
        current: page.url === '/kegiatan' || page.url.startsWith('/kegiatan/'),
    },
    {
        title: 'Tentang Kami',
        href: route('tentang.kami'),
        current: page.url === '/tentang-kami',
    },
]);
// --- END: Logika Navigasi Baru ---
</script>

<template>
    <div class="bg-white font-sans text-gray-800 dark:bg-gray-900 dark:text-gray-200">
        <!-- Header & Navbar -->
        <header class="absolute top-0 right-0 left-0 z-20">
            <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex lg:flex-1">
                    <Link href="/" class="-m-1.5 flex items-center p-1.5">
                        <AppLogo />
                    </Link>
                </div>
                <div class="flex space-x-2 lg:hidden">
                    <button
                        @click="cycleTheme"
                        class="flex items-center gap-2 rounded-lg bg-white/10 p-1.5 pr-3 text-sm font-medium text-white transition-colors hover:bg-white/20 focus:ring-2 focus:ring-white/50 focus:outline-none"
                    >
                        <div class="rounded-md bg-white/20 p-1 shadow-sm">
                            <component :is="currentTheme.Icon" class="h-5 w-5" />
                        </div>
                        <span>{{ currentTheme.label }}</span>
                    </button>
                    <button
                        type="button"
                        @click="isMobileMenuOpen = true"
                        class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-400"
                    >
                        <span class="sr-only">Buka menu</span>
                        <Menu class="h-6 w-6" />
                    </button>
                </div>
                <!-- Navigasi Desktop (Data-driven) -->
                <div class="hidden lg:flex lg:gap-x-12">
                    <template v-for="item in navigationItems" :key="item.title">
                        <Link
                            v-if="item.href !== '#'"
                            :href="item.href"
                            class="text-sm leading-6 font-semibold text-white"
                            :class="{ 'underline underline-offset-4': item.current }"
                            >{{ item.title }}</Link
                        >
                        <a v-else href="#" class="text-sm leading-6 font-semibold text-white">{{ item.title }}</a>
                    </template>
                </div>
                <div class="hidden lg:flex lg:flex-1 lg:items-center lg:justify-end lg:gap-x-6">
                    <button
                        @click="cycleTheme"
                        class="flex items-center gap-2 rounded-lg bg-white/10 p-1.5 pr-3 text-sm font-medium text-white transition-colors hover:bg-white/20 focus:ring-2 focus:ring-white/50 focus:outline-none"
                    >
                        <div class="rounded-md bg-white/20 p-1 shadow-sm">
                            <component :is="currentTheme.Icon" class="h-5 w-5" />
                        </div>
                        <span>{{ currentTheme.label }}</span>
                    </button>
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-sm leading-6 font-semibold text-white"
                        >Dashboard &rarr;</Link
                    >
                    <Link v-else :href="route('login')" class="text-sm leading-6 font-semibold text-white"
                        >Log in <span aria-hidden="true">&rarr;</span></Link
                    >
                </div>
            </nav>
            <!-- Mobile Menu -->
            <div class="lg:hidden" v-if="isMobileMenuOpen">
                <div class="fixed inset-0 z-20 bg-black/20 backdrop-blur-sm" @click="isMobileMenuOpen = false"></div>
                <div
                    class="fixed inset-y-0 right-0 z-30 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10 dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <Link href="/" class="-m-1.5 p-1.5">
                            <span class="sr-only">App Name</span>
                            <svg
                                class="h-8 w-auto text-gray-800 dark:text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                            >
                                <path
                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"
                                ></path>
                            </svg>
                        </Link>
                        <button type="button" @click="isMobileMenuOpen = false" class="-m-2.5 rounded-md p-2.5 text-gray-700 dark:text-gray-300">
                            <span class="sr-only">Tutup menu</span>
                            <X class="h-6 w-6" />
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10 dark:divide-gray-500/20">
                            <div class="space-y-2 py-6">
                                <!-- Navigasi Mobile (Data-driven) -->
                                <template v-for="item in navigationItems" :key="item.title">
                                    <Link
                                        v-if="item.href !== '#'"
                                        :href="item.href"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base leading-7 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800"
                                        :class="{ 'bg-gray-50 dark:bg-gray-800': item.current }"
                                        >{{ item.title }}</Link
                                    >
                                    <a
                                        v-else
                                        href="#"
                                        class="-mx-3 block rounded-lg px-3 py-2 text-base leading-7 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800"
                                        >{{ item.title }}</a
                                    >
                                </template>
                            </div>
                            <div class="py-6">
                                <Link
                                    v-if="$page.props.auth.user"
                                    :href="route('dashboard')"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base leading-7 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800"
                                    >Dashboard</Link
                                >
                                <Link
                                    v-else
                                    :href="route('login')"
                                    class="-mx-3 block rounded-lg px-3 py-2 text-base leading-7 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800"
                                    >Log in</Link
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- The main content from your pages will be injected here -->
        <main>
            <slot />
        </main>
    </div>
</template>
