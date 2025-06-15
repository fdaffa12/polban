<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import { ArrowLeft, FileText, XCircle } from "lucide-vue-next";
import { useToast } from "vue-toastification";

const props = defineProps({
    spj: Object,
    proposal: Object,
});

const toast = useToast();

// State untuk tab aktif
const activeTab = ref("doc_sptp");
const showPreviewModal = ref(false);
const previewUrl = ref(null);
const previewTitle = ref("");

const showApproveModal = ref(false);
const showReviseModal = ref(false);

const approveForm = useForm({
    approved_at: new Date().toISOString(),
});

const reviseForm = useForm({
    revision_note: "",
});

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

// Tambahkan fungsi handle approve dan revise
const handleApprove = () => {
    approveForm.put(route("proposals.spj.approve", props.spj.id), {
        onSuccess: () => {
            showApproveModal.value = false;
            toast.success("SPJ berhasil disetujui!");
        },
        onError: () => {
            toast.error("Gagal menyetujui SPJ. Silakan coba lagi.");
        },
    });
};

const handleRevise = () => {
    reviseForm.put(route("proposals.spj.revise", props.spj.id), {
        onSuccess: () => {
            showReviseModal.value = false;
            reviseForm.reset();
            toast.success("SPJ telah dikirim untuk revisi!");
        },
        onError: () => {
            toast.error("Gagal mengirim revisi. Silakan coba lagi.");
        },
    });
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

                <!-- Tombol Approve/Revise -->
                <div
                    v-if="
                        $page.props.auth.user.role === 'SEKERTARIS_KABINET' &&
                        !spj.approved_at
                    "
                    class="flex justify-end mt-6 gap-3"
                >
                    <button
                        @click="showApproveModal = true"
                        class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 hover:shadow-lg"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Approve
                    </button>
                    <button
                        @click="showReviseModal = true"
                        class="inline-flex items-center px-5 py-2.5 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200 hover:shadow-lg"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                            />
                        </svg>
                        Revise
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

        <!-- Modal Approve -->
        <div
            v-if="showApproveModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Konfirmasi Persetujuan
                </h3>
                <p class="text-gray-600">
                    Apakah Anda yakin ingin menyetujui SPJ ini?
                </p>

                <div class="flex justify-end space-x-3 mt-4">
                    <button
                        @click="showApproveModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        Batal
                    </button>
                    <button
                        @click="handleApprove"
                        :disabled="approveForm.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ approveForm.processing ? "Memproses..." : "Setuju" }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Revise -->
        <div
            v-if="showReviseModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
        >
            <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Revisi SPJ</h3>

                <div class="space-y-2">
                    <label
                        for="revision_note"
                        class="block text-sm font-medium text-gray-700"
                        >Catatan Revisi</label
                    >
                    <textarea
                        id="revision_note"
                        v-model="reviseForm.revision_note"
                        rows="4"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"
                        placeholder="Masukkan catatan revisi..."
                    ></textarea>
                    <p
                        v-if="reviseForm.errors.revision_note"
                        class="mt-1 text-sm text-red-600"
                    >
                        {{ reviseForm.errors.revision_note }}
                    </p>
                </div>

                <div class="flex justify-end space-x-3 mt-4">
                    <button
                        @click="showReviseModal = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                    >
                        Batal
                    </button>
                    <button
                        @click="handleRevise"
                        :disabled="reviseForm.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{
                            reviseForm.processing
                                ? "Memproses..."
                                : "Kirim Revisi"
                        }}
                    </button>
                </div>
            </div>
        </div>
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
