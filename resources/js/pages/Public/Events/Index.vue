<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue'; // Gunakan layout publik
import { Head, Link } from '@inertiajs/vue3';
import { Splide, SplideSlide, SplideTrack } from '@splidejs/vue-splide';
import '@splidejs/vue-splide/css'; // Impor CSS Splide
import { QrCode, Ticket } from 'lucide-vue-next';

//================================================================
// TIPE LOKAL
//================================================================
type EventStatus = 'registration' | 'ongoing';
type AttendanceMode = 'ticketing' | 'barcode';
interface Event {
    id: number;
    name: string;
    poster_image: string | null;
    start_date: string;
    end_date: string;
    status: EventStatus;
    attendance_mode: AttendanceMode;
}
interface PageProps {
    ongoingEvents: Event[];
    upcomingBarcodeEvents: Event[];
    upcomingTicketingEvents: Event[];
}

//----------------------------------------------------------------
// SETUP
//----------------------------------------------------------------
defineProps<PageProps>();

const splideOptions = {
    rewind: false,
    gap: '1.5rem',
    perPage: 3,
    perMove: 1,
    pagination: false,
    arrows: true,
    breakpoints: {
        1024: { perPage: 2, gap: '1rem' },
        640: { perPage: 1 },
    },
};

const formatEventDate = (startDateStr: string, endDateStr: string): string => {
    const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    const startDate = new Date(startDateStr);
    const endDate = new Date(endDateStr);
    if (startDate.getTime() === endDate.getTime()) return startDate.toLocaleDateString('id-ID', options);
    return `${startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })} - ${endDate.toLocaleDateString('id-ID', options)}`;
};
</script>

