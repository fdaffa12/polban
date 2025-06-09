<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import {
    ChevronLeft,
    ChevronRight,
    XCircle,
    FileText,
    CalendarDays,
    ArrowLeft,
} from "lucide-vue-next";

const props = defineProps({
    event: Object,
});

const showImagePreview = ref(false);
const previewImage = ref(null);
const currentGalleryIndex = ref(0);
const galleryImages = ref([]);

const openImagePreview = (imagePath, images = null, index = 0) => {
    previewImage.value = `/storage/${imagePath}`;
    showImagePreview.value = true;
    if (images) {
        galleryImages.value = images;
        currentGalleryIndex.value = index;
    } else {
        galleryImages.value = [];
    }
};

const navigateGallery = (direction) => {
    if (galleryImages.value.length === 0) return;

    if (direction === "next") {
        currentGalleryIndex.value =
            (currentGalleryIndex.value + 1) % galleryImages.value.length;
    } else {
        currentGalleryIndex.value =
            currentGalleryIndex.value === 0
                ? galleryImages.value.length - 1
                : currentGalleryIndex.value - 1;
    }
    previewImage.value = `/storage/${
        galleryImages.value[currentGalleryIndex.value]
    }`;
};

const formatRupiah = (amount) => {
    const numericAmount = parseInt(amount);
    if (isNaN(numericAmount)) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(numericAmount);
};
</script>

<template>
    <AuthenticatedLayout :title="event.event_name">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Back Button -->
                    <div class="p-4 border-b">
                        <button
                            @click="router.visit(route('events.index'))"
                            class="inline-flex items-center text-gray-600 hover:text-gray-900"
                        >
                            <ArrowLeft class="w-5 h-5 mr-2" />
                            Back to Events
                        </button>
                    </div>

                    <!-- Event Content -->
                    <div class="p-6">
                        <!-- Header -->
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-2"
                        >
                            <h2 class="text-2xl font-semibold">
                                {{ event.event_name }}
                            </h2>
                            <span
                                :class="{
                                    'px-3 py-1.5 text-sm font-semibold rounded-full': true,
                                    'bg-yellow-100 text-yellow-800':
                                        event.status === 'coming_soon',
                                    'bg-green-100 text-green-800':
                                        event.status === 'running',
                                    'bg-red-100 text-red-800':
                                        event.status === 'closed',
                                }"
                            >
                                {{
                                    event.status.replace("_", " ").toUpperCase()
                                }}
                            </span>
                        </div>

                        <!-- Main Content -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Left Column -->
                            <div class="space-y-6">
                                <!-- Event Flyer -->
                                <div>
                                    <h3 class="text-lg font-medium mb-3">
                                        Event Flyer
                                    </h3>
                                    <img
                                        :src="`/storage/${event.event_flyer}`"
                                        :alt="event.event_name"
                                        class="w-full rounded-lg shadow-md cursor-pointer hover:opacity-90 transition-opacity"
                                        @click="
                                            openImagePreview(event.event_flyer)
                                        "
                                    />
                                </div>

                                <!-- Event Gallery -->
                                <div v-if="event.event_gallery?.length">
                                    <h3 class="text-lg font-medium mb-3">
                                        Event Gallery
                                    </h3>
                                    <div
                                        class="grid grid-cols-2 sm:grid-cols-3 gap-4"
                                    >
                                        <div
                                            v-for="(
                                                image, index
                                            ) in event.event_gallery"
                                            :key="index"
                                            class="relative aspect-square"
                                        >
                                            <img
                                                :src="`/storage/${image}`"
                                                :alt="`Gallery image ${
                                                    index + 1
                                                }`"
                                                class="w-full h-full object-cover rounded-lg shadow-sm cursor-pointer hover:opacity-75 transition-opacity"
                                                @click="
                                                    openImagePreview(
                                                        image,
                                                        event.event_gallery,
                                                        index
                                                    )
                                                "
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <!-- Quick Info Card -->
                                <div class="bg-gray-50 rounded-xl p-6">
                                    <div class="space-y-4">
                                        <div>
                                            <h3
                                                class="text-lg font-medium mb-2"
                                            >
                                                Department
                                            </h3>
                                            <p class="text-gray-600">
                                                {{ event.department.dept_name }}
                                            </p>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-lg font-medium mb-2"
                                            >
                                                Fee
                                            </h3>
                                            <p class="text-gray-600">
                                                {{
                                                    event.fee_type === "free"
                                                        ? "Free"
                                                        : formatRupiah(
                                                              event.fee_amount
                                                          )
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <h3
                                                class="text-lg font-medium mb-2"
                                            >
                                                Event Dates
                                            </h3>
                                            <div class="space-y-2">
                                                <div
                                                    v-for="date in event.dates"
                                                    :key="date.id"
                                                    class="flex items-center text-gray-600"
                                                >
                                                    <CalendarDays
                                                        class="w-5 h-5 mr-2"
                                                    />
                                                    {{
                                                        new Date(
                                                            date.event_date
                                                        ).toLocaleDateString()
                                                    }}
                                                    at {{ date.event_time }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Event Details -->
                                <div>
                                    <h3 class="text-lg font-medium mb-3">
                                        Event Details
                                    </h3>
                                    <div class="prose max-w-none">
                                        <div v-html="event.event_detail"></div>
                                    </div>
                                </div>

                                <!-- Document Section -->
                                <div v-if="event.event_doc">
                                    <h3 class="text-lg font-medium mb-3">
                                        Documents
                                    </h3>
                                    <a
                                        :href="`/storage/${event.event_doc}`"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        View Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <Modal
            :show="showImagePreview"
            @close="showImagePreview = false"
            max-width="6xl"
        >
            <div class="relative bg-gray-900">
                <!-- Close button -->
                <button
                    @click="showImagePreview = false"
                    class="absolute top-2 right-2 z-50 p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-75"
                >
                    <XCircle class="w-6 h-6" />
                </button>

                <!-- Navigation buttons -->
                <div
                    v-if="galleryImages.length > 1"
                    class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-4"
                >
                    <button
                        @click="navigateGallery('prev')"
                        class="p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-75"
                    >
                        <ChevronLeft class="w-6 h-6" />
                    </button>
                    <button
                        @click="navigateGallery('next')"
                        class="p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-75"
                    >
                        <ChevronRight class="w-6 h-6" />
                    </button>
                </div>

                <!-- Image -->
                <div class="flex items-center justify-center p-4">
                    <img
                        :src="previewImage"
                        class="max-w-full max-h-[85vh] w-auto h-auto object-contain"
                    />
                </div>

                <!-- Gallery counter -->
                <div
                    v-if="galleryImages.length > 1"
                    class="absolute bottom-4 left-0 right-0 text-center"
                >
                    <span
                        class="px-3 py-1 bg-gray-800 bg-opacity-50 rounded-full text-white text-sm"
                    >
                        {{ currentGalleryIndex + 1 }} /
                        {{ galleryImages.length }}
                    </span>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
