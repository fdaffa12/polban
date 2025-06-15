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

// State untuk tab aktif
const activeTab = ref("doc_sptp");
const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref("");

// Daftar tab dokumen
const documentTabs = [
    {
        id: "doc_sptp",
        name: "Dokumen SPTP",
        path: props.spj.doc_sptp,
    },
    {
        id: "doc_spj",
        name: "Dokumen SPJ",
        path: props.spj.doc_spj,
    },
    {
        id: "doc_berita_acara",
        name: "Berita Acara",
        path: props.spj.doc_berita_acara,
    },
];

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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Back Button -->
                <div class="flex items-center justify-between">
                    <button
                        @click="
                            router.visit(route('proposals.spj', proposal.id))
                        "
                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Kembali
                    </button>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <!-- Media Preview Section -->
                    <div class="p-6 border-b">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bukti SPJ Image Card -->
                            <div class="bg-white rounded-lg border shadow-sm">
                                <div class="p-4 border-b">
                                    <h3 class="text-lg font-medium">
                                        Bukti SPJ
                                    </h3>
                                </div>
                                <div class="p-4">
                                    <div
                                        class="aspect-video bg-gray-100 rounded-lg overflow-hidden"
                                    >
                                        <img
                                            :src="
                                                getGoogleDriveImageUrl(
                                                    spj.gambar_bukti_spj
                                                )
                                            "
                                            alt="Bukti SPJ"
                                            class="w-full h-full object-contain cursor-pointer"
                                            @click="
                                                openDocument(
                                                    spj.gambar_bukti_spj
                                                )
                                            "
                                        />
                                    </div>
                                    <button
                                        @click="
                                            openDocument(spj.gambar_bukti_spj)
                                        "
                                        class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>

                            <!-- Video Card -->
                            <div
                                v-if="spj.video"
                                class="bg-white rounded-lg border shadow-sm"
                            >
                                <div class="p-4 border-b">
                                    <h3 class="text-lg font-medium">
                                        Video Dokumentasi
                                    </h3>
                                </div>
                                <div class="p-4">
                                    <div
                                        class="aspect-video bg-gray-100 rounded-lg overflow-hidden"
                                    >
                                        <iframe
                                            :src="getGoogleViewerUrl(spj.video)"
                                            class="w-full h-full"
                                            frameborder="0"
                                            allowfullscreen
                                            allow="autoplay"
                                        ></iframe>
                                    </div>
                                    <p
                                        v-if="spj.caption_video"
                                        class="mt-2 text-sm text-gray-600"
                                    >
                                        {{ spj.caption_video }}
                                    </p>
                                    <button
                                        @click="openDocument(spj.video)"
                                        class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                    >
                                        <FileText class="w-5 h-5 mr-2" />
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Section -->
                    <div class="border-b border-gray-200 overflow-x-auto">
                        <div class="min-w-full">
                            <nav
                                class="-mb-px flex px-4 sm:px-6"
                                aria-label="Tabs"
                            >
                                <div
                                    class="flex space-x-2 sm:space-x-4 md:space-x-8 whitespace-nowrap"
                                >
                                    <button
                                        v-for="tab in documentTabs"
                                        :key="tab.id"
                                        @click="activeTab = tab.id"
                                        :class="[
                                            activeTab === tab.id
                                                ? 'border-indigo-500 text-indigo-600'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                            'group inline-flex items-center py-3 sm:py-4 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm',
                                        ]"
                                    >
                                        <FileText
                                            class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2"
                                            :class="[
                                                activeTab === tab.id
                                                    ? 'text-indigo-500'
                                                    : 'text-gray-400 group-hover:text-gray-500',
                                            ]"
                                        />
                                        <span
                                            class="truncate max-w-[100px] sm:max-w-none"
                                            >{{ tab.name }}</span
                                        >
                                    </button>
                                </div>
                            </nav>
                        </div>
                    </div>

                    <!-- Document Preview -->
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex flex-col space-y-2">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-medium">
                                        {{
                                            documentTabs.find(
                                                (tab) => tab.id === activeTab
                                            )?.name
                                        }}
                                    </h3>
                                    <button
                                        @click="
                                            openDocument(
                                                documentTabs.find(
                                                    (tab) =>
                                                        tab.id === activeTab
                                                )?.path
                                            )
                                        "
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700"
                                    >
                                        Buka di Google Drive
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="pdf-wrapper">
                            <div
                                class="pdf-container bg-gray-100 rounded-lg overflow-hidden mx-auto"
                            >
                                <!-- Image Preview -->
                                <img
                                    v-if="
                                        documentTabs.find(
                                            (tab) => tab.id === activeTab
                                        )?.isImage
                                    "
                                    :src="
                                        getGoogleDriveImageUrl(
                                            documentTabs.find(
                                                (tab) => tab.id === activeTab
                                            )?.path
                                        )
                                    "
                                    :alt="
                                        documentTabs.find(
                                            (tab) => tab.id === activeTab
                                        )?.name
                                    "
                                    class="w-full h-full object-contain"
                                />
                                <!-- PDF Preview -->
                                <iframe
                                    v-else
                                    :src="
                                        getGoogleViewerUrl(
                                            documentTabs.find(
                                                (tab) => tab.id === activeTab
                                            )?.path
                                        )
                                    "
                                    class="w-full h-full"
                                    frameborder="0"
                                    allowfullscreen
                                    referrerpolicy="no-referrer"
                                    loading="lazy"
                                    allow="autoplay"
                                    :key="activeTab"
                                    @load="handleIframeLoad"
                                    @error="handleIframeError"
                                >
                                </iframe>
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
.pdf-wrapper {
    width: 100%;
    max-width: 1024px;
    margin: 0 auto;
    padding: 0 1rem;
}

.pdf-container {
    position: relative;
    width: 100%;
    max-width: 595px;
    margin: 1rem auto;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    background: white;
}

/* Mempertahankan rasio aspek A4 (1:1.4142) menggunakan padding-bottom */
.pdf-container::before {
    content: "";
    display: block;
    padding-bottom: 141.42%;
}

.pdf-container iframe,
.pdf-container img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

/* Tablet (md) */
@media (min-width: 768px) {
    .pdf-wrapper {
        padding: 0 2rem;
    }

    .pdf-container {
        margin: 2rem auto;
    }
}

/* Desktop (lg) */
@media (min-width: 1024px) {
    .pdf-wrapper {
        padding: 0;
    }

    .pdf-container {
        margin: 2rem auto;
    }
}

/* Untuk layar sangat kecil */
@media (max-width: 640px) {
    .pdf-wrapper {
        padding: 0 0.5rem;
    }

    .pdf-container {
        margin: 1rem auto;
    }
}

/* Tambahkan style untuk smooth scrolling di mobile */
.overflow-x-auto {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

/* Animasi transisi untuk hover dan active state */
.group {
    transition: all 0.2s ease-in-out;
}

/* Pastikan truncate bekerja dengan baik */
.truncate {
    text-overflow: ellipsis;
}

/* Add styles for media preview */
.aspect-video {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
}

.aspect-video img,
.aspect-video iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
}
</style>
