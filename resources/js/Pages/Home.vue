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

        <!-- Update Events Section -->
        <section
            class="py-20 bg-gradient-to-b from-white via-blue-50/30 to-white"
        >
            <div class="container-custom">
                <!-- Header Section dengan animasi -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2
                        class="text-blue-600 font-semibold text-lg mb-4 animate-fade-in"
                    >
                        Event Terbaru
                    </h2>
                    <h3
                        class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        Kegiatan dan Acara
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600"
                            >HMJTK</span
                        >
                    </h3>
                    <p
                        class="text-lg text-gray-600 animate-fade-in"
                        style="animation-delay: 0.3s"
                    >
                        Temukan berbagai kegiatan menarik yang diselenggarakan
                        oleh HMJTK Polban
                    </p>
                </div>

                <div class="grid grid-cols-12 gap-4 md:gap-8">
                    <!-- Carousel Column -->
                    <div class="col-span-12 lg:col-span-9">
                        <div
                            class="bg-white rounded-3xl p-4 shadow-xl shadow-blue-100/50"
                        >
                            <Carousel
                                v-if="events.length > 0"
                                :autoplay="3000"
                                :items-to-show="1.5"
                                :wrap-around="true"
                                :mouseDrag="true"
                                :touchDrag="true"
                                :snapAlign="'center'"
                                class="carousel-container"
                            >
                                <Slide v-for="event in events" :key="event.id">
                                    <div class="carousel__item">
                                        <div
                                            class="relative h-[400px] md:h-[500px] rounded-2xl overflow-hidden"
                                        >
                                            <!-- Image -->
                                            <img
                                                :src="event.event_flyer"
                                                :alt="event.event_name"
                                                class="w-full h-full object-cover"
                                            />

                                            <!-- Gradient Overlay -->
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent"
                                            ></div>

                                            <!-- Content -->
                                            <div
                                                class="absolute bottom-0 left-0 right-0 p-6 md:p-8"
                                            >
                                                <div class="space-y-4">
                                                    <!-- Status Badge -->
                                                    <div class="flex gap-2">
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                                            :class="{
                                                                'bg-green-600 text-white':
                                                                    event.status ===
                                                                    'ongoing',
                                                                'bg-blue-600 text-white':
                                                                    event.status ===
                                                                    'upcoming',
                                                                'bg-gray-600 text-white':
                                                                    event.status ===
                                                                    'past',
                                                            }"
                                                        >
                                                            {{
                                                                event.status ===
                                                                "ongoing"
                                                                    ? "Sedang Berlangsung"
                                                                    : event.status ===
                                                                      "upcoming"
                                                                    ? "Akan Datang"
                                                                    : "Selesai"
                                                            }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-600 text-white"
                                                        >
                                                            {{
                                                                event.fee_type ===
                                                                "free"
                                                                    ? "Gratis"
                                                                    : "Berbayar"
                                                            }}
                                                        </span>
                                                    </div>

                                                    <!-- Title -->
                                                    <h4
                                                        class="text-2xl md:text-3xl font-bold text-white"
                                                    >
                                                        {{ event.event_name }}
                                                    </h4>

                                                    <!-- Date -->
                                                    <p
                                                        class="text-blue-200 text-sm"
                                                    >
                                                        {{
                                                            new Date(
                                                                event.start_date
                                                            ).toLocaleDateString(
                                                                "id-ID",
                                                                {
                                                                    weekday:
                                                                        "long",
                                                                    year: "numeric",
                                                                    month: "long",
                                                                    day: "numeric",
                                                                }
                                                            )
                                                        }}
                                                    </p>

                                                    <!-- Description -->
                                                    <p
                                                        class="text-gray-300 line-clamp-3 text-base md:text-lg"
                                                    >
                                                        {{ event.event_detail }}
                                                    </p>

                                                    <!-- Button -->
                                                    <div
                                                        class="flex items-center gap-4 pt-2"
                                                    >
                                                        <Link
                                                            :href="`/events/${event.id}`"
                                                            class="inline-flex items-center px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-all duration-300"
                                                        >
                                                            Selengkapnya
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
                                                                    d="M9 5l7 7-7 7"
                                                                />
                                                            </svg>
                                                        </Link>
                                                    </div>
                                                </div>
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

                    <!-- CTA Column -->
                    <div class="col-span-12 lg:col-span-3 flex items-center">
                        <div
                            class="w-full h-full bg-gradient-to-br from-blue-600 to-indigo-600 p-8 rounded-3xl shadow-xl relative overflow-hidden group"
                        >
                            <!-- Decorative circles -->
                            <div
                                class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"
                            ></div>
                            <div
                                class="absolute -bottom-20 -left-20 w-60 h-60 bg-blue-400/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"
                            ></div>

                            <div class="relative space-y-8">
                                <!-- Icon Container -->
                                <div
                                    class="h-20 w-20 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-300"
                                >
                                    <svg
                                        class="w-10 h-10 text-white"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                        />
                                    </svg>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="text-2xl font-bold text-white">
                                        Jelajahi Semua Event
                                    </h4>
                                    <p class="text-blue-100">
                                        Temukan lebih banyak event menarik yang
                                        diselenggarakan oleh HMJTK Polban
                                    </p>
                                </div>

                                <Link
                                    href="/events"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-blue-600 bg-white rounded-xl hover:bg-blue-50 transition-all duration-300 transform hover:scale-105 shadow-lg"
                                >
                                    Lihat Semua Event
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
:deep(.carousel__viewport) {
    @apply rounded-2xl;
}

:deep(.carousel__track) {
    @apply rounded-2xl;
}

:deep(.carousel__slide) {
    @apply rounded-2xl;
    opacity: 0.4;
    filter: blur(3px);
    transform: scale(0.9);
    transition: all 0.5s ease;
}

:deep(.carousel__slide--active) {
    opacity: 1;
    filter: blur(0);
    transform: scale(1.05);
}

:deep(.carousel__prev),
:deep(.carousel__next) {
    @apply bg-white/20 backdrop-blur-sm rounded-full;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    z-index: 20;
}

:deep(.carousel__prev) {
    left: 1rem;
}

:deep(.carousel__next) {
    right: 1rem;
}

.carousel-container {
    @apply relative z-10;
    padding: 20px 0;
}

.carousel__item {
    @apply transition-all duration-500;
}

.carousel__slide--active .carousel__item {
    @apply shadow-2xl;
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

<style>
.container-custom {
    @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}
</style>
