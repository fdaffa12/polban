<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Edit, Trash2, Eye, FileText } from "lucide-vue-next";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const props = defineProps({
    proposals: Array,
});

const toast = useToast();

const deleteProposal = (proposal) => {
    Swal.fire({
        title: "Hapus Proposal?",
        text: "Tindakan ini tidak dapat dibatalkan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("proposals.destroy", proposal.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("Proposal berhasil dihapus");
                },
                onError: () => toast.error("Gagal menghapus proposal"),
            });
        }
    });
};

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

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });
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

const getJenisKegiatan = (jenis) => {
    const jenisMap = {
        karakter: "Karakter",
        penalaran: "Penalaran",
        peminatan: "Peminatan",
        pengabdian: "Pengabdian",
    };
    return jenisMap[jenis] || jenis;
};

// Tambahkan fungsi untuk memformat tanggal range
const formatDateRange = (startDate, endDate) => {
    const start = formatDate(startDate);
    const end = formatDate(endDate);
    return start === end ? start : `${start} s/d ${end}`;
};
</script>

<template>
    <AuthenticatedLayout title="Manajemen Proposal Kegiatan">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            Daftar Proposal Kegiatan
                        </h2>
                        <PrimaryButton
                            @click="router.visit(route('proposals.create'))"
                        >
                            Buat Proposal
                        </PrimaryButton>
                    </div>

                    <!-- Grid Card Layout -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="proposal in proposals"
                            :key="proposal.id"
                            class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
                        >
                            <!-- Card Header dengan Gambar -->
                            <div class="relative">
                                <img
                                    :src="`/storage/${proposal.poster}`"
                                    :alt="proposal.nama_kegiatan"
                                    class="w-full h-48 object-cover rounded-t-lg"
                                />

                                <!-- Status Badge -->
                                <span
                                    :class="[
                                        'absolute top-4 right-4 px-3 py-1 text-sm font-semibold rounded-full',
                                        getStatusClass(proposal.status),
                                    ]"
                                >
                                    {{ proposal.status.toUpperCase() }}
                                </span>
                            </div>

                            <!-- Card Content -->
                            <div class="p-4">
                                <h3
                                    class="text-lg font-semibold text-gray-900 mb-2"
                                >
                                    {{ proposal.nama_kegiatan }}
                                </h3>

                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center">
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
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        {{ proposal.pic_name }}
                                    </div>

                                    <div class="flex items-center">
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
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                            />
                                        </svg>
                                        {{
                                            proposal.department
                                                ? proposal.department.dept_name
                                                : "-"
                                        }}
                                    </div>

                                    <div class="flex items-center">
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
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        {{
                                            formatDateRange(
                                                proposal.tanggal_mulai,
                                                proposal.tanggal_akhir
                                            )
                                        }}
                                    </div>

                                    <div class="flex items-center">
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
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                        {{ proposal.tempat_kegiatan }}
                                    </div>
                                </div>

                                <!-- Card Actions -->
                                <div class="mt-4 flex justify-end space-x-2">
                                    <button
                                        @click="
                                            router.visit(
                                                route(
                                                    'proposals.show',
                                                    proposal.id
                                                )
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    >
                                        <Eye class="w-4 h-4 mr-1" />
                                        Preview
                                    </button>
                                    <button
                                        @click="
                                            router.visit(
                                                route(
                                                    'proposals.edit',
                                                    proposal.id
                                                )
                                            )
                                        "
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        <Edit class="w-4 h-4 mr-1" />
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteProposal(proposal)"
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                    >
                                        <Trash2 class="w-4 h-4 mr-1" />
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div
                            v-if="proposals.length === 0"
                            class="col-span-full text-center py-12"
                        >
                            <div class="text-gray-500">
                                Belum ada proposal kegiatan. Klik tombol "Buat
                                Proposal" untuk membuat proposal baru.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
