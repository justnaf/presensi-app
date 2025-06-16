<script setup lang="ts">
// Impor dari Vue & Inertia
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import L, { LatLng, Map, Marker } from 'leaflet'; // Impor Leaflet
import { Image } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Impor Layout, Composable & Tipe
import useSweetAlert from '@/composables/useSweetAlert';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, FlashProps } from '@/types';

// 2. Konfigurasi ulang path ikon default Leaflet SEBELUM peta diinisialisasi
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
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
    longitude: number | null;
    latitude: number | null;
    speaker: string | null;
    poster_image: string | null;
    max_attendees: number;
    start_date: string;
    end_date: string;
    category_id: number | null;
    type: string;
    attendance_mode: 'ticketing' | 'barcode';
    rundowns: Rundown[];
}

interface PageProps {
    event?: EventData; // event bersifat opsional (ada saat edit)
    categories: EventCategory[];
}
//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const isUpdating = computed(() => !!props.event);

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

//----------------------------------------------------------------
// FORM STATE
//----------------------------------------------------------------
const form = useForm({
    _method: isUpdating.value ? 'PUT' : 'POST',
    name: props.event?.name ?? '',
    description: props.event?.description ?? '',
    about: props.event?.about ?? '',
    location_name: props.event?.location_name ?? '',
    location_url: props.event?.location_url ?? '',
    longitude: props.event?.longitude ?? null,
    latitude: props.event?.latitude ?? null,
    speaker: props.event?.speaker ?? '',
    poster_image: null as File | null,
    max_attendees: props.event?.max_attendees ?? 0,
    start_date: props.event?.start_date ?? '',
    end_date: props.event?.end_date ?? '',
    category_id: props.event?.category_id ?? null,
    type: props.event?.type ?? '',
    attendance_mode: props.event?.attendance_mode ?? 'ticketing',
    rundowns: (props.event?.rundowns ?? []) as any[],
});

const posterPreview = ref<string | null>(props.event?.poster_image ? `/storage/${props.event.poster_image}` : null);

function handleImageUpload(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        form.poster_image = file;
        posterPreview.value = URL.createObjectURL(file);
    }
}

//----------------------------------------------------------------
// LOGIKA RUNDOWN
//----------------------------------------------------------------
const addRundown = () => {
    form.rundowns.push({
        title: '',
        start_time: '',
        end_time: '',
        description: '',
    });
};

const removeRundown = (index: number) => {
    form.rundowns.splice(index, 1);
};

//----------------------------------------------------------------
// LOGIKA PETA LEAFLET
//----------------------------------------------------------------
let map: Map | null = null;
let marker: Marker | null = null;
const defaultCoords: LatLng = new LatLng(-7.339665, 110.501534); // Titik default (Secang, Jateng)

