<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import useSweetAlert from '@/composables/useSweetAlert';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { List, Plus, Printer, Trash2 } from 'lucide-vue-next';
import QRCode from 'qrcode';
import { h, ref, watch } from 'vue';
import { renderToString } from 'vue/server-renderer';

//================================================================
// TIPE LOKAL
//================================================================
interface StaticQr {
    id: number;
    label: string;
    code: string;
}
interface EventInfo {
    id: number;
    name: string;
    static_qrs?: StaticQr[];
}
interface PageProps {
    events: EventInfo[];
    selectedEvent: EventInfo | null;
    filters: { event_id: string | null };
}

//----------------------------------------------------------------
// SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();
const { confirmDelete } = useSweetAlert();

const selectedEventId = ref(props.filters.event_id || '');

const form = useForm({ label: '' });

//----------------------------------------------------------------
// FUNGSI
//----------------------------------------------------------------
const submitNewQr = () => {
    if (!props.selectedEvent) return;
    form.post(route('admin.events.static-qrs.store', props.selectedEvent.id), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    });
};

const generateQr = (qr: StaticQr) => {
    const canvas = document.getElementById(`qr-canvas-${qr.id}`);
    const url = route('public.checkin.form', { code: qr.code });
    if (canvas) {
        QRCode.toCanvas(canvas, url, { width: 150, margin: 1, color: { dark: '#111827', light: '#FFFFFF' } });
    }
};

const deleteQr = (qr: StaticQr) => {
    confirmDelete(() => {
        router.delete(route('admin.events.static-qrs.destroy', { static_qr: qr.id }), {
            preserveScroll: true,
        });
    });
};

const printQrCode = async (qr: StaticQr, event: EventInfo) => {
    const qrCodeUrl = await QRCode.toDataURL(route('public.checkin.form', { code: qr.code }), {
        width: 380,
        margin: 2,
        errorCorrectionLevel: 'H',
    });
    const appLogoHtml = await renderToString(h(AppLogoIcon));
    openPrintWindow(
        `
        <div class="ticket">
            <div class="header">
                <div class="logo">${appLogoHtml}</div>
                <h1>${event.name}</h1>
            </div>
            <div class="main-content">
                <img src="${qrCodeUrl}" alt="QR Code" />
                <h2>${qr.label}</h2>
            </div>
        </div>
    `,
        `Cetak QR Code - ${qr.label}`,
    );
};

// --- START: Fungsi Cetak Semua ---
const printAllQrCodes = async (qrs: StaticQr[], event: EventInfo) => {
    if (!qrs || qrs.length === 0) return;

    const appLogoHtml = await renderToString(h(AppLogoIcon));

    // Generate all QR code data URLs in parallel for better performance
    const qrCodePromises = qrs.map((qr) =>
        QRCode.toDataURL(route('public.checkin.form', { code: qr.code }), {
            width: 380,
            margin: 2,
            errorCorrectionLevel: 'H',
        }),
    );
    const qrCodeUrls = await Promise.all(qrCodePromises);

    // Build the HTML for all tickets
    const allTicketsHtml = qrs
        .map(
            (qr, index) => `
        <div class="ticket">
            <div class="header">
                <div class="logo">${appLogoHtml}</div>
                <h1>${event.name}</h1>
            </div>
            <div class="main-content">
                <img src="${qrCodeUrls[index]}" alt="QR Code" />
                <h2>${qr.label}</h2>
            </div>
        </div>
    `,
        )
        .join('');

    openPrintWindow(allTicketsHtml, `Cetak Semua QR - ${event.name}`, true);
};
// --- END: Fungsi Cetak Semua ---

// Helper function to create and manage the print window
const openPrintWindow = (content: string, title: string, isBulk: boolean = false) => {
    const printContent = `
        <html>
            <head>
                <title>${title}</title>
                <style>
                    @page {
                        size: A6;
                        margin: 0;
                    }
                    * {
                        box-sizing: border-box;
                    }
                    body {
                        margin: 0;
                        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                        ${isBulk ? '' : 'display: flex; align-items: center; justify-content: center; width: 10.5cm; height: 14.8cm;'}
                    }
                    .ticket {
                        padding: 0.5cm;
                        width: 10.5cm;
                        height: 14.8cm;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        border: 1px solid #ddd;
                        page-break-after: always;
                    }
                    .ticket:last-child {
                        page-break-after: auto;
                    }
                    .header {
                        display: flex; align-items: center; gap: 12px;
                        border-bottom: 1px solid #ddd; padding-bottom: 12px; flex-shrink: 0;
                    }
                    .header .logo { width: 40px; height: 40px; flex-shrink: 0; }
                    .header h1 { font-size: 14px; margin: 0; color: #333; text-align: left; line-height: 1.3; }
                    .main-content {
                        flex-grow: 1; display: flex; flex-direction: column;
                        align-items: center; justify-content: center; padding: 12px 0;
                    }
                    .main-content img { max-width: 90%; height: auto; }
                    .main-content h2 { font-size: 22px; font-weight: bold; margin: 12px 0 0 0; color: #000; }
                </style>
            </head>
            <body>
                ${content}
            </body>
        </html>
    `;

    const printWindow = window.open('', '_blank');
    if (printWindow) {
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }
};

