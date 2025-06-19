<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {
    format,
    startOfMonth,
    endOfMonth,
    eachDayOfInterval,
    isSameDay,
    isToday,
    addMonths,
    subMonths,
} from "date-fns";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";

const props = defineProps({
    events: Array,
});

const currentMonth = ref(new Date());

const calendarDays = computed(() => {
    const start = startOfMonth(currentMonth.value);
    const end = endOfMonth(currentMonth.value);
    return eachDayOfInterval({ start, end });
});

const getEventsForDay = (day) => {
    return props.events.filter((event) =>
        event.dates.some((date) => isSameDay(new Date(date.event_date), day))
    );
};

const navigateMonth = (direction) => {
    currentMonth.value =
        direction === "next"
            ? addMonths(currentMonth.value, 1)
            : subMonths(currentMonth.value, 1);
};

const goToEventDetail = (eventId) => {
    router.visit(route("public.event.detail", eventId));
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

const getStatusBackgroundColor = (status) => {
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

const getStatusOpacity = (status) => {
    switch (status) {
        case "ongoing":
            return "0.15";
        case "upcoming":
            return "0.12";
        case "past":
            return "0.08";
        default:
            return "0.1";
    }
};

// State untuk popup
const showPopup = ref(false);
const popupEvent = ref(null);
const popupStyle = ref({
    top: "0px",
    left: "0px",
});

// Fungsi untuk menampilkan popup
const showEventPopup = (event, eventData) => {
    popupEvent.value = eventData;

    // Hitung posisi popup
    const rect = event.target.getBoundingClientRect();
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const scrollLeft =
        window.pageXOffset || document.documentElement.scrollLeft;

    // Posisikan popup di atas event jika ada ruang, jika tidak di bawah
    let top = rect.top + scrollTop - 10;
    if (rect.top < 100) {
        // Jika terlalu dekat dengan atas
        top = rect.bottom + scrollTop + 10;
    }

    popupStyle.value = {
        top: `${top}px`,
        left: `${rect.left + scrollLeft}px`,
    };

    showPopup.value = true;
};

// Fungsi untuk menyembunyikan popup
const hideEventPopup = () => {
    showPopup.value = false;
};

// Fungsi untuk mendapatkan text status
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
    <GuestLayout title="Event Calendar">
        <div class="py-6 sm:py-12 bg-[var(--color-background)]">
            <div class="container-custom">
                <!-- Calendar Container -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <!-- Header Calendar -->
                    <div
                        class="bg-gradient-to-r from-[var(--color-primary-dark)] to-[var(--color-primary)] p-4 sm:p-6"
                    >
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
                        >
                            <h1
                                class="text-xl sm:text-2xl font-bold text-white"
                            >
                                Event Calendar
                            </h1>
                            <div class="flex items-center space-x-4">
                                <button
                                    @click="navigateMonth('prev')"
                                    class="p-2 rounded-full hover:bg-white/10 transition-colors duration-200"
                                >
                                    <ChevronLeft class="w-5 h-5 text-white" />
                                </button>
                                <span
                                    class="text-base sm:text-lg font-medium text-white min-w-[150px] text-center"
                                >
                                    {{ format(currentMonth, "MMMM yyyy") }}
                                </span>
                                <button
                                    @click="navigateMonth('next')"
                                    class="p-2 rounded-full hover:bg-white/10 transition-colors duration-200"
                                >
                                    <ChevronRight class="w-5 h-5 text-white" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="p-2 sm:p-6">
                        <div
                            class="grid grid-cols-7 gap-[1px] bg-[var(--color-primary-dark)] rounded-xl overflow-hidden"
                        >
                            <!-- Day headers -->
                            <div
                                v-for="(day, index) in [
                                    'Sun',
                                    'Mon',
                                    'Tue',
                                    'Wed',
                                    'Thu',
                                    'Fri',
                                    'Sat',
                                ]"
                                :key="day"
                                class="bg-[var(--color-primary-dark)] text-white py-2 sm:py-3 text-center text-xs sm:text-sm font-medium"
                            >
                                <!-- Responsive day display -->
                                <span class="hidden sm:inline">{{ day }}</span>
                                <span class="sm:hidden">{{
                                    day.charAt(0)
                                }}</span>
                            </div>

                            <!-- Calendar days -->
                            <div
                                v-for="day in calendarDays"
                                :key="day"
                                class="bg-white relative transition-all duration-200 calendar-cell"
                                :class="{
                                    'bg-[var(--color-primary)]/5': isToday(day),
                                }"
                            >
                                <!-- Date number -->
                                <div
                                    class="flex items-center justify-between mb-1 sm:mb-2 p-1 sm:p-3"
                                >
                                    <span
                                        class="text-xs sm:text-sm font-medium inline-flex items-center justify-center w-6 h-6 sm:w-8 sm:h-8 rounded-full transition-all duration-200"
                                        :class="{
                                            'bg-[var(--color-primary)] text-white shadow-md':
                                                isToday(day),
                                            'hover:bg-[var(--color-primary)]/10 text-[var(--text-color)]':
                                                !isToday(day),
                                        }"
                                    >
                                        {{ format(day, "d") }}
                                    </span>
                                </div>

                                <!-- Events untuk hari ini -->
                                <div
                                    class="px-1 sm:px-3 pb-1 sm:pb-3 flex flex-col gap-1 sm:gap-2"
                                >
                                    <template
                                        v-if="getEventsForDay(day).length > 0"
                                    >
                                        <!-- Tampilkan maksimal 3 event -->
                                        <div
                                            v-for="(
                                                event, index
                                            ) in getEventsForDay(day).slice(
                                                0,
                                                3
                                            )"
                                            :key="event.id"
                                            @click="goToEventDetail(event.id)"
                                            @mouseenter="
                                                showEventPopup($event, event)
                                            "
                                            @mouseleave="hideEventPopup"
                                            class="group relative p-1.5 sm:p-2 rounded-lg cursor-pointer transition-all duration-300 hover:transform hover:scale-[1.02] hover:-translate-y-0.5"
                                            :style="{
                                                backgroundColor: `${getStatusBackgroundColor(
                                                    event.status
                                                )}20`,
                                                borderLeft: `3px solid ${getStatusColor(
                                                    event.status
                                                )}`,
                                            }"
                                        >
                                            <div
                                                class="font-medium text-[10px] sm:text-xs truncate group-hover:font-bold"
                                                :style="{
                                                    color: getStatusColor(
                                                        event.status
                                                    ),
                                                }"
                                            >
                                                {{ event.event_name }}
                                            </div>
                                            <div
                                                class="text-[8px] sm:text-[10px] truncate"
                                                :style="{
                                                    color: `${getStatusColor(
                                                        event.status
                                                    )}CC`,
                                                }"
                                            >
                                                {{ event.department.name }}
                                            </div>

                                            <!-- Enhanced Hover effect -->
                                            <div
                                                class="absolute inset-0 rounded-lg transition-all duration-300 opacity-0 group-hover:opacity-100"
                                                :style="{
                                                    boxShadow: `0 4px 12px ${getStatusColor(
                                                        event.status
                                                    )}40`,
                                                    background: `linear-gradient(45deg, ${getStatusColor(
                                                        event.status
                                                    )}10, ${getStatusColor(
                                                        event.status
                                                    )}20)`,
                                                }"
                                            ></div>
                                        </div>

                                        <!-- Indicator untuk event tambahan -->
                                        <div
                                            v-if="
                                                getEventsForDay(day).length > 3
                                            "
                                            class="text-[10px] sm:text-xs text-[var(--color-primary)] font-medium px-2"
                                        >
                                            +{{
                                                getEventsForDay(day).length - 3
                                            }}
                                            more events
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Popup -->
        <div
            v-if="showPopup"
            class="fixed z-50 bg-white rounded-lg shadow-xl p-3 max-w-xs event-popup"
            :style="popupStyle"
        >
            <h4
                class="font-semibold text-sm mb-1"
                :style="{ color: getStatusColor(popupEvent.status) }"
            >
                {{ popupEvent.event_name }}
            </h4>
            <p class="text-xs text-[var(--text-color)]">
                {{ popupEvent.department.name }}
            </p>
            <div
                class="text-xs mt-2"
                :style="{ color: getStatusColor(popupEvent.status) }"
            >
                {{ getStatusText(popupEvent.status) }}
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
.container-custom {
    @apply max-w-7xl mx-auto px-2 sm:px-4 lg:px-8;
}

