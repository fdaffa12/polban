<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    event: Object,
    relatedEvents: Array,
    popularEvents: Array,
});

const activeGalleryImage = ref(0);

const setActiveGalleryImage = (index) => {
    activeGalleryImage.value = index;
};

const goToEvent = (eventId) => {
    router.get(`/event/${eventId}`);
};

const popularEvents = computed(() => {
    if (!props.relatedEvents) return [];
    // Filter event dengan departemen yang sama dan bukan event yang sedang ditampilkan
    return props.relatedEvents
        .filter((e) => e.id !== props.event.id) // Exclude current event
        .slice(0, 5); // Ambil maksimal 5 event
});

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

const formatTime = (timeString) => {
    if (!timeString) return "";
    return timeString.substring(0, 5); // Get HH:MM format
};

const getStatusColor = (status) => {
    switch (status) {
        case "ongoing":
            return "var(--color-success)";
        case "upcoming":
            return "var(--color-primary)";
        case "past":
            return "var(--light-text)";
        default:
            return "var(--color-primary)";
    }
};

const getStatusText = (status) => {
    switch (status) {
        case "ongoing":
            return "Sedang Berlangsung";
        case "upcoming":
            return "Akan Datang";
        case "past":
            return "Selesai";
        default:
            return "Akan Datang";
    }
};

// Tambahkan state untuk preview gambar
const showImagePreview = ref(false);
const previewImageIndex = ref(0);

// Fungsi untuk membuka preview gambar
const openImagePreview = (index) => {
    previewImageIndex.value = index;
    showImagePreview.value = true;
};

// Fungsi untuk menutup preview gambar
const closeImagePreview = () => {
    showImagePreview.value = false;
};

// Fungsi untuk navigasi gambar preview
const prevPreviewImage = () => {
    if (previewImageIndex.value > 0) {
        previewImageIndex.value--;
    } else {
        previewImageIndex.value = props.event.event_gallery.length - 1;
    }
};

const nextPreviewImage = () => {
    if (previewImageIndex.value < props.event.event_gallery.length - 1) {
        previewImageIndex.value++;
    } else {
        previewImageIndex.value = 0;
    }
};
</script>

