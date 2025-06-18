<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { History, Ticket, TicketCheck } from 'lucide-vue-next';

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
interface EventInfo {
    id: number;
    name: string;
    poster_image: string | null;
    start_date: string;
    end_date: string;
}

interface TicketData {
    id: number;
    ticket_code: string;
    registered_at: string;
    event: EventInfo;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    tickets: {
        data: TicketData[];
        links: PaginationLink[];
    };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
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
    <Head title="Tiket Saya" />
    <MobileLayout>
        <!-- Header Halaman -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Tiket Saya</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Tiket Anda untuk event yang akan datang.</p>
                </div>
                <!-- Tombol Riwayat Kehadiran (Updated) -->
                <Link
                    :href="route('histories.my-tickets.attendances')"
                    class="inline-flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-sm font-semibold whitespace-nowrap text-gray-700 shadow-sm transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                    title="Riwayat Kehadiran"
                >
                    <History class="h-4 w-4" />
                    <span>Riwayat Kehadiran</span>
                </Link>
            </div>
        </div>

        <!-- Daftar Kartu Tiket -->
        <div class="space-y-4">
            <!-- Pesan jika tidak ada tiket -->
            <div
                v-if="tickets.data.length === 0"
                class="rounded-lg border-2 border-dashed border-gray-300 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800"
            >
                <TicketCheck class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Tidak Ada Tiket</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum terdaftar di kegiatan mana pun.</p>
                <Link
                    :href="route('user.events.index')"
                    class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                >
                    Cari Kegiatan
                </Link>
            </div>

            <!-- Loop untuk setiap tiket -->
            <div v-else v-for="ticket in tickets.data" :key="ticket.id" class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
                <!-- The main card body now correctly links to the ticket show page -->
                <Link :href="route('histories.my-tickets.show', ticket.id)">
                    <div class="flex items-start gap-4 p-4">
                        <img
                            v-if="ticket.event.poster_image"
                            :src="`/storage/${ticket.event.poster_image}`"
                            :alt="ticket.event.name"
                            class="h-20 w-20 flex-shrink-0 rounded-md object-cover"
                        />
                        <div v-else class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-700">
                            <img src="https://placehold.co/100x100/e2e8f0/a0aec0?text=Event" alt="Placeholder" class="h-full w-full object-cover" />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ formatEventDate(ticket.event.start_date, ticket.event.end_date) }}
                            </p>
                            <h3 class="mt-1 font-semibold text-gray-800 dark:text-gray-200">{{ ticket.event.name }}</h3>
                        </div>
                    </div>
                </Link>
                <div class="border-t border-gray-100 p-4 dark:border-gray-700">
                    <!-- The button also links to the ticket show page -->
                    <Link
                        :href="route('histories.my-tickets.show', ticket.id)"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-500"
                    >
                        <Ticket class="h-4 w-4" />
                        Lihat Tiket
                    </Link>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="tickets.links.length > 3" class="mt-8 flex justify-center">
            <div class="-mb-1 flex flex-wrap">
                <template v-for="(link, key) in tickets.links" :key="key">
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
