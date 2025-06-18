<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Html5QrcodeScanner } from 'html5-qrcode';
import { ScanLine, XCircle } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';

const scannerInstance = ref<Html5QrcodeScanner | null>(null);
const scanError = ref<string | null>(null);

const onScanSuccess = (decodedText: string) => {
    // Stop the scanner immediately after a successful scan
    if (scannerInstance.value) {
        scannerInstance.value.clear();
    }

    // Redirect the user to the URL from the QR code
    // The browser will handle the navigation
    window.location.href = decodedText;
};

const onScanFailure = (error: any) => {
    // This can be noisy, so we only handle specific, persistent errors
    if (error && error.type === 0) {
        // NOT_FOUND_ERROR
        scanError.value = 'Arahkan kamera ke QR Code yang valid.';
    }
};

onMounted(() => {
    // Configuration for the scanner
    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        // Important for mobile experience: use the rear camera
        facingMode: 'environment',
    };

    const scanner = new Html5QrcodeScanner(
        'qr-reader',
        config,
        false, // Set verbose to false
    );

    scanner.render(onScanSuccess, onScanFailure);
    scannerInstance.value = scanner;
});

onUnmounted(() => {
    // Ensure the scanner is properly cleaned up when leaving the page
    if (scannerInstance.value) {
        scannerInstance.value.clear().catch((err) => {
            console.error('Failed to clear scanner on unmount.', err);
        });
    }
});
</script>

<template>
    <Head title="Pindai QR Code" />
    <MobileLayout>
        <!-- Header Halaman -->
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pindai Kode Kehadiran</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Arahkan kamera Anda ke QR Code yang tersedia di lokasi event.</p>
        </div>

        <!-- Scanner Container -->
        <div class="relative mx-auto w-full max-w-sm overflow-hidden rounded-xl border-4 border-white bg-black shadow-lg dark:border-gray-800">
            <div id="qr-reader"></div>
            <!-- Animated Scan Line -->
            <div class="scan-line absolute top-0 h-1 w-full bg-red-500/80 shadow-[0_0_10px_theme(colors.red.500)]"></div>
        </div>

        <!-- Status Message -->
        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <div v-if="scanError" class="flex items-center justify-center gap-2 text-yellow-600 dark:text-yellow-400">
                <XCircle class="h-5 w-5" />
                <span>{{ scanError }}</span>
            </div>
            <div v-else class="flex items-center justify-center gap-2">
                <ScanLine class="h-5 w-5" />
                <span>Menunggu untuk memindai...</span>
            </div>
        </div>
    </MobileLayout>
</template>

<style>
/* Animation for the scan line */
#qr-reader .scan-line {
    animation: scan 2.5s infinite linear;
}

@keyframes scan {
    0% {
        transform: translateY(-20px);
        opacity: 0;
    }
    20% {
        transform: translateY(0px);
        opacity: 1;
    }
    80% {
        transform: translateY(240px);
        opacity: 1;
    }
    100% {
        transform: translateY(260px);
        opacity: 0;
    }
}
</style>
