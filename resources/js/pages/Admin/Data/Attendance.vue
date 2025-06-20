<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { FileSpreadsheet, User as UserIcon, Users } from 'lucide-vue-next'; // Ganti nama import agar tidak konflik
import { computed, ref, watch } from 'vue';

//================================================================
// TIPE LOKAL
//================================================================
interface EventInfo {
    id: number;
    name: string;
}
interface Institution {
    id: number;
    name: string;
}
interface UserInfo {
    id: number;
    name: string;
    email: string;
    avatar: string | null;
    institutions: Institution[]; // Tambahkan relasi institusi
}
interface AttendanceData {
    id: number;
    name: string;
    origin_institution: string | null;
    scanned_at: string;
    scanned_barcode_value: string | null;
    latitude: number | null;
    longitude: number | null;
    user: UserInfo | null;
}
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}
interface PageProps {
    events: EventInfo[];
    attendance: { data: AttendanceData[]; links: PaginationLink[] };
    filters: { event_id: string | null };
}

//----------------------------------------------------------------
// SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const selectedEventId = ref(props.filters.event_id || '');

const exportUrl = computed(() => {
    if (!selectedEventId.value) return '#';
    return route('admin.data.attendances.export', { event_id: selectedEventId.value });
});

watch(selectedEventId, (newEventId) => {
    router.get(
        route('admin.data.attendances.index'),
        { event_id: newEventId },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const formatDateTime = (dateTimeStr: string): string =>
    new Date(dateTimeStr).toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });

const getInstitutionName = (record: AttendanceData): string => {
    return record.user?.institutions?.[0]?.name ?? record.origin_institution ?? '-';
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: '#' },
    { title: 'Riwayat Kehadiran', href: route('admin.data.attendances.index') },
];
</script>

<template>
    <Head title="Riwayat Kehadiran" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="mb-6 space-y-4">
                        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Riwayat Kehadiran Event</h2>
                            <a
                                v-if="selectedEventId && attendance.data.length > 0"
                                :href="exportUrl"
                                target="_blank"
                                class="inline-flex items-center gap-2 rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700"
                            >
                                <FileSpreadsheet class="h-4 w-4" />
                                Export Excel
                            </a>
                        </div>
                        <div>
                            <label for="event-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Event</label>
                            <select
                                v-model="selectedEventId"
                                id="event-select"
                                class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm md:w-1/2 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                            >
                                <option value="">-- Silakan Pilih Event --</option>
                                <option v-for="event in events" :key="event.id" :value="event.id">{{ event.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div
                        v-if="!selectedEventId"
                        class="mt-8 flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-600"
                    >
                        <Users class="h-16 w-16 text-gray-400" />
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Event untuk Memulai</h3>
                        <p class="mt-1 text-sm text-gray-500">Data kehadiran akan muncul di sini setelah Anda memilih event.</p>
                    </div>

                    <div v-else>
                        <div class="shadow-md sm:rounded-lg">
                            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                                <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nama Peserta</th>
                                        <th scope="col" class="px-6 py-3">Asal Instansi</th>
                                        <th scope="col" class="px-6 py-3">Waktu Kehadiran</th>
                                        <th scope="col" class="px-6 py-3">Lokasi (Lat/Lng)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="attendance.data.length === 0">
                                        <td colspan="4" class="px-6 py-4 text-center">Belum ada data kehadiran untuk event ini.</td>
                                    </tr>
                                    <tr
                                        v-for="record in attendance.data"
                                        :key="record.id"
                                        class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                                    >
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <img
                                                    v-if="record.user?.avatar"
                                                    :src="`/storage/${record.user.avatar}`"
                                                    class="h-10 w-10 rounded-full object-cover"
                                                />
                                                <div
                                                    v-else
                                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700"
                                                >
                                                    <UserIcon class="h-5 w-5 text-gray-400" />
                                                </div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ record.name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">{{ getInstitutionName(record) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(record.scanned_at) }}</td>
                                        <td class="px-6 py-4 font-mono text-xs">
                                            <div v-if="record.latitude && record.longitude">{{ record.latitude }}, {{ record.longitude }}</div>
                                            <span v-else>-</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="attendance.links.length > 3" class="mt-6">
                            <div class="-mb-1 flex flex-wrap">
                                <template v-for="(link, key) in attendance.links" :key="key">
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
