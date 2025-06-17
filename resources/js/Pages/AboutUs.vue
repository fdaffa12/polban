<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Carousel, Slide, Navigation } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";

const props = defineProps({
    about: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <GuestLayout title="About Us">
        <Head title="About Us" />

        <!-- Hero Section with Parallax -->
        <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 z-0">
                <img 
                    :src="about.image"
                    class="w-full h-full object-cover scale-105"
                    alt="HMJTK Hero"
                />
                <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-primary-dark)]/90 via-black/50 to-[var(--color-primary)]/80"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--color-primary-dark)_0%,transparent_100%)] opacity-30"></div>
            </div>

            <div class="container-custom relative z-10">
                <div class="max-w-4xl mx-auto text-center space-y-8 animate-fade-in px-4">
                    <span class="inline-block text-white/80 text-lg md:text-xl tracking-wider mb-4 font-light">Welcome to</span>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-bold text-white leading-tight tracking-tight">
                        {{ about.title }}
                        <span class="text-[var(--color-primary)] block mt-4 text-3xl md:text-4xl font-light">
                            HMJTK Polban
                        </span>
                    </h1>
                    <p class="text-xl md:text-2xl text-white/80 max-w-2xl mx-auto font-light leading-relaxed">
                        Himpunan Mahasiswa Jurusan Teknik Komputer dan Informatika
                    </p>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </section>

        <!-- About & Gallery Section -->
        <section class="py-32 bg-gradient-to-b from-white to-[var(--color-background)]/10">
            <div class="container-custom">
                <!-- About Content -->
                <div class="max-w-4xl mx-auto mb-32">
                    <span class="block text-[var(--color-primary)] font-medium text-lg mb-4 text-center">Our Story</span>
                    <h2 class="text-4xl md:text-5xl font-bold text-center mb-16 text-[var(--text-color)]">
                        About Us
                    </h2>
                    <div class="prose prose-lg mx-auto prose-p:leading-relaxed" v-html="about.au_desc"></div>
                </div>

                <!-- Gallery -->
                <div class="max-w-7xl mx-auto">
                    <Carousel
                        v-if="about.au_multiple_image?.length"
                        :items-to-show="4"
                        :wrap-around="true"
                        :transition="500"
                        :autoplay="3000"
                        :snapAlign="'center'"
                        class="gallery-carousel"
                    >
                        <Slide 
                            v-for="(image, index) in about.au_multiple_image"
                            :key="index"
                            class="px-3"
                        >
                            <div class="relative aspect-[4/5] rounded-2xl overflow-hidden group">
                                <img 
                                    :src="image"
                                    :alt="`Gallery Image ${index + 1}`"
                                    class="w-full h-full object-cover transform transition duration-700 group-hover:scale-110"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                            </div>
                        </Slide>

                        <template #addons>
                            <Navigation />
                        </template>
                    </Carousel>
                </div>
            </div>
        </section>

        <!-- History & Values Section -->
        <section class="py-32 bg-[var(--color-background)]/5">
            <div class="container-custom">
                <div class="grid md:grid-cols-2 gap-20">
                    <!-- History Column -->
                    <div class="space-y-8 backdrop-blur-sm bg-white/30 p-8 rounded-2xl shadow-lg">
                        <span class="block text-[var(--color-primary)] font-medium">Looking Back</span>
                        <h2 class="text-3xl font-bold text-[var(--text-color)]">Our History</h2>
                        <div class="prose prose-lg prose-p:leading-relaxed" v-html="about.history"></div>
                    </div>

                    <!-- Values Column -->
                    <div class="space-y-8 backdrop-blur-sm bg-white/30 p-8 rounded-2xl shadow-lg">
                        <span class="block text-[var(--color-primary)] font-medium">What Drives Us</span>
                        <h2 class="text-3xl font-bold text-[var(--text-color)]">Our Values</h2>
                        <div class="prose prose-lg prose-p:leading-relaxed" v-html="about.au_values"></div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.gallery-carousel :deep(.carousel__slide) {
    @apply transform transition-all duration-700;
    opacity: 0.6;
    scale: 0.95;
}

.gallery-carousel :deep(.carousel__slide--active) {
    @apply shadow-2xl;
    opacity: 1;
    scale: 1;
}

.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    @apply bg-white/90 backdrop-blur-sm rounded-full p-3 shadow-xl 
           transition-all duration-300 hover:scale-110;
    width: 48px;
    height: 48px;
}

.prose {
    @apply text-[var(--light-text)];

    h1, h2, h3, h4, h5, h6 {
        @apply text-[var(--text-color)] font-bold tracking-tight;
    }

    a {
        @apply text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] 
               transition-all duration-300;
    }

    p {
        @apply text-lg leading-relaxed;
    }

    strong {
        @apply text-[var(--text-color)] font-semibold;
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}
</style>
