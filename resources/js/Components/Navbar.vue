<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const isOpen = ref(false);
const aboutDropdownOpen = ref(false);
const activitiesDropdownOpen = ref(false);

// Tambahkan settings dari usePage
const page = usePage();
const settings = computed(() => page.props.settings);

// Update mobile menu items structure
const mobileMenuItems = [
    { text: "Home", href: "/" },
    {
        text: "About",
        dropdown: true,
        items: [
            { text: "Ruang Optima", href: "/ruang-optima" },
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
            { text: "Calendar Event", href: "/event-calendar" },
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

// Tambahkan computed property untuk auth
const auth = computed(() => usePage().props.auth);
</script>

<template>
    <nav class="sticky top-0 z-50">
        <!-- Gradient Background -->
        <div class="nav-gradient absolute inset-0"></div>

        <div class="relative max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-20">
                <!-- Logo Section -->
                <div class="flex items-center">
                    <Link href="/" class="flex-shrink-0 flex items-center">
                        <img
                            v-if="settings?.logo"
                            class="h-12 w-auto"
                            :src="`/storage/${settings.logo}`"
                            :alt="settings?.nama || 'Logo'"
                        />
                    </Link>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <!-- Menu Items -->
                    <Link
                        href="/"
                        class="nav-link"
                        :class="{ 'nav-link-active': isActiveRoute('/') }"
                    >
                        Home
                    </Link>

                    <!-- About Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleAboutDropdown"
                            class="nav-link flex items-center"
                            :class="{ 'nav-link-active': isAboutActive }"
                        >
                            About
                            <svg
                                class="ml-1 w-4 h-4 transition-transform duration-200"
                                :class="{ 'rotate-180': aboutDropdownOpen }"
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
                        <!-- Dropdown Menu -->
                        <div v-show="aboutDropdownOpen" class="dropdown-menu">
                            <div class="py-1">
                                <Link
                                    href="/ruang-optima"
                                    class="dropdown-item"
                                    :class="{
                                        'dropdown-item-active':
                                            isActiveRoute('/ruang-optima'),
                                    }"
                                >
                                    Ruang Optima
                                </Link>
                                <Link
                                    href="/about"
                                    class="dropdown-item"
                                    :class="{
                                        'dropdown-item-active':
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
                        class="nav-link"
                        :class="{
                            'nav-link-active': isActiveRoute('/department'),
                        }"
                    >
                        Department
                    </Link>

                    <Link
                        href="/news"
                        class="nav-link"
                        :class="{ 'nav-link-active': isActiveRoute('/news') }"
                    >
                        News
                    </Link>

                    <!-- Activities Dropdown -->
                    <div class="relative">
                        <button
                            @click="toggleActivitiesDropdown"
                            class="nav-link flex items-center"
                            :class="{ 'nav-link-active': isActivitiesActive }"
                        >
                            Activities
                            <svg
                                class="ml-1 w-4 h-4 transition-transform duration-200"
                                :class="{
                                    'rotate-180': activitiesDropdownOpen,
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
                        <!-- Dropdown Menu -->
                        <div
                            v-show="activitiesDropdownOpen"
                            class="dropdown-menu"
                        >
                            <div class="py-1">
                                <Link
                                    href="/our-event"
                                    class="dropdown-item"
                                    :class="{
                                        'dropdown-item-active':
                                            isActiveRoute('/our-event'),
                                    }"
                                >
                                    Events
                                </Link>
                                <Link
                                    href="/event-calendar"
                                    class="dropdown-item"
                                    :class="{
                                        'dropdown-item-active':
                                            isActiveRoute('/event-calendar'),
                                    }"
                                >
                                    Calendar Event
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-4">
                        <template v-if="auth?.user">
                            <Link
                                href="/dashboard"
                                class="nav-button-secondary"
                            >
                                Dashboard
                            </Link>
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="nav-button-primary"
                            >
                                Logout
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="nav-button-secondary"
                            >
                                Login
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="toggleMenu" class="mobile-menu-button">
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
        <div v-show="isOpen" class="mobile-menu">
            <div class="px-4 py-2 space-y-2">
                <!-- Iterate through mobile menu items -->
                <div
                    v-for="(item, index) in mobileMenuItems"
                    :key="index"
                    class="w-full"
                >
                    <!-- Regular menu item -->
                    <template v-if="!item.dropdown">
                        <Link
                            :href="item.href"
                            class="mobile-menu-item"
                            :class="{
                                'mobile-menu-item-active': isActiveRoute(
                                    item.href
                                ),
                            }"
                        >
                            {{ item.text }}
                        </Link>
                    </template>

                    <!-- Dropdown menu item -->
                    <template v-else>
                        <div class="w-full">
                            <button
                                @click="toggleMobileDropdown(index)"
                                class="mobile-menu-item w-full flex items-center justify-between"
                                :class="{
                                    'mobile-menu-item-active':
                                        (item.text === 'About' &&
                                            isAboutActive) ||
                                        (item.text === 'Activities' &&
                                            isActivitiesActive),
                                }"
                            >
                                <span class="flex-1 text-left">{{
                                    item.text
                                }}</span>
                                <svg
                                    class="w-4 h-4 ml-2 flex-shrink-0 transition-transform duration-200"
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
                                class="mobile-dropdown"
                            >
                                <Link
                                    v-for="(subItem, subIndex) in item.items"
                                    :key="subIndex"
                                    :href="subItem.href"
                                    class="mobile-submenu-item"
                                >
                                    {{ subItem.text }}
                                </Link>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Mobile Auth Buttons -->
            <div
                class="px-4 py-4 mt-2 border-t border-[var(--color-secondary)]"
            >
                <template v-if="auth?.user">
                    <Link href="/dashboard" class="mobile-button">
                        Dashboard
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="mobile-button mt-3"
                    >
                        Logout
                    </Link>
                </template>
                <template v-else>
                    <Link :href="route('login')" class="mobile-button">
                        Login
                    </Link>
                </template>
            </div>
        </div>
    </nav>
