<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ref, onMounted } from "vue";
import { Carousel, Slide, Navigation, Pagination } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";

const props = defineProps({
    departments: {
        type: Array,
        required: true,
    },
});

const activeDepartment = ref(null);

const toggleDepartment = (departmentId) => {
    activeDepartment.value =
        activeDepartment.value === departmentId ? null : departmentId;
};

// Fix: Use props.departments instead of departments
onMounted(() => {
    if (props.departments?.length > 0) {
        activeDepartment.value = props.departments[0].id;
    }
});
</script>

<template>
    <GuestLayout title="Department">
        <!-- Hero Section -->
        <section
            class="py-12 bg-gradient-to-b from-white to-[var(--color-background)]/5"
        >
            <div class="container-custom">
                <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                    <span
                        class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                    >
                        Our Team
                    </span>
                    <h1
                        class="text-4xl md:text-5xl font-bold text-[var(--text-color)] leading-tight"
                    >
                        Meet Our Team
                        <span
                            class="block text-xl md:text-2xl font-light text-[var(--light-text)] mt-2"
                        >
                            HMJTK Polban
                        </span>
                    </h1>
                    <div
                        class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                    ></div>
                </div>

                <!-- Department Navigation -->
                <div class="flex flex-wrap gap-4 mb-8 justify-center">
                    <button
                        v-for="department in departments"
                        :key="department.id"
                        @click="toggleDepartment(department.id)"
                        class="px-6 py-3 rounded-full transition-all duration-300 text-sm md:text-base font-medium"
                        :class="{
                            'bg-[var(--color-primary)] text-white shadow-lg scale-110':
                                activeDepartment === department.id,
                            'bg-[var(--color-primary)]/5 text-[var(--color-primary)] hover:bg-[var(--color-primary)]/10':
                                activeDepartment !== department.id,
                        }"
                    >
                        {{ department.dept_name }}
                    </button>
                </div>
            </div>
        </section>

        <!-- Members Section -->
        <section
            class="py-20 bg-gradient-to-b from-[var(--color-background)]/5 to-[var(--color-background)]/10"
        >
            <div class="container-custom">
                <div v-for="department in departments" :key="department.id">
                    <div
                        v-show="activeDepartment === department.id"
                        class="space-y-20 animate-fade-in"
                    >
                        <!-- Board of Department Section -->
                        <div
                            v-if="department.board_members?.length"
                            class="space-y-8"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto mb-16 space-y-4"
                            >
                                <span
                                    class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                                >
                                    Board of Department
                                </span>
                                <h2
                                    class="text-3xl font-bold text-[var(--text-color)] leading-tight"
                                >
                                    {{ department.dept_name }}
                                </h2>
                                <div
                                    class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                                ></div>
                            </div>

                            <div class="relative max-w-7xl mx-auto">
                                <Carousel
                                    :items-to-show="3"
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
                                        v-for="member in department.board_members"
                                        :key="member.id"
                                        class="px-3"
                                    >
                                        <div
                                            class="relative aspect-[3/4] rounded-2xl overflow-hidden group cursor-pointer"
                                        >
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"
                                            ></div>

                                            <!-- Member Image -->
                                            <div class="w-full h-full">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                                />
                                                <div
                                                    v-else
                                                    class="w-full h-full bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-secondary)]/10 flex items-center justify-center"
                                                >
                                                    <span
                                                        class="text-[var(--color-primary)] text-4xl font-bold"
                                                    >
                                                        {{ member.name[0] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Member Info Overlay -->
                                            <div
                                                class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-6 group-hover:translate-y-0 transition-transform duration-500"
                                            >
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-white text-xl font-bold"
                                                    >
                                                        {{ member.name }}
                                                    </h4>
                                                    <p
                                                        class="text-[var(--color-secondary)] font-medium"
                                                    >
                                                        {{ member.position }}
                                                    </p>
                                                    <p
                                                        class="text-white/80 text-sm"
                                                    >
                                                        {{ member.job_titles }}
                                                    </p>
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

                        <!-- Section Head Section -->
                        <div
                            v-if="department.section_heads.length"
                            class="space-y-8"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto mb-16 space-y-4"
                            >
                                <span
                                    class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                                >
                                    Section Head
                                </span>
                                <h2
                                    class="text-3xl font-bold text-[var(--text-color)] leading-tight"
                                >
                                    Department Leaders
                                </h2>
                                <div
                                    class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                                ></div>
                            </div>

                            <div class="relative max-w-7xl mx-auto">
                                <Carousel
                                    :items-to-show="3"
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
                                        v-for="member in department.section_heads"
                                        :key="member.id"
                                        class="px-3"
                                    >
                                        <div
                                            class="relative aspect-[3/4] rounded-2xl overflow-hidden group cursor-pointer"
                                        >
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"
                                            ></div>

                                            <!-- Member Image -->
                                            <div class="w-full h-full">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                                />
                                                <div
                                                    v-else
                                                    class="w-full h-full bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-secondary)]/10 flex items-center justify-center"
                                                >
                                                    <span
                                                        class="text-[var(--color-primary)] text-4xl font-bold"
                                                    >
                                                        {{ member.name[0] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Member Info Overlay -->
                                            <div
                                                class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-6 group-hover:translate-y-0 transition-transform duration-500"
                                            >
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-white text-xl font-bold"
                                                    >
                                                        {{ member.name }}
                                                    </h4>
                                                    <p
                                                        class="text-[var(--color-secondary)] font-medium"
                                                    >
                                                        {{ member.position }}
                                                    </p>
                                                    <p
                                                        class="text-white/80 text-sm"
                                                    >
                                                        {{ member.job_titles }}
                                                    </p>
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

                        <!-- Staff Section -->
                        <div
                            v-if="department.staff_members.length"
                            class="space-y-8"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto mb-16 space-y-4"
                            >
                                <span
                                    class="text-[var(--color-primary)] font-medium text-lg tracking-wide"
                                >
                                    Staff Members
                                </span>
                                <h2
                                    class="text-3xl font-bold text-[var(--text-color)] leading-tight"
                                >
                                    Department Staff
                                </h2>
                                <div
                                    class="w-20 h-1 bg-[var(--color-primary)] mx-auto mt-6 rounded-full"
                                ></div>
                            </div>

                            <div class="relative max-w-7xl mx-auto">
                                <Carousel
                                    :items-to-show="3"
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
                                        v-for="member in department.staff_members"
                                        :key="member.id"
                                        class="px-3"
                                    >
                                        <div
                                            class="relative aspect-[3/4] rounded-2xl overflow-hidden group cursor-pointer"
                                        >
                                            <div
                                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500"
                                            ></div>

                                            <!-- Member Image -->
                                            <div class="w-full h-full">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                                />
                                                <div
                                                    v-else
                                                    class="w-full h-full bg-gradient-to-br from-[var(--color-primary)]/10 to-[var(--color-secondary)]/10 flex items-center justify-center"
                                                >
                                                    <span
                                                        class="text-[var(--color-primary)] text-4xl font-bold"
                                                    >
                                                        {{ member.name[0] }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Member Info Overlay -->
                                            <div
                                                class="absolute bottom-0 left-0 right-0 p-6 transform translate-y-6 group-hover:translate-y-0 transition-transform duration-500"
                                            >
                                                <div class="space-y-2">
                                                    <h4
                                                        class="text-white text-xl font-bold"
                                                    >
                                                        {{ member.name }}
                                                    </h4>
                                                    <p
                                                        class="text-[var(--color-secondary)] font-medium"
                                                    >
                                                        {{ member.position }}
                                                    </p>
                                                    <p
                                                        class="text-white/80 text-sm"
                                                    >
                                                        {{ member.job_titles }}
                                                    </p>
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
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
.gallery-carousel :deep(.carousel__slide) {
    @apply transform transition-all duration-500;
    opacity: 0.4;
    filter: blur(1px);
    scale: 0.95;
}

.gallery-carousel :deep(.carousel__slide--active) {
    @apply shadow-xl;
    opacity: 1;
    filter: blur(0);
    scale: 1;
}

.gallery-carousel :deep(.carousel__pagination) {
    @apply mt-8;
}

.gallery-carousel :deep(.carousel__pagination-button) {
    @apply w-3 h-3 rounded-full bg-gray-300 transition-all duration-300;
}

.gallery-carousel :deep(.carousel__pagination-button--active) {
    @apply bg-[var(--color-primary)] scale-125;
}

.gallery-carousel :deep(.carousel__prev),
.gallery-carousel :deep(.carousel__next) {
    @apply bg-white/90 backdrop-blur-sm rounded-full shadow-lg border border-gray-100
           transition-all duration-300 hover:scale-110;
    width: 40px;
    height: 40px;
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

@media (max-width: 768px) {
    .gallery-carousel :deep(.carousel__slide) {
        opacity: 0.8;
        filter: none;
        scale: 0.95;
    }

    .gallery-carousel :deep(.carousel__slide--active) {
        scale: 1;
    }

    .gallery-carousel :deep(.carousel__prev),
    .gallery-carousel :deep(.carousel__next) {
        width: 36px;
        height: 36px;
    }
}
</style>
