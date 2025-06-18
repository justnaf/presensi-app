<script setup lang="ts">
import useSweetAlert from '@/composables/useSweetAlert';
import MobileLayout from '@/layouts/MobileLayout.vue';
import type { FlashProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Calendar, CheckCircle, Info, QrCode, Ticket } from 'lucide-vue-next';
import { watch } from 'vue';

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
type AttendanceMode = 'ticketing' | 'barcode';

interface EventCard {
    id: number;
    name: string;
    poster_image: string | null;
    start_date: string;
    end_date: string;
    attendance_mode: AttendanceMode;
    is_registered?: boolean; // Add the new optional property
}

interface PageProps {
    ongoingEvents: EventCard[];
    upcomingEvents: EventCard[];
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const { success, error } = useSweetAlert();
const page = usePage();
watch(
    () => page.props as FlashProps,
    (flash) => {
        if (flash.flash?.success) success(flash.flash.success);
        if (flash.flash?.error) error(flash.flash.error);
    },
    { deep: true },
);
const props = defineProps<PageProps>();

const formatEventDate = (startDateStr: string, endDateStr: string): string => {
    const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    const startDate = new Date(startDateStr);
    const endDate = new Date(endDateStr);
    if (startDate.getTime() === endDate.getTime()) {
        return startDate.toLocaleDateString('id-ID', options);
    }
    return `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', options)}`;
};
</script>

<template>
    <Head title="Aktivitas Saya" />
    <MobileLayout>
        <!-- Bagian Event Sedang Berlangsung -->
        <div class="mb-8">
            <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-gray-100">Sedang Berlangsung</h2>
            <div class="space-y-4">
                <div
                    v-if="ongoingEvents.length === 0"
                    class="rounded-lg border-2 border-dashed border-gray-300 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800"
                >
                    <Info class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Tidak Ada Event</h3>
                    <p class="mt-1 text-sm text-gray-500">Tidak ada event yang sedang berlangsung saat ini.</p>
                </div>

                <div v-else v-for="event in ongoingEvents" :key="event.id" class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <Link :href="route('activities.show', event.id)">
                        <img v-if="event.poster_image" :src="`/storage/${event.poster_image}`" :alt="event.name" class="h-40 w-full object-cover" />
                        <div v-else class="flex h-40 w-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                            <img
                                src="https://placehold.co/600x300/e2e8f0/a0aec0?text=Event"
                                alt="Placeholder"
                                class="h-full w-full object-cover opacity-50"
                            />
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatEventDate(event.start_date, event.end_date) }}</p>
                            <h3 class="mt-1 font-semibold text-gray-800 dark:text-gray-200">{{ event.name }}</h3>
                        </div>
                    </Link>
                    <div class="border-t border-gray-100 p-4 dark:border-gray-700">
                        <Link
                            v-if="event.attendance_mode === 'ticketing'"
                            :href="route('activities.show', event.id)"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-500"
                        >
                            <Ticket class="h-4 w-4" />
                            Lihat Tiket
                        </Link>
                        <Link
                            v-else
                            :href="route('activities.show', event.id)"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-500"
                        >
                            <QrCode class="h-4 w-4" />
                            Absen Sekarang
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Event Akan Datang -->
        <div>
            <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-gray-100">Akan Datang</h2>
            <div class="space-y-4">
                <div
                    v-if="upcomingEvents.length === 0"
                    class="rounded-lg border-2 border-dashed border-gray-300 bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800"
                >
                    <Calendar class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Tidak Ada Event</h3>
                    <p class="mt-1 text-sm text-gray-500">Nantikan kegiatan menarik lainnya!</p>
                </div>

                <div v-else v-for="event in upcomingEvents" :key="event.id" class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                    <Link :href="route('activities.show', event.id)">
                        <img v-if="event.poster_image" :src="`/storage/${event.poster_image}`" :alt="event.name" class="h-40 w-full object-cover" />
                        <div v-else class="flex h-40 w-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                            <img
                                src="https://placehold.co/600x300/e2e8f0/a0aec0?text=Event"
                                alt="Placeholder"
                                class="h-full w-full object-cover opacity-50"
                            />
                        </div>
                        <div class="p-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatEventDate(event.start_date, event.end_date) }}</p>
                            <h3 class="mt-1 font-semibold text-gray-800 dark:text-gray-200">{{ event.name }}</h3>
                        </div>
                    </Link>
                    <div class="border-t border-gray-100 p-4 dark:border-gray-700">
                        <!-- Conditional Action Button Block -->
                        <div
                            v-if="event.is_registered"
                            class="flex items-center justify-center gap-2 rounded-lg bg-green-100 px-4 py-2.5 text-sm font-semibold text-green-800 dark:bg-green-900 dark:text-green-300"
                        >
                            <CheckCircle class="h-4 w-4" />
                            Sudah Terdaftar
                        </div>
                        <template v-else>
                            <Link
                                v-if="event.attendance_mode === 'ticketing'"
                                :href="route('activities.join', event.id)"
                                method="post"
                                as="button"
                                preserve-scroll
                                class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                            >
                                <Ticket class="h-4 w-4" />
                                Daftar Sekarang
                            </Link>
                            <div
                                v-else
                                class="inline-flex w-full items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                            >
                                Upcoming
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </MobileLayout>
</template>
