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
    FileSpreadsheetIcon,
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

// Add window type declaration
declare const window: Window & typeof globalThis;

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface MenuItem {
    title: string;
    url?: string;
    icon: any;
    children?: MenuItem[];
}

const page = usePage();
const navTitle = computed(() => page.props.navTitle);
const user = computed(() => page.props.auth.user as User);

// Function to handle external links
const openExternalLink = (url: string | undefined) => {
    if (url) {
        const newWindow = window.open(url, "_blank", "noopener,noreferrer");
        if (newWindow) newWindow.opener = null;
    }
};

// Function to check if user has access to administration
const hasAdministrationAccess = (userRole: string) => {
    const allowedRoles = [
        "BPH",
        "SEKERTARIS_BENDAHARA",
        "SEKERTARIS_KABINET",
        "SEKERTARIS_UMUM_MPH",
    ];
    return allowedRoles.includes(userRole);
};

// Function to check if user has access to content management
const hasContentManagementAccess = (userRole: string) => {
    const allowedRoles = ["BPH", "MEDKOM"];
    return allowedRoles.includes(userRole);
};

// Function to check if user has access to SharePoint
const hasSharePointAccess = (userRole: string) => {
    const allowedRoles = ["BPH", "FUNGSIONARIS"];
    return allowedRoles.includes(userRole);
};

const hasUserManagementAccess = (userRole: string) => {
    const allowedRoles = ["BPH"];
    return allowedRoles.includes(userRole);
};

// Menu items with nested structure
const items = computed(() => {
    const menuItems: MenuItem[] = [
        {
            title: "Halaman Depan",
            url: route("home"),
            icon: Home,
        },
        {
            title: "Dashboard",
            url: route("dashboard"),
            icon: Home,
        },
    ];

    // Only add Administrasi menu if user has access
    if (hasAdministrationAccess(user.value?.role)) {
        menuItems.push({
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
        });
    }

    menuItems.push(
        {
            title: "Rapot HMJT",
            url: route("rapot-hmjt.index"),
            icon: FileText,
        },
        {
            title: "Konten Ekslusif",
            url: route("konten-ekslusif.index"),
            icon: FileText,
        }
    );

    // Only add content management menus if user has access
    if (hasContentManagementAccess(user.value?.role)) {
        menuItems.push(
            {
                title: "Articles",
                url: route("articles.index"),
                icon: Newspaper,
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
                title: "Settings",
                url: route("settings.index"),
                icon: Settings,
            }
        );
    }

    // Add SharePoint link if user has access
    if (hasSharePointAccess(user.value?.role)) {
        menuItems.push({
            title: "OKR",
            url: "https://polbanacid-my.sharepoint.com/:x:/g/personal/niken_anastasya_tkpb23_polban_ac_id/Ec_geVRT759Pr7Y9ZKaSC7EBsNoAhz0bgP3nIaZjNbY-eA?rtime=YFoJMbu23Ug",
            icon: FileSpreadsheetIcon,
        });
    }

    if (hasUserManagementAccess(user.value?.role)) {
        menuItems.push({
            title: "User Management",
            url: route("users.index"),
            icon: Users,
        });
    }

    return menuItems;
}).value;

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
                                    v-if="item.title === 'OKR'"
                                    @click="openExternalLink(item.url)"
                                    class="flex items-center gap-2 py-2 w-full text-left"
                                    :title="isCollapsed ? item.title : ''"
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
                                </button>
                                <button
                                    v-else
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
