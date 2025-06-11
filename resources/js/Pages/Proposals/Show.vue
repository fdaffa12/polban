<script setup>
import { ref, onMounted } from "vue";
import { router, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    Eye,
    FileText,
    Calendar,
    MapPin,
    Users,
    DollarSign,
} from "lucide-vue-next";

const props = defineProps({
    proposal: Object,
});

// State untuk tab aktif
const activeTab = ref("doc_proposal");

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
    window.open(url, '_blank');
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
    return match ? match[0] : '';
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

</script>

<template>
    <AuthenticatedLayout :title="'Preview: ' + proposal.nama_kegiatan">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Tombol Kembali -->
                <div class="flex items-center justify-between">
                    <button @click="router.get(route('proposals.index'))"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        Kembali
                    </button>
                </div>

                <!-- Detail Proposal Section -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-6">Detail Proposal</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Poster dan Status -->
                        <div class="space-y-4">
                            <div class="aspect-[3/4] rounded-lg shadow-lg overflow-hidden">
                                <img :src="getGoogleDriveImageUrl(proposal.poster)" :alt="proposal.nama_kegiatan"
                                    class="w-full h-full object-cover" />
                            </div>
                            <div class="flex justify-between items-center">
                                <span :class="[
                                    'px-4 py-2 text-sm font-semibold rounded-full',
                                    getStatusClass(proposal.status),
                                ]">
                                    {{ proposal.status.toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <!-- Informasi Kegiatan -->
                        <div class="bg-gray-50 rounded-lg p-6 space-y-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">
                                    {{ proposal.nama_kegiatan }}
                                </h3>
                                <p class="text-gray-600 mt-1">
                                    {{ proposal.bidang_kegiatan }} -
                                    {{ proposal.jenis_kegiatan }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-6">
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <Users class="h-5 w-5 text-gray-400 mt-1 mr-3" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Penanggung Jawab
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ proposal.pic_name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ proposal.email }} | {{ proposal.phone }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <Calendar class="h-5 w-5 text-gray-400 mt-1 mr-3" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Waktu Pelaksanaan
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ formatDate(proposal.tanggal_mulai) }}
                                                <span v-if="proposal.tanggal_mulai !== proposal.tanggal_akhir">
                                                    s/d {{ formatDate(proposal.tanggal_akhir) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <MapPin class="h-5 w-5 text-gray-400 mt-1 mr-3" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Tempat Kegiatan
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ proposal.tempat_kegiatan }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <Users class="h-5 w-5 text-gray-400 mt-1 mr-3" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Peserta & Panitia
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ proposal.jumlah_peserta }} Peserta |
                                                {{ proposal.jumlah_panitia }} Panitia |
                                                {{ proposal.jumlah_spj }} SPJ
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <DollarSign class="h-5 w-5 text-gray-400 mt-1 mr-3" />
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                Pendanaan
                                            </p>
                                            <div class="space-y-1">
                                                <p class="text-sm text-gray-600">
                                                    DIPA: {{ formatRupiah(proposal.dana_dipa_polban) }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Swadaya: {{ formatRupiah(proposal.dana_swadaya) }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Sponsor: {{ formatRupiah(proposal.dana_sponsor) }}
                                                </p>
                                            </div>
                                        </div>
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
                            <nav class="-mb-px flex px-4 sm:px-6" aria-label="Tabs">
                                <div class="flex space-x-2 sm:space-x-4 md:space-x-8 whitespace-nowrap">
                                    <button v-for="tab in documentTabs.slice(1)" :key="tab.id"
                                        @click="activeTab = tab.id" :class="[
                                            activeTab === tab.id
                                                ? 'border-indigo-500 text-indigo-600'
                                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                            'group inline-flex items-center py-3 sm:py-4 px-2 sm:px-3 border-b-2 font-medium text-xs sm:text-sm',
                                        ]">
                                        <FileText class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" :class="[
                                            activeTab === tab.id
                                                ? 'text-indigo-500'
                                                : 'text-gray-400 group-hover:text-gray-500',
                                        ]" />
                                        <span class="truncate max-w-[100px] sm:max-w-none">{{ tab.name }}</span>
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
                                        {{documentTabs.find((tab) => tab.id === activeTab)?.name}}
                                    </h3>
                                    <div class="space-x-2">
                                        <button
                                            @click="openDocument(documentTabs.find((tab) => tab.id === activeTab)?.path)"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                                            Buka di Google Drive
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600">
                                    Untuk menambahkan komentar pada PDF, silakan klik tombol "Buka di Google Drive"
                                </p>
                            </div>
                        </div>
                        <div class="pdf-wrapper">
                            <div class="pdf-container bg-gray-100 rounded-lg overflow-hidden mx-auto">
                                <iframe v-if="activeTab !== 'details'"
                                    :src="getGoogleViewerUrl(documentTabs.find((tab) => tab.id === activeTab)?.path)"
                                    class="w-full h-full" frameborder="0" allowfullscreen referrerpolicy="no-referrer"
                                    loading="lazy" allow="autoplay" :key="activeTab" @load="handleIframeLoad"
                                    @error="handleIframeError">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
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
</style>
