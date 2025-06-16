<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    aboutUs: {
        type: Object,
        required: true,
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
                        <div
                            class="absolute -left-16 top-1/2 w-12 h-0.5 bg-gradient-to-r from-transparent to-blue-600"
                        ></div>
                        <div
                            class="absolute -right-16 top-1/2 w-12 h-0.5 bg-gradient-to-l from-transparent to-blue-600"
                        ></div>
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
                        <div
                            class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl opacity-20 blur-lg"
                        ></div>
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
                            <div
                                class="absolute -left-6 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-500 via-purple-500 to-pink-500 rounded-full"
                            ></div>

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

                        <Link
                            href="/about"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 shadow-lg"
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
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"
                                />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="container-custom">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-blue-600 font-semibold text-lg mb-4">
                        Our Features
                    </h2>
                    <h3
                        class="text-3xl md:text-4xl font-bold text-gray-900 mb-6"
                    >
                        Empowering Change Through Innovation
                    </h3>
                    <p class="text-responsive text-gray-600">
                        Discover how we're making a difference with cutting-edge
                        solutions and collaborative approaches.
                    </p>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
                >
                    <!-- Feature Cards -->
                    <div
                        v-for="(feature, index) in 3"
                        :key="index"
                        class="feature-card group"
                    >
                        <div class="h-48 mb-6 overflow-hidden rounded-xl">
                            <img
                                src="https://images.unsplash.com/photo-1749731630653-d9b3f00573ed?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Feature Image"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500"
                            />
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900 mb-3">
                            Feature Title
                        </h4>
                        <p class="text-gray-600">
                            Description of the feature and its benefits to
                            users.
                        </p>
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

<style>
.animate-blob {
    animation: blob 7s infinite;
}
.animation-delay-2000 {
    animation-delay: 2s;
}
.animation-delay-4000 {
    animation-delay: 4s;
}

@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}
</style>
