<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Building, LogIn, User } from 'lucide-vue-next';
import { computed, ref } from 'vue';

//================================================================
// DEFINISI TIPE
//================================================================
interface EventInfo {
    id: number;
    name: string;
}

interface UserInfo {
    id: number;
    name: string;
}

interface PageProps {
    event: EventInfo;
    scan_id: string; // ID unik dari QR Code
    auth: {
        user: UserInfo | null;
    };
    errors: {
        name?: string;
        origin_institution?: string;
    };
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const user = computed(() => usePage().props.auth.user);

// Secara default, jika user tidak login, dia dianggap tamu.
const isGuest = ref(!user.value);

const form = useForm({
    name: '',
    origin_institution: '',
    is_guest: isGuest.value,
    scan_id: props.scan_id, // Sertakan ID unik dari QR Code
    event_id: props.event.id,
});

const submit = () => {
    // Pastikan nilai 'is_guest' sesuai dengan state checkbox
    form.is_guest = isGuest.value;
    form.post(route('public.checkin.process'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Konfirmasi Kehadiran" />
    <div class="flex min-h-screen items-center justify-center bg-gray-100 p-4 font-sans dark:bg-gray-900">
        <div class="w-full max-w-md">
            <div class="space-y-6 rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-800">
                <!-- Header -->
                <div class="text-center">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Konfirmasi Kehadiran</h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        untuk event: <span class="font-semibold">{{ event.name }}</span>
                    </p>
                </div>

                <!-- Tampilan jika pengguna sudah login -->
                <div v-if="user && !isGuest" class="rounded-lg bg-indigo-50 p-4 text-center dark:bg-indigo-900/50">
                    <p class="text-sm text-indigo-800 dark:text-indigo-300">Anda akan check-in sebagai:</p>
                    <p class="text-lg font-semibold text-indigo-900 dark:text-indigo-200">{{ user.name }}</p>
                    <button @click="isGuest = true" class="mt-2 text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                        Bukan Anda? Check-in sebagai tamu.
                    </button>
                </div>

                <!-- Form untuk check-in -->
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Form untuk tamu (muncul jika 'isGuest' true) -->
                    <div v-if="isGuest" class="space-y-4">
                        <div>
                            <label for="name" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <User class="h-4 w-4" />
                                Nama Lengkap
                            </label>
                            <input
                                v-model="form.name"
                                id="name"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                placeholder="Masukkan nama Anda"
                            />
                            <p v-if="form.errors.name" class="mt-2 text-sm text-red-500">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label for="origin_institution" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                <Building class="h-4 w-4" />
                                Asal Instansi/Lembaga (Opsional)
                            </label>
                            <input
                                v-model="form.origin_institution"
                                id="origin_institution"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                placeholder="Contoh: Universitas Gadjah Mada"
                            />
                            <p v-if="form.errors.origin_institution" class="mt-2 text-sm text-red-500">{{ form.errors.origin_institution }}</p>
                        </div>
                        <button v-if="user" @click="isGuest = false" class="mt-2 text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                            Kembali untuk check-in sebagai {{ user.name }}.
                        </button>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-3 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:opacity-50"
                        >
                            <LogIn class="h-5 w-5" />
                            Konfirmasi Kehadiran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
