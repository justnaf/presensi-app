<script setup lang="ts">
// Impor dari Vue & Inertia
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue'; // Ditambahkan 'computed'

// Impor Layout, Composable & Tipe
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { FileSpreadsheet, Search, User, Users } from 'lucide-vue-next'; // Ditambahkan 'FileSpreadsheet'

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
interface EventInfo {
    id: number;
    name: string;
}
interface UserInfo {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
}
interface AttendeeData {
    id: number;
    ticket_code: string;
    registered_at: string;
    user: UserInfo;
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
interface PageProps {
    events: EventInfo[];
    attendees: { data: AttendeeData[]; links: PaginationLink[] };
    filters: { search: string; event_id: string | null };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();

//----------------------------------------------------------------
// STATE & WATCHERS
//----------------------------------------------------------------
const selectedEventId = ref(props.filters.event_id || '');
const search = ref(props.filters.search);

// --- START: Kode yang ditambahkan ---
// Membuat URL untuk tombol export secara dinamis.
// URL akan diperbarui setiap kali 'selectedEventId' berubah.
const exportUrl = computed(() => {
    // Jika tidak ada event yang dipilih, kembalikan URL yang tidak valid
    if (!selectedEventId.value) {
        return '#';
    }
    // Jika ada event yang dipilih, buat route yang benar
    return route('admin.data.attendees.export', { event_id: selectedEventId.value });
});
// --- END: Kode yang ditambahkan ---

// Fungsi untuk memuat ulang data saat filter berubah
const reload = () => {
    router.get(
        route('admin.data.attendees'),
        {
            event_id: selectedEventId.value,
            search: search.value,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
};

watch(selectedEventId, () => {
    search.value = ''; // Reset search saat ganti event
    reload();
});

let searchTimer: ReturnType<typeof setTimeout>;
watch(search, () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(reload, 300);
});

//----------------------------------------------------------------
// FUNGSI FORMAT
//----------------------------------------------------------------
const formatDateTime = (dateTimeStr: string): string => {
    return new Date(dateTimeStr).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

//----------------------------------------------------------------
// DATA LAIN
//----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: '#' },
    { title: 'Daftar Peserta', href: route('admin.data.attendees') },
];
</script>

<template>
    <Head title="Daftar Peserta Event" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <!-- Header & Filter -->
                    <div class="mb-6 space-y-4">
                        <!-- Baris Judul dan Tombol Export -->
                        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Daftar Peserta Event</h2>
                            <a
                                v-if="selectedEventId"
                                :href="exportUrl"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-green-700"
                            >
                                <FileSpreadsheet class="h-4 w-4" />
                                Export Excel
                            </a>
                        </div>
                        <!-- Baris Filter -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="event-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Event</label>
                                <select
                                    v-model="selectedEventId"
                                    id="event-select"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    <option value="">-- Silakan Pilih Event --</option>
                                    <option v-for="event in events" :key="event.id" :value="event.id">{{ event.name }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="search-attendee" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cari Peserta</label>
                                <input
                                    v-model="search"
                                    id="search-attendee"
                                    type="text"
                                    placeholder="Cari nama atau email..."
                                    :disabled="!selectedEventId"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:disabled:bg-gray-700/50"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Placeholder / Tabel Peserta -->
                    <div
                        v-if="!selectedEventId"
                        class="mt-8 flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-600"
                    >
                        <Users class="h-16 w-16 text-gray-400" />
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Event untuk Memulai</h3>
                        <p class="mt-1 text-sm text-gray-500">Daftar peserta akan muncul di sini setelah Anda memilih event.</p>
                    </div>

                    <div v-else>
                        <div class="shadow-md sm:rounded-lg">
                            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                                <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Peserta</th>
                                        <th scope="col" class="px-6 py-3">Kode Tiket</th>
                                        <th scope="col" class="px-6 py-3">Tanggal Registrasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="attendees.data.length === 0">
                                        <td colspan="3" class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center justify-center p-6">
                                                <Search class="h-12 w-12 text-gray-400" />
                                                <h3 class="mt-2 text-sm font-medium">Tidak ada peserta ditemukan</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    Coba kata kunci lain atau periksa kembali event yang dipilih.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr
                                        v-for="attendee in attendees.data"
                                        :key="attendee.id"
                                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                                    >
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    v-if="attendee.user.avatar"
                                                    :src="`/storage/${attendee.user.avatar}`"
                                                    class="h-10 w-10 rounded-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700"
                                                >
                                                    <User class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ attendee.user.name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ attendee.user.email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-mono text-xs text-gray-700 dark:text-gray-300">{{ attendee.ticket_code }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(attendee.registered_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="attendees.links.length > 3" class="mt-6">
                            <div class="-mb-1 flex flex-wrap">
                                <template v-for="(link, key) in attendees.links" :key="key">
                                    <div
                                        v-if="!link.url"
                                        class="mr-1 mb-1 rounded border px-4 py-3 text-sm text-gray-400 dark:border-gray-700"
                                        v-html="link.label"
                                    />
                                    <Link
                                        v-else
                                        class="mr-1 mb-1 rounded border px-4 py-3 text-sm hover:bg-white focus:border-indigo-500 focus:text-indigo-500 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                                        :class="{ 'bg-white font-bold dark:bg-gray-700': link.active }"
                                        :href="link.url"
                                    >
                                        <span v-html="link.label" />
                                    </Link>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
