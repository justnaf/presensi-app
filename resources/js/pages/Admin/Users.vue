<script setup lang="ts">
// Impor dari Vue & Inertia
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

// Impor Layout, Composable & Tipe
import useSweetAlert from '@/composables/useSweetAlert';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, FlashProps } from '@/types';

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
interface Role {
    name: string;
}

interface Institution {
    name: string;
}

interface User {
    id: number;
    name: string;
    username: string;
    email: string;
    avatar: string | null;
    roles: Role[];
    institutions: Institution[];
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PageProps {
    users: {
        data: User[];
        links: PaginationLink[];
    };
    roles: string[];
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
        router.get(route('admin.users.index'), { search: value }, { preserveState: true, replace: true });
    }, 300);
});

//----------------------------------------------------------------
// FORM UNTUK EDIT ROLE
//----------------------------------------------------------------
const form = useForm({
    roles: [] as string[],
});

//----------------------------------------------------------------
// MODAL STATE & LOGIC
//----------------------------------------------------------------
const isModalOpen = ref(false);
const isEditModalOpen = ref(false);
const selectedUser = ref<User | null>(null);

const openShowModal = (user: User) => {
    selectedUser.value = user;
    isModalOpen.value = true;
};

const openEditModal = (user: User) => {
    selectedUser.value = user;
    form.roles = user.roles.map((r) => r.name);
    isEditModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    isEditModalOpen.value = false;
    selectedUser.value = null;
    form.reset();
};

const closeOnEscape = (e: KeyboardEvent) => {
    if (e.key === 'Escape' && (isModalOpen.value || isEditModalOpen.value)) {
        closeModal();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

//----------------------------------------------------------------
// Aksi CRUD
//----------------------------------------------------------------
const submitEdit = () => {
    if (selectedUser.value) {
        form.put(route('admin.users.update', selectedUser.value.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const handleDelete = (user: User) => {
    confirmDelete(() => {
        router.delete(route('admin.users.destroy', user.id), {
            preserveScroll: true,
        });
    });
};

const checkAll = computed({
    get: () => (props.roles ? form.roles.length === props.roles.length : false),
    set: (value: boolean) => {
        form.roles = value ? props.roles.slice() : [];
    },
});

//----------------------------------------------------------------
// DATA LAIN
//----------------------------------------------------------------
const breadcrumbs: BreadcrumbItem[] = [{ title: 'Manajemen Pengguna', href: route('admin.users.index') }];
</script>

<template>
    <Head title="Manajemen Pengguna" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <!-- Header -->
                    <div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Daftar Pengguna</h2>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari pengguna..."
                            class="block w-full rounded-md border-gray-300 bg-white p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-64 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
                        />
                    </div>

                    <!-- Tabel Pengguna -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                            <thead class="bg-gray-50 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Pengguna</th>
                                    <th scope="col" class="px-6 py-3">Institusi</th>
                                    <th scope="col" class="px-6 py-3">Roles</th>
                                    <th scope="col" class="px-6 py-3"><span class="sr-only">Actions</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="users.data.length === 0">
                                    <td colspan="4" class="px-6 py-4 text-center">Tidak ada data ditemukan.</td>
                                </tr>
                                <tr
                                    v-for="user in users.data"
                                    :key="user.id"
                                    class="border-b bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-600"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <img v-if="user.avatar" :src="user.avatar" class="h-10 w-10 rounded-full object-cover" />
                                            <div v-else class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="24"
                                                    height="24"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    class="text-gray-500"
                                                >
                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="12" cy="7" r="4"></circle>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900 dark:text-white">{{ user.name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span v-for="institution in user.institutions" :key="institution.name">{{ institution.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="role in user.roles"
                                                :key="role.name"
                                                class="rounded bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="space-x-2 px-6 py-4 text-right">
                                        <button @click="openShowModal(user)" class="font-medium text-green-600 hover:underline dark:text-green-500">
                                            Lihat
                                        </button>
                                        <button @click="openEditModal(user)" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                                            Edit Role
                                        </button>
                                        <button @click="handleDelete(user)" class="font-medium text-red-600 hover:underline dark:text-red-500">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="users.links.length > 3" class="mt-6">
                        <div class="-mb-1 flex flex-wrap">
                            <template v-for="(link, key) in users.links" :key="key">
                                <div v-if="!link.url" class="mr-1 mb-1 rounded border px-4 py-3 text-sm text-gray-400" v-html="link.label" />
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

        <!-- Modal Lihat Pengguna -->
        <teleport to="body">
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div @click="closeModal" class="absolute inset-0 bg-gray-900/50"></div>
                <div
                    class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white p-6 text-left shadow-xl transition-all dark:bg-gray-800"
                >
                    <div class="flex items-start gap-4">
                        <img v-if="selectedUser?.avatar" :src="selectedUser.avatar" class="h-24 w-24 rounded-lg object-cover" />
                        <div v-else class="flex h-24 w-24 flex-shrink-0 items-center justify-center rounded-lg bg-gray-200 dark:bg-gray-700">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="48"
                                height="48"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                class="text-gray-500"
                            >
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ selectedUser?.name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">@{{ selectedUser?.username }}</p>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ selectedUser?.email }}</p>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-6">
                        <button
                            type="button"
                            @click="closeModal"
                            class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </teleport>

        <!-- Modal Edit Role -->
        <teleport to="body">
            <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div @click="closeModal" class="absolute inset-0 bg-gray-900/50"></div>
                <div class="relative w-full max-w-2xl transform overflow-hidden rounded-lg bg-white shadow-xl transition-all dark:bg-gray-800">
                    <form @submit.prevent="submitEdit" class="flex h-full flex-col">
                        <div class="bg-white p-6 dark:bg-gray-800">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Edit Role untuk: {{ selectedUser?.name }}</h2>
                        </div>
                        <div class="flex-1 space-y-6 overflow-y-auto p-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Roles</label>
                                <div class="mt-2 rounded-md border border-gray-200 dark:border-gray-600">
                                    <div class="border-b border-gray-200 p-2 dark:border-gray-600">
                                        <label class="flex items-center">
                                            <input
                                                type="checkbox"
                                                v-model="checkAll"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Pilih Semua</span>
                                        </label>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 p-4 sm:grid-cols-3">
                                        <label v-for="role in props.roles" :key="role" class="flex items-center">
                                            <input
                                                type="checkbox"
                                                :value="role"
                                                v-model="form.roles"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ role }}</span>
                                        </label>
                                    </div>
                                </div>
                                <p v-if="form.errors.roles" class="mt-2 text-sm text-red-600">{{ form.errors.roles }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 bg-gray-50 p-6 dark:bg-gray-800">
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
                                Perbarui Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>
    </AppLayout>
</template>