//----------------------------------------------------------------
// WATCHER & DATA
//----------------------------------------------------------------
watch(selectedEventId, (newEventId) => {
    router.get(
        route('admin.events.static-qrs.index'),
        { event_id: newEventId },
        {
            preserveState: true,
            replace: true,
        },
    );
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Manajemen Event', href: route('admin.events.index') },
    { title: 'QR Code Statis', href: route('admin.events.static-qrs.index') },
];
</script>

<template>

    <Head title="Manajemen QR Code Statis" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Dropdown Pemilihan Event -->
                <div
                    class="mb-8 overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <label for="event-select"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih
                        Event untuk Dikelola</label>
                    <select v-model="selectedEventId"
                        id="event-select"
                        class="mt-1 block w-full rounded-md border-gray-300 p-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        <option value="">-- Silakan Pilih
                            Event --</option>
                        <option v-for="event in events"
                            :key="event.id"
                            :value="event.id">{{ event.name
                            }}</option>
                    </select>
                </div>

                <!-- Konten ditampilkan setelah event dipilih -->
                <div v-if="selectedEvent" class="space-y-8">
                    <!-- Form Tambah QR -->
                    <div
                        class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Buat QR Code Baru untuk: {{
                                selectedEvent.name }}</h3>
                        <form @submit.prevent="submitNewQr"
                            class="mt-4 flex items-end gap-4">
                            <div class="flex-grow">
                                <label for="label"
                                    class="mb-2 block text-sm font-medium">Label
                                    QR Code</label>
                                <input v-model="form.label"
                                    id="label" type="text"
                                    placeholder="Contoh: Pintu Masuk Barat"
                                    class="mt-1 block w-full rounded-md p-2 dark:bg-gray-700"
                                    required />
                                <p v-if="form.errors.label"
                                    class="mt-1 text-sm text-red-500">
                                    {{ form.errors.label }}
                                </p>
                            </div>
                            <button type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                                <Plus class="h-4 w-4" />
                                Buat
                            </button>
                        </form>
                    </div>

                    <!-- Daftar QR Code -->
                    <div
                        class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div
                            class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
                            <h2
                                class="text-xl font-bold text-gray-800 dark:text-gray-200">
                                Daftar QR Code</h2>
                            <!-- Tombol Cetak Semua -->
                            <button
                                v-if="selectedEvent.static_qrs && selectedEvent.static_qrs.length > 0"
                                @click="printAllQrCodes(selectedEvent.static_qrs, selectedEvent)"
                                class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                                <Printer class="h-4 w-4" />
                                Cetak Semua
                            </button>
                        </div>

                        <div v-if="!selectedEvent.static_qrs || selectedEvent.static_qrs.length === 0"
                            class="mt-6 text-center text-gray-500">
                            Belum ada QR Code dibuat.
                        </div>
                        <div v-else
                            class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <div v-for="qr in selectedEvent.static_qrs"
                                :key="qr.id"
                                class="flex flex-col items-center rounded-lg border p-4 dark:border-gray-700">
                                <h4
                                    class="text-lg font-semibold dark:text-white">
                                    {{ qr.label }}</h4>
                                <canvas
                                    :id="`qr-canvas-${qr.id}`"
                                    @vue:mounted="() => generateQr(qr)"></canvas>
                                <p
                                    class="mt-2 text-center font-mono text-xs break-all text-gray-400">
                                    {{ qr.code }}</p>
                                <div
                                    class="mt-4 flex w-full justify-between">
                                    <button
                                        @click="printQrCode(qr, selectedEvent)"
                                        class="text-sm text-blue-600 hover:underline">Cetak</button>
                                    <button
                                        @click="deleteQr(qr)"
                                        class="text-red-600 hover:text-red-500">
                                        <Trash2
                                            class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pesan Awal -->
                <div v-else
                    class="mt-8 flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-gray-300 p-12 text-center dark:border-gray-600">
                    <List class="h-16 w-16 text-gray-400" />
                    <h3
                        class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">
                        Pilih Event untuk Memulai</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Pilih event dari dropdown di atas
                        untuk mengelola QR Code statisnya.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
