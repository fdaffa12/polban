<script setup lang="ts">
import {
    Calendar,
    Home,
    Inbox,
    Search,
    Settings,
    ChevronDown,
    Mail,
    Send,
    FileText,
    Users,
    Info,
    Newspaper,
    Building2,
    CalendarDays,
} from "lucide-vue-next";
// ... existing code ...
import {
    Sidebar,
    SidebarContent,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarHeader,
} from "@/Components/ui/sidebar";
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
} from "@/Components/ui/dropdown-menu";
import { computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";

interface MenuItem {
    title: string;
    url?: string;
    icon: any;
    children?: MenuItem[];
}

const navTitle = computed(() => page.props.navTitle);

// Menu items with nested structure
const items: MenuItem[] = [
    {
        title: "Dashboard",
        url: route("dashboard"),
        icon: Home, // Icon rumah untuk Dashboard
    },
    {
        title: "Proposals",
        url: route("proposals.index"),
        icon: FileText, // Icon dokumen untuk Proposals
    },
    {
        title: "Articles",
        url: route("articles.index"),
        icon: Newspaper, // Icon koran untuk Articles
    },
    {
        title: "About Us",
        url: route("about-us.index"),
        icon: Info, // Icon informasi untuk About Us
    },
    {
        title: "Himpunan Management",
        url: route("himpunan-management"),
        icon: Mail, // Icon surat untuk Himpunan Management
    },
    {
        title: "Departement",
        url: route("departments.index"),
        icon: Building2, // Icon gedung untuk Departement
    },
    {
        title: "Event",
        url: route("events.index"),
        icon: CalendarDays, // Icon kalender untuk Event
    },
    {
        title: "User Management",
        url: route("users.index"),
        icon: Users,
    },
    {
        title: "Settings",
        url: route("settings.index"),
        icon: Settings,
    },
];

const page = usePage();
const currentRoute = computed(() => page.url);

const isActive = (url: string) => {
    return currentRoute.value.startsWith(url);
};

defineProps<{
    isCollapsed: boolean;
}>();
</script>

<template>
    <Sidebar
        :class="{
            'w-64': !isCollapsed,
            'w-16': isCollapsed,
        }"
        class="flex-1 bg-white transition-all duration-300 overflow-y-auto"
        :collapsible="isCollapsed ? 'icon' : 'none'"
    >
        <SidebarHeader>
            <SidebarMenu>
                <!-- Only show dropdown when sidebar is expanded -->
                <SidebarMenuItem v-if="!isCollapsed">
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <SidebarMenuButton class="w-full">
                                <div
                                    class="flex items-center justify-between w-full px-2"
                                >
                                    <span class="truncate"
                                        >Select Workspace</span
                                    >
                                    <ChevronDown class="w-4 h-4" />
                                </div>
                            </SidebarMenuButton>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56">
                            <DropdownMenuItem class="cursor-pointer">
                                <span>Acme Inc</span>
                            </DropdownMenuItem>
                            <DropdownMenuItem class="cursor-pointer">
                                <span>Acme Corp.</span>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>
        <SidebarContent>
            <SidebarGroup>
                <SidebarGroupLabel
                    v-if="!isCollapsed"
                    class="px-2 py-2 text-sm font-semibold text-gray-500 uppercase"
                >
                    Application
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem
                            v-for="item in items"
                            :key="item.title"
                            :class="{
                                'px-2': true,
                                'bg-gray-100': isActive(item.url),
                            }"
                        >
                            <!-- For items with children (dropdown) -->
                            <template v-if="item.children">
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <SidebarMenuButton class="w-full">
                                            <div
                                                class="flex items-center justify-between w-full py-2"
                                            >
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <component
                                                        :is="item.icon"
                                                        class="w-5 h-5 text-gray-500"
                                                    />
                                                    <span
                                                        v-if="!isCollapsed"
                                                        class="text-sm text-gray-700"
                                                    >
                                                        {{ item.title }}
                                                    </span>
                                                </div>
                                                <ChevronDown
                                                    v-if="!isCollapsed"
                                                    class="w-4 h-4 text-gray-500"
                                                />
                                            </div>
                                        </SidebarMenuButton>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent
                                        :class="{
                                            'ml-2': !isCollapsed,
                                            'ml-16 lg:ml-16': isCollapsed,
                                            'ml-2 lg:ml-16': isCollapsed,
                                        }"
                                        class="w-56"
                                    >
                                        <DropdownMenuItem
                                            v-for="child in item.children"
                                            :key="child.title"
                                            class="cursor-pointer"
                                        >
                                            <a
                                                :href="child.url"
                                                class="flex items-center gap-2"
                                            >
                                                <component
                                                    :is="child.icon"
                                                    class="w-4 h-4 text-gray-500"
                                                />
                                                <span
                                                    class="text-sm text-gray-700"
                                                >
                                                    {{ child.title }}
                                                </span>
                                            </a>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </template>

                            <!-- For regular menu items -->
                            <SidebarMenuButton v-else>
                                <button
                                    @click="router.visit(item.url)"
                                    class="flex items-center gap-2 py-2 w-full text-left"
                                    :title="isCollapsed ? item.title : ''"
                                    :class="{
                                        'text-blue-600': isActive(item.url),
                                    }"
                                >
                                    <component
                                        :is="item.icon"
                                        :class="{
                                            'w-5 h-5': true,
                                            'text-blue-600': isActive(item.url),
                                            'text-gray-500': !isActive(
                                                item.url
                                            ),
                                        }"
                                    />
                                    <span
                                        v-if="!isCollapsed"
                                        :class="{
                                            'text-sm': true,
                                            'text-blue-600 font-medium':
                                                isActive(item.url),
                                            'text-gray-700': !isActive(
                                                item.url
                                            ),
                                        }"
                                    >
                                        {{ item.title }}
                                    </span>
                                </button>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>
    </Sidebar>
</template>
