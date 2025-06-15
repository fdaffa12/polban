<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ArrowLeft, FileText, XCircle } from "lucide-vue-next";

const props = defineProps({
    spj: Object,
    proposal: Object,
});

const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref("");

// Fungsi untuk mendapatkan URL Google Drive Viewer
const getGoogleViewerUrl = (url) => {
    const fileId = url.match(/[-\w]{25,}/);
    if (fileId) {
        return `https://drive.google.com/file/d/${fileId[0]}/preview?embedded=true&rm=minimal`;
    }
    return url;
};

// Fungsi untuk membuka dokumen di Google Drive
const openDocument = (url) => {
    window.open(url, "_blank");
};

// Fungsi untuk mendapatkan URL gambar dari Google Drive
const getGoogleDriveImageUrl = (url) => {
    const fileId = url.match(/[-\w]{25,}/);
    if (fileId) {
        return `https://drive.google.com/thumbnail?id=${fileId[0]}&sz=w1000`;
    }
    return url;
};

const handleIframeLoad = (event) => {
    console.clear();
};

const handleIframeError = (error) => {
    console.clear();
};
</script>

<template>
    <AuthenticatedLayout :title="`SPJ Detail - ${proposal.nama_kegiatan}`">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Back Button -->
                    <div class="p-4 border-b">
                        <button
                            @click="
                                router.visit(
                                    route('proposals.spj.index', proposal.id)
                                )
                            "
                            class="inline-flex items-center text-gray-600 hover:text-gray-900"
                        >
                            <ArrowLeft class="w-5 h-5 mr-2" />
                            Back to SPJ List
                        </button>
                    </div>

                    <!-- SPJ Content -->
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-6">
                            SPJ Documents
                        </h2>

                        <!-- Documents Grid -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                        >
                            <!-- SPTP Document -->
                            <div
                                class="bg-white p-6 rounded-lg border shadow-sm"
                            >
                                <h3 class="text-lg font-medium mb-4">
                                    SPTP Document
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <iframe
                                            :src="
                                                getGoogleViewerUrl(spj.doc_sptp)
                                            "
                                            class="w-full h-64 rounded-lg"
                                            frameborder="0"
                                            allowfullscreen
                                            loading="lazy"
                                            @load="handleIframeLoad"
                                            @error="handleIframeError"
                                        ></iframe>
                                    </div>
                                    <button
                                        @click="openDocument(spj.doc_sptp)"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>

                            <!-- SPJ Document -->
                            <div
                                class="bg-white p-6 rounded-lg border shadow-sm"
                            >
                                <h3 class="text-lg font-medium mb-4">
                                    SPJ Document
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <iframe
                                            :src="
                                                getGoogleViewerUrl(spj.doc_spj)
                                            "
                                            class="w-full h-64 rounded-lg"
                                            frameborder="0"
                                            allowfullscreen
                                            loading="lazy"
                                            @load="handleIframeLoad"
                                            @error="handleIframeError"
                                        ></iframe>
                                    </div>
                                    <button
                                        @click="openDocument(spj.doc_spj)"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>

                            <!-- Berita Acara Document -->
                            <div
                                class="bg-white p-6 rounded-lg border shadow-sm"
                            >
                                <h3 class="text-lg font-medium mb-4">
                                    Berita Acara
                                </h3>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <iframe
                                            :src="
                                                getGoogleViewerUrl(
                                                    spj.doc_berita_acara
                                                )
                                            "
                                            class="w-full h-64 rounded-lg"
                                            frameborder="0"
                                            allowfullscreen
                                            loading="lazy"
                                            @load="handleIframeLoad"
                                            @error="handleIframeError"
                                        ></iframe>
                                    </div>
                                    <button
                                        @click="
                                            openDocument(spj.doc_berita_acara)
                                        "
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>

                            <!-- Bukti SPJ Image -->
                            <div
                                class="bg-white p-6 rounded-lg border shadow-sm"
                            >
                                <h3 class="text-lg font-medium mb-4">
                                    Bukti SPJ
                                </h3>
                                <div class="space-y-4">
                                    <img
                                        :src="
                                            getGoogleDriveImageUrl(
                                                spj.gambar_bukti_spj
                                            )
                                        "
                                        alt="Bukti SPJ"
                                        class="w-full h-64 object-cover rounded-lg shadow-sm cursor-pointer hover:opacity-90 transition-opacity"
                                        @click="
                                            openDocument(spj.gambar_bukti_spj)
                                        "
                                    />
                                    <button
                                        @click="
                                            openDocument(spj.gambar_bukti_spj)
                                        "
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>

                            <!-- Video Section -->
                            <div
                                v-if="spj.video"
                                class="bg-white p-6 rounded-lg border shadow-sm"
                            >
                                <h3 class="text-lg font-medium mb-4">
                                    Video Dokumentasi
                                </h3>
                                <div class="space-y-4">
                                    <video
                                        :src="spj.video"
                                        class="w-full rounded-lg"
                                        controls
                                    ></video>
                                    <p
                                        v-if="spj.caption_video"
                                        class="mt-2 text-sm text-gray-600"
                                    >
                                        {{ spj.caption_video }}
                                    </p>
                                    <button
                                        @click="openDocument(spj.video)"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <Modal
            :show="showPreviewModal"
            @close="showPreviewModal = false"
            max-width="6xl"
        >
            <div class="relative bg-white">
                <!-- Close button -->
                <button
                    @click="showPreviewModal = false"
                    class="absolute top-2 right-2 z-50 p-1 rounded-full text-gray-500 hover:text-gray-700"
                >
                    <XCircle class="w-6 h-6" />
                </button>

                <div class="p-4">
                    <h3 class="text-lg font-medium mb-4">{{ previewTitle }}</h3>

                    <!-- PDF Preview -->
                    <div
                        v-if="previewUrl && previewUrl.endsWith('.pdf')"
                        class="h-[85vh]"
                    >
                        <iframe
                            :src="previewUrl"
                            class="w-full h-full"
                            type="application/pdf"
                        ></iframe>
                    </div>

                    <!-- Image Preview -->
                    <div v-else-if="previewUrl" class="flex justify-center">
                        <img
                            :src="previewUrl"
                            :alt="previewTitle"
                            class="max-w-full max-h-[85vh] object-contain"
                        />
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.pdf-container iframe {
    width: 100%;
    height: 100%;
    border: none;
}

/* Animasi transisi */
.transition-opacity {
    transition: opacity 0.3s ease-in-out;
}

/* Hover effects */
.hover\:opacity-90:hover {
    opacity: 0.9;
}

.hover\:bg-blue-700:hover {
    background-color: rgb(29, 78, 216);
}

/* Shadow effects */
.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Responsive iframe container */
.iframe-container {
    position: relative;
    overflow: hidden;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
}

.iframe-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 0;
}
</style>
