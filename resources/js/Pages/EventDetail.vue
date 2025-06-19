<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    event: Object,
    relatedEvents: Array,
    popularEvents: Array,
});

const goToEvent = (eventId) => {
    router.get(`/event/${eventId}`);
};

const popularEvents = computed(() => props.popularEvents);

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

                            <!-- Event Dates and Times -->
                            <div
                                v-if="event.dates && event.dates.length > 0"
                                class="mb-6"
                            >
                                <h3
                                    class="text-lg font-semibold mb-3"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    Jadwal Event
                                </h3>
                                <div class="space-y-2">
                                    <div
                                        v-for="(date, index) in event.dates"
                                        :key="index"
                                        class="flex items-center gap-3 p-3 rounded-lg"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-background)',
                                        }"
                                    >
                                        <svg
                                            class="w-5 h-5"
                                            :style="{
                                                color: 'var(--color-primary)',
                                            }"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                        <span
                                            :style="{
                                                color: 'var(--text-color)',
                                            }"
                                        >
                                            {{ formatDate(date.event_date) }}
                                            <span
                                                v-if="date.event_time"
                                                class="ml-2"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                -
                                                {{
                                                    formatTime(date.event_time)
                                                }}
                                            </span>
                                        </span>
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
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <div
                                    v-for="(
                                        image, index
                                    ) in event.event_gallery"
                                    :key="index"
                                    class="aspect-square rounded-lg overflow-hidden"
                                >
                                    <img
                                        :src="image"
                                        :alt="`Gallery ${index + 1}`"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Download Document -->
                        <div v-if="event.event_doc" class="mb-8">
                            <div class="bg-white rounded-2xl shadow-lg p-6">
                                <h2
                                    class="text-xl font-bold mb-4"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    Dokumen Event
                                </h2>
                                <a
                                    :href="event.event_doc"
                                    target="_blank"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold transition-all duration-300 text-white"
                                    :style="{
                                        backgroundColor: 'var(--color-primary)',
                                    }"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    Download Dokumen
                                </a>
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
                                Popular Events
                            </h2>
                            <div class="space-y-3 lg:space-y-4">
                                <article
                                    v-for="event in popularEvents"
                                    :key="event.id"
                                    class="group cursor-pointer border-b border-gray-100 pb-3 lg:pb-4 last:border-b-0 last:pb-0"
                                    @click="goToEvent(event.id)"
                                >
                                    <div class="flex gap-3 lg:gap-4">
                                        <div
                                            class="w-16 h-16 lg:w-20 lg:h-20 flex-shrink-0 rounded-lg overflow-hidden"
                                        >
                                            <img
                                                v-if="event.event_flyer"
                                                :src="event.event_flyer"
                                                :alt="event.event_name"
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
                                                {{ event.event_name }}
                                            </h3>
                                            <div
                                                class="flex justify-between items-center text-xs"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                <span>{{
                                                    formatDate(event.start_date)
                                                }}</span>
                                                <span>{{
                                                    event.fee_type === "free"
                                                        ? "Gratis"
                                                        : "Berbayar"
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
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
    min-height: 100vh;
}
</style>
