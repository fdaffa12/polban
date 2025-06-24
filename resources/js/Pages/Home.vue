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
    himpunan: {
        type: Object,
        default: null,
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
    deptLogos: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <GuestLayout title="Home">
        <!-- Hero Section -->
        <div
            class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gray-900"
        >
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img
                    v-if="aboutUs.image"
                    :src="aboutUs.image"
                    :alt="aboutUs.title"
                    class="w-full h-full object-cover"
                />
                <div
                    class="absolute inset-0 bg-gradient-to-b from-gray-900/70 via-gray-900/60 to-gray-900/80"
                ></div>
                <!-- Optional Pattern Overlay -->
                <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
            </div>

            <!-- Content -->
            <div class="container-custom relative z-10 py-20">
                <div
                    class="max-w-4xl mx-auto text-center space-y-8 animate-fade-in px-4"
                >
                    <h1
                        class="text-4xl md:text-6xl lg:text-7xl font-bold text-white leading-tight"
                    >
                        Selamat Datang di Website
                        <span class="text-[var(--color-primary)] block mt-2">
                            Himpunan Mahasiswa Teknik Kimia POLBAN
                        </span>
                    </h1>
                </div>
            </div>
        </div>

        <!-- About Us Section (now Himpunan Section) -->
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
                <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center"
                >
                    <!-- Image Column -->
                    <div class="relative">
                        <!-- Decorative Border -->
                        <div class="about-image-glow"></div>
                        <div class="relative h-[400px]">
                            <img
                                v-if="himpunan?.logo"
                                :src="himpunan.logo"
                                :alt="himpunan.name"
                                class="w-full h-full object-contain rounded-xl shadow-lg transform transition-transform duration-500 hover:scale-105"
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
                                {{ himpunan?.name || "HMJTK Polban" }}
                            </h2>
                            <div
                                class="prose prose-lg text-gray-600 max-w-none"
                                v-html="
                                    himpunan?.description ||
                                    'Belum ada deskripsi'
                                "
                            ></div>
                        </div>

                        <Link href="/ruang-optima" class="about-button">
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
            class="py-20 bg-gradient-to-b from-white via-[var(--color-background)]/30 to-white"
        >
            <div class="container-custom">
                <!-- Header Section dengan animasi -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2
                        class="text-[var(--color-primary)] font-semibold text-lg mb-4 animate-fade-in"
                    >
                        Event Terbaru
                    </h2>
                    <h3
                        class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        Kegiatan dan Acara
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-[var(--color-primary-dark)] to-[var(--color-primary)]"
                        >
                            HMJTK
                        </span>
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
                            class="bg-white rounded-3xl p-4 shadow-xl shadow-[var(--color-primary)]/10"
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
                                                                'bg-[var(--color-secondary)] text-white':
                                                                    event.status ===
                                                                    'ongoing',
                                                                'bg-[var(--color-primary)] text-white':
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
                                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[var(--color-primary)] text-white"
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
                                                        class="text-[var(--color-primary)]/80 text-sm"
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
                                                            :href="`/event/${event.id}`"
                                                            class="inline-flex items-center px-6 py-2 rounded-lg bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white transition-all duration-300"
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
                            class="w-full bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary)] p-6 rounded-3xl shadow-xl relative overflow-hidden group hover:shadow-2xl transition-all duration-300 flex-grow"
                        >
                            <!-- Update Status Badge -->
                            <div
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                :class="
                                    featuredEvent.is_upcoming
                                        ? 'bg-[var(--color-secondary)]/30 text-white'
                                        : 'bg-[var(--color-primary)]/30 text-white'
                                "
                            >
                                {{
                                    featuredEvent.is_upcoming
                                        ? "Event Mendatang"
                                        : "Event Terakhir"
                                }}
                            </div>

                            <!-- Update Text Colors -->
                            <h3
                                class="text-3xl font-bold text-white group-hover:text-[var(--color-background)] transition-colors duration-300"
                            >
                                {{ featuredEvent.department.name }}
                            </h3>
                            <h4
                                class="text-xl font-medium text-white/90 line-clamp-2 group-hover:text-[var(--color-background)] transition-colors duration-300"
                            >
                                {{ featuredEvent.event_name }}
                            </h4>

                            <!-- Update Date Text -->
                            <div
                                class="flex items-center space-x-2 text-[var(--color-background)] text-sm"
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
                                              ).toLocaleDateString("id-ID", {
                                                  day: "numeric",
                                                  month: "long",
                                              })
                                            : ""
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Empty State Card -->
                        <div
                            v-else
                            class="w-full bg-gradient-to-br from-[var(--color-primary-dark)]/60 to-[var(--color-primary)]/60 p-6 rounded-3xl shadow-xl relative overflow-hidden flex-grow"
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
                            class="w-full bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary)] p-8 rounded-3xl shadow-xl relative overflow-hidden group flex-grow"
                        >
                            <!-- Update Text Colors -->
                            <h4 class="text-2xl font-bold text-white">
                                Jelajahi Semua Event
                            </h4>
                            <p class="text-[var(--color-background)]">
                                Temukan lebih banyak event menarik yang
                                diselenggarakan oleh HMJTK Polban
                            </p>

                            <!-- Update Button -->
                            <Link
                                href="/our-event"
                                class="inline-flex items-center justify-center w-full px-6 py-3 text-[var(--color-primary)] bg-white rounded-xl hover:bg-[var(--color-background)] transition-all duration-300 transform hover:scale-105 shadow-lg"
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
        </section>

        <!-- News Articles Section -->
        <section
            class="py-20 bg-gradient-to-b from-white via-[var(--color-background)]/30 to-white"
        >
            <div class="container-custom">
                <!-- Header Section -->
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <h2
                        class="text-[var(--color-primary)] font-semibold text-lg mb-4 animate-fade-in"
                    >
                        Berita Terbaru
                    </h2>
                    <h3
                        class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        Kabar dan Informasi
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-[var(--color-primary-dark)] to-[var(--color-primary)]"
                        >
                            HMJTK
                        </span>
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
                            class="flex-grow bg-gradient-to-br from-[var(--color-primary-dark)] to-[var(--color-primary)] rounded-3xl p-6 text-white group hover:shadow-2xl hover:shadow-[var(--color-primary)]/50 transition-all duration-300 flex flex-col justify-between relative overflow-hidden"
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
                                        <p
                                            class="text-sm text-[var(--color-background)]"
                                        >
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
                                        <p
                                            class="text-sm text-[var(--color-background)]"
                                        >
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
                                        <p
                                            class="text-sm text-[var(--color-background)]"
                                        >
                                            Artikel diperbarui secara rutin
                                            setiap minggu
                                        </p>
                                    </div>
                                </div>

                                <div class="bg-white/10 rounded-lg p-3 mb-6">
                                    <p
                                        class="text-sm text-[var(--color-background)] italic"
                                    >
                                        "Tetap terhubung dengan perkembangan
                                        terkini di HMJTK melalui artikel-artikel
                                        informatif kami"
                                    </p>
                                </div>
                            </div>

                            <Link
                                href="/news"
                                class="relative z-10 inline-flex items-center justify-center px-6 py-3 rounded-xl bg-white text-[var(--color-primary)] hover:bg-[var(--color-background)] transition-colors duration-300 font-semibold group-hover:shadow-lg"
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
                            class="bg-white rounded-3xl p-4 shadow-xl shadow-[var(--color-primary)]/10"
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
                                                        class="flex items-center gap-2 text-[var(--color-secondary)] text-sm"
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
                                                            :href="`/article/${article.slug}`"
                                                            class="inline-flex items-center px-6 py-2 rounded-lg bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)] text-white transition-all duration-300"
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
                        class="text-[var(--color-primary)] font-semibold text-lg mb-4 animate-fade-in"
                    >
                        Departemen HMJTK
                    </h2>
                    <h3
                        class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight animate-fade-in"
                        style="animation-delay: 0.2s"
                    >
                        Struktur
                        <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-[var(--color-primary-dark)] to-[var(--color-primary)]"
                        >
                            Organisasi
                        </span>
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
                                    class="absolute top-1/2 -right-4 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-md hover:bg-[var(--color-background)] disabled:opacity-50 disabled:cursor-not-allowed z-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-2"
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
                                </button>
                                <button
                                    @click="prev"
                                    class="absolute top-1/2 -left-4 -translate-y-1/2 w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-md hover:bg-[var(--color-background)] disabled:opacity-50 disabled:cursor-not-allowed z-10 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)] focus:ring-offset-2"
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
                                </button>
                            </Navigation>
                        </template>

                        <Slide
                            v-for="deptLogo in deptLogos"
                            :key="deptLogo.id"
                            class="px-2 py-3"
                        >
                            <div
                                class="group h-full flex flex-col items-center"
                            >
                                <div
                                    class="relative w-full h-auto overflow-hidden"
                                >
                                    <img
                                        :src="deptLogo.image"
                                        :alt="deptLogo.title"
                                        class="w-full h-auto object-contain transition-transform duration-300 group-hover:scale-105"
                                    />
                                </div>
                                <div class="mt-3">
                                    <h4
                                        class="text-base font-semibold text-gray-800 text-center line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300"
                                    >
                                        {{ deptLogo.title }}
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
    @apply px-2 py-3;
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

/* Responsive slide sizing */
:deep(.carousel__slide) {
    @apply px-2 md:px-3;

    img {
        @apply rounded-xl;
        height: 200px;

        @screen sm {
            height: 220px;
        }

        @screen md {
            height: 240px;
        }

        @screen lg {
            height: 260px;
        }
    }
}

/* Update slide transforms for mobile */
@media (max-width: 640px) {
    .department-carousel .carousel__slide {
        transform: none;
        opacity: 1;
    }

    .department-carousel .carousel__slide--active {
        transform: none;
    }

    .department-carousel .carousel__slide--prev,
    .department-carousel .carousel__slide--next {
        transform: scale(0.95);
    }
}
</style>

<style>
.container-custom {
    @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}
</style>
