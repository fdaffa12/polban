<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Carousel, Slide, Navigation } from 'vue3-carousel';
import 'vue3-carousel/dist/carousel.css';

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

        <!-- Hero Section -->
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gray-900">
            <div class="absolute inset-0 z-0">
                <img 
                    :src="about.image"
                    class="w-full h-full object-cover"
                    alt="HMJTK Hero"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-gray-900/70 via-gray-900/60 to-gray-900/80"></div>
                <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
            </div>

            <div class="container-custom relative z-10">
                <div class="max-w-4xl mx-auto text-center space-y-8 animate-fade-in px-4">
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight">
                        {{ about.title }}
                        <span class="text-[var(--color-primary)] block mt-2">
                            HMJTK Polban
                        </span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-300 max-w-2xl mx-auto">
                        Himpunan Mahasiswa Jurusan Teknik Komputer dan Informatika
                    </p>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section class="py-20 bg-white">
            <div class="container-custom">
                <h2 class="about-section-title text-center mb-16">Gallery</h2>
                <div class="max-w-7xl mx-auto">
                    <Carousel
                        v-if="about.au_multiple_image && about.au_multiple_image.length"
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
                            class="px-2"
                        >
                            <div class="relative aspect-[3/4] rounded-xl overflow-hidden group">
                                <img 
                                    :src="image"
                                    :alt="`Gallery Image ${index + 1}`"
                                    class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>
                        </Slide>

                        <template #addons>
                            <Navigation />
                        </template>
                    </Carousel>
                </div>
            </div>
        </section>

        <!-- About Content Section -->
        <section class="py-20 bg-[var(--color-background)]/5">
            <div class="container-custom">
                <div class="max-w-4xl mx-auto space-y-8">
                    <h2 class="about-section-title text-center">
                        About Us
                    </h2>
                    <div class="prose prose-lg mx-auto" v-html="about.au_desc"></div>
                </div>
            </div>
        </section>

        <!-- History Section -->
        <section class="py-20 bg-white">
            <div class="container-custom">
                <div class="max-w-4xl mx-auto space-y-8">
                    <h2 class="about-section-title text-center">
                        Our History
                    </h2>
                    <div class="prose prose-lg mx-auto" v-html="about.history"></div>
                </div>
            </div>
        </section>

        <!-- Additional Content Section -->
        <section class="py-20 bg-[var(--color-background)]/5">
            <div class="container-custom">
                <div class="max-w-4xl mx-auto space-y-8">
                    <div class="prose prose-lg mx-auto" v-html="about.au_values"></div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.gallery-carousel :deep(.carousel__slide) {
    @apply transform transition-all duration-500;
    opacity: 0.5;
    scale: 0.9;
}

.gallery-carousel :deep(.carousel__slide--active) {
    @apply shadow-xl;
    opacity: 1;
    scale: 1;
}

.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    @apply bg-white/80 backdrop-blur-sm rounded-full p-2 shadow-lg;
    width: 40px;
    height: 40px;
}

.prose {
    @apply text-[var(--light-text)];
    
    h1, h2, h3, h4, h5, h6 {
        @apply text-[var(--text-color)] font-bold;
    }
    
    a {
        @apply text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] 
               transition-colors duration-300;
    }

    strong {
        @apply text-[var(--text-color)];
    }
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}
</style>