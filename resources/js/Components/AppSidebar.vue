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
    ClipboardList,
    BookOpen,
    FileSpreadsheet,
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
        icon: Home,
    },
    {
        title: "Administrasi",
        icon: FileText,
        children: [
            {
                title: "Proposal",
                url: route("proposals.index"),
                icon: ClipboardList,
            },
            {
                title: "Notulensi",
                url: route("notulensi.index"),
                icon: FileSpreadsheet,
            },
            {
                title: "Format Administrasi",
                url: route("format-administrasi.index"),
                icon: FileText,
            },
            {
                title: "Buku Panduan",
                url: route("buku-panduan.index"),
                icon: BookOpen,
            },
        ],
    },
    {
        title: "Articles",
        url: route("articles.index"),
        icon: Newspaper,
    },
    {
        title: "Rapot HMJT",
        url: route("rapot-hmjt.index"),
        icon: FileText,
    },
    {
        title: "History",
        url: route("about-us.index"),
        icon: Info,
    },
    {
        title: "Himpunan Management",
        url: route("himpunan-management"),
        icon: Mail,
    },
    {
        title: "Departement",
        url: route("departments.index"),
        icon: Building2,
    },
    {
        title: "Event",
        url: route("events.index"),
        icon: CalendarDays,
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
                                            @click="router.visit(child.url)"
                                            class="cursor-pointer"
                                        >
                                            <div
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
                                            </div>
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
