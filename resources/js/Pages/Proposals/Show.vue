<script setup>
import { ref, onMounted } from "vue";
import { router, Link, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Eye,
    FileText,
    Calendar,
    MapPin,
    Users,
    DollarSign,
} from "lucide-vue-next";

import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const props = defineProps({
    proposal: Object,
    message: String,
    backUrl: String,
    isRestricted: Boolean,
    restrictedMessage: String,
});

// State untuk tab aktif
const activeTab = ref("doc_proposal");
const showApproveModal = ref(false);
const showReviseModal = ref(false);
const showPosterModal = ref(false);
const showUploadModal = ref(false);
const isUploading = ref(false);
const currentUploadData = ref(null);

// Inisialisasi toast
const toast = useToast();

const approveForm = useForm({
    approved_at: new Date().toISOString(),
});

const reviseForm = useForm({
    revision_note: "",
});
// Form untuk update dokumen
const updateDocForm = useForm({
    doc_proposal: null,
    doc_berkegiatan_ketuplak: null,
    doc_ormawa: null,
    doc_sarana_prasarana: null,
});

const handleApprove = () => {
    approveForm.put(route("proposals.approve", props.proposal.id), {
        onSuccess: () => {
            showApproveModal.value = false;
            toast.success("Proposal berhasil disetujui!", {
                timeout: 3000,
                position: "top-right",
                icon: true,
                closeButton: true,
            });
        },
        onError: () => {
            toast.error("Gagal menyetujui proposal. Silakan coba lagi.", {
                timeout: 3000,
                position: "top-right",
                icon: true,
                closeButton: true,
            });
        },
    });
};

const handleRevise = () => {
    reviseForm.put(route("proposals.revise", props.proposal.id), {
        onSuccess: () => {
            showReviseModal.value = false;
            reviseForm.reset();
            toast.success("Proposal telah dikirim untuk revisi!", {
                timeout: 3000,
                position: "top-right",
                icon: true,
                closeButton: true,
            });
        },
        onError: () => {
            toast.error("Gagal mengirim revisi. Silakan coba lagi.", {
                timeout: 3000,
                position: "top-right",
                icon: true,
                closeButton: true,
            });
        },
    });
};

// Format tanggal
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
};

// Format currency
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

// Daftar tab dokumen
const documentTabs = [
    { id: "details", name: "Detail Proposal", icon: FileText },
    {
        id: "doc_proposal",
        name: "Dokumen Proposal",
        path: props.proposal.doc_proposal,
    },
    {
        id: "doc_berkegiatan_ketuplak",
        name: "Dokumen Berkegiatan Ketuplak",
        path: props.proposal.doc_berkegiatan_ketuplak,
    },
    {
        id: "doc_ormawa",
        name: "Dokumen Ormawa",
        path: props.proposal.doc_ormawa,
    },
    {
        id: "doc_sarana_prasarana",
        name: "Dokumen Sarana Prasarana",
        path: props.proposal.doc_sarana_prasarana,
    },
];

