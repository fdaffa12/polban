<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
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
const activeTab = ref("details");

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

// Fungsi untuk membuka dokumen di tab baru
const openDocument = (path) => {
    window.open(getDocumentUrl(path), "_blank");
};
</script>

<template>
    <AuthenticatedLayout :title="'Preview: ' + proposal.nama_kegiatan">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Navigation Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button
                            v-for="tab in documentTabs"
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[
                                activeTab === tab.id
                                    ? 'border-indigo-500 text-indigo-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm',
                            ]"
                        >
                            <component
                                :is="tab.icon"
                                class="h-5 w-5 mr-2"
                                :class="[
                                    activeTab === tab.id
                                        ? 'text-indigo-500'
                                        : 'text-gray-400 group-hover:text-gray-500',
                                ]"
                            />
                            {{ tab.name }}
                        </button>
                    </nav>
                </div>

                <!-- Content Area -->
                <div class="bg-white shadow-sm rounded-lg">
                    <!-- Detail Proposal Tab -->
                    <div v-if="activeTab === 'details'" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Poster dan Status -->
                            <div class="space-y-4">
                                <img
                                    :src="`/storage/${proposal.poster}`"
                                    :alt="proposal.nama_kegiatan"
                                    class="w-full rounded-lg shadow-lg"
                                />
                                <div class="flex justify-between items-center">
                                    <span
                                        :class="[
                                            'px-3 py-1 text-sm font-semibold rounded-full',
                                            getStatusClass(proposal.status),
                                        ]"
                                    >
                                        {{ proposal.status.toUpperCase() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Informasi Kegiatan -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-lg font-semibold">
                                        {{ proposal.nama_kegiatan }}
                                    </h3>
                                    <p class="text-gray-600">
                                        {{ proposal.bidang_kegiatan }} -
                                        {{ proposal.jenis_kegiatan }}
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <Users
                                            class="h-5 w-5 text-gray-400 mr-2"
                                        />
                                        <div>
                                            <p class="text-sm font-medium">
                                                PIC: {{ proposal.pic_name }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ proposal.email }} |
                                                {{ proposal.phone }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <Calendar
                                            class="h-5 w-5 text-gray-400 mr-2"
                                        />
                                        <div>
                                            <p class="text-sm">
                                                {{
                                                    formatDate(
                                                        proposal.tanggal_mulai
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="text-sm text-gray-500"
                                                v-if="
                                                    proposal.tanggal_mulai !==
                                                    proposal.tanggal_akhir
                                                "
                                            >
                                                s/d
                                                {{
                                                    formatDate(
                                                        proposal.tanggal_akhir
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center">
                                        <MapPin
                                            class="h-5 w-5 text-gray-400 mr-2"
                                        />
                                        <p class="text-sm">
                                            {{ proposal.tempat_kegiatan }}
                                        </p>
                                    </div>

                                    <div class="flex items-center">
                                        <DollarSign
                                            class="h-5 w-5 text-gray-400 mr-2"
                                        />
                                        <div>
                                            <p class="text-sm">
                                                DIPA:
                                                {{
                                                    formatRupiah(
                                                        proposal.dana_dipa_polban
                                                    )
                                                }}
                                            </p>
                                            <p class="text-sm">
                                                Swadaya:
                                                {{
                                                    formatRupiah(
                                                        proposal.dana_swadaya
                                                    )
                                                }}
                                            </p>
                                            <p class="text-sm">
                                                Sponsor:
                                                {{
                                                    formatRupiah(
                                                        proposal.dana_sponsor
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PDF Preview Tabs -->
                    <div v-else class="p-6">
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">
                                    {{
                                        documentTabs.find(
                                            (tab) => tab.id === activeTab
                                        ).name
                                    }}
                                </h3>
                                <div class="space-x-2">
                                    <a
                                        :href="
                                            getDocumentUrl(
                                                documentTabs.find(
                                                    (tab) =>
                                                        tab.id === activeTab
                                                ).path
                                            )
                                        "
                                        download
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700"
                                    >
                                        Download
                                    </a>
                                    <button
                                        @click="
                                            openDocument(
                                                documentTabs.find(
                                                    (tab) =>
                                                        tab.id === activeTab
                                                ).path
                                            )
                                        "
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700"
                                    >
                                        Buka di Tab Baru
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div
                            class="h-screen bg-gray-100 rounded-lg overflow-hidden"
                        >
                            <object
                                :data="
                                    getDocumentUrl(
                                        documentTabs.find(
                                            (tab) => tab.id === activeTab
                                        ).path
                                    )
                                "
                                type="application/pdf"
                                class="w-full h-full"
                            >
                                <div
                                    class="flex items-center justify-center h-full"
                                >
                                    <p class="text-gray-500">
                                        PDF tidak dapat ditampilkan. Silakan
                                        gunakan tombol "Buka di Tab Baru" atau
                                        "Download".
                                    </p>
                                </div>
                            </object>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.h-screen {
    height: calc(100vh - 200px);
}
</style>