/* Enhanced hover transitions */
.group {
    transform: translateZ(0);
    backface-visibility: hidden;
}

.group:hover {
    z-index: 10;
}

/* Custom scrollbar styling */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: var(--color-primary) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 3px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: var(--color-primary);
    border-radius: 20px;
}

/* Calendar cell border styling */
.grid {
    box-shadow: 0 0 0 1px var(--color-primary-dark);
}

/* Enhanced calendar cell hover effect */
.bg-white {
    position: relative;
    isolation: isolate;
}

.bg-white::after {
    content: "";
    position: absolute;
    inset: 0;
    background: var(--color-primary);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.bg-white:hover::after {
    opacity: 0.03;
}

/* Media Queries for better responsiveness */
@media (max-width: 640px) {
    .grid {
        gap: 1px;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 2px;
    }
}

/* Animation for hover effects */
@keyframes float {
    0% {
        transform: translateY(0) scale(1.02);
    }
    50% {
        transform: translateY(-2px) scale(1.02);
    }
    100% {
        transform: translateY(0) scale(1.02);
    }
}

.group:hover {
    animation: float 2s ease-in-out infinite;
}

/* Calendar cell styling */
.calendar-cell {
    min-height: auto;
    height: auto;
    display: flex;
    flex-direction: column;
}

/* Event popup styling */
.event-popup {
    pointer-events: none;
    border: 1px solid var(--color-primary-dark);
    backdrop-filter: blur(8px);
    transform: translateY(-100%);
    margin-top: -10px;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-90%);
    }
    to {
        opacity: 1;
        transform: translateY(-100%);
    }
}

/* Responsive grid adjustments */
@media (min-width: 640px) {
    .calendar-cell {
        min-height: 120px;
    }
}

@media (min-width: 768px) {
    .calendar-cell {
        min-height: 140px;
    }
}

/* Remove scrollbar */
.calendar-cell > div:last-child {
    overflow: visible;
}
</style>
