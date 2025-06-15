<script setup>
import { ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { useToast } from "vue-toastification";
import { Eye, FileText, Trash2, Edit } from "lucide-vue-next";
import Swal from "sweetalert2";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    proposal: Object,
    spjList: Array,
});

const toast = useToast();
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingSpj = ref(null);

const form = useForm({
    doc_sptp: null,
    doc_spj: null,
    doc_berita_acara: null,
    gambar_bukti_spj: null,
    video: null,
    caption_video: "",
});

// State untuk preview image
const imagePreview = ref({
    gambar_bukti_spj: null,
});

const showToast = (icon, title) => {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener("mouseenter", Swal.stopTimer);
            toast.addEventListener("mouseleave", Swal.resumeTimer);
        },
    });

    Toast.fire({
        icon,
        title,
    });
};

const handleSubmit = () => {
    form.post(route("proposals.spj.store", props.proposal.id), {
        onSuccess: () => {
            showAddModal.value = false;
            form.reset();
            toast.success("SPJ berhasil ditambahkan");
        },
        onError: () => {
            toast.error("Gagal menambahkan SPJ");
        },
    });
};

const handleEdit = (spj) => {
    editingSpj.value = spj;
    showEditModal.value = true;
};

const handleUpdate = () => {
    form.post(route("proposals.spj.update", editingSpj.value.id), {
        _method: "PUT",
        onSuccess: () => {
            showEditModal.value = false;
            editingSpj.value = null;
            form.reset();
            toast.success("SPJ berhasil diperbarui");
        },
        onError: () => {
            toast.error("Gagal memperbarui SPJ");
        },
    });
};

const handleDelete = (spj) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "SPJ yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF4444",
        cancelButtonColor: "#6B7280",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("proposals.spj.destroy", spj.id), {
                onSuccess: () => {
                    toast.success("SPJ berhasil dihapus");
                },
                onError: () => {
                    toast.error("SPJ gagal dihapus");
                },
            });
        }
    });
};

// Handle image upload
const handleImageUpload = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        form[field] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value[field] = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Handle video upload
const handleVideoUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.video = file;
    }
};

// Remove file
const removeFile = (fieldName) => {
    form[fieldName] = null;
    if (imagePreview.value[fieldName]) {
        imagePreview.value[fieldName] = null;
    }
    const fileInput = document.getElementById(fieldName);
    if (fileInput) {
        fileInput.value = "";
    }
};
</script>

