<script setup lang="ts">
// Vue & Inertia Imports
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

// Layout & Composables
import PublicLayout from '@/layouts/PublicLayout.vue';
import Swal from 'sweetalert2'; // Import Swal untuk dialog kustom

// Leaflet Imports
import L, { LatLng } from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import 'leaflet/dist/leaflet.css';

// Icon Imports
import { Calendar, CheckCircle, ExternalLink, MapPin, Ticket, User, XCircle } from 'lucide-vue-next';

//================================================================
// TYPE DEFINITIONS
//================================================================
type EventStatus = 'registration' | 'ongoing' | 'completed' | 'draft';
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
    type: string | null;
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
// Tambahkan 'auth' ke PageProps
interface UserInfo {
    id: number;
    name: string;
}
interface PageProps {
    event: EventData;
    isRegistered: boolean;
    auth: {
        user: UserInfo | null;
    };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const activeTab = ref('about'); // State for the active tab

// Dapatkan data user dari page props
const user = computed(() => usePage().props.auth.user);

// Leaflet Icon Configuration
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

//----------------------------------------------------------------
// FUNCTIONS
//----------------------------------------------------------------
const joinEvent = () => {
    // Cek jika pengguna belum login
    if (!user.value) {
        Swal.fire({
            title: 'Anda Belum Login',
            text: 'Untuk mendaftar, silakan masuk ke akun Anda atau buat akun baru terlebih dahulu.',
            icon: 'info',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Login',
            denyButtonText: `Buat Akun`,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#4f46e5',
            denyButtonColor: '#10b981',
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('login'));
            } else if (result.isDenied) {
                router.visit(route('register'));
            }
        });
        return;
    }

    // FIX: Use Swal.fire directly for registration confirmation
    Swal.fire({
        title: 'Konfirmasi Pendaftaran',
        text: `Anda akan mendaftar untuk event "${props.event.name}". Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#4f46e5',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Daftar!',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route('user.events.join', props.event.id), {}, { preserveScroll: true });
        }
    });
};

const formatDateRange = (start: string, end: string) =>
    new Date(start).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) +
    (start !== end ? ' - ' + new Date(end).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '');
const formatTime = (dateTimeStr: string) => new Date(dateTimeStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

const initMap = () => {
    if (props.event.latitude != null && props.event.longitude != null) {
        const coords = new LatLng(props.event.latitude, props.event.longitude);
        const map = L.map('map-container', { scrollWheelZoom: false }).setView(coords, 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker(coords).addTo(map).bindPopup(`<b>${props.event.name}</b>`).openPopup();
    }
};

onMounted(() => initMap());
</script>

<template>
    <Head :title="event.name" />
    <PublicLayout>
        <!-- START: Jumbotron Section -->
        <div class="relative isolate overflow-hidden bg-gray-500 px-6 py-14 text-center shadow-2xl sm:px-16 sm:py-12 dark:bg-gray-900"></div>
        <!-- END: Jumbotron Section -->
        <div class="bg-gray-50 dark:bg-gray-950">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-3">
                    <div class="lg:col-span-1">
                        <div class="sticky top-24">
                            <img
                                v-if="event.poster_image"
                                :src="`/storage/${event.poster_image}`"
                                :alt="event.name"
                                class="aspect-[3/4] w-full rounded-2xl object-cover shadow-lg"
                            />
                            <div v-else class="flex aspect-[3/4] w-full items-center justify-center rounded-2xl bg-gray-200 dark:bg-gray-800">
                                <img
                                    src="https://placehold.co/600x800/e2e8f0/a0aec0?text=Event"
                                    class="h-full w-full rounded-2xl object-cover opacity-50"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <span
                                v-if="event.category"
                                class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300"
                                >{{ event.category.name }}</span
                            >
                            <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">{{ event.name }}</h1>
                            <p class="text-lg text-gray-600 dark:text-gray-400">{{ event.description }}</p>
                        </div>

                        <div class="mt-8 space-y-4 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center gap-4">
                                <Ticket class="h-6 w-6 flex-shrink-0 text-indigo-500" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tipe Event</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">{{ event.type }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <Calendar class="h-6 w-6 flex-shrink-0 text-indigo-500" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
                                    <p class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ formatDateRange(event.start_date, event.end_date) }}
                                    </p>
                                </div>
                            </div>
                            <div v-if="event.location_name" class="flex items-center gap-4">
                                <MapPin class="h-6 w-6 flex-shrink-0 text-indigo-500" />
                                <div>
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi</p>
                                    <a
                                        v-if="event.location_url"
                                        :href="event.location_url"
                                        target="_blank"
                                        class="group font-semibold text-gray-800 hover:text-indigo-600 dark:text-gray-200 dark:hover:text-indigo-400"
                                    >
                                        {{ event.location_name }}
                                        <ExternalLink class="inline-block h-4 w-4 opacity-50 transition group-hover:opacity-100" />
                                    </a>
                                    <p v-else class="font-semibold text-gray-800 dark:text-gray-200">{{ event.location_name }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="event.latitude && event.longitude" class="mt-8">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Lokasi di Peta</h2>
                            <div id="map-container" class="z-0 mt-4 h-80 w-full rounded-lg shadow-md"></div>
                        </div>
                        <div class="mt-12">
                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    <button
                                        @click="activeTab = 'about'"
                                        :class="[
                                            activeTab === 'about'
                                                ? 'border-indigo-500 text-indigo-600'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                                        ]"
                                    >
                                        Tentang Event
                                    </button>
                                    <button
                                        @click="activeTab = 'rundown'"
                                        v-if="event.rundowns.length > 0"
                                        :class="[
                                            activeTab === 'rundown'
                                                ? 'border-indigo-500 text-indigo-600'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                                        ]"
                                    >
                                        Jadwal Acara
                                    </button>
                                    <button
                                        @click="activeTab = 'speaker'"
                                        v-if="event.speaker"
                                        :class="[
                                            activeTab === 'speaker'
                                                ? 'border-indigo-500 text-indigo-600'
                                                : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:hover:text-gray-300',
                                            'border-b-2 px-1 py-4 text-sm font-medium whitespace-nowrap',
                                        ]"
                                    >
                                        Pembicara
                                    </button>
                                </nav>
                            </div>
                            <div class="py-6">
                                <div
                                    v-if="activeTab === 'about'"
                                    class="prose prose-sm dark:prose-invert max-w-none"
                                    v-html="event.about || '<p>Informasi lebih lanjut mengenai event ini akan segera tersedia.</p>'"
                                ></div>

                                <div v-if="activeTab === 'rundown' && event.rundowns.length > 0">
                                    <ul role="list" class="space-y-4">
                                        <li v-for="(item, index) in event.rundowns" :key="index" class="flex gap-x-4">
                                            <div
                                                class="w-24 flex-none text-right text-sm leading-6 font-semibold text-indigo-600 dark:text-indigo-400"
                                            >
                                                {{ formatTime(item.start_time) }} - {{ formatTime(item.end_time) }}
                                            </div>
                                            <div class="flex-auto rounded-md bg-white p-4 ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                                                <div class="font-semibold">{{ item.title }}</div>
                                                <div v-if="item.description" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                    {{ item.description }}
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div v-if="activeTab === 'speaker'">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                            <User class="h-8 w-8 text-gray-500" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold">{{ event.speaker }}</h3>
                                            <p class="text-sm text-gray-500">Pembicara Utama</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="sticky bottom-0 z-10 border-t border-gray-200 bg-white/80 p-4 backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/80">
            <div class="mx-auto flex max-w-7xl items-center justify-end">
                <div
                    v-if="isRegistered"
                    class="flex items-center gap-2 rounded-lg bg-green-100 px-4 py-2.5 text-sm font-semibold text-green-800 dark:bg-green-900 dark:text-green-300"
                >
                    <CheckCircle class="h-5 w-5" />
                    Anda Sudah Terdaftar
                </div>
                <button
                    v-else-if="event.status === 'registration' && event.attendance_mode === 'ticketing'"
                    @click="joinEvent"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    <Ticket class="h-5 w-5" />
                    Daftar Sekarang
                </button>
                <div
                    v-else
                    class="flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-4 py-3 text-sm font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-400"
                >
                    <XCircle class="h-5 w-5" />
                    Pendaftaran Ditutup
                </div>
            </div>
        </footer>
    </PublicLayout>
</template>
