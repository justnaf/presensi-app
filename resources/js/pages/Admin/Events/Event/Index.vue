<script setup lang="ts">
// Impor dari Vue & Inertia
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

// Impor Layout, Composable & Tipe
import useSweetAlert from '@/composables/useSweetAlert';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, FlashProps } from '@/types';
import { Image } from 'lucide-vue-next';

//================================================================
// DEFINISI TIPE LOKAL UNTUK HALAMAN INI
//================================================================
interface EventCategory {
    id: number;
    name: string;
}

interface Event {
    id: number;
    name: string;
    poster_image: string | null;
    start_date: string;
    end_date: string;
    type: string;
    category: EventCategory | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    events: {
        data: Event[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
    };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const { success, error, confirmDelete } = useSweetAlert();
const page = usePage();

//----------------------------------------------------------------
// WATCHERS
//----------------------------------------------------------------
setTimeout(() => {
    watch(
        () => page.props as FlashProps,
        (flash) => {
            console.log('Flash watcher triggered with:', flash);
            if (flash.flash?.success) success(flash.flash.success);
            if (flash.flash?.error) error(flash.flash.error);
        },
        { deep: true },
    );
}, 100);

const search = ref(props.filters.search);
let searchTimer: ReturnType<typeof setTimeout>;
watch(search, (value: string) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('admin.events.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

//----------------------------------------------------------------
// FUNGSI
//----------------------------------------------------------------
const handleDelete = (event: Event) => {
    confirmDelete(() => {
        router.delete(route('admin.events.destroy', event.id), {
            preserveScroll: true,
        });
    });
};

//----------------------------------------------------------------
// DATA LAIN
//----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: route('admin.events.index') },
    { title: 'Daftar Event', href: route('admin.events.index') },
];
</script>

<template>
    <Head title="Manajemen Event" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <!-- Header -->
                    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Daftar Event</h2>
                        <div class="flex w-full items-center gap-2 sm:w-auto">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari event..."
                                class="block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                            />
                            <Link
                                :href="route('admin.events.create')"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest whitespace-nowrap text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                            >
                                Tambah Event
                            </Link>
                        </div>
                    </div>

                    <!-- Tabel Event -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Event</th>
                                    <th scope="col" class="px-6 py-3">Kategori</th>
                                    <th scope="col" class="px-6 py-3">Tanggal</th>
                                    <th scope="col" class="px-6 py-3">Tipe</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="events.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center">Tidak ada data event ditemukan.</td>
                                </tr>
                                <tr
                                    v-for="event in events.data"
                                    :key="event.id"
                                    class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="event.poster_image"
                                                :src="`/storage/${event.poster_image}`"
                                                class="h-10 w-10 rounded-md object-cover"
                                            />
                                            <div
                                                v-else
                                                class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-md bg-gray-200 dark:bg-gray-700"
                                            >
                                                <Image class="h-5 w-5 text-gray-400" />
                                            </div>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ event.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            v-if="event.category"
                                            class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                                        >
                                            {{ event.category.name }}
                                        </span>
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{
                                            new Date(event.start_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                        }}
                                    </td>
                                    <td class="px-6 py-4">{{ event.type }}</td>
                                    <td class="space-x-2 px-6 py-4 text-right whitespace-nowrap">
                                        <Link
                                            :href="route('admin.events.edit', event.id)"
                                            class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                            >Edit</Link
                                        >
                                        <button @click="handleDelete(event)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="events.links.length > 3" class="mt-6">
                        <div class="-mb-1 flex flex-wrap">
                            <template v-for="(link, key) in events.links" :key="key">
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
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