// Status badge class
const getStatusClass = (status) => {
    switch (status) {
        case "pending":
            return "bg-yellow-100 text-yellow-800";
        case "approved":
            return "bg-green-100 text-green-800";
        case "revised":
            return "bg-orange-100 text-orange-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

// Fungsi untuk mendapatkan URL dokumen
const getDocumentUrl = (path) => {
    return `/storage/${path}`;
};

// Fungsi untuk mendapatkan URL Google Drive Viewer
const getGoogleViewerUrl = (url) => {
    const fileId = url.match(/[-\w]{25,}/);
    if (fileId) {
        // Gunakan format URL yang lebih aman
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

// Fungsi untuk mendapatkan file ID dari URL Google Drive
const getFileId = (url) => {
    const match = url.match(/[-\w]{25,}/);
    return match ? match[0] : "";
};

// Tambahkan di script setup
const handleIframeLoad = (event) => {
    // Clear console errors
    console.clear();
};

const handleIframeError = (error) => {
    console.clear();
    // Optional: Tampilkan pesan error yang lebih user-friendly
};
// Handle file upload
const handleFileUpload = (event, docType) => {
    const file = event.target.files[0];
    if (!file) return;

    currentUploadData.value = {
        file,
        docType,
        event,
    };
    showUploadModal.value = true;
};

const cancelUpload = () => {
    if (currentUploadData.value) {
        currentUploadData.value.event.target.value = "";
    }
    currentUploadData.value = null;
    showUploadModal.value = false;
};

const confirmUpload = () => {
    if (!currentUploadData.value) return;

    const { file, docType, event } = currentUploadData.value;
    const formData = new FormData();
    formData.append(docType, file);

    isUploading.value = true;

    router.post(route("proposals.update", props.proposal.id), formData, {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Dokumen berhasil diperbarui", {
                timeout: 3000,
                position: "top-right",
                icon: true,
                closeButton: true,
            });
            showUploadModal.value = false;
            isUploading.value = false;
            window.location.reload();
        },
        onError: (error) => {
            toast.error(error.message || "Gagal memperbarui dokumen", {
                timeout: 3000,
                position: "top-right",
                icon: true,
                closeButton: true,
            });
            event.target.value = "";
            showUploadModal.value = false;
            isUploading.value = false;
        },
    });
};
</script>

<template>
    <AuthenticatedLayout :title="'Proposal'">
        <Head>
            <title>Proposal Management</title>
            <meta name="description" content="Manage your proposal" />
        </Head>

        <!-- Tampilan Restricted Message -->
        <div v-if="isRestricted" class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <!-- Icon Restricted -->
                        <div class="mx-auto w-24 h-24 mb-6">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-full h-full text-red-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m0 0v2m0-2h2m-2 0H8m4-6V4m0 0v2m0-2h2m-2 0H8m16 0a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6z"
                                />
                            </svg>
                        </div>

                        <!-- Pesan Error -->
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            Akses Dibatasi
                        </h2>
                        <p class="text-gray-600 mb-8">
                            {{ restrictedMessage }}
                        </p>

                        <!-- Tombol Kembali -->
                        <Link
                            :href="route('proposals.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
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
                            Kembali ke Halaman Sebelumnya
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konten Normal -->
        <div v-else class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Tombol Kembali -->
                <div class="flex items-center justify-between">
                    <button
                        @click="router.get(route('proposals.index'))"
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

                <!-- Detail Proposal Section -->
                <div class="bg-white shadow-sm rounded-lg p-4 sm:p-6 lg:p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-900">
                        Detail Proposal
                    </h2>

                    <!-- Status Badge -->
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center space-x-4 w-full">
                            <!-- Status Timeline -->
                            <div
                                class="flex items-center justify-center w-full max-w-2xl mx-auto relative"
                            >
                                <!-- Garis Penghubung Background -->
                                <div
                                    class="absolute top-6 left-[10%] right-[10%] h-1 bg-gray-200 transform -translate-y-1/2"
                                ></div>

                                <!-- Progress Bar -->
                                <div
                                    class="absolute top-6 left-[10%] h-1 bg-blue-500 transform -translate-y-1/2 transition-all duration-500"
                                    :style="{
                                        width:
                                            proposal.view_by ===
                                            'SEKERTARIS_UMUM_MPH'
                                                ? '80%'
                                                : proposal.view_by ===
                                                  'SEKERTARIS_KABINET'
                                                ? '40%'
                                                : '0%',
                                    }"
                                ></div>

                                <!-- Timeline Items -->
                                <div
                                    class="flex justify-between w-full relative z-10"
                                >
                                    <!-- Submitted -->
                                    <div class="flex flex-col items-center">
                                        <div class="relative">
                                            <div
                                                class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center shadow-md"
                                            >
                                                <!-- Paper Airplane Icon -->
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-white"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                                    />
                                                </svg>
                                            </div>
                                            <!-- Check Badge -->
                                            <div
                                                class="absolute -top-1 -right-1 bg-green-500 rounded-full w-5 h-5 flex items-center justify-center shadow-sm"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3 w-3 text-white"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <span
                                            class="text-sm font-medium mt-2 text-gray-700"
                                            >Submitted</span
                                        >
                                    </div>

                                    <!-- Sekertaris Kabinet -->
                                    <div class="flex flex-col items-center">
                                        <div class="relative">
                                            <div
                                                :class="[
                                                    'w-12 h-12 rounded-full flex items-center justify-center shadow-md',
                                                    proposal.view_by ===
                                                        'SEKERTARIS_KABINET' ||
                                                    proposal.view_by ===
                                                        'SEKERTARIS_UMUM_MPH'
                                                        ? 'bg-yellow-500'
                                                        : 'bg-gray-300',
                                                ]"
                                            >
                                                <!-- Cabinet Icon -->
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-white"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                    />
                                                </svg>
                                            </div>
                                            <!-- Check Badge -->
                                            <div
                                                v-if="
                                                    proposal.view_by ===
                                                        'SEKERTARIS_KABINET' ||
                                                    proposal.view_by ===
                                                        'SEKERTARIS_UMUM_MPH'
                                                "
                                                class="absolute -top-1 -right-1 bg-green-500 rounded-full w-5 h-5 flex items-center justify-center shadow-sm"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3 w-3 text-white"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <span
                                            class="text-sm font-medium mt-2 text-gray-700"
                                            >Sekertaris Kabinet</span
                                        >
                                    </div>

                                    <!-- Sekertaris Umum MPH -->
                                    <div class="flex flex-col items-center">
                                        <div class="relative">
                                            <div
                                                :class="[
                                                    'w-12 h-12 rounded-full flex items-center justify-center shadow-md',
                                                    proposal.view_by ===
                                                    'SEKERTARIS_UMUM_MPH'
                                                        ? 'bg-green-500'
                                                        : 'bg-gray-300',
                                                ]"
                                            >
                                                <!-- Secretary Icon -->
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-6 w-6 text-white"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                                    />
                                                </svg>
                                            </div>
                                            <!-- Check Badge -->
                                            <div
                                                v-if="
                                                    proposal.view_by ===
                                                    'SEKERTARIS_UMUM_MPH'
                                                "
                                                class="absolute -top-1 -right-1 bg-green-500 rounded-full w-5 h-5 flex items-center justify-center shadow-sm"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-3 w-3 text-white"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <span
                                            class="text-sm font-medium mt-2 text-gray-700"
                                            >Sekertaris Umum</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kegiatan -->
                    <div class="bg-gray-50 rounded-xl p-4 sm:p-6 space-y-6">
                        <!-- Header Kegiatan -->
                        <div
                            class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    {{ proposal.nama_kegiatan }}
                                </h3>
                                <button
                                    @click="showPosterModal = true"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 text-sm font-medium transition-colors duration-200"
                                >
                                    <Eye class="h-4 w-4 mr-1.5" />
                                    Lihat Poster
                                </button>
                            </div>
                            <div
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100"
                            >
                                {{ proposal.bidang_kegiatan }} -
                                {{ proposal.jenis_kegiatan }}
                            </div>
                        </div>

                        <!-- Grid Informasi Detail -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6"
                        >
                            <!-- Penanggung Jawab -->
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                            >
                                <div class="flex items-start">
                                    <Users
                                        class="h-5 w-5 text-indigo-500 mt-1 mr-3"
                                    />
                                    <div class="flex-1">
                                        <label
                                            class="text-sm font-medium text-gray-700"
                                            >Penanggung Jawab</label
                                        >
                                        <input
                                            type="text"
                                            disabled
                                            :value="proposal.pic_name"
                                            class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm"
                                        />
                                        <p
                                            class="mt-2 text-sm text-indigo-600 font-medium"
                                        >
                                            {{ proposal.email }} |
                                            {{ proposal.phone }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Waktu Pelaksanaan -->
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                            >
                                <div class="flex items-start">
                                    <Calendar
                                        class="h-5 w-5 text-emerald-500 mt-1 mr-3"
                                    />
                                    <div class="flex-1">
                                        <label
                                            class="text-sm font-medium text-gray-700"
                                            >Waktu Pelaksanaan</label
                                        >
                                        <input
                                            type="text"
                                            disabled
                                            :value="
                                                formatDate(
                                                    proposal.tanggal_mulai
                                                ) +
                                                (proposal.tanggal_mulai !==
                                                proposal.tanggal_akhir
                                                    ? ' s/d ' +
                                                      formatDate(
                                                          proposal.tanggal_akhir
                                                      )
                                                    : '')
                                            "
                                            class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Tempat Kegiatan -->
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                            >
                                <div class="flex items-start">
                                    <MapPin
                                        class="h-5 w-5 text-rose-500 mt-1 mr-3"
                                    />
                                    <div class="flex-1">
                                        <label
                                            class="text-sm font-medium text-gray-700"
                                            >Tempat Kegiatan</label
                                        >
                                        <input
                                            type="text"
                                            disabled
                                            :value="proposal.tempat_kegiatan"
                                            class="mt-1 block w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 text-sm"
                                        />
                                    </div>
                                </div>
                            </div>

                            <!-- Peserta & Panitia -->
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                            >
                                <div class="flex items-start">
                                    <Users
                                        class="h-5 w-5 text-amber-500 mt-1 mr-3"
                                    />
                                    <div class="flex-1">
                                        <label
                                            class="text-sm font-medium text-gray-700"
                                            >Peserta & Panitia</label
                                        >
                                        <div
                                            class="mt-2 grid grid-cols-3 gap-3"
                                        >
                                            <div
                                                class="bg-amber-50 px-3 py-2.5 rounded-lg text-center shadow-sm"
                                            >
                                                <div
                                                    class="text-amber-700 font-semibold text-lg"
                                                >
                                                    {{
                                                        proposal.jumlah_peserta
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xs text-amber-600 font-medium"
                                                >
                                                    Peserta
                                                </div>
                                            </div>
                                            <div
                                                class="bg-amber-50 px-3 py-2.5 rounded-lg text-center shadow-sm"
                                            >
                                                <div
                                                    class="text-amber-700 font-semibold text-lg"
                                                >
                                                    {{
                                                        proposal.jumlah_panitia
                                                    }}
                                                </div>
                                                <div
                                                    class="text-xs text-amber-600 font-medium"
                                                >
                                                    Panitia
                                                </div>
                                            </div>
                                            <div
                                                class="bg-amber-50 px-3 py-2.5 rounded-lg text-center shadow-sm"
                                            >
                                                <div
                                                    class="text-amber-700 font-semibold text-lg"
                                                >
                                                    {{ proposal.jumlah_spj }}
                                                </div>
                                                <div
                                                    class="text-xs text-amber-600 font-medium"
                                                >
                                                    SPJ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pendanaan -->
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 md:col-span-2"
                            >
                                <div class="flex items-start">
                                    <DollarSign
                                        class="h-5 w-5 text-green-500 mt-1 mr-3"
                                    />
                                    <div class="flex-1">
                                        <label
                                            class="text-sm font-medium text-gray-700"
                                            >Pendanaan</label
                                        >
                                        <div
                                            class="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-3"
                                        >
                                            <div
                                                class="bg-green-50 p-4 rounded-lg shadow-sm"
                                            >
                                                <div
                                                    class="text-xs text-green-600 font-medium"
                                                >
                                                    DIPA
                                                </div>
                                                <div
                                                    class="text-green-700 font-semibold text-lg mt-1"
                                                >
                                                    {{
                                                        formatRupiah(
                                                            proposal.dana_dipa_polban
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                class="bg-blue-50 p-4 rounded-lg shadow-sm"
                                            >
                                                <div
                                                    class="text-xs text-blue-600 font-medium"
                                                >
                                                    Swadaya
                                                </div>
                                                <div
                                                    class="text-blue-700 font-semibold text-lg mt-1"
                                                >
                                                    {{
                                                        formatRupiah(
                                                            proposal.dana_swadaya
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                            <div
                                                class="bg-purple-50 p-4 rounded-lg shadow-sm"
                                            >
                                                <div
                                                    class="text-xs text-purple-600 font-medium"
                                                >
                                                    Sponsor
                                                </div>
                                                <div
                                                    class="text-purple-700 font-semibold text-lg mt-1"
                                                >
                                                    {{
                                                        formatRupiah(
                                                            proposal.dana_sponsor
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Approve/Revise -->
                <div
                    class="flex justify-end mt-6 gap-3"
                    v-if="
                        // Kondisi 1: Status pending, hanya SEKERTARIS_KABINET yang bisa lihat tombol
                        ($page.props.auth.user.role === 'SEKERTARIS_KABINET' &&
                            proposal.status === 'pending') ||
                        // Kondisi 2: Sudah approve by SEKERTARIS_KABINET, hanya SEKERTARIS_UMUM_MPH yang bisa lihat tombol
                        ($page.props.auth.user.role === 'SEKERTARIS_UMUM_MPH' &&
                            proposal.approver?.role === 'SEKERTARIS_KABINET' &&
                            proposal.status === 'approved') ||
                        // Kondisi 3: Jika SEKERTARIS_KABINET melakukan review, tombol tetap ada untuk SEKERTARIS_KABINET
                        ($page.props.auth.user.role === 'SEKERTARIS_KABINET' &&
                            proposal.reviewer?.role === 'SEKERTARIS_KABINET' &&
                            !proposal.approver) ||
                        // Kondisi 4: Sudah review by SEKERTARIS_UMUM_MPH, hanya SEKERTARIS_UMUM_MPH yang bisa lihat tombol
                        ($page.props.auth.user.role === 'SEKERTARIS_UMUM_MPH' &&
                            proposal.reviewer?.role === 'SEKERTARIS_UMUM_MPH' &&
                            proposal.approver?.role !== 'SEKERTARIS_UMUM_MPH')
                    "
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

                <!-- Tabel Status dan Review -->
                <div class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden">
                    <!-- Table view untuk desktop -->
                    <div class="hidden md:block">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Review By
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Review At
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Approve By
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Approve At
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Catatan Review
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                getStatusClass(proposal.status),
                                            ]"
                                        >
                                            {{ proposal.status.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            proposal.reviewer
                                                ? proposal.reviewer.role.replace(
                                                      /_/g,
                                                      " "
                                                  )
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            proposal.review_at
                                                ? formatDate(proposal.review_at)
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            proposal.approver
                                                ? proposal.approver.role.replace(
                                                      /_/g,
                                                      " "
                                                  )
                                                : "-"
                                        }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            proposal.approved_at
                                                ? formatDate(
                                                      proposal.approved_at
                                                  )
                                                : "-"
                                        }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div
                                            v-if="proposal.revision_note"
                                            class="bg-yellow-50 p-3 rounded-md"
                                        >
                                            {{ proposal.revision_note }}
                                        </div>
                                        <span v-else>-</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Card view untuk mobile -->
                    <div class="block md:hidden">
                        <div class="p-4 space-y-4">
                            <div
                                class="bg-white rounded-lg shadow-sm border border-gray-200"
                            >
                                <div class="p-4 space-y-3">
                                    <!-- Status -->
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Status</span
                                        >
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                getStatusClass(proposal.status),
                                            ]"
                                        >
                                            {{ proposal.status.toUpperCase() }}
                                        </span>
                                    </div>

                                    <!-- Review By -->
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Review By</span
                                        >
                                        <span class="text-sm text-gray-700">
                                            {{
                                                proposal.reviewer
                                                    ? proposal.reviewer.role.replace(
                                                          /_/g,
                                                          " "
                                                      )
                                                    : "-"
                                            }}
                                        </span>
                                    </div>

                                    <!-- Review At -->
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Review At</span
                                        >
                                        <span class="text-sm text-gray-700">
                                            {{
                                                proposal.review_at
                                                    ? formatDate(
                                                          proposal.review_at
                                                      )
                                                    : "-"
                                            }}
                                        </span>
                                    </div>

                                    <!-- Approve By -->
                                    <div
                                        class="flex items-center justify-between"
                                        v-if="proposal.status === 'approved'"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Approve By</span
                                        >
                                        <span class="text-sm text-gray-700">
                                            {{
                                                proposal.approver
                                                    ? proposal.approver.role.replace(
                                                          /_/g,
                                                          " "
                                                      )
                                                    : "-"
                                            }}
                                        </span>
                                    </div>

                                    <!-- Approve At -->
                                    <div
                                        class="flex items-center justify-between"
                                        v-if="proposal.status === 'approved'"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Approve At</span
                                        >
                                        <span class="text-sm text-gray-700">
                                            {{
                                                proposal.approved_at
                                                    ? formatDate(
                                                          proposal.approved_at
                                                      )
                                                    : "-"
                                            }}
                                        </span>
                                    </div>

                                    <!-- Catatan Review -->
                                    <div class="space-y-2">
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Catatan Review</span
                                        >
                                        <div
                                            v-if="proposal.revision_note"
                                            class="mt-2 bg-yellow-50 p-3 rounded-md text-sm text-gray-700"
                                        >
                                            {{ proposal.revision_note }}
                                        </div>
                                        <span
                                            v-else
                                            class="block text-sm text-gray-700"
                                            >-</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dokumen Section -->
                <div class="bg-white shadow-sm rounded-lg">
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
                                        v-for="tab in documentTabs.slice(1)"
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

                    <!-- PDF Preview -->
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
                                    <div class="space-x-2 flex items-center">
                                        <!-- Tombol Edit Dokumen (hanya muncul jika status revised) -->
                                        <div
                                            v-if="proposal.status === 'revised'"
                                            class="flex items-center space-x-2"
                                        >
                                            <input
                                                :id="'edit_' + activeTab"
                                                type="file"
                                                @input="
                                                    handleFileUpload(
                                                        $event,
                                                        activeTab
                                                    )
                                                "
                                                class="hidden"
                                                accept=".pdf"
                                            />
                                            <label
                                                :for="'edit_' + activeTab"
                                                class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 cursor-pointer"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 mr-2"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                                    />
                                                </svg>
                                                Upload File Baru
                                            </label>
                                        </div>
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
                                <p class="text-sm text-gray-600">
                                    Untuk menambahkan komentar pada PDF, silakan
                                    klik tombol "Buka di Google Drive"
                                </p>
                            </div>
                        </div>
                        <div class="pdf-wrapper">
                            <div
                                class="pdf-container bg-gray-100 rounded-lg overflow-hidden mx-auto"
                            >
                                <iframe
                                    v-if="activeTab !== 'details'"
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
    </AuthenticatedLayout>
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
                Apakah Anda yakin ingin menyetujui proposal ini?
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
            <h3 class="text-lg font-semibold text-gray-900">Revisi Proposal</h3>

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
                        reviseForm.processing ? "Memproses..." : "Kirim Revisi"
                    }}
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Poster -->
    <div
        v-if="showPosterModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div
            class="bg-white rounded-lg overflow-hidden shadow-lg max-w-md w-full"
        >
            <div class="flex justify-between items-center p-4 border-b">
                <h4 class="text-lg font-semibold">Poster Kegiatan</h4>
                <button
                    @click="showPosterModal = false"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
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
            <div class="p-4">
                <img
                    :src="getGoogleDriveImageUrl(proposal.poster)"
                    :alt="proposal.nama_kegiatan"
                    class="w-full rounded"
                />
            </div>
        </div>
    </div>
    <!-- Modal Upload File -->
    <div
        v-if="showUploadModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    >
        <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">
                Konfirmasi Upload File
            </h3>
            <p class="text-gray-600">
                Apakah Anda yakin ingin mengganti dokumen ini? File lama akan
                dihapus.
            </p>

            <div class="flex justify-end space-x-3 mt-4">
                <button
                    @click="cancelUpload"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                >
                    Batal
                </button>
                <button
                    @click="confirmUpload"
                    :disabled="isUploading"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ isUploading ? "Mengupload..." : "Upload" }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pdf-wrapper {
    width: 100%;
    max-width: 1024px;
    /* Maksimum lebar wrapper */
    margin: 0 auto;
    padding: 0 1rem;
}

.pdf-container {
    position: relative;
    width: 100%;
    max-width: 595px;
    /* Lebar maksimum A4 */
    margin: 1rem auto;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    background: white;
}

/* Mempertahankan rasio aspek A4 (1:1.4142) menggunakan padding-bottom */
.pdf-container::before {
    content: "";
    display: block;
    padding-bottom: 141.42%;
    /* Rasio aspek A4 */
}

.pdf-container iframe {
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
    /* Firefox */
    -ms-overflow-style: none;
    /* IE and Edge */
}

.overflow-x-auto::-webkit-scrollbar {
    display: none;
    /* Chrome, Safari, Opera */
}

/* Animasi transisi untuk hover dan active state */
.group {
    transition: all 0.2s ease-in-out;
}

/* Pastikan truncate bekerja dengan baik */
.truncate {
    text-overflow: ellipsis;
}

/* Tambahkan di bagian style yang sudah ada */
.absolute.w-full.h-1 {
    z-index: -1;
    transition: background-color 0.5s ease;
}

/* Animasi transisi untuk perubahan warna */
.w-12.h-12 {
    transition: all 0.3s ease-in-out;
    border: 2px solid transparent;
}

.w-12.h-12:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
        0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.flex.flex-col.items-center {
    transition: transform 0.2s ease-in-out;
}

.flex.flex-col.items-center:hover {
    transform: translateY(-2px);
}

/* Animasi untuk badge ceklis */
.absolute.-top-1.-right-1 {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.5);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Progress bar animation */
.bg-blue-500 {
    transition: width 0.5s ease-in-out;
}

/* Container styling */
.max-w-2xl {
    padding: 0 2rem;
}

/* Timeline item spacing */
.flex.justify-between {
    gap: 2rem;
}

/* Smooth transitions */
.w-12.h-12 {
    transition: all 0.3s ease-in-out;
}

/* Progress line styling */
.absolute.h-1 {
    transition: all 0.3s ease-in-out;
}

.bg-white {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
