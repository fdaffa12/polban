<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

defineProps({
    title: {
        type: String,
        required: true
    }
});
</script>

<template>

    <Head :title="title" />

    <div class="min-h-screen bg-white">
        <Navbar />

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="container-custom">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <h5 class="text-xl font-semibold mb-4">About Us</h5>
                        <p class="text-gray-400">
                            Brief description of your organization and its mission.
                        </p>
                    </div>
                    <div>
                        <h5 class="text-xl font-semibold mb-4">Quick Links</h5>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Home</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">About</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Services</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Contact</a></li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-xl font-semibold mb-4">Contact</h5>
                        <ul class="space-y-2 text-gray-400">
                            <li>Email: info@example.com</li>
                            <li>Phone: (123) 456-7890</li>
                            <li>Address: Your Address Here</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="text-xl font-semibold mb-4">Follow Us</h5>
                        <div class="flex space-x-4">
                            <!-- Add your social media icons here -->
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 Your Organization. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
