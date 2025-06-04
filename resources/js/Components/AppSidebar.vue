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
    FileText, // Import FileText from lucide-vue-next
} from "lucide-vue-next";
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
import { usePage } from "@inertiajs/vue3";

// Menu items with nested structure
const items = [
    {
        title: "Home",
        url: "#",
        icon: Home,
    },
    {
        title: "Mail",
        icon: Mail,
        children: [
            {
                title: "Inbox",
                url: "#",
                icon: Inbox,
            },
            {
                title: "Sent",
                url: "#",
                icon: Send,
            },
        ],
    },
    {
        title: "Calendar",
        url: "#",
        icon: Calendar,
    },
    {
        title: "Search",
        url: "#",
        icon: Settings,
    },
    {
        title: "Settings",
        url: "#",
        icon: Settings,
    },
    {
        title: "Articles",
        url: "/articles",
        icon: FileText, // Import FileText from lucide-vue-next
    },
    {
        title: "About Us",
        url: "/about-us",
        icon: FileText,
    },
    {
        title: "Lentera Restorasi",
        url: "/lentera-restorasi",
        icon: FileText,
    },
    {
        title: "Departement",
        url: "/departments",
        icon: FileText,
    },
    {
        title: "Event",
        url: "/events",
        icon: FileText,
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
                            <SidebarMenuButton v-else asChild>
                                <a
                                    :href="item.url"
                                    class="flex items-center gap-2 py-2"
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
                                </a>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>
    </Sidebar>
</template>