const initMap = () => {
    const initialCoords = form.latitude && form.longitude ? new LatLng(form.latitude, form.longitude) : defaultCoords;

    map = L.map('map-container').setView(initialCoords, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    marker = L.marker(initialCoords, { draggable: true }).addTo(map);

    marker.on('dragend', (event) => {
        const position = event.target.getLatLng();
        form.latitude = position.lat;
        form.longitude = position.lng;
    });

    map.on('click', (event) => {
        const position = event.latlng;
        marker?.setLatLng(position);
        form.latitude = position.lat;
        form.longitude = position.lng;
    });
};

onMounted(() => {
    // Inisialisasi peta setelah komponen di-mount
    initMap();
});

//----------------------------------------------------------------
// SUBMIT & DATA LAIN
//----------------------------------------------------------------
const submit = () => {
    const url = isUpdating.value ? route('admin.events.update', props.event!.id) : route('admin.events.store');
    form.post(url, {
        onError: (errors) => {
            error('Terdapat kesalahan pada input Anda. Silakan periksa kembali.');
        },
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: route('admin.events.index') },
    { title: isUpdating.value ? 'Edit Event' : 'Tambah Event', href: '#' },
];
</script>

<template>
    <Head>
        <title>{{ isUpdating ? 'Edit Event' : 'Tambah Event' }}</title>
        <!-- Tambahkan CSS Leaflet ke halaman ini -->
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""
        />
    </Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-8">
                    <!-- Detail Event Utama -->
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Detail Event</h3>
                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Event</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    id="name"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                                <select
                                    v-model="form.category_id"
                                    id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    <option :value="null">Pilih Kategori</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                </select>
                                <p v-if="form.errors.category_id" class="mt-2 text-sm text-red-500">{{ form.errors.category_id }}</p>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi Singkat</label>
                                <textarea
                                    v-model="form.description"
                                    id="description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                ></textarea>
                            </div>
                            <div class="sm:col-span-6">
                                <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tentang Event (About)</label>
                                <textarea
                                    v-model="form.about"
                                    id="about"
                                    rows="5"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                ></textarea>
                            </div>
                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Poster Event</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <img v-if="posterPreview" :src="posterPreview" class="h-24 w-24 rounded-md object-cover" />
                                    <div v-else class="flex h-24 w-24 items-center justify-center rounded-md bg-gray-100 dark:bg-gray-700">
                                        <Image class="h-8 w-8 text-gray-400" />
                                    </div>
                                    <input
                                        @change="handleImageUpload"
                                        type="file"
                                        accept="image/*"
                                        class="p-2 text-sm text-gray-500 file:mr-4 file:rounded-full file:border-0 file:bg-gray-200 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-300 dark:file:bg-gray-600 dark:file:text-gray-200 dark:hover:file:bg-gray-500"
                                    />
                                </div>
                                <p v-if="form.errors.poster_image" class="mt-2 text-sm text-red-500">{{ form.errors.poster_image }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Tambahan -->
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Detail Pelaksanaan</h3>
                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    id="start_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    required
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    id="end_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    required
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe Event</label>
                                <input
                                    v-model="form.type"
                                    type="text"
                                    id="type"
                                    placeholder="Contoh: Seminar, Workshop"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    required
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="speaker" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pembicara</label>
                                <input
                                    v-model="form.speaker"
                                    type="text"
                                    id="speaker"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="max_attendees" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Kapasitas Maksimal</label
                                >
                                <input
                                    v-model="form.max_attendees"
                                    type="number"
                                    id="max_attendees"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="attendance_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mode Kehadiran</label>
                                <select
                                    v-model="form.attendance_mode"
                                    id="attendance_mode"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    <option value="ticketing">Ticketing</option>
                                    <option value="barcode">Barcode</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Peta Lokasi -->
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Lokasi Event</h3>
                        <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-6">
                            <div class="sm:col-span-3">
                                <label for="location_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Lokasi</label>
                                <input
                                    v-model="form.location_name"
                                    type="text"
                                    id="location_name"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="location_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >URL Peta (Google Maps, dll)</label
                                >
                                <input
                                    v-model="form.location_url"
                                    type="url"
                                    id="location_url"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                />
                            </div>
                            <div class="sm:col-span-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Titik di Peta</label>
                                <div id="map-container" class="z-0 mt-2 h-96 w-full rounded-lg"></div>
                            </div>
                            <div class="sm:col-span-3">
                                <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude</label>
                                <input
                                    v-model="form.latitude"
                                    type="number"
                                    step="any"
                                    id="latitude"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                />
                            </div>
                            <div class="sm:col-span-3">
                                <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude</label>
                                <input
                                    v-model="form.longitude"
                                    type="number"
                                    step="any"
                                    id="longitude"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Acara (Rundown) -->
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Jadwal Acara (Rundown)</h3>
                            <button
                                @click="addRundown"
                                type="button"
                                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                            >
                                Tambah Jadwal
                            </button>
                        </div>
                        <div class="mt-6 space-y-4">
                            <div v-if="form.rundowns.length === 0" class="text-center text-sm text-gray-500">Belum ada jadwal.</div>
                            <div
                                v-for="(rundown, index) in form.rundowns"
                                :key="index"
                                class="rounded-md border border-gray-200 p-4 dark:border-gray-700"
                            >
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                    <div class="sm:col-span-5">
                                        <label :for="`rundown_title_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                            >Judul Sesi</label
                                        >
                                        <input
                                            v-model="rundown.title"
                                            type="text"
                                            :id="`rundown_title_${index}`"
                                            class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                            required
                                        />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label :for="`rundown_start_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                            >Waktu Mulai</label
                                        >
                                        <input
                                            v-model="rundown.start_time"
                                            type="datetime-local"
                                            :id="`rundown_start_${index}`"
                                            class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                            required
                                        />
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label :for="`rundown_end_${index}`" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                            >Waktu Selesai</label
                                        >
                                        <input
                                            v-model="rundown.end_time"
                                            type="datetime-local"
                                            :id="`rundown_end_${index}`"
                                            class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                            required
                                        />
                                    </div>
                                    <div class="flex items-end sm:col-span-1">
                                        <button
                                            @click="removeRundown(index)"
                                            type="button"
                                            class="rounded-md bg-red-600 p-2 text-white shadow-sm hover:bg-red-500"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="20"
                                                height="20"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <path d="M3 6h18"></path>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex justify-end gap-4 pt-8">
                        <Link
                            :href="route('admin.events.index')"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-300"
                            >Batal</Link
                        >
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-sm font-semibold tracking-widest text-white uppercase transition hover:bg-gray-700 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800"
                        >
                            {{ isUpdating ? 'Perbarui Event' : 'Simpan Event' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
