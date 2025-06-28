<script setup>
import { ref } from "vue";
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
    notulensi: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showPreviewModal = ref(false);
const editingNotulensi = ref(null);
const previewFile = ref(null);

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

const openCreateModal = () => {
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (notulensi) => {
    editingNotulensi.value = notulensi;
    editForm.title = notulensi.title;
    editForm.description = notulensi.description;
    showEditModal.value = true;
};

const submitCreate = () => {
    form.post(route("notulensi.store"), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            toast.success("Notulensi berhasil ditambahkan");
        },
        onError: () => {
            toast.error("Gagal menambahkan notulensi");
        },
    });
};

const submitEdit = () => {
    editForm.post(route("notulensi.update", editingNotulensi.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            toast.success("Notulensi berhasil diperbarui");
        },
        onError: () => {
            toast.error("Gagal memperbarui notulensi");
        },
    });
};

const deleteNotulensi = (id) => {
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
            router.delete(route("notulensi.destroy", id), {
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
</script>

<template>
    <AuthenticatedLayout title="Notulensi Management">
        <Head>
            <title>Notulensi Management</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Header Section -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold text-gray-800">
                                Daftar Notulensi
                            </h2>
                            <PrimaryButton @click="openCreateModal"
                                >Tambah Notulensi</PrimaryButton
                            >
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
                                        v-for="item in notulensi"
                                        :key="item.id"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ item.title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ item.description }}
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
                                                    deleteNotulensi(item.id)
                                                "
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Tambah Notulensi
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
                    Edit Notulensi
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
