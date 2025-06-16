<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <GuestLayout title="Home">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-b from-blue-50 to-white">
            <!-- Background Pattern -->
            <div class="absolute inset-0 z-0">
                <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
            </div>

            <div class="container-custom relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 min-h-[calc(100vh-80px)] items-center py-20">
                    <!-- Text Content -->
                    <div class="space-y-8 animate-fade-in">
                        <h3 class="text-blue-600 font-semibold text-xl md:text-2xl tracking-wide">
                            Building with Light
                        </h3>

                        <h1 class="heading-responsive font-bold text-gray-900 leading-tight">
                            Create a brighter future through
                            <span class="text-blue-600 block mt-2">
                                innovation, collaboration, and growth
                            </span>
                        </h1>

                        <p class="text-responsive text-gray-600 max-w-xl">
                            Join us in our mission to illuminate paths, foster innovation, and build sustainable
                            solutions for tomorrow's challenges.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <a href="#" class="btn-primary">
                                Get Started
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                            <a href="#" class="btn-secondary">
                                Learn More
                            </a>
                        </div>
                    </div>

                    <!-- Hero Image -->
                    <div class="relative h-[400px] lg:h-[600px] animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/10 to-purple-500/10 rounded-3xl">
                            <img src="https://images.unsplash.com/photo-1749731630653-d9b3f00573ed?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Innovation Illustration"
                                class="w-full h-full object-cover rounded-3xl shadow-2xl" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="container-custom">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-blue-600 font-semibold text-lg mb-4">Our Features</h2>
                    <h3 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                        Empowering Change Through Innovation
                    </h3>
                    <p class="text-responsive text-gray-600">
                        Discover how we're making a difference with cutting-edge solutions and collaborative approaches.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature Cards -->
                    <div v-for="(feature, index) in 3" :key="index" class="feature-card group">
                        <div class="h-48 mb-6 overflow-hidden rounded-xl">
                            <img src="https://images.unsplash.com/photo-1749731630653-d9b3f00573ed?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                alt="Feature Image"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" />
                        </div>
                        <h4 class="text-xl font-semibold text-gray-900 mb-3">
                            Feature Title
                        </h4>
                        <p class="text-gray-600">
                            Description of the feature and its benefits to users.
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
                        Join us today and be part of the innovation that shapes tomorrow.
                    </p>
                    <a href="#" class="btn-white">
                        Get Started Now
                    </a>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>
