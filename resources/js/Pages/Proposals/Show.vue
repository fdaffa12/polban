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
});

// State untuk tab aktif
const activeTab = ref("doc_proposal");
const showApproveModal = ref(false);
const showReviseModal = ref(false);
const showPosterModal = ref(false);

// Tambahkan state untuk halaman
const currentPage = ref("details"); // 'details' atau 'documents'

// Inisialisasi toast
const toast = useToast();

const approveForm = useForm({
    approved_at: new Date().toISOString(),
});

const reviseForm = useForm({
    revision_note: "",
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

// Fungsi untuk navigasi halaman
const navigateToPage = (page) => {
    currentPage.value = page;
};
</script>

<template>
    <AuthenticatedLayout :title="'Proposal'">
        <Head>
            <title>Proposal Management</title>
            <meta name="description" content="Manage your proposal" />
        </Head>
        <div class="py-12">
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

                <!-- Halaman Detail -->
                <div v-if="currentPage === 'details'">
                    <!-- Detail Proposal Section -->
                    <div
                        class="bg-white shadow-sm rounded-lg p-4 sm:p-6 lg:p-8"
                    >
                        <h2 class="text-2xl font-bold mb-6 text-gray-900">
                            Detail Proposal
                        </h2>

                        <!-- Status Badge -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center gap-4">
                                <span
                                    :class="[
                                        'px-4 py-2 text-sm font-semibold rounded-full shadow-sm',
                                        getStatusClass(proposal.status),
                                    ]"
                                >
                                    {{ proposal.status.toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <!-- Informasi Kegiatan -->
                        <div class="bg-gray-50 rounded-xl p-4 sm:p-6 space-y-6">
                            <!-- Header Kegiatan -->
                            <div
                                class="bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200"
                            >
                                <div
                                    class="flex items-center justify-between mb-3"
                                >
                                    <h3
                                        class="text-xl font-semibold text-gray-900"
                                    >
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
                                                :value="
                                                    proposal.tempat_kegiatan
                                                "
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
                                                        {{
                                                            proposal.jumlah_spj
                                                        }}
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

                    <!-- Tabel Status dan Review -->
                    <div
                        class="mt-6 bg-white shadow-sm rounded-lg overflow-hidden"
                    >
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
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="[
                                                    'px-2 py-1 text-xs font-semibold rounded-full',
                                                    getStatusClass(
                                                        proposal.status
                                                    ),
                                                ]"
                                            >
                                                {{
                                                    proposal.status.toUpperCase()
                                                }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                proposal.reviewer
                                                    ? proposal.reviewer.name
                                                    : "-"
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                proposal.review_at
                                                    ? formatDate(
                                                          proposal.review_at
                                                      )
                                                    : "-"
                                            }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{
                                                proposal.approver
                                                    ? proposal.approver.name
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
                                        <td
                                            class="px-6 py-4 text-sm text-gray-500"
                                        >
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
                                                    getStatusClass(
                                                        proposal.status
                                                    ),
                                                ]"
                                            >
                                                {{
                                                    proposal.status.toUpperCase()
                                                }}
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
                                                        ? proposal.reviewer.name
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
                                            v-if="
                                                proposal.status === 'approved'
                                            "
                                        >
                                            <span
                                                class="text-sm font-medium text-gray-500"
                                                >Approve By</span
                                            >
                                            <span class="text-sm text-gray-700">
                                                {{
                                                    proposal.approver
                                                        ? proposal.approver.name
                                                        : "-"
                                                }}
                                            </span>
                                        </div>

                                        <!-- Approve At -->
                                        <div
                                            class="flex items-center justify-between"
                                            v-if="
                                                proposal.status === 'approved'
                                            "
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

                    <!-- Tombol Next (diubah posisinya) -->
                    <div class="flex justify-center mt-6">
                        <button
                            @click="navigateToPage('documents')"
                            class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 hover:scale-105"
                        >
                            Lihat Dokumen
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 ml-2"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Halaman Dokumen -->
                <div v-if="currentPage === 'documents'">
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
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <h3 class="text-lg font-medium">
                                            {{
                                                documentTabs.find(
                                                    (tab) =>
                                                        tab.id === activeTab
                                                )?.name
                                            }}
                                        </h3>
                                        <div class="space-x-2">
                                            <button
                                                @click="
                                                    openDocument(
                                                        documentTabs.find(
                                                            (tab) =>
                                                                tab.id ===
                                                                activeTab
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
                                        Untuk menambahkan komentar pada PDF,
                                        silakan klik tombol "Buka di Google
                                        Drive"
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
                                                    (tab) =>
                                                        tab.id === activeTab
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

                    <!-- Tombol Back (diubah posisinya) -->
                    <div class="flex justify-center mt-6">
                        <button
                            @click="navigateToPage('details')"
                            class="inline-flex items-center px-5 py-2.5 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 hover:scale-105"
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
                            Kembali ke Detail
                        </button>
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

/* Tambahkan animasi transisi halaman */
.page-enter-active,
.page-leave-active {
    transition: opacity 0.3s ease;
}

.page-enter-from,
.page-leave-to {
    opacity: 0;
}
</style>
