<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Carousel, Slide, Navigation, Pagination } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";
import { ref } from "vue";

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const activeImage = ref(0);
const setActiveImage = (index) => {
    activeImage.value = index;
};
</script>

<template>
    <GuestLayout title="Ruang Optima">
        <Head title="Ruang Optima" />

        <!-- Hero Section -->
        <section class="py-12 bg-white">
            <div class="container-custom">
                <div class="grid grid-cols-12 gap-8">
                    <!-- Text Content -->
                    <div class="col-span-12 md:col-span-6 space-y-8">
                        <div class="space-y-4">
                            <h1
                                class="text-4xl md:text-5xl font-bold text-[var(--text-color)] leading-tight"
                            >
                                Ruang Optima
                                <span
                                    class="block text-xl md:text-2xl font-light text-[var(--light-text)] mt-2"
                                >
                                    HMJTK Polban
                                </span>
                            </h1>
                            <div
                                class="w-20 h-1 bg-[var(--color-primary)]"
                            ></div>
                        </div>

                        <div class="prose prose-lg max-w-none">
                            <p class="mb-4 text-[var(--light-text)]">
                                The center or core of an activity or
                                organization. The centrum is often the focal
                                point or source of everything that happens
                                around it.
                            </p>
                            <p class="mb-4 text-[var(--light-text)]">
                                Elaboration is a further explanation of the
                                objectives, work plans or strategies that will
                                be implemented. Elaboration ensures that
                                information submitted is complete, clear and can
                                be understood by all parties involved,
                                especially HIMATEKK ITS members.
                            </p>
                            <p class="italic text-[var(--light-text)]">
                                It is hoped that Himatekk ITS can develop more
                                advanced in the future with ocean-wide
                                elaboration among ITS FT-IRS Chemical
                                Engineering students so that the existence of
                                this organization can be felt in all aspects of
                                its benefits.
                            </p>
                        </div>
                    </div>

                    <!-- Empty space for the right side -->
                    <div class="hidden md:block md:col-span-6"></div>
                </div>
            </div>
        </section>

        <!-- Gallery Section -->
        <section class="py-20 bg-white">
            <div class="container-custom">
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <span
                        class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                    >
                        Our Gallery
                    </span>
                    <h2
                        class="text-4xl md:text-5xl font-bold text-[var(--text-color)] leading-tight"
                    >
                        Moments & Memories
                    </h2>
                    <div
                        class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                    ></div>
                </div>

                <div class="relative max-w-7xl mx-auto">
                    <!-- Title Navigation -->
                    <div class="flex flex-wrap gap-4 mb-8 justify-center">
                        <button
                            v-for="(image, index) in content.images"
                            :key="image.id"
                            @click="setActiveImage(index)"
                            class="px-6 py-3 rounded-full transition-all duration-300 text-sm md:text-base font-medium"
                            :class="[
                                activeImage === index
                                    ? 'bg-[var(--color-primary)] text-white shadow-lg scale-110'
                                    : 'bg-[var(--color-primary)]/5 text-[var(--color-primary)] hover:bg-[var(--color-primary)]/10',
                            ]"
                        >
                            {{ image.title }}
                        </button>
                    </div>

                    <!-- Carousel -->
                    <Carousel
                        :items-to-show="3"
                        :wrap-around="true"
                        :transition="500"
                        :snap-align="'center'"
                        :modelValue="activeImage"
                        @update:modelValue="setActiveImage"
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
                                itemsToShow: 2,
                                snapAlign: 'center',
                            },
                            1024: {
                                itemsToShow: 3,
                                snapAlign: 'center',
                            },
                        }"
                        class="gallery-carousel"
                    >
                        <Slide
                            v-for="(image, index) in content.images"
                            :key="image.id"
                            class="px-3"
                        >
                            <div
                                class="relative aspect-[4/3] rounded-2xl overflow-hidden group cursor-pointer"
                                @click="setActiveImage(index)"
                            >
                                <img
                                    :src="image.image"
                                    :alt="image.title"
                                    class="w-full h-full object-cover transition-transform duration-700"
                                    :class="{
                                        'scale-105': activeImage === index,
                                    }"
                                />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"
                                    :class="{
                                        '!opacity-100': activeImage === index,
                                    }"
                                >
                                    <div
                                        class="absolute bottom-0 left-0 right-0 p-6"
                                    >
                                        <h4
                                            class="text-white font-medium text-lg md:text-xl"
                                        >
                                            {{ image.title }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </Slide>

                        <template #addons>
                            <Navigation />
                        </template>
                    </Carousel>
                </div>
            </div>
        </section>

        <!-- Vision & Mission Section -->
        <section
            class="py-20 bg-gradient-to-b from-white to-[var(--color-background)]/10"
        >
            <div class="container-custom">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                        <span
                            class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                        >
                            Our Vision & Mission
                        </span>
                        <h2
                            class="text-4xl md:text-5xl font-bold text-[var(--text-color)] leading-tight"
                        >
                            Visi & Misi Kami
                        </h2>
                        <div
                            class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                        ></div>
                    </div>

                    <!-- Combined Vision & Mission Carousel -->
                    <div class="max-w-5xl mx-auto">
                        <Carousel
                            :items-to-show="1"
                            :wrap-around="true"
                            :transition="500"
                            :autoplay="0"
                            class="vision-mission-carousel"
                        >
                            <!-- Vision Slide First -->
                            <Slide
                                v-for="(vision, index) in content.visions"
                                :key="`vision-${index}`"
                            >
                                <div class="px-4">
                                    <div
                                        class="bg-white/50 backdrop-blur-sm p-8 md:p-12 rounded-2xl shadow-lg border border-white/20"
                                    >
                                        <div
                                            class="flex items-center justify-center mb-6"
                                        >
                                            <span
                                                class="text-[var(--color-primary)] font-semibold text-xl"
                                            >
                                                Visi
                                            </span>
                                        </div>
                                        <p
                                            class="text-[var(--light-text)] text-lg md:text-xl leading-relaxed text-center"
                                            v-html="vision.content"
                                        ></p>
                                    </div>
                                </div>
                            </Slide>

                            <!-- Mission Slides -->
                            <Slide
                                v-for="(mission, index) in content.missions"
                                :key="`mission-${mission.id}`"
                            >
                                <div class="px-4">
                                    <div
                                        class="bg-white/50 backdrop-blur-sm p-8 md:p-12 rounded-2xl shadow-lg border border-white/20"
                                    >
                                        <div
                                            class="flex items-center justify-center mb-6"
                                        >
                                            <span
                                                class="text-[var(--color-primary)] font-semibold text-xl"
                                            >
                                                Misi {{ index + 1 }}
                                            </span>
                                        </div>
                                        <p
                                            class="text-[var(--light-text)] text-lg md:text-xl leading-relaxed text-center"
                                            v-html="mission.content"
                                        ></p>
                                    </div>
                                </div>
                            </Slide>

                            <template #addons>
                                <Navigation />
                                <Pagination />
                            </template>
                        </Carousel>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Values Section -->
        <section class="py-20 bg-[var(--color-background)]/5">
            <div class="container-custom">
                <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                    <span
                        class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                    >
                        Our Principles
                    </span>
                    <h2
                        class="text-4xl md:text-5xl font-bold text-[var(--text-color)] leading-tight"
                    >
                        Core Values
                    </h2>
                    <div
                        class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                    ></div>
                </div>

                <div class="max-w-4xl mx-auto space-y-6">
                    <div
                        v-for="value in content.coreValues"
                        :key="value.id"
                        class="group p-8 md:p-10 rounded-2xl bg-white/50 backdrop-blur-sm shadow-lg hover:shadow-xl transition-all duration-500 border border-[var(--color-primary)]/10"
                    >
                        <div
                            class="flex flex-col md:flex-row md:items-start gap-6"
                        >
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 rounded-xl bg-[var(--color-primary)]/10 flex items-center justify-center group-hover:bg-[var(--color-primary)] transition-colors duration-500"
                                >
                                    <svg
                                        class="w-6 h-6 text-[var(--color-primary)] group-hover:text-white transition-colors duration-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 13l4 4L19 7"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow space-y-4">
                                <h3
                                    class="text-2xl md:text-3xl font-bold text-[var(--text-color)]"
                                >
                                    {{ value.title }}
                                </h3>
                                <p
                                    class="text-[var(--light-text)] text-lg leading-relaxed"
                                    v-html="value.description"
                                ></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
/* Copy the same carousel and animation styles from AboutUs.vue */
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

/* Vision & Mission Carousel Styles */
.vision-carousel :deep(.carousel__slide),
.mission-carousel :deep(.carousel__slide) {
    @apply transform transition-all duration-700;
    opacity: 0.6;
    scale: 0.95;
}

.vision-carousel :deep(.carousel__slide--active),
.mission-carousel :deep(.carousel__slide--active) {
    @apply shadow-2xl;
    opacity: 1;
    scale: 1;
}

.vision-carousel :deep(.carousel__prev),
.vision-carousel :deep(.carousel__next),
.mission-carousel :deep(.carousel__prev),
.mission-carousel :deep(.carousel__next) {
    @apply bg-white/90 backdrop-blur-sm rounded-full p-2 md:p-3 shadow-xl 
           transition-all duration-300 hover:scale-110;
    width: 40px;
    height: 40px;
    @screen md {
        width: 48px;
        height: 48px;
    }
}

.vision-carousel :deep(.carousel__pagination),
.mission-carousel :deep(.carousel__pagination) {
    @apply mt-8;
}

.vision-carousel :deep(.carousel__pagination-button),
.mission-carousel :deep(.carousel__pagination-button) {
    @apply w-3 h-3 rounded-full bg-gray-300 transition-all duration-300;
}

.vision-carousel :deep(.carousel__pagination-button--active),
.mission-carousel :deep(.carousel__pagination-button--active) {
    @apply bg-[var(--color-primary)] scale-125;
}

/* Hero Section Animations */
@keyframes blob {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(20px, -30px) scale(1.1);
    }
    50% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    75% {
        transform: translate(30px, 30px) scale(1.1);
    }
}

