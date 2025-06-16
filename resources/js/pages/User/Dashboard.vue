<script setup>
import MobileLayout from '@/layouts/MobileLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Beranda" />
    <MobileLayout>
        <div class="mb-5 rounded-lg bg-white p-4 shadow dark:bg-gray-800">
            <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-gray-100">Selamat Datang!</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Ini adalah contoh konten utama pada aplikasi mobile Anda. Anda bisa menempatkan berbagai komponen di sini.
            </p>
        </div>

        <div class="mb-5 rounded-lg bg-white p-4 shadow dark:bg-gray-800">
            <h3 class="mb-3 font-semibold text-gray-900 dark:text-gray-100">Aktivitas Terbaru</h3>
            <ul class="space-y-3">
                <li class="flex items-center space-x-3">
                    <div class="rounded-full bg-green-100 p-2 dark:bg-green-500/20">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-green-600 dark:text-green-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Anda berhasil login.</span>
                </li>
                <li class="flex items-center space-x-3">
                    <div class="rounded-full bg-blue-100 p-2 dark:bg-blue-500/20">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-blue-600 dark:text-blue-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            />
                        </svg>
                    </div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">Profil Anda diperbarui.</span>
                </li>
            </ul>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
            <img
                src="https://images.unsplash.com/photo-1465146344425-f00d5f5c8f07?q=80&w=2952&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                alt="Random Nature"
                class="h-40 w-full object-cover"
            />
            <div class="p-4">
                <p class="font-semibold text-gray-800 dark:text-gray-200">Jelajahi Dunia</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Gambar placeholder dari Unsplash.</p>
            </div>
        </div>
    </MobileLayout>
</template>
