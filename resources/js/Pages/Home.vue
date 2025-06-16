<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Link } from "@inertiajs/vue3";
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide, Pagination, Navigation } from "vue3-carousel";
import { onMounted } from "vue";

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    aboutUs: {
        type: Object,
        required: true,
    },
    events: {
        type: Array,
        default: () => [],
    },
});

onMounted(() => {
    console.log("Events length:", props.events.length);
    console.log("First event:", props.events[0]);
});
</script>

<template>
    <GuestLayout title="Home">
        <!-- Hero Section -->
        <div
            class="relative overflow-hidden bg-gradient-to-b from-blue-50 to-white"
        >
            <!-- Background Pattern -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
            </div>

            <div class="container-custom relative z-10">
                <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 min-h-[calc(100vh-80px)] items-center py-20"
                >
                    <!-- Text Content -->
                    <div class="space-y-8 animate-fade-in">
                        <h3
                            class="text-blue-600 font-semibold text-xl md:text-2xl tracking-wide"
                        >
                            Building with Light
                        </h3>

                        <h1
                            class="heading-responsive font-bold text-gray-900 leading-tight"
                        >
                            Create a brighter future through
                            <span class="text-blue-600 block mt-2">
                                innovation, collaboration, and growth
                            </span>
                        </h1>

                        <p class="text-responsive text-gray-600 max-w-xl">
                            Join us in our mission to illuminate paths, foster
                            innovation, and build sustainable solutions for
                            tomorrow's challenges.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <a href="#" class="btn-primary">
                                Get Started
                                <svg
                                    class="w-5 h-5 ml-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"
                                    />
                                </svg>
                            </a>
                            <a href="#" class="btn-secondary"> Learn More </a>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div
                        class="relative h-[400px] lg:h-[600px] animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-blue-500/10 to-purple-500/10 rounded-3xl"
                        >
                            <img
                                src="https://images.unsplash.com/photo-1749731630653-d9b3f00573ed?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Innovation Illustration"
                                class="w-full h-full object-cover rounded-3xl shadow-2xl"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Us Section -->
        <section class="py-20 bg-white relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 pointer-events-none">
                <div
                    class="absolute top-0 left-0 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"
                ></div>
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-purple-50 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"
                ></div>
                <div
                    class="absolute -bottom-8 left-20 w-64 h-64 bg-pink-50 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"
                ></div>
            </div>

            <div class="container-custom relative z-10">
                <!-- Section Header -->
                <div class="text-center mb-16 relative">
                    <h2 class="text-blue-600 font-semibold text-lg mb-4">
                        Tentang Kami
                    </h2>
                    <div class="relative inline-block">
                        <h3
                            class="text-4xl md:text-5xl font-bold text-gray-900 mb-6"
                        >
                            HMJTK'25
                        </h3>
                        <!-- Decorative Lines -->
                        <div class="about-title-line-left"></div>
                        <div class="about-title-line-right"></div>
                    </div>
                    <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                        Himpunan Mahasiswa Jurusan Teknik Komputer dan
                        Informatika
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"
                >
                    <!-- Image Column -->
                    <div class="relative">
                        <!-- Decorative Border -->
                        <div class="about-image-glow"></div>
                        <div class="relative h-[400px]">
                            <img
                                v-if="aboutUs.image"
                                :src="aboutUs.image"
                                :alt="aboutUs.title"
                                class="w-full h-full object-cover rounded-xl shadow-lg transform transition-transform duration-500 hover:scale-105"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-gray-100 rounded-xl flex items-center justify-center"
                            >
                                <span class="text-gray-400"
                                    >No image available</span
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Content Column -->
                    <div class="space-y-8">
                        <div class="relative">
                            <!-- Decorative Line -->
                            <div class="about-content-line"></div>

                            <h2
                                class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"
                            >
                                {{ aboutUs.title }}
                            </h2>
                            <div
                                class="prose prose-lg text-gray-600 max-w-none"
                                v-html="aboutUs.description"
                            ></div>
                        </div>

                        <Link href="/about" class="about-button">
                            Baca Selengkapnya
                            <svg
                                class="w-5 h-5 ml-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"
                                />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Events Section -->
        <section class="py-20 bg-white">
            <div class="container-custom">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-blue-600 font-semibold text-lg mb-4">
                        Event Terbaru
                    </h2>
                    <h3
                        class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"
                    >
                        Kegiatan dan Acara HMJTK
                    </h3>
                    <p class="text-responsive text-gray-600">
                        Temukan berbagai kegiatan menarik yang diselenggarakan
                        oleh HMJTK Polban
                    </p>
                </div>

                <!-- Debug Info -->
                <div class="text-center mb-8">
                    <p>Total Events: {{ events.length }}</p>
                </div>

                <!-- Carousel -->
                <div class="max-w-6xl mx-auto">
                    <Carousel
                        v-if="events.length > 0"
                        :autoplay="3000"
                        :items-to-show="1.5"
                        :wrap-around="true"
                        :mouseDrag="true"
                        :touchDrag="true"
                        :snapAlign="'center'"
                    >
                        <Slide v-for="event in events" :key="event.id">
                            <div class="carousel__item px-2">
                                <div
                                    class="relative h-[500px] rounded-xl overflow-hidden transition-all duration-300"
                                >
                                    <img
                                        :src="event.event_flyer"
                                        :alt="event.event_name"
                                        class="w-full h-full object-cover"
                                    />

                                    <!-- Gradient Overlay -->
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent"
                                    ></div>

                                    <!-- Content -->
                                    <div
                                        class="absolute bottom-0 left-0 right-0 p-8 text-white"
                                    >
                                        <h4 class="text-2xl font-bold mb-3">
                                            {{ event.event_name }}
                                        </h4>
                                        <p class="text-gray-200 line-clamp-3">
                                            {{ event.event_detail }}
                                        </p>

                                        <div
                                            class="mt-4 flex items-center gap-4"
                                        >
                                            <span
                                                class="bg-blue-600 px-4 py-1 rounded-full text-sm"
                                            >
                                                {{ event.fee_type }}
                                            </span>
                                            <Link
                                                :href="`/events/${event.id}`"
                                                class="inline-flex items-center text-blue-400 hover:text-blue-300 transition-colors"
                                            >
                                                Selengkapnya
                                                <svg
                                                    class="w-4 h-4 ml-2"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5l7 7-7 7"
                                                    />
                                                </svg>
                                            </Link>
                                        </div>
                                    </div>
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
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-blue-600">
            <div class="container-custom">
                <div class="text-center text-white max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">
                        Ready to Start Your Journey?
                    </h2>
                    <p class="text-responsive text-blue-100 mb-8">
                        Join us today and be part of the innovation that shapes
                        tomorrow.
                    </p>
                    <a href="#" class="btn-white"> Get Started Now </a>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
