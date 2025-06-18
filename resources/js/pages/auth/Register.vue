<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, User } from 'lucide-vue-next';
import { ref } from 'vue';

// 1. Definisikan tipe untuk props institusi
interface Institution {
    id: number;
    name: string;
}
interface PageProps {
    institutions: Institution[];
}

// 2. Terima props institutions dari controller
const props = defineProps<PageProps>();

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    avatar: null as File | null,
    institution_id: null as number | null, // Tambahkan institution_id ke form
});

// State untuk pratinjau avatar
const avatarPreview = ref<string | null>(null);

// Fungsi untuk menangani unggahan avatar
function handleAvatarChange(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (!file) return;

    form.avatar = file;
    avatarPreview.value = URL.createObjectURL(file);
}

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-4">
                <!-- Bagian Input Avatar -->
                <div class="flex flex-col items-center gap-2">
                    <Label for="avatar-upload" class="cursor-pointer">
                        <div class="relative h-24 w-24 rounded-full">
                            <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar Preview" class="h-full w-full rounded-full object-cover" />
                            <div v-else class="flex h-full w-full items-center justify-center rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <User class="h-12 w-12 text-neutral-400" />
                            </div>
                            <div
                                class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 transition-opacity hover:opacity-100"
                            >
                                <span class="text-xs font-semibold text-white">Change</span>
                            </div>
                        </div>
                    </Label>
                    <input id="avatar-upload" type="file" class="hidden" @change="handleAvatarChange" accept="image/*" />
                    <InputError :message="form.errors.avatar" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input id="username" type="text" required v-model="form.username" placeholder="Username" />
                    <InputError :message="form.errors.username" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <!-- Dropdown Institusi -->
                <div class="grid gap-2">
                    <Label for="institution_id">Institution</Label>
                    <select
                        id="institution_id"
                        v-model="form.institution_id"
                        required
                        class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:ring-2 focus:ring-ring focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    >
                        <option :value="null" disabled>-- Pilih institusi Anda --</option>
                        <option v-for="institution in institutions" :key="institution.id" :value="institution.id">
                            {{ institution.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.institution_id" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input id="password" type="password" required autocomplete="new-password" v-model="form.password" placeholder="Password" />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" class="mt-2 w-full" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