.animate-blob {
    animation: blob 10s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.bg-grid-pattern {
    background-size: 20px 20px;
    background-image: linear-gradient(
            to right,
            rgba(255, 255, 255, 0.1) 1px,
            transparent 1px
        ),
        linear-gradient(
            to bottom,
            rgba(255, 255, 255, 0.1) 1px,
            transparent 1px
        );
}

/* Responsive navigation buttons */
:deep(.carousel__prev),
:deep(.carousel__next) {
    @apply bg-white/90 backdrop-blur-sm rounded-full shadow-lg;
    width: 36px;
    height: 36px;

    @screen sm {
        width: 40px;
        height: 40px;
    }

    @screen md {
        width: 44px;
        height: 44px;
    }
}

:deep(.carousel__prev) {
    @apply -left-2 md:-left-4 lg:-left-6;
}

:deep(.carousel__next) {
    @apply -right-2 md:-right-4 lg:-right-6;
}

/* Responsive styles */
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

/* Gallery transitions */
.gallery-fade-enter-active,
.gallery-fade-leave-active {
    transition: all 0.5s ease;
}

.gallery-fade-enter-from,
.gallery-fade-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .container-custom {
        @apply px-4;
    }
}

/* Update Gallery Carousel Styles */
.gallery-carousel :deep(.carousel__slide) {
    @apply transform transition-all duration-500 px-2 md:px-3;
    opacity: 0.4;
    filter: blur(1px);
    scale: 0.9;
}

