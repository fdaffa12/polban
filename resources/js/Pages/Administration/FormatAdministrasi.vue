<script setup>
import { ref, computed } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import Modal from "@/Components/Modal.vue";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const toast = useToast();
const props = defineProps({
    formatAdministrasi: Object,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showPreviewModal = ref(false);
const editingFormatAdministrasi = ref(null);
const previewFile = ref(null);
const search = ref("");
const perPage = ref(10);

// Computed property untuk filter data
const filteredFormatAdministrasi = computed(() => {
    if (!props.formatAdministrasi?.data) return [];
    if (!search.value) return props.formatAdministrasi.data;

    const searchTerm = search.value.toLowerCase();
    return props.formatAdministrasi.data.filter(
        (item) =>
            (item.title?.toLowerCase() || "").includes(searchTerm) ||
            (item.description?.toLowerCase() || "").includes(searchTerm)
    );
});

const form = useForm({
    title: "",
    file: null,
    description: "",
});

const editForm = useForm({
    title: "",
    file: null,
    description: "",
});

const getFileTypeLabel = (filePath) => {
    const type = getFileType(filePath);
    switch (type) {
        case "image":
            return "Gambar";
        case "video":
            return "Video";
        case "pdf":
            return "PDF";
        default:
            return "Dokumen";
    }
};

const getFileTypeIcon = (filePath) => {
    const type = getFileType(filePath);
    switch (type) {
        case "image":
            return "🖼️";
        case "video":
            return "🎥";
        case "pdf":
            return "📄";
        default:
            return "📎";
    }
};

const handlePerPageChange = () => {
    router.get(
        route("format-administrasi.index"),
        { search: search.value, per_page: perPage.value },
        { preserveState: true }
    );
};

const openCreateModal = () => {
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (formatAdministrasi) => {
    editingFormatAdministrasi.value = formatAdministrasi;
    editForm.title = formatAdministrasi.title;
    editForm.description = formatAdministrasi.description;
    showEditModal.value = true;
};

const submitCreate = () => {
    form.post(route("format-administrasi.store"), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            toast.success("Format administrasi berhasil ditambahkan");
        },
        onError: () => {
            toast.error("Gagal menambahkan format administrasi");
        },
    });
};

const submitEdit = () => {
    editForm.post(
        route("format-administrasi.update", editingFormatAdministrasi.value.id),
        {
            preserveScroll: true,
            onSuccess: () => {
                showEditModal.value = false;
                toast.success("Format administrasi berhasil diperbarui");
            },
            onError: () => {
                toast.error("Gagal memperbarui format administrasi");
            },
        }
    );
};

const deleteFormatAdministrasi = (id) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("format-administrasi.destroy", id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire("Terhapus!", "Data berhasil dihapus.", "success");
                },
                onError: () => {
                    Swal.fire("Gagal!", "Data gagal dihapus.", "error");
                },
            });
        }
    });
};

const getFileType = (filePath) => {
    if (!filePath) return "other";
    const extension = filePath.split(".").pop().toLowerCase();
    const imageTypes = ["jpg", "jpeg", "png", "gif", "webp"];
    const videoTypes = ["mp4", "webm", "ogg", "mkv"];

    if (imageTypes.includes(extension)) return "image";
    if (videoTypes.includes(extension)) return "video";
    if (extension === "pdf") return "pdf";
    return "other";
};

const openPreview = (file) => {
    previewFile.value = file;
    showPreviewModal.value = true;
};

const getFileExtension = (filePath) => {
    if (!filePath) return "";
    return filePath.split(".").pop().toLowerCase();
};
</script>

