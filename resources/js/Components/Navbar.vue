<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

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
            { text: "Events", href: "/our-event" },
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

// Add current route checking
const page = usePage();
const currentRoute = computed(() => page.url);

// Update the active route checking functions
const isActiveRoute = (path) => currentRoute.value === path;
const isActiveParentRoute = (paths) => {
    // Check if current route matches or starts with any of the paths
    return paths.some(
        (path) =>
            currentRoute.value === path ||
            currentRoute.value.startsWith(path + "/")
    );
};

// Add computed properties for each section
const isAboutActive = computed(() => {
    return isActiveParentRoute(["/ruang-optima", "/about"]);
});

const isActivitiesActive = computed(() => {
    return isActiveParentRoute(["/events", "/calendar"]);
});
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
                    <!-- Update Desktop Menu Links -->
                    <Link
                        href="/"
                        class="text-[var(--text-color)] hover:text-[var(--color-primary)] px-3 py-2 rounded-md text-sm font-medium"
                        :class="{
                            'text-[var(--color-primary)] font-semibold border-b-2 border-[var(--color-primary)]':
                                isActiveRoute('/'),
                        }"
                    >
                        Home
                    </Link>

                    <!-- About Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleAboutDropdown"
                            class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium flex items-center"
                            :class="{
                                'text-[var(--color-primary)] font-semibold':
                                    isAboutActive,
                                'border-b-2 border-[var(--color-primary)]':
                                    isAboutActive,
                            }"
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
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var(--color-background)] hover:text-[var,--color-primary)]"
                                    :class="{
                                        'text-[var(--color-primary)] bg-[var(--color-background)]':
                                            isActiveRoute('/ruang-optima'),
                                    }"
                                >
                                    Lentera Restorasi
                                </Link>
                                <Link
                                    href="/about"
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var(--color-background)] hover:text-[var,--color-primary)]"
                                    :class="{
                                        'text-[var(--color-primary)] bg-[var(--color-background)]':
                                            isActiveRoute('/about'),
                                    }"
                                >
                                    About Us
                                </Link>
                            </div>
                        </div>
                    </div>

                    <Link
                        href="/department"
                        class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium"
                        :class="{
                            'text-[var(--color-primary)] font-semibold border-b-2 border-[var(--color-primary)]':
                                isActiveRoute('/department'),
                        }"
                    >
                        Department
                    </Link>

                    <Link
                        href="/news"
                        class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium"
                        :class="{
                            'text-[var(--color-primary)] font-semibold border-b-2 border-[var(--color-primary)]':
                                isActiveRoute('/news'),
                        }"
                    >
                        News
                    </Link>

                    <!-- Activities Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleActivitiesDropdown"
                            class="text-[var(--text-color)] hover:text-[var,--color-primary)] px-3 py-2 rounded-md text-sm font-medium flex items-center"
                            :class="{
                                'text-[var(--color-primary)] font-semibold':
                                    isActivitiesActive,
                                'border-b-2 border-[var(--color-primary)]':
                                    isActivitiesActive,
                            }"
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
                                    href="/our-event"
                                    class="block px-4 py-2 text-sm text-[var(--text-color)] hover:bg-[var(--color-background)] hover:text-[var,--color-primary)]"
                                    :class="{
                                        'text-[var(--color-primary)] bg-[var(--color-background)]':
                                            isActiveRoute('/our-event'),
                                    }"
                                >
                                    Events
                                </Link>
                                <Link
                                    href="/calendar"
                                    class="block px-4 py-2 text-sm text-[var,--color-primary)] hover:bg-[var,--color-background)] hover:text-[var,--color-primary)]"
                                    :class="{
                                        'text-[var(--color-primary)] bg-[var(--color-background)]':
                                            isActiveRoute('/calendar'),
                                    }"
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
                            :class="{
                                'text-[var(--color-primary)] bg-[var(--color-background)]':
                                    isActiveRoute(item.href),
                            }"
                        >
                            {{ item.text }}
                        </Link>
                    </template>

                    <!-- Dropdown menu item -->
                    <template v-else>
                        <button
                            @click="toggleMobileDropdown(index)"
                            class="flex items-center justify-between w-full px-3 py-2 rounded-md text-base font-medium text-[var(--text-color)] hover:text-[var,--color-primary)] hover:bg-[var(--color-background)]"
                            :class="{
                                'text-[var(--color-primary)] bg-[var(--color-background)]':
                                    (item.text === 'About' && isAboutActive) ||
                                    (item.text === 'Activities' &&
                                        isActivitiesActive),
                            }"
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
