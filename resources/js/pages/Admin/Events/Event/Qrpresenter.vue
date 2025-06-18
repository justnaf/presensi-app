<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { CheckCircle, LoaderCircle, QrCode } from 'lucide-vue-next';
import QRCode from 'qrcode';
import { onUnmounted, ref, watch } from 'vue';

interface EventInfo {
    id: number;
    name: string;
}
interface PageProps {
    events: EventInfo[];
}

const props = defineProps<PageProps>();

const selectedEvent = ref<EventInfo | null>(null);
const currentScanId = ref<string | null>(null);
const lastAttendeeName = ref<string | null>(null);
const isLoading = ref(false);
const statusMessage = ref('Pilih event untuk memulai...');
let pollingInterval: number | undefined;

const generateNewQrCode = () => {
    if (!selectedEvent.value) return;

    isLoading.value = true;
    lastAttendeeName.value = null;
    statusMessage.value = 'Menghasilkan QR Code baru...';

    clearInterval(pollingInterval);

    const scanId = crypto.randomUUID();
    currentScanId.value = scanId;

    const checkinUrl = route('public.checkin.form', { scan_id: scanId, event_id: selectedEvent.value.id });

    const canvas = document.getElementById('qrcode-canvas');
    if (canvas) {
        QRCode.toCanvas(canvas, checkinUrl, { width: 300, margin: 2 }).then(() => {
            isLoading.value = false;
            statusMessage.value = 'Silakan pindai kode ini.';
            startPolling(scanId);
        });
    }
};

const checkStatus = async (scanId: string) => {
    if (!selectedEvent.value) return;
    try {
        const response = await axios.get(route('admin.events.qrcode.presenter.status', { event: selectedEvent.value.id, scan_id: scanId }));
        if (response.data.status === 'used') {
            lastAttendeeName.value = response.data.attendee_name;
            statusMessage.value = 'Check-in Berhasil!';
            clearInterval(pollingInterval);
            setTimeout(generateNewQrCode, 3000);
        }
    } catch (e) {
        console.error('Gagal memeriksa status:', e);
    }
};

const startPolling = (scanId: string) => {
    pollingInterval = window.setInterval(() => checkStatus(scanId), 2000);
};

// Pantau perubahan pada dropdown event
watch(selectedEvent, (newEvent) => {
    if (newEvent) {
        generateNewQrCode();
    } else {
        // Reset jika tidak ada event yang dipilih
        clearInterval(pollingInterval);
        currentScanId.value = null;
        isLoading.value = false;
        statusMessage.value = 'Pilih event untuk memulai...';
    }
});

onUnmounted(() => {
    clearInterval(pollingInterval);
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: route('admin.events.index') },
    { title: 'Presenter QR Code', href: '#' },
];
</script>

<template>
    <Head title="Presenter Kehadiran" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 text-center shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Absensi Event (Barcode)</h2>

                    <!-- Dropdown Pemilihan Event -->
                    <div class="mx-auto my-6 max-w-sm">
                        <label for="event-select" class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Event</label>
                        <select
                            v-model="selectedEvent"
                            id="event-select"
                            class="block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >
                            <option :value="null">-- Silakan Pilih --</option>
                            <option v-for="event in events" :key="event.id" :value="event">{{ event.name }}</option>
                        </select>
                    </div>

                    <!-- Area QR Code (tampil setelah event dipilih) -->
                    <div v-if="selectedEvent">
                        <div class="relative inline-block h-[320px] w-[320px] rounded-lg bg-gray-100 p-4 dark:bg-gray-700">
                            <div v-if="isLoading" class="flex h-full w-full items-center justify-center">
                                <LoaderCircle class="h-12 w-12 animate-spin text-gray-500" />
                            </div>
                            <div v-else-if="lastAttendeeName" class="flex h-full w-full flex-col items-center justify-center text-green-500">
                                <CheckCircle class="h-16 w-16" />
                                <p class="mt-4 text-lg font-bold">Check-in Berhasil!</p>
                                <p class="text-gray-800 dark:text-gray-200">{{ lastAttendeeName }}</p>
                            </div>
                            <canvas v-show="!isLoading && !lastAttendeeName" id="qrcode-canvas"></canvas>
                        </div>

                        <div class="mt-4 text-sm text-gray-400">
                            <p class="flex items-center justify-center gap-2">
                                <QrCode class="h-4 w-4" />
                                <span>{{ statusMessage }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Pesan Awal -->
                    <div v-else class="mt-8 text-gray-500">
                        <p>Pilih event di atas untuk menampilkan QR Code absensi.</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
