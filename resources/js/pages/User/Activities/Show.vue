<script setup lang="ts">
// Impor dari Vue & Inertia
import useSweetAlert from '@/composables/useSweetAlert';
import { FlashProps } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { Calendar, CheckCircle, ExternalLink, MapPin, Ticket, User, XCircle } from 'lucide-vue-next';
import { onMounted, watch } from 'vue';

// Impor Leaflet dan asetnya
import L, { LatLng } from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Impor Layout dan SweetAlert
import MobileLayout from '@/layouts/MobileLayout.vue';
import Swal from 'sweetalert2'; // 1. Impor Swal secara langsung

// Konfigurasi path ikon default Leaflet
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

//================================================================
// DEFINISI TIPE
//================================================================
type EventStatus = 'draft' | 'registration' | 'ongoing' | 'completed';
type AttendanceMode = 'ticketing' | 'barcode';

interface EventCategory {
    id: number;
    name: string;
}
interface Rundown {
    title: string;
    start_time: string;
    end_time: string;
    description: string | null;
}
interface EventData {
    id: number;
    name: string;
    description: string | null;
    about: string | null;
    location_name: string | null;
    location_url: string | null;
    latitude: number | null;
    longitude: number | null;
    speaker: string | null;
    poster_image: string | null;
    start_date: string;
    end_date: string;
    category: EventCategory | null;
    rundowns: Rundown[];
    status: EventStatus;
    attendance_mode: AttendanceMode;
}
interface PageProps {
    event: EventData;
    isRegistered: boolean;
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

// 2. Perbarui fungsi joinEvent dengan dialog konfirmasi
const joinEvent = () => {
    Swal.fire({
        title: 'Konfirmasi Pendaftaran',
        text: `Anda akan mendaftar untuk event "${props.event.name}". Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5', // Warna Indigo
        cancelButtonColor: '#6b7280', // Warna Abu-abu
        confirmButtonText: 'Ya, Daftar!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        // Hanya kirim request jika pengguna mengonfirmasi
        if (result.isConfirmed) {
            router.post(
                route('activities.join', props.event.id), // Menggunakan route yang benar
                {},
                {
                    preserveScroll: true,
                },
            );
        }
    });
};

const formatFullDate = (dateStr: string) =>
    new Date(dateStr).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
const formatTime = (dateTimeStr: string) => new Date(dateTimeStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

//----------------------------------------------------------------
// LOGIKA PETA LEAFLET
//----------------------------------------------------------------
const initMap = () => {
    if (props.event.latitude != null && props.event.longitude != null) {
        const eventCoords = new LatLng(props.event.latitude, props.event.longitude);
        const map = L.map('map-container', {
            scrollWheelZoom: false,
            touchZoom: 'center',
        }).setView(eventCoords, 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
        L.marker(eventCoords)
            .addTo(map)
            .bindPopup(`<b>${props.event.name}</b><br>${props.event.location_name || ''}`)
            .openPopup();
    }
};

onMounted(() => {
    initMap();
});
</script>

<template>
    <Head>
        <title>{{ event.name }}</title>
        <!-- Muat CSS Leaflet -->
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    </Head>

    <MobileLayout>
        <!-- Poster Event -->
        <div class="relative -mx-4 -mt-4">
            <img v-if="event.poster_image" :src="`/storage/${event.poster_image}`" :alt="event.name" class="h-60 w-full object-cover" />
            <div v-else class="flex h-60 w-full items-center justify-center bg-gray-200 dark:bg-gray-700">
                <img src="https://placehold.co/600x400/e2e8f0/a0aec0?text=Event" alt="Placeholder" class="h-full w-full object-cover opacity-50" />
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-100 via-gray-100/50 to-transparent dark:from-gray-900 dark:via-gray-900/50"></div>
        </div>

        <!-- Konten Utama -->
        <div class="p-4">
            <!-- Judul & Kategori -->
            <div class="relative -mt-12">
                <span
                    v-if="event.category"
                    class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300"
                >
                    {{ event.category.name }}
                </span>
                <h1 class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ event.name }}</h1>
                <p class="mt-1 text-gray-600 dark:text-gray-400">{{ event.description }}</p>
            </div>

            <!-- Detail Info -->
            <div class="mt-6 space-y-4 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-4">
                    <Calendar class="h-5 w-5 flex-shrink-0 text-indigo-500" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ formatFullDate(event.start_date) }}</span>
                </div>
                <div v-if="event.location_name" class="flex items-start gap-4">
                    <MapPin class="h-5 w-5 flex-shrink-0 text-indigo-500" />
                    <!-- Tautan Lokasi -->
                    <a
                        v-if="event.location_url"
                        :href="event.location_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="group text-sm text-gray-700 dark:text-gray-300"
                    >
                        {{ event.location_name }}
                        <ExternalLink class="inline-block h-3 w-3 text-gray-400 opacity-0 transition-opacity group-hover:opacity-100" />
                    </a>
                    <span v-else class="text-sm text-gray-700 dark:text-gray-300">{{ event.location_name }}</span>
                </div>
                <div v-if="event.speaker" class="flex items-center gap-4">
                    <User class="h-5 w-5 flex-shrink-0 text-indigo-500" />
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ event.speaker }}</span>
                </div>
            </div>

            <!-- Peta Lokasi (hanya tampil jika ada koordinat) -->
            <div v-if="event.latitude && event.longitude" class="mt-8">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Lokasi di Peta</h2>
                <div id="map-container" class="z-0 mt-2 h-64 w-full rounded-lg shadow-md"></div>
            </div>

            <!-- Tentang Event -->
            <div v-if="event.about" class="mt-8">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Tentang Event</h2>
                <div class="prose prose-sm mt-2 max-w-none text-gray-600 dark:text-gray-400" v-html="event.about"></div>
            </div>

            <!-- Rundown Acara -->
            <div v-if="event.rundowns.length > 0" class="mt-8">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Jadwal Acara</h2>
                <ul role="list" class="mt-4 space-y-4">
                    <li v-for="(item, index) in event.rundowns" :key="index" class="flex gap-x-3">
                        <div class="flex-none text-right text-sm leading-6 font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ formatTime(item.start_time) }}
                        </div>
                        <div class="flex-auto rounded-md border-l-2 border-indigo-600 p-2 dark:border-indigo-400">
                            <div class="font-semibold text-gray-900 dark:text-gray-100">{{ item.title }}</div>
                            <div v-if="item.description" class="mt-1 text-gray-500">{{ item.description }}</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tombol Aksi (Footer) -->
        <template #footer>
            <div class="p-4">
                <!-- Status 1: Sudah Terdaftar -->
                <div
                    v-if="isRegistered"
                    class="flex items-center justify-center gap-2 rounded-lg bg-green-100 px-4 py-3 text-sm font-semibold text-green-800 dark:bg-green-900 dark:text-green-300"
                >
                    <CheckCircle class="h-5 w-5" />
                    Anda Sudah Terdaftar
                </div>

                <!-- Status 2: Pendaftaran Dibuka -->
                <button
                    v-else-if="event.status === 'registration' && event.attendance_mode === 'ticketing'"
                    @click="joinEvent"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    <Ticket class="h-5 w-5" />
                    Daftar Sekarang
                </button>

                <!-- Status 3: Kondisi Lain (Pendaftaran ditutup, event selesai, dll) -->
                <div
                    v-else
                    class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-400"
                >
                    <XCircle class="h-5 w-5" />
                    Pendaftaran Ditutup
                </div>
            </div>
        </template>
    </MobileLayout>
</template>
