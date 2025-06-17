<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

const isOpen = ref(false);
const aboutDropdownOpen = ref(false);
const activitiesDropdownOpen = ref(false);

// Update mobile menu items structure
const mobileMenuItems = [
    { text: "Home", href: "/" },
    {
        text: "About",
        dropdown: true,
        items: [
            { text: "Lentera Restorasi", href: "/ruang-optima" },
            { text: "About Us", href: "/about" },
        ],
    },
    { text: "Department", href: "/department" },
    { text: "News", href: "/news" },
    {
        text: "Activities",
        dropdown: true,
        items: [
            { text: "Events", href: "/events" },
            { text: "Calendar Event", href: "/calendar" },
        ],
    },
];

// Add state for mobile dropdowns
const mobileDropdowns = ref({});

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

const toggleAboutDropdown = () => {
    aboutDropdownOpen.value = !aboutDropdownOpen.value;
    activitiesDropdownOpen.value = false; // Tutup dropdown lain
};

const toggleActivitiesDropdown = () => {
    activitiesDropdownOpen.value = !activitiesDropdownOpen.value;
    aboutDropdownOpen.value = false; // Tutup dropdown lain
};

const toggleMobileDropdown = (index) => {
    mobileDropdowns.value[index] = !mobileDropdowns.value[index];
};

// Tambahkan method untuk menutup semua dropdown
const closeAllDropdowns = () => {
    aboutDropdownOpen.value = false;
    activitiesDropdownOpen.value = false;
};
</script>

<template>
    <nav class="bg-white shadow-lg shadow-[var(--color-primary)]/5">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <Link href="/" class="flex-shrink-0">
                        <img
                            class="h-12 w-auto"
                            src="/path-to-your-logo.png"
                            alt="Logo"
                        />
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link
                        href="/"
                        class="text-[var(--text-color)] hover:text-[var(--color-primary)] px-3 py-2 rounded-md text-sm font-medium"
                    >
                        Home
                    </Link>

                    <!-- About Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleAboutDropdown"
                            class="text-[var(--text-color)] hover:text-[var(--color-primary)] px-3 py-2 rounded-md text-sm font-medium flex items-center"
                        >
                            About
                            <svg
                                class="ml-1 w-4 h-4"
                                :class="{
                                    'transform rotate-180': aboutDropdownOpen,
                                }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>
                        <div
                            v-show="aboutDropdownOpen"
                            class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-[var(--color-primary)]/20"
                        >
                            <div class="py-1">
                                <Link
                                    href="/ruang-optima"
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var(--color-background)] hover:text-[var(--color-primary)]"
                                >
                                    Lentera Restorasi
                                </Link>
                                <Link
                                    href="/about"
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var(--color-background)] hover:text-[var,--color-primary)]"
                                >
                                    About Us
                                </Link>
                            </div>
                        </div>
                    </div>

                    <Link
                        href="/department"
                        class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium"
                    >
                        Department
                    </Link>

                    <Link
                        href="/news"
                        class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium"
                    >
                        News
                    </Link>

                    <!-- Activities Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleActivitiesDropdown"
                            class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium flex items-center"
                        >
                            Activities
                            <svg
                                class="ml-1 w-4 h-4"
                                :class="{
                                    'transform rotate-180':
                                        activitiesDropdownOpen,
                                }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>
                        <div
                            v-show="activitiesDropdownOpen"
                            class="absolute z-50 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-[var(--color-primary)]/20"
                        >
                            <div class="py-1">
                                <Link
                                    href="/events"
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var(--color-background)] hover:text-[var,--color-primary)]"
                                >
                                    Events
                                </Link>
                                <Link
                                    href="/calendar"
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var,--color-background)] hover:text-[var,--color-primary)]"
                                >
                                    Calendar Event
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button
                        @click="toggleMenu"
                        class="inline-flex items-center justify-center p-2 rounded-md text-[var(--text-color)] hover:text-[var,--color-primary)] focus:outline-none"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                v-if="!isOpen"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                v-else
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div
            v-show="isOpen"
            class="md:hidden bg-white border-t border-[var(--color-primary)]/10"
        >
            <div class="px-2 pt-2 pb-3 space-y-1">
                <!-- Iterate through mobile menu items -->
                <div v-for="(item, index) in mobileMenuItems" :key="index">
                    <!-- Regular menu item -->
                    <template v-if="!item.dropdown">
                        <Link
                            :href="item.href"
                            class="block px-3 py-2 rounded-md text-base font-medium text-[var(--text-color)] hover:text-[var,--color-primary)] hover:bg-[var(--color-background)]"
                        >
                            {{ item.text }}
                        </Link>
                    </template>

                    <!-- Dropdown menu item -->
                    <template v-else>
                        <button
                            @click="toggleMobileDropdown(index)"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-md text-base font-medium text-[var(--text-color)] hover:text-[var,--color-primary)] hover:bg-[var(--color-background)]"
                        >
                            <span>{{ item.text }}</span>
                            <svg
                                class="w-4 h-4 ml-2"
                                :class="{
                                    'transform rotate-180':
                                        mobileDropdowns[index],
                                }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <!-- Dropdown content -->
                        <div
                            v-show="mobileDropdowns[index]"
                            class="pl-4 space-y-1"
                        >
                            <Link
                                v-for="(subItem, subIndex) in item.items"
                                :key="subIndex"
                                :href="subItem.href"
                                class="block px-3 py-2 rounded-md text-sm font-medium text-[var(--text-color)] hover:text-[var,--color-primary)] hover:bg-[var(--color-background)]"
                            >
                                {{ subItem.text }}
                            </Link>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>
