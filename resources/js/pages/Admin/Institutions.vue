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
interface Institution {
    id: number;
    name: string;
    type: string | null;
}

// Tipe untuk link pagination dari Laravel
interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

// Tipe untuk props spesifik halaman ini
interface InstitutionPageProps {
    institutions: {
        data: Institution[];
        links: PaginationLink[];
    };
    filters: {
        search: string;
    };
    flash?: {
        success?: string;
        error?: string;
    };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<InstitutionPageProps>();
const page = usePage();
const { success, error, confirmDelete } = useSweetAlert();

//----------------------------------------------------------------
// WATCHERS (Pemantau Perubahan)
//----------------------------------------------------------------

// Watcher untuk menampilkan notifikasi flash message dari backend
watch(
    () => page.props as FlashProps,
    (flash) => {
        if (flash.flash?.success) success(flash.flash.success);
        if (flash.flash?.error) error(flash.flash.error);
    },
    { deep: true },
);

// Watcher untuk fungsionalitas pencarian dengan debounce manual
const search = ref(props.filters.search);
let searchTimer: ReturnType<typeof setTimeout>;

watch(search, (value: string) => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(route('admin.institutions.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

//----------------------------------------------------------------
// FORM STATE (useForm dari Inertia)
//----------------------------------------------------------------
const form = useForm({
    id: null as number | null,
    name: '',
    type: '',
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
    modalTitle.value = 'Tambah Institusi Baru';
    isModalOpen.value = true;
    nextTick(() => nameInput.value?.focus());
};

const openEditModal = (institution: Institution) => {
    isUpdating.value = true;
    form.id = institution.id;
    form.name = institution.name;
    form.type = institution.type ?? '';
    modalTitle.value = 'Edit Institusi';
    isModalOpen.value = true;
    nextTick(() => nameInput.value?.focus());
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

// Menangani penutupan modal dengan tombol 'Escape'
const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && isModalOpen.value) {
        closeModal();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

//----------------------------------------------------------------
// Aksi CRUD (Create, Read, Update, Delete)
//----------------------------------------------------------------
const submit = () => {
    const options = {
        onSuccess: () => closeModal(),
        onError: () => nameInput.value?.focus(),
    };

    if (isUpdating.value) {
        form.put(route('admin.institutions.update', form.id!), options);
    } else {
        form.post(route('admin.institutions.store'), options);
    }
};

const handleDelete = (institution: Institution) => {
    confirmDelete(() => {
        router.delete(route('admin.institutions.destroy', institution.id), {
            preserveScroll: true,
        });
    });
};

//----------------------------------------------------------------
// DATA LAIN (Contoh: Breadcrumbs)
//----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manajemen Institusi',
        href: route('admin.institutions.index'),
    },
];
</script>

<template>
    <Head title="Institutions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Daftar Institusi</h2>
                        <div class="flex w-full items-center gap-2 sm:w-auto">
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari institusi..."
                                class="block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                            />
                            <button
                                @click="openCreateModal"
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900 disabled:opacity-25 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                            >
                                Tambah
                            </button>
                        </div>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama</th>
                                    <th scope="col" class="px-6 py-3">Tipe</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="institutions.data.length === 0">
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data ditemukan.</td>
                                </tr>
                                <tr
                                    v-for="institution in institutions.data"
                                    :key="institution.id"
                                    class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                                >
                                    <td class="px-6 py-4 font-medium whitespace-nowrap text-gray-900 dark:text-white">{{ institution.name }}</td>
                                    <td class="px-6 py-4">{{ institution.type || '-' }}</td>
                                    <td class="space-x-2 px-6 py-4 text-right">
                                        <button
                                            @click="openEditModal(institution)"
                                            class="font-medium text-blue-600 hover:underline dark:text-blue-500"
                                        >
                                            Edit
                                        </button>
                                        <button @click="handleDelete(institution)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="institutions.links.length > 3" class="mt-6">
                        <div class="-mb-1 flex flex-wrap">
                            <template v-for="(link, key) in institutions.links" :key="key">
                                <div
                                    v-if="link.url === null"
                                    class="mr-1 mb-1 rounded border border-gray-300 px-4 py-3 text-sm leading-4 text-gray-400 dark:border-gray-700"
                                    v-html="link.label"
                                />
                                <Link
                                    v-else
                                    class="mr-1 mb-1 rounded border border-gray-300 px-4 py-3 text-sm leading-4 text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:text-indigo-500 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700"
                                    :class="{ 'bg-indigo-50 font-bold text-indigo-600 dark:bg-gray-700 dark:text-white': link.active }"
                                    :href="link.url"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <teleport to="body">
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape="closeModal">
                <div @click="closeModal" class="absolute inset-0 bg-gray-900/50"></div>
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all dark:bg-gray-800">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ modalTitle }}</h2>
                        <form @submit.prevent="submit" class="mt-6 space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Institusi</label>
                                <input
                                    id="name"
                                    ref="nameInput"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe </label>
                                <input
                                    id="type"
                                    v-model="form.type"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                                />
                                <p v-if="form.errors.type" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ form.errors.type }}</p>
                            </div>

                            <div class="flex justify-end gap-4">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-300 dark:hover:bg-gray-500"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-none active:bg-gray-900 disabled:opacity-25 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white"
                                >
                                    {{ isUpdating ? 'Perbarui' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