.gallery-carousel :deep(.carousel__slide--active) {
    opacity: 1;
    filter: blur(0);
    scale: 1;
}

.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    @apply bg-white/90 backdrop-blur-sm rounded-full shadow-lg border border-gray-100 
           transition-all duration-300 hover:scale-110;
    width: 40px;
    height: 40px;
}

/* Update responsive styles */
@media (max-width: 768px) {
    .gallery-carousel :deep(.carousel__slide) {
        opacity: 0.8;
        filter: none;
        scale: 0.95;
    }

    .gallery-carousel :deep(.carousel__slide--active) {
        opacity: 1;
        scale: 1;
    }

    .gallery-carousel :deep(.carousel__prev),
    .gallery-carousel :deep(.carousel__next) {
        width: 36px;
        height: 36px;
    }
}

/* Update container spacing for mobile */
@media (max-width: 640px) {
    .gallery-carousel {
        @apply px-2;
    }

    .gallery-carousel :deep(.carousel__prev) {
        @apply -left-2;
    }

    .gallery-carousel :deep(.carousel__next) {
        @apply -right-2;
    }

    /* Adjust title navigation for mobile */
    .flex.flex-wrap.gap-4.mb-8 {
        @apply gap-2 px-4;
    }

    .flex.flex-wrap.gap-4.mb-8 button {
        @apply px-4 py-2 text-sm;
    }
}

/* Additional container adjustments */
.container-custom {
    @apply px-4 sm:px-6 lg:px-8 mx-auto;
    max-width: 1280px;
}
</style>
