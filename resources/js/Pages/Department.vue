<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { ref, onMounted } from "vue";
import { Carousel, Slide, Navigation, Pagination } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";
import { useIntersectionObserver } from "@/composables/useIntersectionObserver";

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

// Helper function to determine layout type
const getLayoutType = (members) => {
    const count = members?.length || 0;
    if (count >= 3) return "carousel"; // Ubah dari > 3 menjadi >= 3
    if (count === 2) return "grid-2";
    if (count === 1) return "single";
    return "none";
};

useIntersectionObserver();
</script>

<template>
    <GuestLayout title="Department">
        <!-- Hero Section -->
        <section class="py-16 bg-white float-in-section">
            <div class="container-custom">
                <div
                    class="text-center max-w-4xl mx-auto mb-20 space-y-6 float-in-section delay-100"
                >
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border"
                        :style="{ borderColor: 'var(--color-secondary)' }"
                    >
                        <div
                            class="w-2 h-2 rounded-full animate-pulse"
                            :style="{ backgroundColor: 'var(--color-primary)' }"
                        ></div>
                        <span
                            class="font-semibold text-sm tracking-wide uppercase"
                            :style="{ color: 'var(--color-primary)' }"
                        >
                            Our Team
                        </span>
                    </div>
                    <h1
                        class="text-5xl md:text-6xl font-bold leading-tight"
                        :style="{ color: 'var(--text-color)' }"
                    >
                        Meet Our
                        <span
                            class="bg-gradient-to-r bg-clip-text text-transparent"
                            :style="{
                                background: `linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%)`,
                                WebkitBackgroundClip: 'text',
                                WebkitTextFillColor: 'transparent',
                            }"
                        >
                            Amazing Team
                        </span>
                        <span
                            class="block text-2xl md:text-3xl font-light mt-4"
                            :style="{ color: 'var(--light-text)' }"
                        >
                            HMJTK Polban
                        </span>
                    </h1>
                    <div class="flex items-center justify-center gap-2">
                        <div
                            class="w-12 h-1 rounded-full"
                            :style="{ backgroundColor: 'var(--color-primary)' }"
                        ></div>
                        <div
                            class="w-3 h-3 rounded-full"
                            :style="{ backgroundColor: 'var(--color-primary)' }"
                        ></div>
                        <div
                            class="w-12 h-1 rounded-full"
                            :style="{ backgroundColor: 'var(--color-primary)' }"
                        ></div>
                    </div>
                </div>

                <!-- Department Navigation -->
                <div
                    class="flex flex-wrap gap-3 justify-center max-w-4xl mx-auto float-in-section delay-200"
                >
                    <button
                        v-for="department in departments"
                        :key="department.id"
                        @click="toggleDepartment(department.id)"
                        class="group relative px-8 py-4 rounded-2xl transition-all duration-300 text-sm md:text-base font-semibold overflow-hidden"
                        :style="
                            activeDepartment === department.id
                                ? {
                                      backgroundColor: 'var(--color-primary)',
                                      color: 'white',
                                  }
                                : {
                                      backgroundColor: 'white',
                                      color: 'var(--color-primary)',
                                      border: '1px solid var(--color-secondary)',
                                  }
                        "
                    >
                        <span class="relative z-10">{{
                            department.dept_name
                        }}</span>
                        <div
                            v-if="activeDepartment !== department.id"
                            class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            :style="{
                                backgroundColor: 'var(--color-secondary)',
                                opacity: '0.1',
                            }"
                        ></div>
                    </button>
                </div>
            </div>
        </section>

        <!-- Members Section -->
        <section
            class="py-24 relative overflow-hidden"
            :style="{
                background: `linear-gradient(to bottom, white 0%, var(--color-background) 100%)`,
            }"
        >
            <!-- Gradient overlay at bottom -->
            <div
                class="absolute inset-x-0 bottom-0 h-32 pointer-events-none"
                :style="{
                    background: `linear-gradient(to top, var(--color-primary-dark), transparent)`,
                    opacity: '0.1',
                }"
            ></div>

            <div class="container-custom relative z-10">
                <div v-for="department in departments" :key="department.id">
                    <div
                        v-show="activeDepartment === department.id"
                        class="space-y-24 animate-fade-in float-in-section"
                    >
                        <!-- Board of Department Section -->
                        <div
                            v-if="department.board_members?.length"
                            class="space-y-12 float-in-section"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto space-y-6 float-in-section delay-100"
                            >
                                <div
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border"
                                    :style="{
                                        borderColor: 'var(--color-secondary)',
                                    }"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-primary)',
                                        }"
                                    ></div>
                                    <span
                                        class="font-semibold text-sm tracking-wide uppercase"
                                        :style="{
                                            color: 'var(--color-primary)',
                                        }"
                                    >
                                        Board of Department
                                    </span>
                                </div>
                                <h2
                                    class="text-4xl font-bold leading-tight"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    {{ department.dept_name }}
                                    <span
                                        class="block text-lg font-normal mt-2"
                                        :style="{ color: 'var(--light-text)' }"
                                    >
                                        Leadership Team
                                    </span>
                                </h2>
                            </div>

                            <!-- Dynamic Layout for Board Members -->
                            <div
                                class="relative max-w-7xl mx-auto float-in-section delay-200"
                            >
                                <!-- Carousel Layout (>3 members) -->
                                <div
                                    v-if="
                                        getLayoutType(
                                            department.board_members
                                        ) === 'carousel'
                                    "
                                >
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
                                            1024: {
                                                itemsToShow: 3,
                                                snapAlign: 'center',
                                            },
                                        }"
                                        class="modern-carousel"
                                    >
                                        <Slide
                                            v-for="member in department.board_members"
                                            :key="member.id"
                                            class="px-4"
                                        >
                                            <div class="member-card">
                                                <!-- Card Header -->
                                                <div class="card-header">
                                                    <h3 class="member-name">
                                                        {{ member.name }}
                                                    </h3>
                                                    <p class="member-position">
                                                        {{ member.position }}
                                                    </p>
                                                    <p class="member-job-title">
                                                        {{ member.job_titles }}
                                                    </p>
                                                </div>
                                                <!-- Card Body -->
                                                <div class="card-body">
                                                    <div
                                                        class="member-image-container"
                                                    >
                                                        <img
                                                            v-if="member.image"
                                                            :src="member.image"
                                                            :alt="member.name"
                                                            class="member-image"
                                                        />
                                                        <div
                                                            v-else
                                                            class="member-placeholder"
                                                        >
                                                            <span
                                                                class="placeholder-text"
                                                                >{{
                                                                    member
                                                                        .name[0]
                                                                }}</span
                                                            >
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

                                <!-- Grid Layout (2 members) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.board_members
                                        ) === 'grid-2'
                                    "
                                    class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto"
                                >
                                    <div
                                        v-for="member in department.board_members"
                                        :key="member.id"
                                        class="member-card"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Single Layout (1 member) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.board_members
                                        ) === 'single'
                                    "
                                    class="flex justify-center"
                                >
                                    <div
                                        v-for="member in department.board_members"
                                        :key="member.id"
                                        class="member-card max-w-sm"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vice Board of Department Section -->
                        <div
                            v-if="department.vice_board_members?.length"
                            class="space-y-12 float-in-section"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto space-y-6 float-in-section delay-100"
                            >
                                <div
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border"
                                    :style="{
                                        borderColor: 'var(--color-secondary)',
                                    }"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-primary)',
                                        }"
                                    ></div>
                                    <span
                                        class="font-semibold text-sm tracking-wide uppercase"
                                        :style="{
                                            color: 'var(--color-primary)',
                                        }"
                                    >
                                        Vice Board of Department
                                    </span>
                                </div>
                                <h2
                                    class="text-4xl font-bold leading-tight"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    {{ department.dept_name }}
                                    <span
                                        class="block text-lg font-normal mt-2"
                                        :style="{ color: 'var(--light-text)' }"
                                    >
                                        Vice Leadership Team
                                    </span>
                                </h2>
                            </div>

                            <!-- Dynamic Layout for Vice Board Members -->
                            <div
                                class="relative max-w-7xl mx-auto float-in-section delay-200"
                            >
                                <!-- Carousel Layout (>3 members) -->
                                <div
                                    v-if="
                                        getLayoutType(
                                            department.vice_board_members
                                        ) === 'carousel'
                                    "
                                >
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
                                            1024: {
                                                itemsToShow: 3,
                                                snapAlign: 'center',
                                            },
                                        }"
                                        class="modern-carousel"
                                    >
                                        <Slide
                                            v-for="member in department.vice_board_members"
                                            :key="member.id"
                                            class="px-4"
                                        >
                                            <div class="member-card">
                                                <!-- Card Header -->
                                                <div class="card-header">
                                                    <h3 class="member-name">
                                                        {{ member.name }}
                                                    </h3>
                                                    <p class="member-position">
                                                        {{ member.position }}
                                                    </p>
                                                    <p class="member-job-title">
                                                        {{ member.job_titles }}
                                                    </p>
                                                </div>
                                                <!-- Card Body -->
                                                <div class="card-body">
                                                    <div
                                                        class="member-image-container"
                                                    >
                                                        <img
                                                            v-if="member.image"
                                                            :src="member.image"
                                                            :alt="member.name"
                                                            class="member-image"
                                                        />
                                                        <div
                                                            v-else
                                                            class="member-placeholder"
                                                        >
                                                            <span
                                                                class="placeholder-text"
                                                                >{{
                                                                    member
                                                                        .name[0]
                                                                }}</span
                                                            >
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

                                <!-- Grid Layout (2 members) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.vice_board_members
                                        ) === 'grid-2'
                                    "
                                    class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto"
                                >
                                    <div
                                        v-for="member in department.vice_board_members"
                                        :key="member.id"
                                        class="member-card"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Single Layout (1 member) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.vice_board_members
                                        ) === 'single'
                                    "
                                    class="flex justify-center"
                                >
                                    <div
                                        v-for="member in department.vice_board_members"
                                        :key="member.id"
                                        class="member-card max-w-sm"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Head Section -->
                        <div
                            v-if="department.section_heads?.length"
                            class="space-y-12 float-in-section"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto space-y-6 float-in-section delay-100"
                            >
                                <div
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border"
                                    :style="{
                                        borderColor: 'var(--color-secondary)',
                                    }"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-primary)',
                                        }"
                                    ></div>
                                    <span
                                        class="font-semibold text-sm tracking-wide uppercase"
                                        :style="{
                                            color: 'var(--color-primary)',
                                        }"
                                    >
                                        Section Head
                                    </span>
                                </div>
                                <h2
                                    class="text-4xl font-bold leading-tight"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    Department Leaders
                                    <span
                                        class="block text-lg font-normal mt-2"
                                        :style="{ color: 'var(--light-text)' }"
                                    >
                                        Section Management
                                    </span>
                                </h2>
                            </div>

                            <!-- Dynamic Layout for Section Heads -->
                            <div
                                class="relative max-w-7xl mx-auto float-in-section delay-200"
                            >
                                <!-- Carousel Layout (>3 members) -->
                                <div
                                    v-if="
                                        getLayoutType(
                                            department.section_heads
                                        ) === 'carousel'
                                    "
                                >
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
                                            1024: {
                                                itemsToShow: 3,
                                                snapAlign: 'center',
                                            },
                                        }"
                                        class="modern-carousel"
                                    >
                                        <Slide
                                            v-for="member in department.section_heads"
                                            :key="member.id"
                                            class="px-4"
                                        >
                                            <div class="member-card">
                                                <!-- Card Header -->
                                                <div class="card-header">
                                                    <h3 class="member-name">
                                                        {{ member.name }}
                                                    </h3>
                                                    <p class="member-position">
                                                        {{ member.position }}
                                                    </p>
                                                    <p class="member-job-title">
                                                        {{ member.job_titles }}
                                                    </p>
                                                </div>
                                                <!-- Card Body -->
                                                <div class="card-body">
                                                    <div
                                                        class="member-image-container"
                                                    >
                                                        <img
                                                            v-if="member.image"
                                                            :src="member.image"
                                                            :alt="member.name"
                                                            class="member-image"
                                                        />
                                                        <div
                                                            v-else
                                                            class="member-placeholder"
                                                        >
                                                            <span
                                                                class="placeholder-text"
                                                                >{{
                                                                    member
                                                                        .name[0]
                                                                }}</span
                                                            >
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

                                <!-- Grid Layout (2 members) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.section_heads
                                        ) === 'grid-2'
                                    "
                                    class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto"
                                >
                                    <div
                                        v-for="member in department.section_heads"
                                        :key="member.id"
                                        class="member-card"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Single Layout (1 member) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.section_heads
                                        ) === 'single'
                                    "
                                    class="flex justify-center"
                                >
                                    <div
                                        v-for="member in department.section_heads"
                                        :key="member.id"
                                        class="member-card max-w-sm"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Section -->
                        <div
                            v-if="department.staff_members?.length"
                            class="space-y-12 float-in-section"
                        >
                            <div
                                class="text-center max-w-3xl mx-auto space-y-6 float-in-section delay-100"
                            >
                                <div
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border"
                                    :style="{
                                        borderColor: 'var(--color-secondary)',
                                    }"
                                >
                                    <div
                                        class="w-2 h-2 rounded-full"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-primary)',
                                        }"
                                    ></div>
                                    <span
                                        class="font-semibold text-sm tracking-wide uppercase"
                                        :style="{
                                            color: 'var(--color-primary)',
                                        }"
                                    >
                                        Staff Section
                                    </span>
                                </div>
                                <h2
                                    class="text-4xl font-bold leading-tight"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    Our Team
                                    <span
                                        class="block text-lg font-normal mt-2"
                                        :style="{ color: 'var(--light-text)' }"
                                    >
                                        Staff Members
                                    </span>
                                </h2>
                            </div>

                            <!-- Dynamic Layout for Staff Members -->
                            <div
                                class="relative max-w-7xl mx-auto float-in-section delay-200"
                            >
                                <!-- Carousel Layout (>3 members) -->
                                <div
                                    v-if="
                                        getLayoutType(
                                            department.staff_members
                                        ) === 'carousel'
                                    "
                                >
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
                                            1024: {
                                                itemsToShow: 3,
                                                snapAlign: 'center',
                                            },
                                        }"
                                        class="modern-carousel"
                                    >
                                        <Slide
                                            v-for="member in department.staff_members"
                                            :key="member.id"
                                            class="px-4"
                                        >
                                            <div class="member-card">
                                                <!-- Card Header -->
                                                <div class="card-header">
                                                    <h3 class="member-name">
                                                        {{ member.name }}
                                                    </h3>
                                                    <p class="member-position">
                                                        {{ member.position }}
                                                    </p>
                                                    <p class="member-job-title">
                                                        {{ member.job_titles }}
                                                    </p>
                                                </div>
                                                <!-- Card Body -->
                                                <div class="card-body">
                                                    <div
                                                        class="member-image-container"
                                                    >
                                                        <img
                                                            v-if="member.image"
                                                            :src="member.image"
                                                            :alt="member.name"
                                                            class="member-image"
                                                        />
                                                        <div
                                                            v-else
                                                            class="member-placeholder"
                                                        >
                                                            <span
                                                                class="placeholder-text"
                                                                >{{
                                                                    member
                                                                        .name[0]
                                                                }}</span
                                                            >
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

                                <!-- Grid Layout (2 members) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.staff_members
                                        ) === 'grid-2'
                                    "
                                    class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto"
                                >
                                    <div
                                        v-for="member in department.staff_members"
                                        :key="member.id"
                                        class="member-card"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Single Layout (1 member) -->
                                <div
                                    v-else-if="
                                        getLayoutType(
                                            department.staff_members
                                        ) === 'single'
                                    "
                                    class="flex justify-center"
                                >
                                    <div
                                        v-for="member in department.staff_members"
                                        :key="member.id"
                                        class="member-card max-w-sm"
                                    >
                                        <!-- Card Header -->
                                        <div class="card-header">
                                            <h3 class="member-name">
                                                {{ member.name }}
                                            </h3>
                                            <p class="member-position">
                                                {{ member.position }}
                                            </p>
                                            <p class="member-job-title">
                                                {{ member.job_titles }}
                                            </p>
                                        </div>
                                        <!-- Card Body -->
                                        <div class="card-body">
                                            <div class="member-image-container">
                                                <img
                                                    v-if="member.image"
                                                    :src="member.image"
                                                    :alt="member.name"
                                                    class="member-image"
                                                />
                                                <div
                                                    v-else
                                                    class="member-placeholder"
                                                >
                                                    <span
                                                        class="placeholder-text"
                                                        >{{
                                                            member.name[0]
                                                        }}</span
                                                    >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped>
