<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import { SidebarProvider, SidebarTrigger } from "@/Components/ui/sidebar";
import AppSidebar from "@/Components/AppSidebar.vue";
import { ChevronLeft } from "lucide-vue-next";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
});

const showingNavigationDropdown = ref(false);
const isCollapsed = ref(false);
const isMobileMenuOpen = ref(false);

const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Mobile Menu Overlay -->
        <div
            v-if="isMobileMenuOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
            @click="toggleMobileMenu"
        ></div>

        <div class="flex">
            <!-- Sidebar -->
            <div
                :class="{
                    'translate-x-0': isMobileMenuOpen,
                    '-translate-x-full': !isMobileMenuOpen,
                    'w-64': !isCollapsed,
                    'w-16': isCollapsed,
                }"
                class="fixed inset-y-0 left-0 z-50 bg-white transform transition-all duration-300 lg:relative lg:translate-x-0"
            >
                <SidebarProvider
                    :class="{
                        'w-64': !isCollapsed,
                        'w-16': isCollapsed,
                    }"
                    class="flex flex-col h-full transition-all duration-300"
                >
                    <!-- Sidebar Header with Logo -->
                    <div
                        class="h-16 border-b border-gray-200 bg-white flex items-center justify-between px-4"
                    >
                        <ApplicationLogo
                            class="w-8 h-8"
                            :class="{
                                'mx-auto': isCollapsed,
                            }"
                        />
                        <!-- Close button for mobile -->
                        <button
                            @click="toggleMobileMenu"
                            class="lg:hidden p-2 hover:bg-gray-100 rounded-lg"
                            v-if="!isCollapsed"
                        >
                            <span class="sr-only">Close menu</span>
                            <svg
                                class="w-6 h-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                    <AppSidebar :is-collapsed="isCollapsed" />
                </SidebarProvider>
            </div>

            <!-- Main Content Section -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Top Navbar -->
                <nav
                    class="h-16 bg-white border-b border-gray-200 sticky top-0 z-40"
                >
                    <div class="h-full px-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Mobile menu button -->
                            <button
                                @click="toggleMobileMenu"
                                class="lg:hidden p-2 rounded-lg hover:bg-gray-100"
                            >
                                <span class="sr-only">Open menu</span>
                                <svg
                                    class="w-6 h-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                </svg>
                            </button>

                            <!-- Desktop collapse button -->
                            <button
                                @click="toggleSidebar"
                                class="hidden lg:block p-2 rounded-lg hover:bg-gray-100"
                            >
                                <ChevronLeft
                                    :class="{ 'rotate-180': isCollapsed }"
                                    class="w-5 h-5 transition-transform duration-300"
                                />
                            </button>
                            <h1 class="text-xl font-semibold">{{ title }}</h1>
                        </div>

                        <!-- Right side of navbar -->
                        <div class="flex items-center gap-4">
                            <Dropdown>
                                <template #trigger>
                                    <button
                                        class="p-2 hover:bg-gray-100 rounded-full"
                                    >
                                        <span>User</span>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink href="/profile"
                                        >Profile</DropdownLink
                                    >
                                    <DropdownLink href="/logout" method="post"
                                        >Logout</DropdownLink
                                    >
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </nav>

                <!-- Page Content -->
                <main class="flex-1 p-4 overflow-auto">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
