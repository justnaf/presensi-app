<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Clock, History } from 'lucide-vue-next';

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
interface EventInfo {
    id: number;
    name: string;
}

interface AttendanceRecord {
    id: number;
    scanned_at: string;
    event: EventInfo;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    history: {
        data: AttendanceRecord[];
        links: PaginationLink[];
    };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();

const formatDateTime = (dateTimeStr: string) => {
    const date = new Date(dateTimeStr);
    const dateOptions: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    const timeOptions: Intl.DateTimeFormatOptions = { hour: '2-digit', minute: '2-digit', hour12: false };

    const formattedDate = date.toLocaleDateString('id-ID', dateOptions);
    const formattedTime = date.toLocaleTimeString('id-ID', timeOptions);

    return {
        date: formattedDate,
        time: formattedTime,
    };
};
</script>

<template>
    <Head title="Riwayat Kehadiran" />
    <MobileLayout>
        <!-- Header Halaman -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Riwayat Kehadiran</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Rekam jejak semua kegiatan yang pernah Anda hadiri.</p>
        </div>

        <!-- Daftar Riwayat -->
        <div class="space-y-4">
            <!-- Pesan jika tidak ada riwayat -->
            <div
                v-if="history.data.length === 0"
                class="rounded-lg border-2 border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800"
            >
                <History class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Belum Ada Riwayat</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum pernah tercatat hadir di kegiatan mana pun.</p>
                <Link
                    :href="route('activities.index')"
                    class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                >
                    Cari Kegiatan
                </Link>
            </div>

            <!-- Loop untuk setiap riwayat -->
            <div v-else v-for="record in history.data" :key="record.id" class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
                <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ record.event.name }}</p>
                <div
                    class="mt-2 flex flex-col gap-2 border-t border-gray-100 pt-3 text-sm text-gray-600 sm:flex-row sm:items-center sm:gap-6 dark:border-gray-700 dark:text-gray-400"
                >
                    <div class="flex items-center gap-2">
                        <Calendar class="h-4 w-4 text-gray-400" />
                        <span>{{ formatDateTime(record.scanned_at).date }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Clock class="h-4 w-4 text-gray-400" />
                        <span>Hadir pukul {{ formatDateTime(record.scanned_at).time }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="history.links.length > 3" class="mt-8 flex justify-center">
            <div class="-mb-1 flex flex-wrap">
                <template v-for="(link, key) in history.links" :key="key">
                    <div v-if="!link.url" class="mr-1 mb-1 rounded border px-4 py-2 text-sm text-gray-400 dark:border-gray-700" v-html="link.label" />
                    <Link
                        v-else
                        class="mr-1 mb-1 rounded border px-4 py-2 text-sm hover:bg-white focus:border-indigo-500 focus:text-indigo-500 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                        :class="{ 'bg-white font-bold dark:bg-gray-700': link.active }"
                        :href="link.url"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </MobileLayout>
</template>