</template>

<style scoped>
.nav-gradient {
    background: linear-gradient(
        to right,
        var(--color-primary-dark) 0%,
        var(--color-primary) 100%
    );
}

/* Hapus backdrop-blur dan opacity yang mengurangi ketajaman navbar */
nav {
    background-color: transparent;
}

.nav-link {
    @apply px-3 py-2 rounded-md text-sm font-medium transition-all duration-200;
    color: var(--color-background);
}

.nav-link:hover {
    color: var(--color-secondary);
}

.nav-link-active {
    @apply font-semibold border-b-2;
    color: var(--color-secondary);
    border-color: var(--color-secondary);
}

.dropdown-menu {
    @apply absolute z-50 mt-2 w-48 rounded-lg shadow-lg ring-1 ring-black/5 transform transition-all duration-200 origin-top;
    background-color: var(--color-primary-dark);
}

.dropdown-item {
    @apply block px-4 py-2 text-sm transition-colors duration-200;
    color: var(--color-background);
}

.dropdown-item:hover {
    background-color: var(--color-primary);
    color: var(--color-secondary);
}

.dropdown-item-active {
    color: var(--color-secondary);
    background-color: var(--color-primary);
}

.nav-button-primary {
    @apply px-4 py-2 rounded-lg text-white transition-all duration-200 transform hover:scale-105;
    background-color: var(--color-secondary);
}

.nav-button-primary:hover {
    background-color: var(--color-primary);
    @apply shadow-md;
}

.nav-button-secondary {
    @apply px-4 py-2 rounded-lg transition-all duration-200 transform hover:scale-105;
    background-color: var(--color-background);
    color: var(--color-primary-dark);
    border: 2px solid var(--color-background);
}

.nav-button-secondary:hover {
    background-color: transparent;
    color: var(--color-background);
    @apply shadow-md;
}

.mobile-menu-button {
    @apply inline-flex items-center justify-center p-2 rounded-md transition-colors duration-200;
    color: var(--color-background);
}

.mobile-menu-button:hover {
    color: var(--color-secondary);
}

.mobile-menu {
    @apply md:hidden fixed top-20 left-0 right-0;
    background-color: var(--color-primary-dark);
    border-top: 1px solid var(--color-secondary);
    animation: mobileMenuFadeIn 0.3s ease-out;
    max-height: calc(100vh - 5rem);
    overflow-y: auto;
}

.mobile-menu-item {
    @apply block w-full px-4 py-3 rounded-lg text-base font-medium transition-colors duration-200;
    color: var(--color-background);
    display: flex;
    align-items: center;
}

.mobile-menu-item:hover {
    color: var(--color-secondary);
    background-color: var(--color-primary);
}

.mobile-menu-item-active {
    color: var(--color-secondary);
    background-color: var(--color-primary);
}

.mobile-dropdown {
    @apply mt-1 space-y-1 pl-4;
    animation: dropdownFadeIn 0.2s ease-out;
}

.mobile-submenu-item {
    @apply block w-full px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200;
    color: var(--color-background);
}

.mobile-submenu-item:hover {
    color: var(--color-secondary);
    background-color: var(--color-primary);
}

.mobile-button {
    @apply block w-full text-center px-4 py-3 rounded-lg transition-colors duration-200;
    background-color: var(--color-background);
    color: var(--color-primary-dark);
    border: 2px solid var(--color-background);
}

.mobile-button:hover {
    background-color: transparent;
    color: var(--color-background);
}

@keyframes mobileMenuFadeIn {
    from {
        opacity: 0;
        transform: translateY(-1rem);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
