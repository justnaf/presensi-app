<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue'; // Import the new layout
import { Head, Link } from '@inertiajs/vue3';
import { CalendarDays, QrCode, Ticket } from 'lucide-vue-next';
import { computed } from 'vue';

//================================================================
// TYPE DEFINITIONS
//================================================================
type EventStatus = 'registration' | 'ongoing' | 'completed' | 'draft';

interface Event {
    id: number;
    name: string;
    poster_image: string | null;
    start_date: string;
    end_date: string;
    status: EventStatus;
}

interface PageProps {
    events: Event[];
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

const statusBadge = computed(() => (status: EventStatus) => {
    switch (status) {
        case 'ongoing':
            return { text: 'Sedang Berlangsung', class: 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-900/50 dark:text-green-300' };
        case 'registration':
        default:
            return {
                text: 'Pendaftaran Dibuka',
                class: 'bg-indigo-50 text-indigo-700 ring-indigo-600/20 dark:bg-indigo-900/50 dark:text-indigo-300',
            };
    }
});
</script>

<template>
    <Head title="Selamat Datang">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <PublicLayout>
        <!-- Jumbotron / Hero Section -->
        <div
            class="relative isolate overflow-hidden bg-gray-500 px-6 pt-20 shadow-2xl sm:px-16 md:pt-24 lg:flex lg:gap-x-20 lg:px-24 lg:pt-0 dark:bg-gray-900"
        >
            <div class="mx-auto max-w-md text-center lg:mx-0 lg:flex-shrink-0 lg:py-32 lg:text-left">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">Temukan & Ikuti Kegiatan Favoritmu</h1>
                <p class="mt-6 text-lg leading-8 text-gray-300">
                    Platform manajemen event modern untuk pendaftaran yang mudah, absensi cepat, dan pengalaman tak terlupakan.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6 lg:justify-start">
                    <Link
                        :href="route('kegiatan')"
                        class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400"
                        >Cari Kegiatan</Link
                    >
                    <Link :href="route('register')" class="text-sm leading-6 font-semibold text-white"
                        >Daftar Akun <span aria-hidden="true">→</span></Link
                    >
                </div>
            </div>
            <div class="relative mx-auto mt-16 h-80 w-full max-w-2xl lg:mt-0 lg:h-auto">
                <img
                    class="absolute inset-0 h-full w-full object-cover"
                    src="https://plus.unsplash.com/premium_photo-1680985551058-2759df426d0d?q=80&w=1035&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                    alt="App screenshot"
                />
            </div>
        </div>

        <!-- Keunggulan Aplikasi -->
        <section class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-base leading-7 font-semibold text-indigo-600 dark:text-indigo-400">Semua Jadi Mudah</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-gray-100">Keunggulan Aplikasi Kami</p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold">
                                <Ticket class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                Pendaftaran Cepat
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Daftar ke event favoritmu hanya dengan beberapa klik. Dapatkan e-tiket instan tanpa repot.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold">
                                <QrCode class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                Absensi Modern
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">Gunakan QR Code untuk check-in di lokasi acara. Cepat, aman, dan mengurangi antrean.</p>
                            </dd>
                        </div>
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold">
                                <CalendarDays class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                Jadwal Terorganisir
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-300">
                                <p class="flex-auto">
                                    Jangan lewatkan event penting. Lihat semua jadwal kegiatan yang akan datang dan yang sudah diikuti di satu tempat.
                                </p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <!-- Informasi Kegiatan -->
        <section class="bg-gray-100 py-24 sm:py-32 dark:bg-gray-900/50">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-gray-100">Kegiatan Terbaru</h2>
                    <a href="#" class="text-sm leading-6 font-semibold text-indigo-600 dark:text-indigo-400"
                        >Lihat semua <span aria-hidden="true">→</span></a
                    >
                </div>
                <div class="mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                    <article v-for="event in events" :key="event.id" class="flex flex-col items-start justify-between">
                        <Link href="#" class="w-full">
                            <div class="relative w-full">
                                <img
                                    v-if="event.poster_image"
                                    :src="`/storage/${event.poster_image}`"
                                    class="aspect-[16/9] w-full rounded-2xl bg-gray-100 object-cover sm:aspect-[2/1] lg:aspect-[3/2]"
                                />
                                <div v-else class="flex aspect-[16/9] w-full items-center justify-center rounded-2xl bg-gray-100 dark:bg-gray-800">
                                    <img
                                        src="https://placehold.co/600x400/e2e8f0/a0aec0?text=Event"
                                        class="h-full w-full rounded-2xl object-cover opacity-50"
                                    />
                                </div>
                                <div class="absolute inset-0 rounded-2xl ring-1 ring-gray-900/10 ring-inset"></div>
                            </div>
                            <div class="max-w-xl">
                                <div class="mt-8 flex items-center gap-x-4 text-xs">
                                    <time :datetime="event.start_date" class="text-gray-500">{{
                                        formatEventDate(event.start_date, event.end_date)
                                    }}</time>
                                    <span
                                        class="relative z-10 rounded-full px-3 py-1.5 font-medium ring-1 ring-inset"
                                        :class="statusBadge(event.status).class"
                                    >
                                        {{ statusBadge(event.status).text }}
                                    </span>
                                </div>
                                <div class="group relative">
                                    <h3
                                        class="mt-3 text-lg leading-6 font-semibold text-gray-900 group-hover:text-gray-600 dark:text-gray-100 dark:group-hover:text-gray-300"
                                    >
                                        {{ event.name }}
                                    </h3>
                                </div>
                            </div>
                        </Link>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