/* Member Card Styles */
.member-card {
    @apply bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden;
    border: 1px solid #e5e7eb;
}

.member-card:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Card Header */
.card-header {
    @apply p-6 text-center;
    background-color: var(--color-primary);
    color: white;
}

.member-name {
    @apply text-xl font-bold mb-2 text-white;
}

.member-position {
    @apply font-semibold text-sm uppercase tracking-wide mb-1;
    color: rgba(255, 255, 255, 0.9);
}

.member-job-title {
    @apply text-sm leading-relaxed;
    color: rgba(255, 255, 255, 0.8);
}

/* Card Body */
.card-body {
    @apply p-6 bg-white;
}

.member-image-container {
    @apply relative aspect-square rounded-xl overflow-hidden bg-gray-100;
}

.member-image {
    @apply w-full h-full object-cover transition-transform duration-500 hover:scale-105;
}

.member-placeholder {
    @apply w-full h-full flex items-center justify-center bg-gray-100;
}

.placeholder-text {
    @apply text-4xl font-bold;
    color: var(--color-primary);
}

/* Modern Carousel Styles */
.modern-carousel :deep(.carousel__slide) {
    @apply transform transition-all duration-500;
    opacity: 0.8;
    scale: 0.95;
}

.modern-carousel :deep(.carousel__slide--active) {
    opacity: 1;
    scale: 1;
}

