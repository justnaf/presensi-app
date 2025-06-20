<script setup lang="ts">
// Impor dari Vue & Inertia
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';

// Impor Layout, Composable & Tipe
import useSweetAlert from '@/composables/useSweetAlert';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, FlashProps } from '@/types';

//================================================================
// DEFINISI TIPE LOKAL UNTUK HALAMAN INI
//================================================================
interface EventCategory {
    id: number;
    name: string;
    description: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    categories: {
        data: EventCategory[];
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
watch(
    () => page.props as FlashProps,
    (flash) => {
        if (flash.flash?.success) success(flash.flash.success);
        if (flash.flash?.error) error(flash.flash.error);
    },
    { deep: true },
);

const search = ref(props.filters.search);
let searchTimer: ReturnType<typeof setTimeout>;
watch(search, (value: string) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('admin.event-categories.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

//----------------------------------------------------------------
// FORM STATE
//----------------------------------------------------------------
const form = useForm({
    id: null as number | null,
    name: '',
    description: '',
});

//----------------------------------------------------------------
// MODAL STATE & LOGIC
//----------------------------------------------------------------
const isModalOpen = ref(false);
const isUpdating = ref(false);
const modalTitle = ref('');
const nameInput = ref<HTMLInputElement | null>(null);

const openCreateModal = () => {
    isUpdating.value = false;
    form.reset();
    modalTitle.value = 'Tambah Kategori Baru';
    isModalOpen.value = true;
    nextTick(() => nameInput.value?.focus());
};

const openEditModal = (category: EventCategory) => {
    isUpdating.value = true;
    form.id = category.id;
    form.name = category.name;
    form.description = category.description ?? '';
    modalTitle.value = 'Edit Kategori';
    isModalOpen.value = true;
    nextTick(() => nameInput.value?.focus());
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && isModalOpen.value) {
        closeModal();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

//----------------------------------------------------------------
// Aksi CRUD
//----------------------------------------------------------------
const submit = () => {
    const options = {
        onSuccess: () => closeModal(),
        onError: () => nameInput.value?.focus(),
    };
    if (isUpdating.value) {
        form.put(route('admin.event-categories.update', form.id!), options);
    } else {
        form.post(route('admin.event-categories.store'), options);
    }
};

const handleDelete = (category: EventCategory) => {
    confirmDelete(() => {
        router.delete(route('admin.event-categories.destroy', category.id), {
            preserveScroll: true,
        });
    });
};

//----------------------------------------------------------------
// DATA LAIN
//----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: '#' },
    { title: 'Kategori', href: route('admin.event-categories.index') },
];
</script>

<template>
    <Head title="Manajemen Kategori Event" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <!-- Header -->
                    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Kategori Event</h2>
                        <div class="flex w-full items-center gap-2 sm:w-auto">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari kategori..."
                                class="block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                            />
                            <button
                                @click="openCreateModal"
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:outline-none active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                            >
                                Tambah
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Kategori -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Kategori</th>
                                    <th scope="col" class="px-6 py-3">Deskripsi</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="categories.data.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center">Tidak ada data ditemukan.</td>
                                </tr>
                                <tr
                                    v-for="category in categories.data"
                                    :key="category.id"
                                    class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                                >
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ category.name }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ category.description || '-' }}</td>
                                    <td class="space-x-2 px-6 py-4 text-right">
                                        <button @click="openEditModal(category)" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                                            Edit
                                        </button>
                                        <button @click="handleDelete(category)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="categories.links.length > 3" class="mt-6">
                        <div class="-mb-1 flex flex-wrap">
                            <template v-for="(link, key) in categories.links" :key="key">
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
                                    <span v-html="link.label"
                                /></Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Form -->
        <teleport to="body">
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div @click="closeModal" class="absolute inset-0 bg-gray-900/50"></div>
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all dark:bg-gray-800">
                    <form @submit.prevent="submit" class="flex h-full flex-col">
                        <div class="bg-white p-6 dark:bg-gray-800">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ modalTitle }}</h2>
                        </div>
                        <div class="flex-1 space-y-6 overflow-y-auto p-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kategori</label>
                                <input
                                    id="name"
                                    ref="nameInput"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                                    >Deskripsi (Opsional)</label
                                >
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                ></textarea>
                                <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 bg-gray-50 p-6 dark:bg-gray-700">
                            <button
                                type="button"
                                @click="closeModal"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-300"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition hover:bg-gray-700 active:bg-gray-900 dark:bg-gray-200 dark:text-gray-800"
                            >
                                {{ isUpdating ? 'Perbarui' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