:deep(.carousel__slide) {
    padding: 1px;
    opacity: 0.5;
    transform: scale(0.9);
    transition: all 0.3s ease;
    filter: blur(2px);
}

:deep(.carousel__slide--active) {
    opacity: 1;
    transform: scale(1);
    filter: blur(0);
}

:deep(.carousel__viewport) {
    perspective: 2000px;
}

:deep(.carousel__track) {
    transform-style: preserve-3d;
}

:deep(.carousel__prev),
:deep(.carousel__next) {
    background-color: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    transition: all 0.3s;
    backdrop-filter: blur(4px);
}

:deep(.carousel__prev) {
    left: 10px;
}

:deep(.carousel__next) {
    right: 10px;
}

:deep(.carousel__prev:hover),
:deep(.carousel__next:hover) {
    background-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-50%) scale(1.1);
}

:deep(.carousel__pagination) {
    padding: 1rem 0;
}

:deep(.carousel__pagination-button) {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin: 0 4px;
    background-color: rgba(255, 255, 255, 0.3);
    transition: all 0.3s;
}

:deep(.carousel__pagination-button--active) {
    background-color: rgb(37, 99, 235);
    transform: scale(1.2);
}

.carousel__item {
    width: 100%;
    height: 100%;
    position: relative;
}
</style>

<style>
.container-custom {
    @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}
</style>
