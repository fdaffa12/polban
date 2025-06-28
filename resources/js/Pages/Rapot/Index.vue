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
    rapotHmjt: Object,
    users: Array,
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showPreviewModal = ref(false);
const editingRapot = ref(null);
const previewFile = ref(null);
const search = ref("");
const perPage = ref(10);

// Computed property untuk filter data
const filteredRapot = computed(() => {
    if (!props.rapotHmjt?.data) return [];
    if (!search.value) return props.rapotHmjt.data;

    const searchTerm = search.value.toLowerCase();
    return props.rapotHmjt.data.filter(
        (item) =>
            (item.user?.name?.toLowerCase() || "").includes(searchTerm) ||
            (item.jabatan?.toLowerCase() || "").includes(searchTerm) ||
            (item.description?.toLowerCase() || "").includes(searchTerm)
    );
});

const form = useForm({
    user_id: "",
    jabatan: "",
    periode_awal: "",
    periode_akhir: "",
    file: null,
    description: "",
});

const editForm = useForm({
    user_id: "",
    jabatan: "",
    periode_awal: "",
    periode_akhir: "",
    file: null,
    description: "",
});

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

const handlePerPageChange = () => {
    router.get(
        route("rapot-hmjt.index"),
        { search: search.value, per_page: perPage.value },
        { preserveState: true }
    );
};

const openCreateModal = () => {
    form.reset();
    showCreateModal.value = true;
};

const openEditModal = (rapot) => {
    editingRapot.value = rapot;
    editForm.user_id = rapot.user_id;
    editForm.jabatan = rapot.jabatan;
    editForm.periode_awal = rapot.periode_awal;
    editForm.periode_akhir = rapot.periode_akhir;
    editForm.description = rapot.description;
    showEditModal.value = true;
};

const submitCreate = () => {
    form.post(route("rapot-hmjt.store"), {
        preserveScroll: true,
        onSuccess: () => {
            showCreateModal.value = false;
            toast.success("Rapot HMJTK berhasil ditambahkan");
        },
        onError: () => {
            toast.error("Gagal menambahkan rapot HMJTK");
        },
    });
};

const submitEdit = () => {
    editForm.post(route("rapot-hmjt.update", editingRapot.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showEditModal.value = false;
            toast.success("Rapot HMJTK berhasil diperbarui");
        },
        onError: () => {
            toast.error("Gagal memperbarui rapot HMJTK");
        },
    });
};

const deleteRapot = (id) => {
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
            router.delete(route("rapot-hmjt.destroy", id), {
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
    <AuthenticatedLayout title="Rapot HMJTK Management">
        <Head>
            <title>Rapot HMJTK Management</title>
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
                                Daftar Rapot HMJTK
                            </h2>
                            <div class="flex items-center space-x-4">
                                <TextInput
                                    v-model="search"
                                    type="search"
                                    placeholder="Cari rapot..."
                                    class="w-64"
                                />
                                <PrimaryButton @click="openCreateModal">
                                    Tambah Rapot
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
                                            Nama
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Jabatan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Periode
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
                                        v-for="item in filteredRapot"
                                        :key="item.id"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ item.user?.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ item.jabatan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ item.periode_awal }} -
                                            {{ item.periode_akhir }}
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
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                        >
                                            <button
                                                @click="openEditModal(item)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                @click="deleteRapot(item.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredRapot.length === 0">
                                        <td
                                            colspan="6"
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
                                    v-for="link in rapotHmjt.links"
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
                    Tambah Rapot HMJTK
                </h2>
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <InputLabel for="user_id" value="Nama" />
                        <select
                            id="user_id"
                            v-model="form.user_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Pilih Nama</option>
                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.user_id"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel for="jabatan" value="Jabatan" />
                        <TextInput
                            id="jabatan"
                            type="text"
                            v-model="form.jabatan"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.jabatan"
                            class="mt-2"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel
                                for="periode_awal"
                                value="Periode Awal"
                            />
                            <input
                                id="periode_awal"
                                type="month"
                                v-model="form.periode_awal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <InputError
                                :message="form.errors.periode_awal"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="periode_akhir"
                                value="Periode Akhir"
                            />
                            <input
                                id="periode_akhir"
                                type="month"
                                v-model="form.periode_akhir"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <InputError
                                :message="form.errors.periode_akhir"
                                class="mt-2"
                            />
                        </div>
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
                    Edit Rapot HMJTK
                </h2>
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <div>
                        <InputLabel for="edit_user_id" value="Nama" />
                        <select
                            id="edit_user_id"
                            v-model="editForm.user_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >
                            <option value="">Pilih Nama</option>
                            <option
                                v-for="user in users"
                                :key="user.id"
                                :value="user.id"
                            >
                                {{ user.name }}
                            </option>
                        </select>
                        <InputError
                            :message="editForm.errors.user_id"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel for="edit_jabatan" value="Jabatan" />
                        <TextInput
                            id="edit_jabatan"
                            type="text"
                            v-model="editForm.jabatan"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="editForm.errors.jabatan"
                            class="mt-2"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel
                                for="edit_periode_awal"
                                value="Periode Awal"
                            />
                            <input
                                id="edit_periode_awal"
                                type="month"
                                v-model="editForm.periode_awal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <InputError
                                :message="editForm.errors.periode_awal"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="edit_periode_akhir"
                                value="Periode Akhir"
                            />
                            <input
                                id="edit_periode_akhir"
                                type="month"
                                v-model="editForm.periode_akhir"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                            <InputError
                                :message="editForm.errors.periode_akhir"
                                class="mt-2"
                            />
                        </div>
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
                        Preview File
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
