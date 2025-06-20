<script setup>
import { computed } from "vue";
import { usePage, Link } from "@inertiajs/vue3";
import {
    Facebook,
    Instagram,
    Twitter,
    Youtube,
    Linkedin,
    Github,
    Mail,
    Phone,
    MapPin,
    X,
} from "lucide-vue-next";

const page = usePage();
const settings = computed(() => page.props.settings);

// Mapping untuk icon media sosial
const socialIcons = {
    facebook: Facebook,
    instagram: Instagram,
    twitter: Twitter,
    youtube: Youtube,
    linkedin: Linkedin,
    github: Github,
    x: X, // Menambahkan icon X (Twitter)
};

// Function untuk mendapatkan icon berdasarkan nama media sosial
const getSocialIcon = (name) => {
    const lowercaseName = name.toLowerCase();
    return socialIcons[lowercaseName] || null;
};
</script>

<template>
    <footer class="bg-[var(--color-primary-dark)] text-white py-12">
        <div class="container-custom">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- About Us Section -->
                <div>
                    <h5 class="text-xl font-semibold mb-4">About Us</h5>
                    <p class="text-[var(--color-background)]/80">
                        {{ settings?.about_us_excerpt }}
                    </p>
                </div>

                <!-- Quick Links Section -->
                <div>
                    <h5 class="text-xl font-semibold mb-4">Quick Links</h5>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                href="/about"
                                class="text-[var(--color-background)]/80 hover:text-white"
                            >
                                About Us
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/news"
                                class="text-[var(--color-background)]/80 hover:text-white"
                            >
                                News
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/department"
                                class="text-[var(--color-background)]/80 hover:text-white"
                            >
                                Department
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/articles"
                                class="text-[var(--color-background)]/80 hover:text-white"
                            >
                                Articles
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Contact Section -->
                <div>
                    <h5 class="text-xl font-semibold mb-4">Contact</h5>
                    <ul class="space-y-2 text-[var(--color-background)]/80">
                        <li class="flex items-center gap-2">
                            <Mail class="w-4 h-4" />
                            {{ settings?.email }}
                        </li>
                        <li class="flex items-center gap-2">
                            <Phone class="w-4 h-4" />
                            {{ settings?.phone }}
                        </li>
                        <li class="flex items-start gap-2">
                            <MapPin class="w-4 h-4 mt-1" />
                            {{ settings?.address }}
                        </li>
                    </ul>
                </div>

                <!-- Social Media Section -->
                <div>
                    <h5 class="text-xl font-semibold mb-4">Follow Us</h5>
                    <div class="flex space-x-4">
                        <template v-if="settings?.social_media">
                            <a
                                v-for="social in settings.social_media"
                                :key="social.name"
                                :href="social.url"
                                :title="social.name"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-[var(--color-background)]/80 hover:text-white transition-colors duration-200"
                            >
                                <component
                                    :is="getSocialIcon(social.name)"
                                    class="w-6 h-6"
                                />
                                <span class="sr-only">{{ social.name }}</span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Copyright Section -->
            <div
                class="border-t border-[var(--color-primary)] mt-12 pt-8 text-center text-[var(--color-background)]/80"
            >
                <p>
                    &copy; {{ new Date().getFullYear() }} {{ settings?.nama }}.
                    All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
.container-custom {
    @apply max-w-7xl mx-auto px-4 sm:px-6 lg:px-8;
}
</style>
