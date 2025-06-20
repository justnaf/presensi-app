<script setup lang="ts">
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

//================================================================
// DEFINISI TIPE LOKAL
//================================================================

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
</script>

<template>
    <Head title="Beranda" />
    <MobileLayout>
        <!-- Kartu Selamat Datang -->
        <div class="mb-6 rounded-xl bg-indigo-600 p-5 text-white shadow-lg">
            <h1 class="text-xl font-bold">Selamat Datang!</h1>
            <p class="mt-1 text-sm opacity-90">Temukan dan ikuti berbagai kegiatan menarik yang akan datang.</p>
        </div>

        <!-- Judul Bagian -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Kegiatan Terbaru</h2>
            <Link :href="route('activities.index')" class="text-sm font-medium text-indigo-600 hover:underline dark:text-indigo-400"
                >Lihat Semua</Link
            >
        </div>
    </MobileLayout>
</template>
