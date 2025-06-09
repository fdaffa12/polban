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
        'karakter': 'Karakter',
        'penalaran': 'Penalaran',
        'peminatan': 'Peminatan',
        'pengabdian': 'Pengabdian'
    };
    return jenisMap[jenis] || jenis;
};
</script>

<template>
    <AuthenticatedLayout title="Manajemen Proposal Kegiatan">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Daftar Proposal Kegiatan</h2>
                        <PrimaryButton @click="router.visit(route('proposals.create'))">
                            Buat Proposal
                        </PrimaryButton>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kegiatan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PIC
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Perangkat
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tempat
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="proposal in proposals" :key="proposal.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                <img class="h-10 w-10 rounded-full object-cover" :src="`/storage/${proposal.poster}`" :alt="proposal.nama_kegiatan" />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ proposal.nama_kegiatan }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ proposal.bidang_kegiatan }} - {{ getJenisKegiatan(proposal.jenis_kegiatan) }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ proposal.pic_name }}</div>
                                        <div class="text-sm text-gray-500">{{ proposal.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ proposal.department ? proposal.department.dept_name : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ formatDate(proposal.tanggal_mulai) }}
                                        </div>
                                        <div class="text-sm text-gray-500" v-if="proposal.tanggal_mulai !== proposal.tanggal_akhir">
                                            s/d {{ formatDate(proposal.tanggal_akhir) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ proposal.tempat_kegiatan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="[
                                            'px-2 py-1 text-xs font-semibold rounded-full',
                                            getStatusClass(proposal.status)
                                        ]">
                                            {{ proposal.status.toUpperCase() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button
                                            @click="router.visit(route('proposals.show', proposal.id))"
                                            class="text-green-600 hover:text-green-900 mr-3"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="router.visit(route('proposals.edit', proposal.id))"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="deleteProposal(proposal)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="proposals.length === 0">
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        Belum ada proposal kegiatan. Klik tombol "Buat Proposal" untuk membuat proposal baru.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>