<template>
    <AuthenticatedLayout title="Format Administrasi Management">
        <Head>
            <title>Format Administrasi Management</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header Section with Search -->
                        <div
                            class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0"
                        >
                            <h2 class="text-2xl font-semibold text-gray-800">
                                Daftar Format Administrasi
                            </h2>
                            <div class="flex items-center space-x-4">
                                <TextInput
                                    v-model="search"
                                    type="search"
                                    placeholder="Cari format administrasi..."
                                    class="w-64"
                                />
                                <PrimaryButton @click="openCreateModal">
                                    Tambah Format Administrasi
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Judul
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Deskripsi
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Ekstensi File
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            File
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="item in filteredFormatAdministrasi"
                                        :key="item.id"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ item.title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ item.description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                                :class="{
                                                    'bg-blue-100 text-blue-800':
                                                        [
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                        ].includes(
                                                            getFileExtension(
                                                                item.file_path
                                                            )
                                                        ),
                                                    'bg-green-100 text-green-800':
                                                        [
                                                            'mp4',
                                                            'mkv',
                                                            'webm',
                                                        ].includes(
                                                            getFileExtension(
                                                                item.file_path
                                                            )
                                                        ),
                                                    'bg-red-100 text-red-800':
                                                        getFileExtension(
                                                            item.file_path
                                                        ) === 'pdf',
                                                    'bg-gray-100 text-gray-800':
                                                        ![
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                            'mp4',
                                                            'mkv',
                                                            'webm',
                                                            'pdf',
                                                        ].includes(
                                                            getFileExtension(
                                                                item.file_path
                                                            )
                                                        ),
                                                }"
                                            >
                                                {{
                                                    getFileExtension(
                                                        item.file_path
                                                    )
                                                }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <template
                                                v-if="
                                                    [
                                                        'image',
                                                        'video',
                                                        'pdf',
                                                    ].includes(
                                                        getFileType(
                                                            item.file_path
                                                        )
                                                    )
                                                "
                                            >
                                                <button
                                                    @click="openPreview(item)"
                                                    class="text-blue-600 hover:text-blue-800"
                                                >
                                                    Preview
                                                </button>
                                            </template>
                                            <template v-else>
                                                <a
                                                    :href="`/storage/${item.file_path}`"
                                                    target="_blank"
                                                    class="text-blue-600 hover:text-blue-800"
                                                >
                                                    Download
                                                </a>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button
                                                @click="openEditModal(item)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="
                                                    deleteFormatAdministrasi(
                                                        item.id
                                                    )
                                                "
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr
                                        v-if="
                                            filteredFormatAdministrasi.length ===
                                            0
                                        "
                                    >
                                        <td
                                            colspan="5"
                                            class="px-6 py-4 text-center text-gray-500"
                                        >
                                            Tidak ada data yang ditemukan
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700"
                                    >Tampilkan</span
                                >
                                <select
                                    v-model="perPage"
                                    class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    @change="handlePerPageChange"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm text-gray-700"
                                    >per halaman</span
                                >
                            </div>

                            <div class="flex items-center space-x-2">
                                <button
                                    v-for="link in formatAdministrasi.links"
                                    :key="link.label"
                                    class="px-3 py-1 rounded"
                                    :class="{
                                        'bg-indigo-500 text-white': link.active,
                                        'text-gray-700 hover:bg-gray-100':
                                            !link.active,
                                        'opacity-50 cursor-not-allowed':
                                            !link.url,
                                    }"
                                    @click="link.url && router.get(link.url)"
                                    v-html="link.label"
                                ></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Tambah Format Administrasi
                </h2>
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <InputLabel for="title" value="Judul" />
                        <TextInput
                            id="title"
                            type="text"
                            v-model="form.title"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="file" value="File" />
                        <input
                            type="file"
                            id="file"
                            @input="form.file = $event.target.files[0]"
                            class="mt-1 block w-full"
                            required
                        />
                        <p class="text-sm text-gray-500 mt-1">
                            Maksimal ukuran file: 10MB
                        </p>
                        <InputError :message="form.errors.file" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="description" value="Deskripsi" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            rows="3"
                        ></textarea>
                        <InputError
                            :message="form.errors.description"
                            class="mt-2"
                        />
                    </div>

                    <div class="flex justify-end mt-6">
                        <PrimaryButton :disabled="form.processing"
                            >Simpan</PrimaryButton
                        >
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Modal -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Edit Format Administrasi
                </h2>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <InputLabel for="edit_title" value="Judul" />
                        <TextInput
                            id="edit_title"
                            type="text"
                            v-model="editForm.title"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="editForm.errors.title"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_file" value="File" />
                        <input
                            type="file"
                            id="edit_file"
                            @input="editForm.file = $event.target.files[0]"
                            class="mt-1 block w-full"
                        />
                        <p class="text-sm text-gray-500 mt-1">
                            Biarkan kosong jika tidak ingin mengubah file
                        </p>
                        <InputError
                            :message="editForm.errors.file"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_description" value="Deskripsi" />
                        <textarea
                            id="edit_description"
                            v-model="editForm.description"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            rows="3"
                        ></textarea>
                        <InputError
                            :message="editForm.errors.description"
                            class="mt-2"
                        />
                    </div>

                    <div class="flex justify-end mt-6">
                        <PrimaryButton :disabled="editForm.processing"
                            >Update</PrimaryButton
                        >
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Preview Modal -->
        <Modal
            :show="showPreviewModal"
            @close="showPreviewModal = false"
            :maxWidth="
                previewFile && getFileType(previewFile.file_path) === 'pdf'
                    ? '2xl'
                    : 'md'
            "
        >
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900">
                        Preview File: {{ previewFile?.title }}
                    </h2>
                    <button
                        @click="showPreviewModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <svg
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

                <div v-if="previewFile" class="mt-4">
                    <!-- Image Preview -->
                    <div
                        v-if="
                            previewFile.file_path &&
                            getFileType(previewFile.file_path) === 'image'
                        "
                        class="flex justify-center items-center"
                    >
                        <img
                            :src="`/storage/${previewFile.file_path}`"
                            class="max-h-[500px] w-auto object-contain"
                            :alt="previewFile.title"
                        />
                    </div>

                    <!-- Video Preview -->
                    <div
                        v-if="
                            previewFile.file_path &&
                            getFileType(previewFile.file_path) === 'video'
                        "
                        class="flex justify-center items-center"
                    >
                        <video class="max-h-[500px] w-auto" controls>
                            <source
                                :src="`/storage/${previewFile.file_path}`"
                                :type="`video/${previewFile.file_path
                                    .split('.')
                                    .pop()}`"
                            />
                            Browser Anda tidak mendukung pemutaran video.
                        </video>
                    </div>

                    <!-- PDF Preview -->
                    <div
                        v-if="
                            previewFile.file_path &&
                            getFileType(previewFile.file_path) === 'pdf'
                        "
                        class="flex justify-center items-center"
                    >
                        <iframe
                            :src="`/storage/${previewFile.file_path}`"
                            class="w-[595px] h-[842px]"
                            type="application/pdf"
                        >
                            <p>Browser Anda tidak mendukung preview PDF.</p>
                        </iframe>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
