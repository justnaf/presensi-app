<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head } from '@inertiajs/vue3';
import QRCode from 'qrcode'; // Import the qrcode library
import { onMounted } from 'vue';

//================================================================
// TYPE DEFINITIONS
//================================================================
interface EventInfo {
    name: string;
    start_date: string;
}
interface UserInfo {
    name: string;
}
interface TicketData {
    ticket_code: string;
    event: EventInfo;
    user: UserInfo;
}
interface PageProps {
    ticket: TicketData;
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();

const formatDate = (dateStr: string): string => {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

// Generate QR Code when component is mounted
onMounted(() => {
    const canvas = document.getElementById('qrcode-canvas');
    if (canvas) {
        QRCode.toCanvas(canvas, props.ticket.ticket_code, {
            width: 220,
            margin: 2,
            errorCorrectionLevel: 'H',
        });
    }
});
</script>

<template>
    <Head title="E-Ticket" />
    <MobileLayout>
        <div class="flex flex-col items-center justify-center p-4">
            <!-- Digital Ticket Card -->
            <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-800">
                <!-- Ticket Header -->
                <div class="bg-indigo-600 p-5 text-center text-white">
                    <p class="text-sm tracking-wider uppercase">Event E-Ticket</p>
                    <h1 class="mt-1 text-2xl font-bold">{{ ticket.event.name }}</h1>
                </div>

                <!-- QR Code Section -->
                <div class="flex flex-col items-center bg-gray-50 p-6 dark:bg-gray-700/50">
                    <p class="mb-4 text-center text-sm text-gray-600 dark:text-gray-300">Pindai kode QR ini di pintu masuk</p>
                    <div class="rounded-lg bg-white p-4 shadow-md">
                        <canvas id="qrcode-canvas"></canvas>
                    </div>
                </div>

                <!-- Ticket Details Section -->
                <div class="p-6">
                    <div class="space-y-4 text-center">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Atas Nama</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ ticket.user.name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal</p>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ formatDate(ticket.event.start_date) }}</p>
                        </div>
                    </div>

                    <!-- Dashed Separator -->
                    <div class="my-6 border-t-2 border-dashed border-gray-300 dark:border-gray-600"></div>

                    <!-- Ticket Code -->
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Kode Tiket</p>
                        <p class="font-mono text-lg font-medium tracking-widest text-gray-800 select-all dark:text-gray-200">
                            {{ ticket.ticket_code }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </MobileLayout>
</template>
