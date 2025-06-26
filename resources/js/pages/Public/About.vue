<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Mail, MapPin, Phone } from 'lucide-vue-next';
import { onMounted } from 'vue';

// Leaflet Imports
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import L, { LatLng } from 'leaflet';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';
import 'leaflet/dist/leaflet.css';

// Leaflet Icon Configuration
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIcon2x,
    shadowUrl: markerShadow,
});

// Dummy data for the team section
const teamMembers = [
    {
        name: 'Naufal aka Snafcat',
        role: 'Developer',
        imageUrl: 'https://avatars.githubusercontent.com/u/33770553?v=4',
    },
    {
        name: 'Ahmad Arief Prasetyo',
        role: 'SysSquad',
        imageUrl: 'https://kajian.snafcat.com/dev/ahmad-arief.jpeg',
    },
    {
        name: 'Novi',
        role: 'HappyDesk',
        imageUrl: 'https://kajian.snafcat.com/dev/novi.jpeg',
    },
];

const initMap = () => {
    // Koordinat untuk PDM Kota Magelang
    const coords = new LatLng(-7.48647205587111, 110.21859311884923);
    const map = L.map('contact-map', { scrollWheelZoom: false }).setView(coords, 20);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);
    L.marker(coords).addTo(map).bindPopup('<b>Kantor PDM Kota Magelang</b><br>Jl. Tidar No. 21').openPopup();
};

onMounted(() => {
    initMap();
});
</script>

<template>

    <Head title="Tentang Kami" />
    <PublicLayout>
        <!-- Jumbotron Section -->
        <div
            class="relative isolate overflow-hidden bg-gray-500 px-6 py-24 text-center shadow-2xl sm:px-16 sm:py-32 dark:bg-gray-900">
            <h1
                class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Tentang Kami & Kontak</h1>
            <p
                class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300">
                Mengenal lebih dekat siapa kami, visi kami,
                dan bagaimana Anda bisa terhubung dengan
                kami.
            </p>
            <div class="absolute -top-24 left-1/2 -z-10 h-[50rem] w-[50rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]"
                aria-hidden="true">
                <svg viewBox="0 0 1024 1024"
                    class="absolute left-1/2 -translate-x-1/2 -translate-y-1/2">
                    <circle cx="512" cy="512" r="512"
                        fill="url(#8d958450-c69f-4251-94bc-4e091a323369)"
                        fill-opacity="0.7"></circle>
                    <defs>
                        <radialGradient
                            id="8d958450-c69f-4251-94bc-4e091a323369">
                            <stop stop-color="#7775D6">
                            </stop>
                            <stop offset="1"
                                stop-color="#E935C1"></stop>
                        </radialGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900">
            <!-- App Logo & About Us Section -->
            <div
                class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
                <div
                    class="grid grid-cols-1 items-center gap-x-16 gap-y-10 lg:grid-cols-2">
                    <div
                        class="flex items-center justify-center">
                        <img :src="'logo/Logo_SintesaTeks.svg'"
                            alt="SINTESA" class="w-full">
                    </div>
                    <div>
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Platform Digital PDM Kota
                            Magelang</h2>
                        <p
                            class="mt-4 text-gray-600 dark:text-gray-300">
                            Platform ini didedikasikan untuk
                            digitalisasi manajemen kegiatan
                            di lingkungan Pimpinan Daerah
                            Muhammadiyah (PDM) Kota
                            Magelang. Dengan teknologi
                            modern, kami menyederhanakan
                            proses pendaftaran, absensi, dan
                            pengelolaan acara, demi
                            terwujudnya dakwah digital yang
                            terorganisir dan efisien.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact & Map Section -->
            <div
                class="bg-gray-50 py-24 sm:py-32 dark:bg-gray-950">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div
                        class="grid grid-cols-1 gap-x-16 gap-y-10 lg:grid-cols-2">
                        <!-- Left Column: Contact Info & Map -->
                        <div class="space-y-8">
                            <div>
                                <h2
                                    class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    Hubungi Kami</h2>
                                <p
                                    class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                                    Kami siap membantu Anda.
                                    Jangan ragu untuk
                                    menghubungi kami melalui
                                    informasi di bawah ini
                                    atau kunjungi kantor
                                    kami.
                                </p>
                            </div>
                            <div id="contact-map"
                                class="z-0 h-96 w-full rounded-lg shadow-md">
                            </div>
                        </div>
                        <!-- Right Column: Explanation -->
                        <div class="space-y-8">
                            <div>
                                <h2
                                    class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                    Kunjungi Kami</h2>
                                <div
                                    class="mt-6 space-y-4 text-base text-gray-600 dark:text-gray-300">
                                    <p>
                                        Kantor pusat kami
                                        terbuka untuk
                                        kunjungan pada jam
                                        kerja. Kami senang
                                        bisa berdiskusi
                                        langsung mengenai
                                        kebutuhan event Anda
                                        atau sekadar berbagi
                                        ide.
                                    </p>
                                    <ul role="list"
                                        class="mt-8 space-y-4">
                                        <li
                                            class="flex gap-x-3">
                                            <MapPin
                                                class="mt-1 h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                            <span><strong
                                                    class="font-semibold text-gray-900 dark:text-white">Alamat:</strong><br />Jl.
                                                Tidar No.21,
                                                Magersari,
                                                Kec.
                                                Magelang
                                                Sel., Kota
                                                Magelang,
                                                Jawa Tengah
                                                56126</span>
                                        </li>
                                        <li
                                            class="flex gap-x-3">
                                            <Phone
                                                class="mt-1 h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                            <span><strong
                                                    class="font-semibold text-gray-900 dark:text-white">Telepon:</strong><br />(0293)
                                                3643-889</span>
                                        </li>
                                        <li
                                            class="flex gap-x-3">
                                            <Mail
                                                class="mt-1 h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                            <span><strong
                                                    class="font-semibold text-gray-900 dark:text-white">Email:</strong><br /><a
                                                    href="mailto:muhammadiyahkotamgl@gmail.com"
                                                    class="hover:text-indigo-600">muhammadiyahkotamgl@gmail.com</a></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Our Team Section -->
            <div class="py-24 sm:py-32">
                <div class="mx-auto max-w-7xl px-6 lg:px-8">
                    <div
                        class="mx-auto max-w-2xl text-center">
                        <h2
                            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-white">
                            Tim Kami</h2>
                        <p
                            class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                            Bertemu dengan orang-orang hebat
                            di balik platform ini, yang
                            berdedikasi untuk kesuksesan
                            setiap kegiatan.
                        </p>
                    </div>
                    <ul role="list"
                        class="mx-auto mt-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3">
                        <li v-for="person in teamMembers"
                            :key="person.name"
                            class="text-center">
                            <img class="mx-auto h-56 w-56 rounded-full object-cover"
                                :src="person.imageUrl"
                                :alt="`Foto ${person.name}`" />
                            <h3
                                class="mt-6 text-base leading-7 font-semibold tracking-tight text-gray-900 dark:text-white">
                                {{ person.name }}</h3>
                            <p
                                class="text-sm leading-6 text-indigo-600 dark:text-indigo-400">
                                {{ person.role }}</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