.modern-carousel :deep(.carousel__pagination) {
    @apply mt-12;
}

.modern-carousel :deep(.carousel__pagination-button) {
    @apply w-3 h-3 rounded-full bg-gray-300 transition-all duration-300 mx-1;
}

.modern-carousel :deep(.carousel__pagination-button--active) {
    background-color: var(--color-primary);
    @apply scale-125 shadow-lg;
}

.modern-carousel :deep(.carousel__prev),
.modern-carousel :deep(.carousel__next) {
    @apply bg-white backdrop-blur-sm rounded-full shadow-xl border border-gray-200
           transition-all duration-300 hover:scale-110 hover:shadow-2xl;
    width: 48px;
    height: 48px;
}

.modern-carousel :deep(.carousel__prev):hover,
.modern-carousel :deep(.carousel__next):hover {
    background-color: var(--color-primary);
    @apply text-white;
}

/* Animation */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out forwards;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modern-carousel :deep(.carousel__slide) {
        opacity: 0.9;
        scale: 0.98;
    }

    .modern-carousel :deep(.carousel__slide--active) {
        scale: 1;
    }

    .modern-carousel :deep(.carousel__prev),
    .modern-carousel :deep(.carousel__next) {
        width: 40px;
        height: 40px;
    }

    .member-card {
        @apply mx-2;
    }

    .card-header {
        @apply p-4;
    }

    .card-body {
        @apply p-4;
    }

    .member-name {
        @apply text-lg;
    }
}

@media (max-width: 480px) {
    .member-card {
        @apply mx-1;
    }

    .card-header {
        @apply p-3;
    }

    .card-body {
        @apply p-3;
    }
}

/* Tambahkan style untuk float-in animation */
:deep(.float-in-section) {
    will-change: transform, opacity;
}

:deep(.float-in-section.visible) {
    will-change: auto;
}

@keyframes floatIn {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.float-in-section {
    opacity: 0;
    transform: translateY(50px);
}

.float-in-section.visible {
    animation: floatIn 0.8s ease-out forwards;
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

.delay-300 {
    animation-delay: 0.3s;
}

/* Pastikan animasi berjalan smooth */
* {
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
}
</style>