<template>
    <Head title="Semua Kegiatan" />

    <PublicLayout>
        <!-- START: Jumbotron Section -->
        <div class="relative isolate overflow-hidden bg-gray-500 px-6 py-24 text-center shadow-2xl sm:px-16 sm:py-32 dark:bg-gray-900">
            <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl">Semua Kegiatan</h2>
            <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300">
                Jelajahi semua kegiatan yang sedang berlangsung dan yang akan datang. Temukan acara yang paling menarik bagi Anda.
            </p>
            <div
                class="absolute -top-24 left-1/2 -z-10 h-[50rem] w-[50rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]"
                aria-hidden="true"
            >
                <svg viewBox="0 0 1024 1024" class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2">
                    <circle cx="512" cy="512" r="512" fill="url(#8d958450-c69f-4251-94bc-4e091a323369)" fill-opacity="0.7"></circle>
                    <defs>
                        <radialGradient id="8d958450-c69f-4251-94bc-4e091a323369">
                            <stop stop-color="#7775D6"></stop>
                            <stop offset="1" stop-color="#E935C1"></stop>
                        </radialGradient>
                    </defs>
                </svg>
            </div>
        </div>
        <!-- END: Jumbotron Section -->
        <div class="bg-white dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
                <!-- Bagian Sedang Berlangsung -->
                <section class="mb-20">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Sedang Berlangsung</h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Ikuti kegiatan yang sedang berjalan saat ini.</p>
                    <div v-if="ongoingEvents.length > 0" class="mt-8">
                        <Splide :options="splideOptions" :has-track="false">
                            <SplideTrack>
                                <SplideSlide v-for="event in ongoingEvents" :key="event.id">
                                    <Link :href="route('kegiatan.show', { kegiatan: event.id })" class="group block">
                                        <div class="relative overflow-hidden rounded-2xl">
                                            <img
                                                v-if="event.poster_image"
                                                :src="`/storage/${event.poster_image}`"
                                                class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105"
                                            />
                                            <div
                                                v-else
                                                class="flex aspect-[4/3] w-full items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800"
                                            >
                                                <img
                                                    src="https://placehold.co/600x450/e2e8f0/a0aec0?text=Event"
                                                    class="h-full w-full object-cover opacity-50"
                                                />
                                            </div>
                                            <div
                                                class="absolute top-3 right-3 flex items-center gap-1.5 rounded-full bg-white/80 px-3 py-1 text-xs font-semibold text-gray-800 backdrop-blur-sm dark:bg-black/50 dark:text-white"
                                            >
                                                <Ticket v-if="event.attendance_mode === 'ticketing'" class="h-4 w-4" />
                                                <QrCode v-else class="h-4 w-4" />
                                                <span class="capitalize">{{ event.attendance_mode }}</span>
                                            </div>
                                        </div>
                                        <h3 class="mt-4 text-lg leading-6 font-semibold text-gray-900 group-hover:text-indigo-600 dark:text-gray-100">
                                            {{ event.name }}
                                        </h3>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ formatEventDate(event.start_date, event.end_date) }}
                                        </p>
                                    </Link>
                                </SplideSlide>
                            </SplideTrack>
                        </Splide>
                    </div>
                    <p v-else class="mt-8 text-center text-gray-500">Tidak ada event yang sedang berlangsung.</p>
                </section>

                <!-- Bagian Upcoming Barcode -->
                <section class="mb-20">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Kegiatan Terdekat</h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Absensi langsung di lokasi dengan memindai QR Code.</p>
                    <div v-if="upcomingBarcodeEvents.length > 0" class="mt-8">
                        <Splide :options="splideOptions">
                            <SplideSlide v-for="event in upcomingBarcodeEvents" :key="event.id">
                                <Link :href="route('kegiatan.show', { kegiatan: event.id })" class="group block">
                                    <div class="relative overflow-hidden rounded-2xl">
                                        <img
                                            v-if="event.poster_image"
                                            :src="`/storage/${event.poster_image}`"
                                            class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105"
                                        />
                                        <div
                                            v-else
                                            class="flex aspect-[4/3] w-full items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800"
                                        >
                                            <img
                                                src="https://placehold.co/600x450/e2e8f0/a0aec0?text=Event"
                                                class="h-full w-full object-cover opacity-50"
                                            />
                                        </div>
                                    </div>
                                    <h3 class="mt-4 text-lg leading-6 font-semibold text-gray-900 group-hover:text-indigo-600 dark:text-gray-100">
                                        {{ event.name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatEventDate(event.start_date, event.end_date) }}
                                    </p>
                                </Link>
                            </SplideSlide>
                        </Splide>
                    </div>
                    <p v-else class="mt-8 text-center text-gray-500">Tidak ada kegiatan barcode yang akan datang.</p>
                </section>

                <!-- Bagian Ticketing -->
                <section>
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">Pesan Tiket Dahulu</h2>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">Daftar sekarang dan dapatkan e-tiket Anda.</p>
                    <div v-if="upcomingTicketingEvents.length > 0" class="mt-8">
                        <Splide :options="splideOptions">
                            <SplideSlide v-for="event in upcomingTicketingEvents" :key="event.id">
                                <Link :href="route('kegiatan.show', { kegiatan: event.id })" class="group block">
                                    <div class="relative overflow-hidden rounded-2xl">
                                        <img
                                            v-if="event.poster_image"
                                            :src="`/storage/${event.poster_image}`"
                                            class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-105"
                                        />
                                        <div
                                            v-else
                                            class="flex aspect-[4/3] w-full items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800"
                                        >
                                            <img
                                                src="https://placehold.co/600x450/e2e8f0/a0aec0?text=Event"
                                                class="h-full w-full object-cover opacity-50"
                                            />
                                        </div>
                                    </div>
                                    <h3 class="mt-4 text-lg leading-6 font-semibold text-gray-900 group-hover:text-indigo-600 dark:text-gray-100">
                                        {{ event.name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ formatEventDate(event.start_date, event.end_date) }}
                                    </p>
                                </Link>
                            </SplideSlide>
                        </Splide>
                    </div>
                    <p v-else class="mt-8 text-center text-gray-500">Tidak ada kegiatan ticketing yang akan datang.</p>
                </section>
            </div>
        </div>
    </PublicLayout>
</template>
