<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    departments: Array,
    events: Array,
    activeDepartment: Number,
    pagination: Object,
    popularEvents: Array,
});

const activeDepartment = ref(props.activeDepartment || null);
const displayedCount = ref(5);
const loading = ref(false);

// Computed properties
const displayedEvents = computed(() => {
    return props.events.slice(0, displayedCount.value);
});

const canLoadMore = computed(() => {
    return displayedCount.value < props.events.length;
});

const popularEvents = computed(() => props.popularEvents);

// Methods
const filterByDepartment = (departmentId) => {
    activeDepartment.value = departmentId;
    displayedCount.value = 5; // Reset to initial count
    if (departmentId) {
        router.get(`/our-event/department/${departmentId}`);
    } else {
        router.get("/our-event");
    }
};

const goToEvent = (eventId) => {
    router.get(`/event/${eventId}`);
};

const loadMore = () => {
    loading.value = true;
    setTimeout(() => {
        displayedCount.value += 5;
        loading.value = false;
    }, 500); // Simulate loading delay
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};

const getStatusColor = (status) => {
    switch (status) {
        case "ongoing":
            return "var(--color-secondary)";
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

// Reset displayed count when events change
onMounted(() => {
    displayedCount.value = 5;
});
</script>

<template>
    <GuestLayout title="Portal Event">
        <!-- Main Container -->
        <section class="relative py-12" :style="{ backgroundColor: 'white' }">
            <div class="container-custom">
                <div class="grid-cols-layout">
                    <!-- Left Column (Main Content) -->
                    <div>
                        <!-- Title and Description -->
                        <div class="mb-8">
                            <h1
                                class="text-3xl lg:text-4xl xl:text-5xl font-bold mb-4"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                Portal Event
                            </h1>
                            <p
                                class="text-base lg:text-lg mb-6"
                                :style="{ color: 'var(--light-text)' }"
                            >
                                Temukan berbagai event menarik dari setiap
                                departemen
                            </p>
                        </div>

                        <!-- Department Navigation -->
                        <div class="mb-8">
                            <div class="flex flex-wrap gap-2 lg:gap-3">
                                <button
                                    @click="filterByDepartment(null)"
                                    class="group relative px-4 lg:px-6 py-2 lg:py-3 rounded-xl transition-all duration-300 text-xs lg:text-sm font-semibold overflow-hidden"
                                    :style="
                                        !activeDepartment
                                            ? {
                                                  backgroundColor:
                                                      'var(--color-primary)',
                                                  color: 'white',
                                              }
                                            : {
                                                  backgroundColor: 'white',
                                                  color: 'var(--color-primary)',
                                                  border: '1px solid var(--color-secondary)',
                                              }
                                    "
                                >
                                    <span class="relative z-10"
                                        >Semua Event</span
                                    >
                                    <div
                                        v-if="activeDepartment"
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-secondary)',
                                            opacity: '0.1',
                                        }"
                                    ></div>
                                </button>
                                <button
                                    v-for="department in departments"
                                    :key="department.id"
                                    @click="filterByDepartment(department.id)"
                                    class="group relative px-4 lg:px-6 py-2 lg:py-3 rounded-xl transition-all duration-300 text-xs lg:text-sm font-semibold overflow-hidden"
                                    :style="
                                        Number(activeDepartment) ===
                                        Number(department.id)
                                            ? {
                                                  backgroundColor:
                                                      'var(--color-primary)',
                                                  color: 'white',
                                              }
                                            : {
                                                  backgroundColor: 'white',
                                                  color: 'var(--color-primary)',
                                                  border: '1px solid var(--color-secondary)',
                                              }
                                    "
                                >
                                    <span class="relative z-10">
                                        {{ department.name }} ({{
                                            department.events_count
                                        }})
                                    </span>
                                    <div
                                        v-if="
                                            Number(activeDepartment) !==
                                            Number(department.id)
                                        "
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-secondary)',
                                            opacity: '0.1',
                                        }"
                                    ></div>
                                </button>
                            </div>
                        </div>

                        <!-- Events List -->
                        <div class="space-y-4 lg:space-y-6 mb-8">
                            <article
                                v-for="event in displayedEvents"
                                :key="event.id"
                                class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer"
                                @click="goToEvent(event.id)"
                            >
                                <div class="flex flex-col md:flex-row">
                                    <!-- Event Image -->
                                    <div
                                        class="md:w-1/3 relative overflow-hidden"
                                    >
                                        <img
                                            v-if="event.event_flyer"
                                            :src="event.event_flyer"
                                            :alt="event.event_name"
                                            class="w-full h-48 md:h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                        <div
                                            v-else
                                            class="w-full h-48 md:h-full flex items-center justify-center"
                                            :style="{
                                                backgroundColor:
                                                    'var(--color-background)',
                                            }"
                                        >
                                            <span
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                                >No Image</span
                                            >
                                        </div>
                                        <div
                                            class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-medium text-white"
                                            :style="{
                                                backgroundColor: getStatusColor(
                                                    event.status
                                                ),
                                            }"
                                        >
                                            {{ getStatusText(event.status) }}
                                        </div>
                                        <div
                                            class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-medium text-white"
                                            :style="{
                                                backgroundColor:
                                                    'var(--color-primary)',
                                            }"
                                        >
                                            {{ event.department.name }}
                                        </div>
                                    </div>

                                    <!-- Event Content -->
                                    <div
                                        class="md:w-2/3 p-4 lg:p-6 flex flex-col justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg lg:text-xl font-bold mb-2 lg:mb-3 line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300"
                                                :style="{
                                                    color: 'var(--text-color)',
                                                }"
                                            >
                                                {{ event.event_name }}
                                            </h3>
                                            <p
                                                class="text-sm mb-3 lg:mb-4 line-clamp-3"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                {{ event.event_detail }}
                                            </p>
                                            <div
                                                class="flex items-center gap-4 mb-3"
                                            >
                                                <div
                                                    class="flex items-center gap-2 text-sm"
                                                    :style="{
                                                        color: 'var(--light-text)',
                                                    }"
                                                >
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
                                                        {{
                                                            formatDate(
                                                                event.start_date
                                                            )
                                                        }}
                                                        <span
                                                            v-if="
                                                                event.end_date &&
                                                                event.end_date !==
                                                                    event.start_date
                                                            "
                                                        >
                                                            -
                                                            {{
                                                                formatDate(
                                                                    event.end_date
                                                                )
                                                            }}
                                                        </span>
                                                    </span>
                                                </div>
                                                <div
                                                    class="flex items-center gap-2 text-sm"
                                                    :style="{
                                                        color: 'var(--light-text)',
                                                    }"
                                                >
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
                                                            event.fee_type ===
                                                            "free"
                                                                ? "Gratis"
                                                                : `Rp ${
                                                                      event.fee_amount?.toLocaleString(
                                                                          "id-ID"
                                                                      ) || 0
                                                                  }`
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Load More Button -->
                        <div v-if="canLoadMore" class="text-center">
                            <button
                                @click="loadMore"
                                :disabled="loading"
                                class="px-6 lg:px-8 py-2 lg:py-3 rounded-xl font-semibold transition-all duration-300 disabled:opacity-50 text-sm lg:text-base"
                                :style="{
                                    backgroundColor: 'var(--color-primary)',
                                    color: 'white',
                                }"
                            >
                                {{ loading ? "Loading..." : "Load More" }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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
</style>
