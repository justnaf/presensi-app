<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight, Shield, University, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';

//================================================================
// DEFINISI TIPE LOKAL
//================================================================
interface CalendarEvent {
    id: number;
    name: string;
    date: string; // Format 'YYYY-MM-DD'
}

interface PageProps {
    stats: {
        rolesCount: number;
        institutionsCount: number;
        usersCount: number;
    };
    eventsForCalendar: CalendarEvent[];
}

//----------------------------------------------------------------
// PROPS & SETUP
//----------------------------------------------------------------
const props = defineProps<PageProps>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/admin/dashboard',
    },
];

//----------------------------------------------------------------
// LOGIKA KALENDER & EVENT
//----------------------------------------------------------------
const currentDate = ref(new Date());

// Buat Set untuk pencarian tanggal event yang cepat
const eventDatesSet = computed(() => new Set(props.eventsForCalendar.map((e) => e.date)));

const monthYear = computed(() => {
    return currentDate.value.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
});

// Computed property untuk memfilter event pada bulan yang ditampilkan
const eventsInCurrentMonth = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    return props.eventsForCalendar.filter((event) => {
        const eventDate = new Date(event.date);
        return eventDate.getFullYear() === year && eventDate.getMonth() === month;
    });
});

const calendarDays = computed(() => {
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();
    const firstDayOfMonth = new Date(year, month, 1);
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const daysInMonth = lastDayOfMonth.getDate();
    const startDayOfWeek = firstDayOfMonth.getDay();

    const days = [];
    for (let i = 0; i < startDayOfWeek; i++) {
        days.push({ day: '', date: null, hasEvent: false });
    }
    for (let i = 1; i <= daysInMonth; i++) {
        const fullDate = new Date(year, month, i);
        const dateString = fullDate.toISOString().split('T')[0];
        days.push({
            day: i,
            date: fullDate,
            hasEvent: eventDatesSet.value.has(dateString),
        });
    }
    return days;
});

const goToPreviousMonth = () => {
    currentDate.value = new Date(currentDate.value.setMonth(currentDate.value.getMonth() - 1));
};

const goToNextMonth = () => {
    currentDate.value = new Date(currentDate.value.setMonth(currentDate.value.getMonth() + 1));
};

const isToday = (date: Date | null) => {
    if (!date) return false;
    const today = new Date();
    return date.getDate() === today.getDate() && date.getMonth() === today.getMonth() && date.getFullYear() === today.getFullYear();
};

const formatEventCardDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4 md:gap-8 md:p-6">
            <!-- Kartu Statistik -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div
                    class="relative flex flex-col justify-between overflow-hidden rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div>
                        <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400">Total Roles</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ props.stats.rolesCount }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <Shield :size="80" :stroke-width="2" class="text-gray-900 dark:text-gray-100" />
                    </div>
                </div>
                <div
                    class="relative flex flex-col justify-between overflow-hidden rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div>
                        <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400">Total Institutions</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ props.stats.institutionsCount }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <University :size="80" :stroke-width="2" class="text-gray-900 dark:text-gray-100" />
                    </div>
                </div>
                <div
                    class="relative flex flex-col justify-between overflow-hidden rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900"
                >
                    <div>
                        <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400">Total User (Non Admin)</h3>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ props.stats.usersCount }}</p>
                    </div>
                    <div class="absolute -right-4 -bottom-4 opacity-10">
                        <Users :size="80" :stroke-width="2" class="text-gray-900 dark:text-gray-100" />
                    </div>
                </div>
            </div>

            <!-- Bagian Kalender & Daftar Event -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Kolom Kalender (lebih lebar) -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 lg:col-span-2 dark:border-gray-800 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ monthYear }}</h2>
                        <div class="flex items-center space-x-2">
                            <button @click="goToPreviousMonth" class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <ChevronLeft class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                            </button>
                            <button @click="goToNextMonth" class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <ChevronRight class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-7 gap-1 text-center text-sm">
                        <div
                            v-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']"
                            :key="day"
                            class="font-medium text-gray-500 dark:text-gray-400"
                        >
                            {{ day }}
                        </div>
                        <div v-for="(day, index) in calendarDays" :key="index" class="relative pt-[100%]">
                            <div v-if="day.date" class="absolute inset-0 flex flex-col items-center justify-center">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full"
                                    :class="{ 'bg-indigo-600 text-white': isToday(day.date), 'text-gray-900 dark:text-gray-100': !isToday(day.date) }"
                                    >{{ day.day }}</span
                                >
                                <div v-if="day.hasEvent" class="mt-1 h-1.5 w-1.5 rounded-full bg-red-500"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Daftar Event -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 lg:col-span-1 dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Event di bulan {{ monthYear }}</h2>
                    <div class="mt-4 space-y-4">
                        <div v-if="eventsInCurrentMonth.length === 0" class="flex flex-col items-center justify-center py-8 text-center">
                            <CalendarIcon class="h-12 w-12 text-gray-400" />
                            <p class="mt-2 text-sm text-gray-500">Tidak ada event dijadwalkan.</p>
                        </div>
                        <div v-else v-for="event in eventsInCurrentMonth" :key="event.id">
                            <Link
                                :href="route('admin.events.edit', event.id)"
                                class="block rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                            >
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ event.name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatEventCardDate(event.date) }}</p>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