<template>
    <AuthenticatedLayout title="SPJ Proposal">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            SPJ - {{ proposal.nama_kegiatan }}
                        </h2>
                        <PrimaryButton
                            @click="showAddModal = true"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                        >
                            Tambah SPJ
                        </PrimaryButton>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        No
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Nama Kegiatan
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Tanggal Unggah
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Tahap Review
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr
                                    v-for="(spj, index) in spjList"
                                    :key="spj.id"
                                >
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ proposal.nama_kegiatan }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            :class="{
                                                'px-2 py-1 text-xs font-medium rounded-full': true,
                                                'bg-yellow-100 text-yellow-800':
                                                    spj.status === 'revised',
                                                'bg-green-100 text-green-800':
                                                    spj.status === 'approved',
                                                'bg-gray-100 text-gray-800':
                                                    !spj.status,
                                            }"
                                        >
                                            {{
                                                spj.status
                                                    ? spj.status === "revised"
                                                        ? "Perlu Revisi"
                                                        : "Disetujui"
                                                    : "Menunggu Review"
                                            }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{
                                            new Date(
                                                spj.created_at
                                            ).toLocaleDateString("id-ID", {
                                                day: "numeric",
                                                month: "long",
                                                year: "numeric",
                                            })
                                        }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div
                                            v-if="spj.approved_at"
                                            class="text-sm text-gray-600"
                                        >
                                            Disetujui oleh Sekertaris Kabinet
                                        </div>
                                        <div
                                            v-else-if="spj.review_at"
                                            class="text-sm text-gray-600"
                                        >
                                            Direview oleh Sekertaris Kabinet
                                        </div>
                                        <div
                                            v-else
                                            class="text-sm text-gray-600"
                                        >
                                            Menunggu Review
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-2">
                                            <button
                                                @click="
                                                    router.visit(
                                                        route(
                                                            'proposals.spj.show',
                                                            {
                                                                proposal:
                                                                    proposal.id,
                                                                spj: spj.id,
                                                            }
                                                        )
                                                    )
                                                "
                                                class="text-blue-600 hover:text-blue-800"
                                                title="Preview"
                                            >
                                                <Eye class="w-5 h-5" />
                                            </button>
                                            <button
                                                v-if="!spj.approved_at"
                                                @click="handleDelete(spj)"
                                                class="text-red-600 hover:text-red-800"
                                                title="Delete"
                                            >
                                                <Trash2 class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div
            v-if="showAddModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto"
        >
            <div
                class="bg-white rounded-lg w-full max-w-4xl mx-4 my-8 p-4 sm:p-6 lg:p-8 relative"
            >
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4 sm:mb-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-900">
                        Tambah SPJ Baru
                    </h3>
                    <button
                        @click="showAddModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <svg
                            class="h-5 w-5 sm:h-6 sm:w-6"
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

                <form
                    @submit.prevent="handleSubmit"
                    class="space-y-4 sm:space-y-6"
                >
                    <!-- Form Grid Layout -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- SPTP -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Dokumen SPTP</label
                            >
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label
                                        class="flex flex-col w-full h-24 sm:h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-3 pb-4 sm:pt-5 sm:pb-6"
                                        >
                                            <svg
                                                class="w-6 h-6 sm:w-8 sm:h-8 mb-2 sm:mb-4 text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                                />
                                            </svg>
                                            <p
                                                class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500"
                                            >
                                                Klik untuk upload SPTP
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                PDF (MAX. 10MB)
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            class="hidden"
                                            @input="
                                                form.doc_sptp =
                                                    $event.target.files[0]
                                            "
                                            accept=".pdf"
                                        />
                                    </label>
                                    <!-- Preview File Name -->
                                    <div
                                        v-if="form.doc_sptp"
                                        class="mt-2 flex items-center gap-2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{
                                            form.doc_sptp.name
                                        }}</span>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <button
                                    v-if="form.doc_sptp"
                                    @click="removeFile('doc_sptp')"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
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
                            <div
                                v-if="form.errors.doc_sptp"
                                class="text-xs sm:text-sm text-red-600"
                            >
                                {{ form.errors.doc_sptp }}
                            </div>
                        </div>

                        <!-- SPJ -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Dokumen SPJ</label
                            >
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label
                                        class="flex flex-col w-full h-24 sm:h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-3 pb-4 sm:pt-5 sm:pb-6"
                                        >
                                            <svg
                                                class="w-6 h-6 sm:w-8 sm:h-8 mb-2 sm:mb-4 text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                                />
                                            </svg>
                                            <p
                                                class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500"
                                            >
                                                Klik untuk upload SPJ
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                PDF (MAX. 10MB)
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            class="hidden"
                                            @input="
                                                form.doc_spj =
                                                    $event.target.files[0]
                                            "
                                            accept=".pdf"
                                        />
                                    </label>
                                    <!-- Preview File Name -->
                                    <div
                                        v-if="form.doc_spj"
                                        class="mt-2 flex items-center gap-2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{
                                            form.doc_spj.name
                                        }}</span>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <button
                                    v-if="form.doc_spj"
                                    @click="removeFile('doc_spj')"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
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
                            <div
                                v-if="form.errors.doc_spj"
                                class="text-xs sm:text-sm text-red-600"
                            >
                                {{ form.errors.doc_spj }}
                            </div>
                        </div>

                        <!-- Berita Acara -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Dokumen Berita Acara</label
                            >
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label
                                        class="flex flex-col w-full h-24 sm:h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-3 pb-4 sm:pt-5 sm:pb-6"
                                        >
                                            <svg
                                                class="w-6 h-6 sm:w-8 sm:h-8 mb-2 sm:mb-4 text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                                />
                                            </svg>
                                            <p
                                                class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500"
                                            >
                                                Klik untuk upload Berita Acara
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                PDF (MAX. 10MB)
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            class="hidden"
                                            @input="
                                                form.doc_berita_acara =
                                                    $event.target.files[0]
                                            "
                                            accept=".pdf"
                                        />
                                    </label>
                                    <!-- Preview File Name -->
                                    <div
                                        v-if="form.doc_berita_acara"
                                        class="mt-2 flex items-center gap-2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{
                                            form.doc_berita_acara.name
                                        }}</span>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <button
                                    v-if="form.doc_berita_acara"
                                    @click="removeFile('doc_berita_acara')"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
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
                            <div
                                v-if="form.errors.doc_berita_acara"
                                class="text-xs sm:text-sm text-red-600"
                            >
                                {{ form.errors.doc_berita_acara }}
                            </div>
                        </div>

                        <!-- Gambar Bukti -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Gambar Bukti SPJ</label
                            >
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label
                                        class="flex flex-col w-full h-24 sm:h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-3 pb-4 sm:pt-5 sm:pb-6"
                                        >
                                            <svg
                                                class="w-6 h-6 sm:w-8 sm:h-8 mb-2 sm:mb-4 text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            <p
                                                class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500"
                                            >
                                                Klik untuk upload Gambar
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                JPG, PNG (MAX. 2MB)
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            class="hidden"
                                            @input="
                                                handleImageUpload(
                                                    $event,
                                                    'gambar_bukti_spj'
                                                )
                                            "
                                            accept="image/*"
                                        />
                                    </label>
                                    <!-- Preview Image -->
                                    <div
                                        v-if="imagePreview.gambar_bukti_spj"
                                        class="mt-2"
                                    >
                                        <img
                                            :src="imagePreview.gambar_bukti_spj"
                                            alt="Preview"
                                            class="max-w-xs h-auto rounded-lg shadow-sm"
                                        />
                                    </div>
                                    <!-- Preview File Name -->
                                    <div
                                        v-if="form.gambar_bukti_spj"
                                        class="mt-2 flex items-center gap-2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{
                                            form.gambar_bukti_spj.name
                                        }}</span>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <button
                                    v-if="form.gambar_bukti_spj"
                                    @click="removeFile('gambar_bukti_spj')"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
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
                            <div
                                v-if="form.errors.gambar_bukti_spj"
                                class="text-xs sm:text-sm text-red-600"
                            >
                                {{ form.errors.gambar_bukti_spj }}
                            </div>
                        </div>

                        <!-- Video -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Video Dokumentasi</label
                            >
                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label
                                        class="flex flex-col w-full h-24 sm:h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50"
                                    >
                                        <div
                                            class="flex flex-col items-center justify-center pt-3 pb-4 sm:pt-5 sm:pb-6"
                                        >
                                            <svg
                                                class="w-6 h-6 sm:w-8 sm:h-8 mb-2 sm:mb-4 text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                                />
                                            </svg>
                                            <p
                                                class="mb-1 sm:mb-2 text-xs sm:text-sm text-gray-500"
                                            >
                                                Klik untuk upload Video
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                MP4, MOV, AVI (MAX. 100MB)
                                            </p>
                                        </div>
                                        <input
                                            type="file"
                                            class="hidden"
                                            @input="handleVideoUpload($event)"
                                            accept="video/*"
                                        />
                                    </label>
                                    <!-- Preview Video Name -->
                                    <div
                                        v-if="form.video"
                                        class="mt-2 flex items-center gap-2"
                                    >
                                        <svg
                                            class="w-5 h-5 text-gray-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{
                                            form.video.name
                                        }}</span>
                                    </div>
                                </div>
                                <!-- Remove Button -->
                                <button
                                    v-if="form.video"
                                    @click="removeFile('video')"
                                    type="button"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
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
                            <div
                                v-if="form.errors.video"
                                class="text-xs sm:text-sm text-red-600"
                            >
                                {{ form.errors.video }}
                            </div>
                        </div>

                        <!-- Caption Video - Full Width -->
                        <div class="space-y-2 md:col-span-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Caption Video</label
                            >
                            <textarea
                                v-model="form.caption_video"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-xs sm:text-sm"
                                placeholder="Masukkan caption video..."
                            ></textarea>
                            <div
                                v-if="form.errors.caption_video"
                                class="text-xs sm:text-sm text-red-600"
                            >
                                {{ form.errors.caption_video }}
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div
                        class="flex justify-end space-x-3 pt-4 sm:pt-6 border-t"
                    >
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ form.processing ? "Menyimpan..." : "Simpan" }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal - Gunakan struktur yang sama dengan Add Modal, sesuaikan judul dan handler -->
    </AuthenticatedLayout>
</template>