<template>
    <GuestLayout :title="event.event_name">
        <!-- Event Header -->
        <section
            class="relative py-12"
            :style="{
                background: `linear-gradient(to bottom, white, white 30%, var(--color-background))`,
            }"
        >
            <div class="container-custom">
                <nav
                    class="flex items-center space-x-2 text-sm mb-8"
                    :style="{ color: 'var(--light-text)' }"
                >
                    <a
                        href="/"
                        class="hover:text-[var(--color-primary)] transition-colors duration-300"
                        >Home</a
                    >
                    <span>/</span>
                    <a
                        href="/events"
                        class="hover:text-[var(--color-primary)] transition-colors duration-300"
                        >Events</a
                    >
                    <span>/</span>
                    <span :style="{ color: 'var(--color-primary)' }">{{
                        event.department.name
                    }}</span>
                </nav>

                <!-- Two Column Layout -->
                <div class="grid-cols-layout">
                    <!-- Main Content Column -->
                    <div>
                        <div
                            class="inline-block px-4 py-2 rounded-full text-sm font-medium text-white mb-6"
                            :style="{
                                backgroundColor: getStatusColor(event.status),
                            }"
                        >
                            {{ getStatusText(event.status) }}
                        </div>

                        <h1
                            class="heading-responsive font-bold mb-6"
                            :style="{ color: 'var(--text-color)' }"
                        >
                            {{ event.event_name }}
                        </h1>

                        <div
                            class="flex items-center space-x-6 text-sm mb-8"
                            :style="{ color: 'var(--light-text)' }"
                        >
                            <div class="flex items-center space-x-2">
                                <svg
                                    class="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>
                                    {{ formatDate(event.start_date) }}
                                    <span
                                        v-if="
                                            event.end_date &&
                                            event.end_date !== event.start_date
                                        "
                                    >
                                        - {{ formatDate(event.end_date) }}
                                    </span>
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg
                                    class="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>
                                    {{
                                        event.fee_type === "free"
                                            ? "Gratis"
                                            : `Rp ${
                                                  event.fee_amount?.toLocaleString(
                                                      "id-ID"
                                                  ) || 0
                                              }`
                                    }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg
                                    class="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>{{ event.department.name }}</span>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div v-if="event.event_flyer" class="mb-8">
                            <img
                                :src="event.event_flyer"
                                :alt="event.event_name"
                                class="w-full h-96 object-cover rounded-2xl shadow-lg"
                            />
                        </div>

                        <!-- Event Details -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                            <h2
                                class="text-xl font-bold mb-4"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                Detail Event
                            </h2>

                            <!-- Event Dates and Times - Updated Design -->
                            <div
                                v-if="event.dates && event.dates.length > 0"
                                class="mb-6"
                            >
                                <h3
                                    class="text-lg font-semibold mb-4"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    Jadwal Event
                                </h3>
                                <div class="grid gap-3">
                                    <div
                                        v-for="(date, index) in event.dates"
                                        :key="index"
                                        class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-[var(--color-primary)] transition-colors duration-300"
                                    >
                                        <div class="flex items-center gap-4">
                                            <div class="flex-shrink-0">
                                                <div
                                                    class="w-12 h-12 rounded-full flex items-center justify-center"
                                                    :style="{
                                                        backgroundColor:
                                                            'var(--color-primary)',
                                                    }"
                                                >
                                                    <svg
                                                        class="w-6 h-6 text-white"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <div
                                                    class="font-semibold text-lg"
                                                    :style="{
                                                        color: 'var(--text-color)',
                                                    }"
                                                >
                                                    {{
                                                        formatDate(
                                                            date.event_date
                                                        )
                                                    }}
                                                </div>
                                                <div
                                                    class="text-sm"
                                                    :style="{
                                                        color: 'var(--light-text)',
                                                    }"
                                                >
                                                    Hari {{ index + 1 }}
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            v-if="date.event_time"
                                            class="text-right"
                                        >
                                            <div
                                                class="font-semibold text-lg"
                                                :style="{
                                                    color: 'var(--color-primary)',
                                                }"
                                            >
                                                {{
                                                    formatTime(date.event_time)
                                                }}
                                            </div>
                                            <div
                                                class="text-sm"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                WIB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Event Content -->
                            <div
                                class="prose prose-lg max-w-none"
                                :style="{ color: 'var(--text-color)' }"
                                v-html="event.event_detail"
                            ></div>
                        </div>

                        <!-- Event Gallery -->
                        <div
                            v-if="
                                event.event_gallery &&
                                event.event_gallery.length > 0
                            "
                            class="mb-8"
                        >
                            <h2
                                class="text-xl font-bold mb-4"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                Galeri Event
                            </h2>
                            <div class="bg-white rounded-2xl shadow-lg p-4">
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4"
                                >
                                    <div
                                        v-for="(
                                            image, index
                                        ) in event.event_gallery"
                                        :key="index"
                                        class="relative h-48 rounded-xl overflow-hidden group cursor-pointer"
                                        @click="openImagePreview(index)"
                                    >
                                        <img
                                            :src="image"
                                            :alt="`Gallery ${index + 1}`"
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                        />
                                        <div
                                            class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center"
                                        >
                                            <div
                                                class="bg-white/20 backdrop-blur-sm rounded-full p-3"
                                            >
                                                <svg
                                                    class="w-6 h-6 text-white"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"
                                                    ></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Preview Modal -->
                        <div
                            v-if="showImagePreview"
                            class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4"
                            @click="closeImagePreview"
                        >
                            <div class="relative max-w-4xl max-h-full">
                                <!-- Close Button -->
                                <button
                                    @click="closeImagePreview"
                                    class="absolute top-4 right-4 z-10 bg-white/20 backdrop-blur-sm rounded-full p-2 text-white hover:bg-white/30 transition-all duration-300"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        ></path>
                                    </svg>
                                </button>

                                <!-- Navigation Buttons -->
                                <button
                                    v-if="event.event_gallery.length > 1"
                                    @click.stop="prevPreviewImage"
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 z-10 bg-white/20 backdrop-blur-sm rounded-full p-3 text-white hover:bg-white/30 transition-all duration-300"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 19l-7-7 7-7"
                                        ></path>
                                    </svg>
                                </button>

                                <button
                                    v-if="event.event_gallery.length > 1"
                                    @click.stop="nextPreviewImage"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 z-10 bg-white/20 backdrop-blur-sm rounded-full p-3 text-white hover:bg-white/30 transition-all duration-300"
                                >
                                    <svg
                                        class="w-6 h-6"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5l7 7-7 7"
                                        ></path>
                                    </svg>
                                </button>

                                <!-- Image -->
                                <img
                                    :src="
                                        event.event_gallery[previewImageIndex]
                                    "
                                    :alt="`Gallery ${previewImageIndex + 1}`"
                                    class="max-w-full max-h-full object-contain rounded-lg"
                                    @click.stop
                                />

                                <!-- Image Counter -->
                                <div
                                    class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 text-white text-sm"
                                >
                                    {{ previewImageIndex + 1 }} /
                                    {{ event.event_gallery.length }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Column -->
                    <div>
                        <div
                            class="bg-white rounded-2xl shadow-lg p-4 lg:p-6 lg:sticky lg:top-8"
                        >
                            <h2
                                class="text-xl lg:text-2xl font-bold mb-4 lg:mb-6"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                Event {{ event.department.name }} Lainnya
                            </h2>
                            <div
                                v-if="popularEvents.length > 0"
                                class="space-y-3 lg:space-y-4"
                            >
                                <article
                                    v-for="relatedEvent in popularEvents"
                                    :key="relatedEvent.id"
                                    class="group cursor-pointer border-b border-gray-100 pb-3 lg:pb-4 last:border-b-0 last:pb-0"
                                    @click="goToEvent(relatedEvent.id)"
                                >
                                    <div class="flex gap-3 lg:gap-4">
                                        <div
                                            class="w-16 h-16 lg:w-20 lg:h-20 flex-shrink-0 rounded-lg overflow-hidden"
                                        >
                                            <img
                                                v-if="relatedEvent.event_flyer"
                                                :src="relatedEvent.event_flyer"
                                                :alt="relatedEvent.event_name"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            />
                                            <div
                                                v-else
                                                class="w-full h-full flex items-center justify-center"
                                                :style="{
                                                    backgroundColor:
                                                        'var(--color-background)',
                                                }"
                                            >
                                                <span
                                                    class="text-xs"
                                                    :style="{
                                                        color: 'var(--light-text)',
                                                    }"
                                                    >No Image</span
                                                >
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3
                                                class="font-semibold text-xs lg:text-sm line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300 mb-1 lg:mb-2"
                                                :style="{
                                                    color: 'var(--text-color)',
                                                }"
                                            >
                                                {{ relatedEvent.event_name }}
                                            </h3>
                                            <div
                                                class="flex justify-between items-center text-xs"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                <span>{{
                                                    formatDate(
                                                        relatedEvent.start_date
                                                    )
                                                }}</span>
                                                <span>{{
                                                    relatedEvent.fee_type ===
                                                    "free"
                                                        ? "Gratis"
                                                        : "Berbayar"
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div
                                v-else
                                class="text-center py-4"
                                :style="{ color: 'var(--light-text)' }"
                            >
                                Tidak ada event lain dari departemen ini
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.container-custom {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (min-width: 640px) {
    .container-custom {
        padding: 0 2rem;
    }
}

@media (min-width: 1024px) {
    .container-custom {
        padding: 0 3rem;
    }
}

.grid-cols-layout {
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

@media (min-width: 1024px) {
    .grid-cols-layout {
        grid-template-columns: 2fr 1fr;
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    line-height: 1.8;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
    color: var(--text-color);
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose p {
    color: var(--text-color);
    margin-bottom: 1.5rem;
}

.prose strong {
    color: var(--text-color);
    font-weight: 600;
}

.prose a {
    color: var(--color-primary);
    text-decoration: none;
}

.prose a:hover {
    color: var(--color-primary-dark);
}

.prose ul,
.prose ol {
    margin: 1.5rem 0;
    padding-left: 2rem;
}

.prose li {
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.prose blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: var(--light-text);
}

.heading-responsive {
    font-size: 1.875rem;
    line-height: 2.25rem;
}

@media (min-width: 1024px) {
    .heading-responsive {
        font-size: 2.25rem;
        line-height: 2.5rem;
    }
}

section {
    min-height: auto;
    padding-top: 2rem;
    padding-bottom: 4rem;
}

/* Gallery Carousel Styles */
.gallery-carousel :deep(.carousel__slide),
.gallery-carousel :deep(.carousel__viewport),
.gallery-carousel :deep(.carousel__track),
.gallery-carousel :deep(.carousel__slide--sliding),
.gallery-carousel :deep(.carousel__slide),
.gallery-carousel :deep(.carousel__slide--active),
.gallery-carousel :deep(.carousel__slide--prev),
.gallery-carousel :deep(.carousel__slide--next),
.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    /* Hapus semua style ini */
}

/* Tambahkan style baru untuk spacing yang lebih rapat */
section {
    min-height: auto; /* Ubah dari 100vh */
}

/* Tambahkan margin yang lebih kecil untuk section event terkait */
section.py-16 {
    padding-top: 2rem; /* Kurangi padding atas */
    padding-bottom: 4rem;
}
</style>
