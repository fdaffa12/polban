<script setup>
import { Head } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
import Footer from "@/Components/Footer.vue";

defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="title" />

    <div class="min-h-screen bg-[var(--color-background)]">
        <Navbar />

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <Footer />
    </div>
</template>
