<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Carousel, Slide, Navigation } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";
import { useIntersectionObserver } from "@/composables/useIntersectionObserver";

const props = defineProps({
    about: {
        type: Object,
        required: true,
    },
});

useIntersectionObserver();
</script>

<template>
    <GuestLayout title="About Us">
        <Head title="About Us" />

        <!-- Hero Section -->
        <section class="py-12 bg-white">
            <div class="container-custom">
                <div class="max-w-5xl">
                    <div class="space-y-6 float-in-section delay-100">
                        <div class="space-y-2">
                            <span
                                class="text-[var(--color-primary)] font-semibold text-lg tracking-wide"
                            >
                                About Us
                            </span>
                            <h1
                                class="text-4xl md:text-5xl lg:text-6xl font-bold text-[var(--text-color)] leading-tight"
                            >
                                {{ about.title }}
                                <span
                                    class="block text-xl md:text-2xl font-light text-[var(--light-text)] mt-4"
                                >
                                    Himpunan Mahasiswa Jurusan Teknik Kimia
                                </span>
                            </h1>
                        </div>

                        <div class="w-20 h-1 bg-[var(--color-primary)]"></div>

                        <div class="prose prose-lg max-w-3xl">
                            <div
                                class="text-lg md:text-xl text-[var(--light-text)]"
                            >
                                {{ about.description }}
                            </div>
                            <div class="mt-6" v-html="about.au_desc"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section
            class="py-12 bg-gradient-to-b from-white to-[var(--color-background)]/10"
        >
            <div class="container-custom">
                <!-- Gallery -->
                <div class="max-w-7xl mx-auto">
                    <!-- Gallery Title -->
                    <div
                        class="text-center max-w-3xl mx-auto mb-16 space-y-4 float-in-section delay-100"
                    >
                        <h2
                            class="text-4xl md:text-5xl font-bold text-[var(--text-color)] leading-tight"
                        >
                            HMJTK Transformation
                        </h2>
                        <div
                            class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                        ></div>
                    </div>

                    <!-- Responsive Carousel -->
                    <div
                        class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 float-in-section delay-200"
                    >
                        <Carousel
                            v-if="about.au_multiple_image?.length"
                            :items-to-show="4"
                            :wrap-around="true"
                            :transition="500"
                            :snap-align="'center'"
                            :autoplay="3000"
                            :breakpoints="{
                                320: {
                                    itemsToShow: 1,
                                    snapAlign: 'center',
                                },
                                640: {
                                    itemsToShow: 2,
                                    snapAlign: 'center',
                                },
                                768: {
                                    itemsToShow: 3,
                                    snapAlign: 'center',
                                },
                                1024: {
                                    itemsToShow: 4,
                                    snapAlign: 'center',
                                },
                            }"
                            class="-mx-2"
                        >
                            <template #addons>
                                <Navigation v-slot="{ next, prev }">
                                    <button
                                        @click="next"
                                        class="absolute top-1/2 -right-4 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-white/80 backdrop-blur-sm shadow-lg hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed z-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-2"
                                    >
                                        <div
                                            class="bg-[var(--color-primary)]/10 rounded-full p-2"
                                        >
                                            <svg
                                                class="w-6 h-6 text-[var(--color-primary-dark)]"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 5l7 7-7 7"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                    <button
                                        @click="prev"
                                        class="absolute top-1/2 -left-4 -translate-y-1/2 w-12 h-12 flex items-center justify-center rounded-full bg-white/80 backdrop-blur-sm shadow-lg hover:bg-white disabled:opacity-50 disabled:cursor-not-allowed z-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-2"
                                    >
                                        <div
                                            class="bg-[var(--color-primary)]/10 rounded-full p-2"
                                        >
                                            <svg
                                                class="w-6 h-6 text-[var(--color-primary-dark)]"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 19l-7-7 7-7"
                                                />
                                            </svg>
                                        </div>
                                    </button>
                                </Navigation>
                            </template>

                            <Slide
                                v-for="(
                                    image, index
                                ) in about.au_multiple_image"
                                :key="index"
                                v-slot="{ isActive }"
                                class="px-2 py-3"
                            >
                                <div
                                    class="group h-full flex flex-col items-center"
                                >
                                    <div
                                        class="relative w-full aspect-[4/3] overflow-hidden rounded-2xl flex items-center justify-center transition-all duration-300"
                                        :class="{
                                            'opacity-40 blur-sm scale-90':
                                                !isActive,
                                            'opacity-100 blur-0 scale-100':
                                                isActive,
                                        }"
                                    >
                                        <img
                                            :src="image"
                                            :alt="`Gallery Image ${index + 1}`"
                                            class="max-w-full max-h-full object-contain transition-transform duration-300 group-hover:scale-105"
                                        />
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"
                                        ></div>
                                    </div>
                                </div>
                            </Slide>
                        </Carousel>
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
    @apply bg-white/90 backdrop-blur-sm rounded-full p-2 md:p-3 shadow-xl 
           transition-all duration-300 hover:scale-110;
    width: 40px;
    height: 40px;
    @screen md {
        width: 48px;
        height: 48px;
    }
}

/* Update carousel responsive styles */
.gallery-carousel :deep(.carousel__viewport) {
    @apply px-4;
}

/* Adjust navigation buttons position for mobile */
.gallery-carousel :deep(.carousel__prev) {
    @apply -left-2 md:-left-6;
}

.gallery-carousel :deep(.carousel__next) {
    @apply -right-2 md:-right-6;
}

/* Adjust slide spacing for different screens */
.gallery-carousel :deep(.carousel__slide) {
    @apply px-2 md:px-3;
}

.prose {
    @apply text-[var(--light-text)] max-w-none;

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        @apply text-[var(--text-color)] font-bold tracking-tight;
        font-size: theme("fontSize.3xl");
        line-height: 1.3;
    }

    p {
        @apply text-lg leading-relaxed mb-6;
    }

    strong {
        @apply text-[var(--text-color)] font-semibold;
    }

    a {
        @apply text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] 
               transition-colors duration-300;
    }
}

/* Add responsive text utilities from Home.vue */
.text-responsive {
    @apply text-base md:text-lg lg:text-xl;
}

.heading-responsive {
    @apply text-3xl md:text-4xl lg:text-5xl xl:text-6xl;
}

/* Media queries for responsive text */
@media (max-width: 640px) {
    .text-responsive {
        @apply text-base;
    }
    .heading-responsive {
        @apply text-3xl;
    }
}

@media (min-width: 641px) and (max-width: 1024px) {
    .text-responsive {
        @apply text-lg;
    }
    .heading-responsive {
        @apply text-4xl;
    }
}

@media (min-width: 1025px) {
    .text-responsive {
        @apply text-xl;
    }
    .heading-responsive {
        @apply text-5xl;
    }
}

/* Animation styles */
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
    animation: fade-in 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

/* Tambahkan style untuk float-in animation */
:deep(.float-in-section) {
    will-change: transform, opacity;
}

:deep(.float-in-section.visible) {
    will-change: auto;
}

@keyframes floatIn {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.float-in-section {
    opacity: 0;
    transform: translateY(50px);
}

.float-in-section.visible {
    animation: floatIn 0.8s ease-out forwards;
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

.delay-300 {
    animation-delay: 0.3s;
}

/* Pastikan animasi berjalan smooth */
* {
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
}
</style>
