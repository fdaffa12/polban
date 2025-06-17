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
    featuredEvent: {
        type: Object,
        default: null,
    },
    newsArticles: {
        type: Array,
        default: () => [],
    },
    departments: {
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
                                                            event.end_date
                                                                ? `${new Date(
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
                                                                  )} - ${new Date(
                                                                      event.end_date
                                                                  ).toLocaleDateString(
                                                                      "id-ID",
                                                                      {
                                                                          weekday:
                                                                              "long",
                                                                          year: "numeric",
                                                                          month: "long",
                                                                          day: "numeric",
                                                                      }
                                                                  )}`
                                                                : new Date(
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
                    <div class="col-span-12 lg:col-span-3 flex flex-col gap-4">
                        <!-- Featured Event Card -->
                        <div
                            v-if="featuredEvent"
                            class="w-full bg-gradient-to-br from-purple-600 to-blue-600 p-6 rounded-3xl shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-300 flex-grow"
                        >
                            <!-- Decorative Elements -->
                            <div
                                class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"
                            ></div>
                            <div
                                class="absolute -bottom-20 -left-20 w-60 h-60 bg-purple-400/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"
                            ></div>

                            <div class="relative space-y-4">
                                <!-- Status Badge -->
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                        :class="
                                            featuredEvent.is_upcoming
                                                ? 'bg-green-500/30 text-green-100'
                                                : 'bg-blue-500/30 text-blue-100'
                                        "
                                    >
                                        {{
                                            featuredEvent.is_upcoming
                                                ? "Event Mendatang"
                                                : "Event Terakhir"
                                        }}
                                    </div>
                                </div>

                                <!-- Event Info -->
                                <div class="space-y-3">
                                    <!-- Department Name -->
                                    <h3
                                        class="text-3xl font-bold text-white group-hover:text-blue-100 transition-colors duration-300"
                                    >
                                        {{ featuredEvent.department.name }}
                                    </h3>

                                    <!-- Event Title -->
                                    <h4
                                        class="text-xl font-medium text-white/90 line-clamp-2 group-hover:text-blue-100 transition-colors duration-300"
                                    >
                                        {{ featuredEvent.event_name }}
                                    </h4>

                                    <div
                                        class="flex items-center space-x-2 text-blue-200 text-sm"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span>
                                            {{
                                                new Date(
                                                    featuredEvent.start_date
                                                ).toLocaleDateString("id-ID", {
                                                    day: "numeric",
                                                    month: "long",
                                                })
                                            }}
                                            {{
                                                featuredEvent.end_date
                                                    ? " - " +
                                                      new Date(
                                                          featuredEvent.end_date
                                                      ).toLocaleDateString(
                                                          "id-ID",
                                                          {
                                                              day: "numeric",
                                                              month: "long",
                                                          }
                                                      )
                                                    : ""
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <Link
                                    :href="`/events/${featuredEvent.id}`"
                                    class="inline-flex items-center px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white backdrop-blur-sm transition-all duration-300 group-hover:scale-105 hover:shadow-lg"
                                >
                                    Lihat Detail
                                    <svg
                                        class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-300"
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
                        <div
                            v-else
                            class="w-full bg-gradient-to-br from-gray-600 to-gray-700 p-6 rounded-3xl shadow-xl relative overflow-hidden flex-grow"
                        >
                            <div class="text-center text-white">
                                <svg
                                    class="w-12 h-12 mx-auto mb-4 text-gray-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                    />
                                </svg>
                                <p class="text-lg font-medium">
                                    Tidak ada event saat ini
                                </p>
                            </div>
                        </div>

                        <!-- Explore All Events Card -->
                        <div
                            class="w-full bg-gradient-to-br from-blue-600 to-indigo-600 p-8 rounded-3xl shadow-xl relative overflow-hidden group flex-grow"
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

        <!-- News Articles Section -->
        <section
            class="py-20 bg-gradient-to-b from-white via-blue-50/30 to-white"
        >
            <div class="container-custom">
                <!-- Header Section -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2
                        class="text-blue-600 font-semibold text-lg mb-4 animate-fade-in"
                    >
                        Berita Terbaru
                    </h2>
                    <h3
                        class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        Kabar dan Informasi
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600"
                            >HMJTK</span
                        >
                    </h3>
                    <p
                        class="text-lg text-gray-600 animate-fade-in"
                        style="animation-delay: 0.3s"
                    >
                        Temukan berita dan informasi terkini seputar HMJTK
                        Polban
                    </p>
                </div>

                <div class="grid grid-cols-12 gap-4 md:gap-8">
                    <!-- CTA Column -->
                    <div class="col-span-12 lg:col-span-3 flex flex-col gap-4">
                        <!-- View All Articles Card -->
                        <div
                            class="flex-grow bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 text-white group hover:shadow-2xl hover:shadow-blue-200/50 transition-all duration-300 flex flex-col justify-between relative overflow-hidden"
                        >
                            <!-- Decorative Background Pattern -->
                            <div
                                class="absolute top-0 right-0 opacity-10 transform translate-x-8 -translate-y-8"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-32 h-32"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                >
                                    <path
                                        d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"
                                    />
                                </svg>
                            </div>

                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-4">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-8 h-8"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                    >
                                        <path
                                            d="M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"
                                        />
                                    </svg>
                                    <h3 class="text-2xl font-bold">
                                        Artikel Terbaru
                                    </h3>
                                </div>

                                <!-- Added Features Section -->
                                <div class="space-y-4 mb-8">
                                    <div class="flex items-start gap-3">
                                        <svg
                                            class="w-5 h-5 mt-1 text-blue-200"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <p class="text-sm text-blue-100">
                                            Informasi terkini seputar kegiatan
                                            dan prestasi HMJTK
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <svg
                                            class="w-5 h-5 mt-1 text-blue-200"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z"
                                            />
                                        </svg>
                                        <p class="text-sm text-blue-100">
                                            Update berkala tentang teknologi dan
                                            inovasi terbaru
                                        </p>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <svg
                                            class="w-5 h-5 mt-1 text-blue-200"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <p class="text-sm text-blue-100">
                                            Artikel diperbarui secara rutin
                                            setiap minggu
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-white/10 rounded-lg p-3 mb-6">
                                    <p class="text-sm text-blue-100 italic">
                                        "Tetap terhubung dengan perkembangan
                                        terkini di HMJTK melalui artikel-artikel
                                        informatif kami"
                                    </p>
                                </div>
                            </div>

                            <Link
                                href="/articles"
                                class="relative z-10 inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-blue-600 hover:bg-blue-50 transition-colors duration-300 font-semibold group-hover:shadow-lg"
                            >
                                <span>Lihat Semua Artikel</span>
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
                    <!-- News Carousel -->
                    <div class="col-span-12 lg:col-span-9">
                        <div
                            class="bg-white rounded-3xl p-4 shadow-xl shadow-blue-100/50"
                        >
                            <Carousel
                                v-if="newsArticles.length > 0"
                                :autoplay="3000"
                                :items-to-show="1.5"
                                :wrap-around="true"
                                :mouseDrag="true"
                                :touchDrag="true"
                                :snapAlign="'center'"
                                class="carousel-container"
                            >
                                <Slide
                                    v-for="article in newsArticles"
                                    :key="article.id"
                                >
                                    <div class="carousel__item">
                                        <div
                                            class="relative h-[400px] md:h-[500px] rounded-2xl overflow-hidden"
                                        >
                                            <!-- Image -->
                                            <img
                                                :src="article.featured_image"
                                                :alt="article.title"
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
                                                    <!-- Date & Author -->
                                                    <div
                                                        class="flex items-center gap-2 text-blue-200 text-sm"
                                                    >
                                                        <span>{{
                                                            article.created_at
                                                        }}</span>
                                                        <span>•</span>
                                                        <span>{{
                                                            article.author
                                                        }}</span>
                                                    </div>

                                                    <!-- Title -->
                                                    <h4
                                                        class="text-2xl md:text-3xl font-bold text-white"
                                                    >
                                                        {{ article.title }}
                                                    </h4>

                                                    <!-- Description -->
                                                    <p
                                                        class="text-gray-300 line-clamp-3 text-base md:text-lg"
                                                    >
                                                        {{ article.content }}
                                                    </p>

                                                    <!-- Button -->
                                                    <div
                                                        class="flex items-center gap-4 pt-2"
                                                    >
                                                        <Link
                                                            :href="`/articles/${article.id}`"
                                                            class="inline-flex items-center px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-all duration-300"
                                                        >
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
                                    <!-- Removed Pagination component -->
                                </template>
                            </Carousel>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section
            class="py-20 bg-gradient-to-b from-white via-blue-50/30 to-white"
        >
            <div class="container-custom">
                <!-- Header Section -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2
                        class="text-blue-600 font-semibold text-lg mb-4 animate-fade-in"
                    >
                        Departemen HMJTK
                    </h2>
                    <h3
                        class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        Struktur
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600"
                            >Organisasi</span
                        >
                    </h3>
                    <p
                        class="text-lg text-gray-600 animate-fade-in"
                        style="animation-delay: 0.3s"
                    >
                        Kenali lebih dekat departemen-departemen yang ada di
                        HMJTK Polban
                    </p>
                </div>

                <!-- Department Carousel -->
                    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <Carousel
                            :items-to-show="4"
                            :wrap-around="true"
                            :transition="500"
                            :snap-align="'center'"
                            class="-mx-2"
                        >
                            <template #addons>
                                <Navigation v-slot="{ next, prev }">
                                    <button 
                                        @click="next"
                                        class="absolute top-1/2 -right-4 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed z-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    >
                                        <svg class="w-6 h-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="prev"
                                        class="absolute top-1/2 -left-4 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed z-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                    >
                                        <svg class="w-6 h-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                </Navigation>
                            </template>

                            <Slide
                                v-for="department in departments"
                                :key="department.id"
                                class="px-2 py-3"
                            >
                                <div
                                    class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 group overflow-hidden h-full"
                                >
                                    <div
                                        class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-blue-50/50 to-indigo-50/50"
                                    >
                                        <img
                                            :src="department.image"
                                            :alt="department.dept_name"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        />
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                    <div class="p-4">
                                        <h4
                                            class="text-base font-semibold text-gray-800 text-center line-clamp-2 group-hover:text-blue-600 transition-colors duration-300"
                                        >
                                            {{ department.dept_name }}
                                        </h4>
                                    </div>
                                </div>
                            </Slide>
                        </Carousel>
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

.department-carousel .carousel__slide {
    padding: 1rem;
}

.department-carousel .carousel__viewport {
    perspective: 2000px;
}

.department-carousel .carousel__track {
    transform-style: preserve-3d;
}

.department-carousel .carousel__slide--sliding {
    transition: 0.5s;
}

.department-carousel .carousel__slide {
    opacity: 0.9;
    transform: rotateY(-20deg) scale(0.9);
}

.department-carousel .carousel__slide--active ~ .carousel__slide {
    transform: rotateY(20deg) scale(0.9);
}

.department-carousel .carousel__slide--prev {
    opacity: 1;
    transform: rotateY(-10deg) scale(0.95);
}

.department-carousel .carousel__slide--next {
    opacity: 1;
    transform: rotateY(10deg) scale(0.95);
}

.department-carousel .carousel__slide--active {
    opacity: 1;
    transform: rotateY(0) scale(1);
}
</style>

<style>
.container-custom {
    @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}
</style>
