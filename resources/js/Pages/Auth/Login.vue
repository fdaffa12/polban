<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout title="Login">
        <Head title="Log in" />

        <section class="py-12 md:py-20">
            <div class="container-custom">
                <div class="flex min-h-[80vh] items-center justify-center">
                    <!-- Login Card -->
                    <div class="w-full max-w-md animate-fade-in">
                        <div class="relative">
                            <!-- Decorative Blobs -->
                            <div
                                class="absolute -top-4 -right-4 h-72 w-72 animate-blob rounded-full bg-[var(--color-primary-dark)] opacity-10 mix-blend-multiply blur-xl filter"
                            ></div>
                            <div
                                class="absolute -bottom-8 -left-4 h-72 w-72 animate-blob animation-delay-4000 rounded-full bg-[var(--color-secondary)] opacity-10 mix-blend-multiply blur-xl filter"
                            ></div>

                            <!-- Login Form Card -->
                            <div
                                class="relative rounded-2xl bg-white/80 p-8 shadow-xl backdrop-blur-sm"
                            >
                                <!-- Header -->
                                <div class="mb-8 text-center">
                                    <h1
                                        class="mb-2 text-3xl font-bold"
                                        style="color: var(--color-primary-dark)"
                                    >
                                        Welcome Back
                                    </h1>
                                    <p
                                        class="text-sm"
                                        style="color: var(--light-text)"
                                    >
                                        Please sign in to your account
                                    </p>
                                </div>

                                <!-- Status Message -->
                                <div
                                    v-if="status"
                                    class="mb-4 rounded-lg bg-green-50 p-4 text-sm font-medium text-green-600"
                                >
                                    {{ status }}
                                </div>

                                <!-- Login Form -->
                                <form
                                    @submit.prevent="submit"
                                    class="space-y-6"
                                >
                                    <div>
                                        <InputLabel
                                            for="email"
                                            value="Email"
                                            class="text-[var(--text-color)]"
                                        />
                                        <TextInput
                                            id="email"
                                            type="email"
                                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white/50 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                            v-model="form.email"
                                            required
                                            autofocus
                                            autocomplete="username"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.email"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel
                                            for="password"
                                            value="Password"
                                            class="text-[var(--text-color)]"
                                        />
                                        <TextInput
                                            id="password"
                                            type="password"
                                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-white/50 shadow-sm focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                            v-model="form.password"
                                            required
                                            autocomplete="current-password"
                                        />
                                        <InputError
                                            class="mt-2"
                                            :message="form.errors.password"
                                        />
                                    </div>

                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <label class="flex items-center">
                                            <Checkbox
                                                name="remember"
                                                v-model:checked="form.remember"
                                                class="rounded border-gray-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                            />
                                            <span
                                                class="ml-2 text-sm"
                                                style="color: var(--light-text)"
                                                >Remember me</span
                                            >
                                        </label>
                                    </div>

                                    <div>
                                        <button
                                            type="submit"
                                            class="btn-primary w-full"
                                            :class="{
                                                'opacity-75 cursor-not-allowed':
                                                    form.processing,
                                            }"
                                            :disabled="form.processing"
                                        >
                                            <span v-if="form.processing">
                                                Processing...
                                            </span>
                                            <span v-else> Sign In </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.btn-primary {
    @apply flex items-center justify-center rounded-lg px-6 py-3 text-base font-medium text-white transition-all duration-300;
    background-color: var(--color-primary);
}

.btn-primary:hover {
    background-color: var(--color-primary-dark);
    @apply shadow-lg;
}

.btn-primary:focus {
    @apply outline-none ring-2 ring-[var(--color-primary)] ring-offset-2;
}
</style>
