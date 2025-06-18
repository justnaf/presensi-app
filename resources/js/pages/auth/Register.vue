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

const form = useForm({
    name: '',
    username: '', // Add username
    email: '',
    password: '',
    password_confirmation: '',
    avatar: null as File | null, // Add avatar
});

// State for the avatar preview
const avatarPreview = ref<string | null>(null);

// Handle file input changes for the avatar
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
                <!-- Avatar Input Section -->
                <div class="flex flex-col items-center gap-2">
                    <Label for="avatar-upload">
                        <div class="relative h-24 w-24 cursor-pointer rounded-full">
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

                <!-- Username Input -->
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
