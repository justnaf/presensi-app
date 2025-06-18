<script setup lang="ts">
import useSweetAlert from '@/composables/useSweetAlert'; // Import your composable
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Building, LocateFixed, LogIn, User } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

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
interface Institution {
    id: number;
    name: string;
}
interface StaticQr {
    id: number;
    code: string;
    label: string;
}

interface PageProps {
    event: EventInfo;
    staticQr: StaticQr;
    institutions: Institution[];
    auth: { user: UserInfo | null };
    errors: Record<string, string>; // More generic error type
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const page = usePage(); // Use the PageProps type
const { error } = useSweetAlert(); // Get the error function from your composable
const user = computed(() => page.props.auth.user);
const isGuest = ref(!user.value);

const form = useForm({
    name: '',
    is_guest: isGuest.value,
    code: props.staticQr.code,
    event_id: props.event.id,
    institution_id: null as number | null,
    latitude: null as number | null,
    longitude: null as number | null,
});

const submit = () => {
    form.is_guest = isGuest.value;
    form.post(route('public.checkin.process'), {
        preserveScroll: true,
        onError: (errors) => {
            // If there's a general error not tied to a specific field, show a popup
            if (Object.keys(errors).length > 0) {
                const firstError = Object.values(errors)[0];
                error(firstError || 'Terdapat kesalahan pada input Anda. Silakan periksa kembali.');
            }
        },
    });
};

//----------------------------------------------------------------
// LOGIKA GEOLOCATION
//----------------------------------------------------------------
const isLocating = ref(true);
const locationError = ref<string | null>(null);

const getLocation = () => {
    if (!navigator.geolocation) {
        locationError.value = 'Geolocation tidak didukung oleh browser Anda.';
        isLocating.value = false;
        return;
    }

    isLocating.value = true;
    locationError.value = null;

    navigator.geolocation.getCurrentPosition(
        (position) => {
            form.latitude = position.coords.latitude;
            form.longitude = position.coords.longitude;
            isLocating.value = false;
        },
        () => {
            locationError.value = 'Izin lokasi ditolak. Aktifkan untuk melanjutkan.';
            isLocating.value = false;
        },
    );
};

onMounted(() => {
    getLocation();
});

const isSubmitDisabled = computed(() => {
    return form.processing || isLocating.value || !!locationError.value;
});
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

                <!-- Status Geolocation -->
                <div
                    class="rounded-lg p-3 text-center text-sm"
                    :class="{
                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300': isLocating,
                        'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300': locationError,
                    }"
                >
                    <p v-if="isLocating" class="flex items-center justify-center gap-2">
                        <LocateFixed class="h-4 w-4 animate-pulse" />
                        Mencari lokasi Anda...
                    </p>
                    <p v-if="locationError">{{ locationError }}</p>
                    <!-- Add error display for location fields -->
                    <p v-if="form.errors.latitude" class="mt-1">{{ form.errors.latitude }}</p>
                    <p v-if="form.errors.longitude" class="mt-1">{{ form.errors.longitude }}</p>
                </div>

                <!-- Tampilan jika pengguna sudah login -->
                <div v-if="user && !isGuest" class="rounded-lg bg-indigo-50 p-4 text-center dark:bg-indigo-900/50">
                    <p class="text-sm text-indigo-800 dark:text-indigo-300">Anda akan check-in sebagai:</p>
                    <p class="text-lg font-semibold text-indigo-900 dark:text-indigo-200">{{ user.name }}</p>
                    <button @click="isGuest = true" class="mt-2 text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                        Bukan Anda? Check-in sebagai tamu.
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Form untuk tamu -->
                    <div v-if="isGuest" class="space-y-4">
                        <div>
                            <label for="name" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                                ><User class="h-4 w-4" />Nama Lengkap</label
                            >
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
                            <label for="institution_id" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300"
                                ><Building class="h-4 w-4" />Asal Instansi/Lembaga</label
                            >
                            <select
                                v-model="form.institution_id"
                                id="institution_id"
                                class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                            >
                                <option :value="null">-- Pilih Instansi (Opsional) --</option>
                                <option v-for="institution in institutions" :key="institution.id" :value="institution.id">
                                    {{ institution.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.institution_id" class="mt-2 text-sm text-red-500">{{ form.errors.institution_id }}</p>
                        </div>
                        <button v-if="user" @click="isGuest = false" class="mt-2 text-xs text-indigo-600 hover:underline dark:text-indigo-400">
                            Kembali untuk check-in sebagai {{ user.name }}.
                        </button>
                    </div>

                    <div>
                        <button
                            type="submit"
                            :disabled="isSubmitDisabled"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-3 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 disabled:cursor-not-allowed disabled:bg-indigo-400 dark:disabled:bg-indigo-800"
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
