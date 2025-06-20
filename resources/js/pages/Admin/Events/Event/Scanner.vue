<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Html5QrcodeScanner } from 'html5-qrcode';
import { CameraOff, CheckCircle, Users, XCircle } from 'lucide-vue-next';
import { nextTick, onUnmounted, ref, watch } from 'vue'; // 1. Import nextTick

interface EventInfo {
    id: number;
    name: string;
}
interface PageProps {
    events: EventInfo[];
}

defineProps<PageProps>();

const selectedEvent = ref<EventInfo | null>(null);
const scannerInstance = ref<Html5QrcodeScanner | null>(null);
const scanResult = ref({
    success: false,
    error: false,
    message: '',
    name: '',
});
const isProcessing = ref(false);

const startScanner = () => {
    // Clear any previous scanner instance
    if (scannerInstance.value) {
        scannerInstance.value.clear().catch((err) => console.error('Error clearing scanner:', err));
    }

    const onScanSuccess = (decodedText: string) => {
        if (isProcessing.value || !selectedEvent.value) return;

        isProcessing.value = true;

        axios
            .post(route('admin.events.scan', selectedEvent.value.id), { ticket_code: decodedText })
            .then((response) => {
                scanResult.value = { success: true, error: false, message: response.data.message, name: response.data.attendee_name };
            })
            .catch((error) => {
                scanResult.value = {
                    success: false,
                    error: true,
                    message: error.response?.data?.message || 'Error',
                    name: error.response?.data?.attendee_name || '',
                };
            })
            .finally(() => {
                setTimeout(() => {
                    isProcessing.value = false;
                    scanResult.value = { success: false, error: false, message: '', name: '' };
                }, 3000); // Reset result after 3 seconds
            });
    };

    try {
        const scanner = new Html5QrcodeScanner('qr-reader', { fps: 10, qrbox: { width: 250, height: 250 } }, false);
        scanner.render(onScanSuccess, () => {});
        scannerInstance.value = scanner;
    } catch (e) {
        console.error('Failed to initialize scanner:', e);
    }
};

// 2. FIX: Use nextTick to ensure the DOM is ready before starting the scanner
watch(selectedEvent, (newEvent) => {
    if (newEvent) {
        // Wait for Vue to render the #qr-reader element
        nextTick(() => {
            startScanner();
        });
    } else if (scannerInstance.value) {
        scannerInstance.value.clear().catch((err) => console.error('Error clearing scanner on deselect:', err));
        scannerInstance.value = null;
    }
});

onUnmounted(() => {
    if (scannerInstance.value) {
        scannerInstance.value.clear().catch((error) => {
            console.error('Failed to clear scanner on unmount.', error);
        });
    }
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: route('admin.events.index') },
    { title: 'Scanner Kehadiran', href: '#' },
];
</script>

<template>
    <Head title="Scanner Kehadiran" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 text-center shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Scanner Absensi</h2>

                    <div class="mx-auto my-6 max-w-sm">
                        <label for="event-select" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >Pilih Event untuk Dipindai</label
                        >
                        <select
                            v-model="selectedEvent"
                            id="event-select"
                            class="block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >
                            <option :value="null">-- Silakan Pilih Event --</option>
                            <option v-for="event in events" :key="event.id" :value="event">{{ event.name }}</option>
                        </select>
                    </div>

                    <div v-if="selectedEvent">
                        <div id="qr-reader" class="mx-auto w-full max-w-sm rounded-lg border-2 border-gray-300 dark:border-gray-600"></div>

                        <div class="mt-6 min-h-[80px]">
                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                            >
                                <div v-if="scanResult.success" class="flex flex-col items-center justify-center text-green-500">
                                    <CheckCircle class="h-12 w-12" />
                                    <p class="mt-2 font-bold">{{ scanResult.message }}</p>
                                    <p class="text-lg text-gray-800 dark:text-gray-200">{{ scanResult.name }}</p>
                                </div>
                                <div v-else-if="scanResult.error" class="flex flex-col items-center justify-center text-red-500">
                                    <XCircle class="h-12 w-12" />
                                    <p class="mt-2 font-bold">{{ scanResult.message }}</p>
                                    <p v-if="scanResult.name" class="text-lg text-gray-800 dark:text-gray-200">{{ scanResult.name }}</p>
                                </div>
                                <div v-else class="flex flex-col items-center justify-center text-gray-400">
                                    <CameraOff class="h-12 w-12" />
                                    <p class="mt-2 text-sm">Arahkan kamera ke QR Code tiket</p>
                                </div>
                            </transition>
                        </div>
                    </div>

                    <div
                        v-else
                        class="mt-8 flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-600"
                    >
                        <Users class="h-16 w-16 text-gray-400" />
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Event untuk Memulai</h3>
                        <p class="mt-1 text-sm text-gray-500">Kamera pemindai akan aktif setelah event dipilih.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
